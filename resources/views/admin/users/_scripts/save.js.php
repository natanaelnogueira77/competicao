<script>
    $(function () {
        const form = $("#save-user");
        const update_password = $("input[name$='update_password']");
        const password_area = $("#password");

        update_password.change(function () {
            if($('#update_password1').is(':checked')) {
                password_area.show('fast');
            }

            if($('#update_password2').is(':checked')) {
                password_area.hide('fast');
            }
        });

        App.form(form, function (response) {
            if(response.link) window.location.href = response.link;
        }).apply();
    });
</script>