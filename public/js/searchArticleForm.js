$(document).ready(function() {
    // will force anchor #list is some page is specified
    let page = window.location.href.split('/').pop();
    if (!isNaN(page)) {
        window.location.href = '#list'; 
    }

    let codeRegExp = /^[\w_%]+$/;

    $('#articleSearchFeedback').hide();

    $('#searchArticleForm').submit(function(event)  {
        let queryString = $.trim($('#searchArticleForm #queryString').val());
        if (queryString !== '' && !codeRegExp.test(queryString)) {
            event.preventDefault();
            $('#articleSearchFeedback').show();
            $('#articleSearchFeedback').text("Les champs n'acceptent qu'une lettre non accentuée ou des nombres de 0 à 999."); 
        } else {
            $('#articleSearchFeedback').hide();
        }
    });
});
