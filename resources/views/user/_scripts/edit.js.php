<script>
    $(function () {
        App.form($("#save-user"), function (response) {
            if(response.link) window.location.href = response.link;
        }).apply();

        $("input[name$='update_password']").change(function(){
            if($('#update_password1').is(':checked')) {
                $("#password").show('fast');
            }

            if($('#update_password2').is(':checked')) {
                $("#password").hide('fast');
            }
        });
    });
</script>