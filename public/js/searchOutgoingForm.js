$(document).ready(function() {
    let codeRegExp = /^[\w-_ ]+$/;

    $('#outgoingSearchFeedback').hide();

    $('#searchOutgoingForm').submit(function(event)  {
        let queryString = $.trim($('#searchOutgoingForm #queryString').val());
        if (queryString !== '' && !codeRegExp.test(queryString)) {
            event.preventDefault();
            $('#outgoingSearchFeedback').show();
            $('#outgoingSearchFeedback').text('Vous devez utiliser des chiffres, des lettres non accentuées ou bien des tirets ou underscores.'); 
        } else {
            $('#outgoingSearchFeedback').hide();
        }
    });
});
