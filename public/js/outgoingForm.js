class OutgoingForm
{
    constructor() 
    {
        let that = this; // fix around Jquery "this" handling
        this.lineNumber = 0;

        $('#feedbackOutgoingForm').hide();

        // handling delete button for existing lines
        $('.orderRow').each(function() {
            that.addDeleteEventForReserved($(this));
            that.addArticleSuggestion($(this));
            that.addLocationSuggestion($(this));
        });

        $('#addRowButton').click(function(e) {
            e.preventDefault();
            that.addRow();
        });

        $('#outgoingForm').submit(function() {
            let $url = $('#outgoingForm').attr('action');
            let $params = $(this).serialize();

            // some articles must have been added
            if ($('.orderRow').length < 1) {
                $('#feedbackOutgoingForm').show();
                $('#feedbackOutgoingForm').text('Vous devez enregister au moins un article.');
                return false;
            }       
            
            let regexp = /^[\w-_ ]+$/;
            if (!regexp.test($('#provider').val()) || !regexp.test($('#reference').val())) {
                $('#feedbackOutgoingForm').show();
                $('#feedbackOutgoingForm').text('Vous devez utiliser des chiffres, des lettres non accentuées ou bien des tirets ou underscores.');
                return false;
            }

            $.post($url, $params, function(data) {
                if (data === 'moveOK' || data == 'tokenError') {
                    $(window).attr('location', '/outgoing/index');
                }

                if (data === 'moveNOK') {
                    $('#feedbackOutgoingForm').show();
                    $('#feedbackOutgoingForm').text('Une erreur est survenue lors de la validation.');
                }
            });
            return false; 
        });
    }

    addDeleteEvent(orderRow)
    {
        let that = this; // fix around Jquery "this" handling

        orderRow.find('.button').last().click(function(e) {
            e.preventDefault();
            orderRow.remove();
        });        
    }

    addDeleteEventForReserved(orderRow)
    {
        let $currentId = $('#currentId').val(); 

        orderRow.find('.button').last().click(function(e) {
            if ($currentId !== '' && confirm('Ces produits ne seront plus réservés. Confirmer ?')) {
                e.preventDefault();
                let $articleField = orderRow.children().first().find('input');
                $articleField.val($.trim($articleField.val()));
                let code = $articleField.val();
                $.ajax({
                    type: 'POST',
                    url: '/outgoing/unreserve',
                    data: {
                        id:$currentId, 
                        code:code
                    },
                    success: function(data){
                        console.log(data);
                        if (data === true) {
                            orderRow.remove();
                        } else {
                            $('#feedbackOutgoingForm').show();
                            $('#feedbackOutgoingForm').text('Une erreur est survenue lors de la dé-réservation');
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        $('#feedbackOutgoingForm').show();
                        $('#feedbackOutgoingForm').text('Une erreur est survenue lors de la dé-réservation');
                    },
                    dataType: "json"
                });
            }    
        });
    }

    addArticleSuggestion(orderRow)
    {
        orderRow.find('input').first().keyup(function(e) {
            let $ul = $(this).parent().find('.dynamicList').first();
            $ul.html('');

            let $articleField = $(e.target);

            if ($.trim($articleField.val()) !== '') {
                $.ajax({
                    type: 'POST',
                    url: '/article/suggestions',
                    data: {code:$.trim($articleField.val())},
                    success: function(data){
                        if (data.length !== 0) {
                            $ul.html('');
                            for (let i=0; i < data.length; i++) {
                                $ul.append('<li class="dynamicListItem">' + data[i] + '</li>');
                                $ul.find('li').last().click(function(e) {
                                    $articleField.val(data[i]);
                                    $ul.html('');
                                });
                            }
                        } else {
                            $ul.html('');
                            $ul.append('<li>Pas de résultat</li>');
                        }
                    },
                    dataType: "json"
                });
            }
        });
    }

    addLocationSuggestion(orderRow)
    {
        let that = this; // fix around Jquery "this" handling

        orderRow.find('.button').first().click(function(e) {
            e.preventDefault();

            let $sl = $(this).parent().find('.stocksList').first();
            $sl.html('');

            let $articleField = orderRow.children().first().find('input');
            let $quantityField = $articleField.parent().parent().find('input[type="number"]');

            $articleField.val($.trim($articleField.val()));

            let code = $articleField.val();

            let regex = /^article([0-9]+)$/;
            let currentLine = $articleField.attr('name').match(regex)[1];

            let quantity = $quantityField.val();
            let articleOccurences = 0;

            // an Article code can only be used once in an order
            $('.orderRow input[type="text"]').each(function() {
                if ($(this).val() === code) {
                    articleOccurences += 1;
                }
            });

            if (articleOccurences <= 1 && code !== '' && quantity !== '' && quantity >= 1) {
                $.ajax({
                    type: 'POST',
                    url: '/outgoing/available',
                    data: {
                        code:code,
                        quantity:quantity
                    },
                    success: function(data) {
                        if (data.length !== 0) {
                            $sl.html('');
                            for (let i=0; i < data.length; i++) {
                                $sl.append(`<label for="location${currentLine}_${i}">Loc.<input type="text" name="location${currentLine}_${i}" id="location${currentLine}_${i}" value="${data[i]['location']}" readonly></label>`);
                                $sl.append(`<label for="qty${currentLine}_${i}">Qté<input type="text" name="qty${currentLine}_${i}" id="qty${currentLine}_${i}" value="${data[i]['availableQty']}" readonly></label>`);
                                if (i < data.length - 1) {
                                    $sl.append('<hr>');
                                }
                            }
                        } else {
                            $sl.html('');
                            $sl.append('<p>Quantité insuffisante</p>');
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        $sl.html('');
                        console.log(jqXHR)
                        $sl.append('<p>Les données saisies sont incorrectes</p>');
                    },
                    dataType: "json"
                });
            } else if (articleOccurences > 1) {
                $sl.html('');
                $sl.append(`<p>Le code ${code} se trouve déjà dans la liste</p>`);
            } else if (quantity <= 0) {
                $sl.html('');
                $sl.append('<p>La quantité doit être supérieure ou égale à 1</p>');
            }
        });
    }

    addRow()
    {
        let i = this.lineNumber;
        let row = '';
        row += '<div class="orderRow">';
        row += '<div class="orderField">';
        row += `<label for="article${i}">Article</label>`;
        row += `<input type="text" name="article${i}" id="article${i}" autocomplete="off" required>`;
        row += '<ul class="dynamicList"></ul>';
        row += '</div>';
        row += '<div class="orderField">';
        row += `<label for="globalQty${i}">Quantité</label>`;
        row += `<input type="number" min="1" name="globalQty${i}" id="globalQty${i}" required>`;
        row += '</div>';
        row += '<div class="orderField">';
        row += `<h4>Emplacements</h4>`;
        row += '<a href="#" class="button icon search-icon">Chercher</a>';
        row += '<div class="stocksList"></div>';
        row += '</div>';
        row += '<div class="orderField">';
        row += '<a href="#" class="button icon delete-icon">Effacer</a>';
        row += '</div>';
        row += '</div>';
        $('#orderRows').append(row);
        this.lineNumber++;

        // handling delete button
        this.addDeleteEvent($('.orderRow').last());

        // handling article input
        this.addArticleSuggestion($('.orderRow').last());

        // handling location input
        this.addLocationSuggestion($('.orderRow').last());
    }
}

$(document).ready(function() {
    new OutgoingForm();
});
