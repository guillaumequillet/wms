//https://www.developpez.net/forums/d1127096/javascript/bibliotheques-frameworks/jquery/empecher-non-soumission-d-formulaire-jquery/

class IncomingForm
{
    constructor() 
    {
        let that = this; // fix around Jquery "this" handling
        this.lineNumber = 0;

        $('#feedbackIncomingForm').hide();

        $('#addRowButton').click(function(e) {
            e.preventDefault();
            that.addRow();
        });

        $('#incomingForm').submit(function(e) {
            $('#feedbackIncomingForm').hide();

            // some articles must have been added
            if ($('.orderRow').length < 1) {
                $('#feedbackIncomingForm').show();
                $('#feedbackIncomingForm').text('Vous devez enregister au moins un article.');
            }            

            // check of provider and reference
            let regexp = /^[\w-_ ]+$/;
            if (!regexp.test($('#provider').val()) || !regexp.test($('#reference').val())) {
                $('#feedbackIncomingForm').show();
                $('#feedbackIncomingForm').text('Vous devez utiliser des chiffres, des lettres non accentuées ou bien des tirets ou underscores.');
            }

            // final check : all rows do have some valid article / qty / location
            $(".orderRow").each(function() {
                let article = $(this).find('.orderField input')[0].value;
                $.post('/article/exists', {code:article}, function(data) {
                    if (data !== true) {
                        $('#feedbackIncomingForm').show();
                        $('#feedbackIncomingForm').text("L'article " + article + " n'existe pas.");
                    } 
                });

                let inputQty = $(this).find('.orderField input')[1].value;
                if (inputQty < 0 || isNaN(inputQty)) {
                    $('#feedbackIncomingForm').show();
                    $('#feedbackIncomingForm').text("La quantité indiquée est incorecte.");
                }

                let location = $(this).find('.orderField input')[2].value;
                $.post('/location/exists', {concatenate:location}, function(data) {
                    if (data !== true) {
                        $('#feedbackIncomingForm').show();
                        $('#feedbackIncomingForm').text("L'emplacement " + location + " n'existe pas.");
                    } 
                });
            });

            if ($('#feedbackIncomingForm').text().length !== 0) {
                e.preventDefault();
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
        row += `<input type="number" name="quantity${i}" id="quantity${i}" required>`;
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
        $('.orderRow').last().find('.button').click(function(e) {
            e.preventDefault();
            $('.orderRow').last().remove();
        });

        // handling article input
        $('.orderRow').last().find('input').first().keyup(function(e) {
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

        // handling location input
        $('.orderRow').last().find('input').last().keyup(function(e) {
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
                    dataType: "json"
                });
            }
        });
    }
}

$(document).ready(function() {
    new IncomingForm();
});
