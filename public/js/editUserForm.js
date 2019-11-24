$(document).ready(function() {
    $('#editUserFeedback').hide();

    $('#editUserForm').submit(function(event) {
        let error = false;
        $('#editUserForm input[type="text"]').each(function() {
            $(this).val($.trim($(this).val()));
        });
        $('#editUserForm input[type="password"]').each(function() {
            $(this).val($.trim($(this).val()));
        });

        if (!/^[\w.]+$/.test($('#editUserForm #username').val())) {
            error = true;
            $('#editUserFeedback').text("Le nom d'utilisateur n'accepte que les lettres non accentuées, les chiffres et le point (.).");             
        }

        if (!/^[\w\-\+]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)*\.[\w\-]{2,4}$/.test($('#editUserForm #email').val())) {
            error = true;
            $('#editUserFeedback').text("L'adresse email doit être de la forme identifiant@host.domaine, et seuls les caractères non accentués, le point (.) et les chiffres sont autorisés.");             
        }

        if ($('#editUserForm #newPassword').val() !== $('#editUserForm #newPasswordConfirm').val()) {
            error = true;
            $('#editUserFeedback').text('Les mots de passe saisis sont différents.');
        }

        if (error) {
            event.preventDefault();
            $('#editUserFeedback').show();
        } else {
            $('#editUserFeedback').hide();
        }
    });
});
