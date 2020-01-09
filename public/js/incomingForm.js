class IncomingForm
{
    constructor() 
    {
        let that = this; // fix around Jquery "this" handling
        this.lineNumber = $('.orderRow').length;

        $('#feedbackIncomingForm').hide();

        $('.orderRow').each(function() {
            that.addDeleteEvent($(this));
            that.addArticleSuggestion($(this));
            that.addLocationSuggestion($(this));
        });

        $('#addRowButton').click(function(e) {
            e.preventDefault();
            that.addRow();
        });

        $('#incomingForm').submit(function() {
            let $url = $('#incomingForm').attr('action');
            let $params = $(this).serialize();

            // some articles must have been added
            if ($('.orderRow').length < 1) {
                $('#feedbackIncomingForm').show();
                $('#feedbackIncomingForm').text('Vous devez enregister au moins un article.');
                return false;
            }       
            
            let regexp = /^[\w-_ ]+$/;
            if (!regexp.test($('#provider').val()) || !regexp.test($('#reference').val())) {
                $('#feedbackIncomingForm').show();
                $('#feedbackIncomingForm').text('Vous devez utiliser des chiffres, des lettres non accentuées ou bien des tirets ou underscores.');
                return false;
            }

            $.post($url, $params, function(data) {
                if (data === 'moveOK' || data == 'tokenError') {
                    $(window).attr('location', '/incoming/index');
                }

                if (data === 'moveNOK') {
                    $('#feedbackIncomingForm').show();
                    $('#feedbackIncomingForm').text('Une erreur est survenue lors de la validation.');
                }
            });
            return false; 
        });
    }

    addDeleteEvent(orderRow)
    {
        orderRow.find('.button').click(function(e) {
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
                            // if only one result exists, we set it directly
                            if (data.length === 1) {
                                $articleField.val(data[0]);
                            } else {
                                for (let i=0; i < data.length; i++) {
                                    $ul.append('<li class="dynamicListItem">' + data[i] + '</li>');
                                    $ul.find('li').last().click(function(e) {
                                        $articleField.val(data[i]);
                                        $ul.html('');
                                    });
                                }
                            }
                        } else {
                            $ul.html('');
                            $ul.append('<li>Pas de résultat</li>');
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        $ul.html('');
                        $ul.append('<li>Une erreur est survenue</li>');
                    },
                    dataType: "json"
                });
            }
        });
    }

    addLocationSuggestion(orderRow)
    {
        orderRow.find('input').last().keyup(function(e) {
            let $ul = $(this).parent().find('.dynamicList').first();
            $ul.html('');

            let $locationField =  $(e.target);

            if ($.trim($locationField.val()) !== '') {
                $.ajax({
                    type: 'POST',
                    url: '/location/suggestions',
                    data: {concatenate:$.trim($locationField.val())},
                    success: function(data) {
                        if (data.length !== 0) {
                            $ul.html('');
                            for (let i=0; i < data.length; i++) {
                                $ul.append('<li class="dynamicListItem">' + data[i] + '</li>');
                                $ul.find('li').last().click(function(e) {
                                    $locationField.val(data[i]);
                                    $ul.html('');
                                });
                            }
                        } else {
                            $ul.html('');
                            $ul.append('<li>Pas de résultat</li>');
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        $ul.html('');
                        $ul.append('<li>Une erreur est survenue</li>');
                    },
                    dataType: "json"
                });
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
        row += `<label for="location${i}">Emplacement</label>`;
        row += `<input type="text" name="location${i}" id="location${i}" autocomplete="off" required>`;
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
    new IncomingForm();
});
