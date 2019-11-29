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

            let $articleField = $('.orderRow').last().find('input').first();

            if ($.trim($articleField.val()) !== '') {
                $.ajax({
                    type: 'POST',
                    url: '/article/suggestions',
                    data: {code:$.trim($articleField.val())},
                    success: function(data){
                        if (data.length !== 0) {
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

            let $locationField = $('.orderRow').last().find('input').last();

            if ($.trim($locationField.val()) !== '') {
                $.ajax({
                    type: 'POST',
                    url: '/location/suggestions',
                    data: {concatenate:$.trim($locationField.val())},
                    success: function(data) {
                        if (data.length !== 0) {
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
