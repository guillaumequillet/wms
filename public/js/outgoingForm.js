class OutgoingForm
{
    constructor() 
    {
        let that = this; // fix around Jquery "this" handling
        this.lineNumber = 0;

        $('#feedbackOutgoingForm').hide();

        // handling delete button for existing lines
        $('.orderRow').each(function() {
            that.addDeleteEvent($(this));
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

            let $ul = $(this).parent().find('.dynamicList').first();
            $ul.html('');

            let $articleField = orderRow.children().first().find('input');
            let $quantityField = $articleField.parent().parent().find('input[type="number"]');

            $articleField.val($.trim($articleField.val()));

            let code = $articleField.val();
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
                            $ul.html('');
                            for (let i=0; i < data.length; i++) {
                                $ul.append(`<li class="dynamicListItem">${data[i]['location']} [ x ${data[i]['availableQty']} ]</li>`);
                            }
                        } else {
                            $ul.html('');
                            $ul.append('<li>Quantité insuffisante</li>');
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        $ul.html('');
                        console.log(jqXHR)
                        $ul.append('<li>Les données saisies sont incorrectes</li>');
                    },
                    dataType: "json"
                });
            } else if (articleOccurences > 1) {
                $ul.html('');
                $ul.append(`<li>Le code ${code} se trouve déjà dans la liste</li>`);
            } else if (quantity <= 0) {
                $ul.html('');
                $ul.append('<li>La quantité doit être supérieure ou égale à 1</li>');
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
        row += `<label for="quantity${i}">Quantité</label>`;
        row += `<input type="number" min="1" name="quantity${i}" id="quantity${i}" required>`;
        row += '</div>';
        row += '<div class="orderField">';
        row += `<label for="location${i}">Emplacements</label>`;
        row += '<a href="#" class="button icon search-icon">Chercher</a>';
        row += '<ul class="dynamicList"></ul>';
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
