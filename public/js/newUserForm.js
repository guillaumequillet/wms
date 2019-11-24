$(document).ready(function() {
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
