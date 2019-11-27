$(document).ready(function() {
    // will force anchor #list is some page is specified
    let page = window.location.href.split('/').pop();
    if (!isNaN(page)) {
        window.location.href = '#list'; 
    }

    $('#locationSingleFeedback').hide();
    $('#locationSingleForm').submit(function(event) {
        let error = false;
        $('#locationSingleForm input[type="text"]').each(function() {
            $(this).val($.trim($(this).val()));
            if ($(this).attr("name") === "area" && !areaRegex.test($(this).val())) {
                error = true;
            }
            if ($(this).attr("name") !== "area" && !globalRegex.test($(this).val())) {
                error = true;
            }
        });
        if (error) {
            event.preventDefault();           
            $('#locationSingleFeedback').show();
            $('#locationSingleFeedback').text("Les champs n'acceptent qu'une lettre non accentuée ou des nombres de 0 à 999."); 
        } else {
            $('#locationSingleFeedback').hide();
        }
    });

    // Location Interval FORM
    $('#locationIntervalFeedback').hide();
    let areaRegex = /^[\w]+$/;
    let globalRegex = /(^[a-zA-Z]$)|(^[0-9]{1,3}$)/;

    $('#locationIntervalForm').submit(function(event) {
        let error = false;
        $('#locationIntervalForm input[type="text"]').each(function() {
            $(this).val($.trim($(this).val()));

            if ($(this).attr("name") === "area" && !areaRegex.test($(this).val())) {
                error = true;
            }
            if ($(this).attr("name") !== "area" && !globalRegex.test($(this).val())) {
                error = true;
            }
        });
        if (error) {
            event.preventDefault();           
            $('#locationIntervalFeedback').show();
            $('#locationIntervalFeedback').text("Les champs n'acceptent qu'une lettre non accentuée ou des nombres de 0 à 999"); 
        } else {
            $('#locationIntervalFeedback').hide();
        }
    });

    // Location Search FORM
    let codeRegExp = /^[\w_-]+$/;

    $('#locationSearchFeedback').hide();

    $('#searchArticleForm').submit(function(event)  {
        let queryString = $.trim($('#searchLocationForm #queryString').val());
        if (queryString !== '' && !codeRegExp.test(queryString)) {
            event.preventDefault();
            $('#locationSearchFeedback').show();
            $('#locationSearchFeedback').text("Les champs n'acceptent qu'une lettre non accentuée ou des nombres de 0 à 999."); 
        } else {
            $('#locationSearchFeedback').hide();
        }
    });
});
