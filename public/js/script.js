$(document).ready(function() {
    // Article FORM 
    let codeRegExp = regex = /^[\w_]+$/;
    $('#articleFeedback').hide();

	$('#articleForm #code').change(function(){
		let code=$('#articleForm #code').val();
 
		if(code != "")
		{
            $.post('/article/exists', {code:code}, function(data){
                console.log(data);
                if (data === "true") {
                    $('#articleFeedback').show();
                    $('#articleFeedback').text("L'article existe déjà.");
                    $('#articleForm #articleSubmit').prop("disabled", true);
                } 
                else if (!codeRegExp.test(code)) {
                    $('#articleFeedback').show();
                    $('#articleFeedback').text("Le format saisi est incorrect");
                    $('#articleForm #articleSubmit').prop("disabled", true);
                } else {
                    $('#articleFeedback').text("");
                    $('#articleFeedback').hide();
                    $('#articleForm #articleSubmit').prop("disabled", false);
                }
			});
		}
    });

    // Location Single FORM
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
});