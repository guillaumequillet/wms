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

    // Location FORM
    $('#locationSingleFeedback').hide();
    let globalRegex = /(^[a-zA-Z]$)|(^[0-9]{1,3}$)/;

    $('#locationSingleForm').submit(function(event) {
        let error = false;
        $('#locationSingleForm input[type="text"]').each(function() {
            if (!globalRegex.test($(this).val())) {
                error = true;
            }
        });
        if (error) {
            event.preventDefault();           
            $('#locationSingleFeedback').show();
            $('#locationSingleFeedback').text("Les champs n'acceptent qu'une lettre non accentuée et des nombres de 0 à 999."); 
            $('#loginForm #locationSingleSubmit').prop("disabled", true);
        } else {
            $('#locationSingleFeedback').hide();
            $('#loginForm #locationSingleSubmit').prop("disabled", false);            
        }
    });
});