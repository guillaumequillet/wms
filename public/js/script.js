$(document).ready(function() {
    // Article FORM 
    let codeRegExp = /^[\w_]+$/;
    $('#articleFeedback').hide();

	$('#articleForm #code').change(function(){
		let code = $.trim($('#articleForm #code').val());
 
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

    // Search Article FORM
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

    // new User FORM
    $('#newUserFeedback').hide();

    $('#userForm').submit(function(event) {
        let error = false;
        $('#userForm input[type="text"]').each(function() {
            $(this).val($.trim($(this).val()));
        });
        $('#userForm input[type="password"]').each(function() {
            $(this).val($.trim($(this).val()));
        });

        if (!/^[\w.]+$/.test($('#userForm #username').val())) {
            error = true;
            $('#newUserFeedback').text("Le nom d'utilisateur n'accepte que les lettres non accentuées, les chiffres et le point (.).");             
        }

        if (!/^[\w\-\+]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)*\.[\w\-]{2,4}$/.test($('#userForm #email').val())) {
            error = true;
            $('#newUserFeedback').text("L'adresse email doit être de la forme identifiant@host.domaine, et seuls les caractères non accentués, le point (.) et les chiffres sont autorisés.");             
        }

        if ($('#userForm #password').val() !== $('#userForm #confirmPassword').val()) {
            error = true;
            $('#newUserFeedback').text('Les mots de passe saisis sont différents.');
        }

        if (error) {
            event.preventDefault();
            $('#newUserFeedback').show();
        } else {
            $('#newUserFeedback').hide();
        }
    });
});