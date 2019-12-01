$(document).ready(function() {
    let codeRegExp = /^[\w-_ ]+$/;

    $('#incomingSearchFeedback').hide();

    $('#searchIncomingForm').submit(function(event)  {
        let queryString = $.trim($('#searchIncomingForm #queryString').val());
        if (queryString !== '' && !codeRegExp.test(queryString)) {
            event.preventDefault();
            $('#incomingSearchFeedback').show();
            $('#incomingSearchFeedback').text('Vous devez utiliser des chiffres, des lettres non accentuées ou bien des tirets ou underscores.'); 
        } else {
            $('#incomingSearchFeedback').hide();
        }
    });
});
