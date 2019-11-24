$(document).ready(function() {
    let codeRegExp = /^[\w_]+$/;
    $('#articleFeedback').hide();

	$('#articleForm #code').change(function(){
		let code = $.trim($('#articleForm #code').val());
 
		if(code != "")
		{
            $.post('/article/exists', {code:code}, function(data){
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
});
