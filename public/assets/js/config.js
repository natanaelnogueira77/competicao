DynamicForm.setDefaultAjaxParams({
    beforeSend: function () {
        App.load("open");
    },
    complete: function () {
        App.load("close");
    }
});

DynamicForm.setDefaultSuccessCallback(function (instance, response) {
    if(response.message) {
        App.showMessage(response.message[1], response.message[0]);
    }

    instance.form.find(".is-invalid").toggleClass("is-invalid");
    instance.form.find("[data-error]").html(``);
    instance.form.find(".invalid-feedback").html(``);
});

DynamicForm.setDefaultErrorCallback(function (instance, response) {
    if(response.message) {
        App.showMessage(response.message[1], response.message[0]);
    }
    
    instance.form.find(".is-invalid").toggleClass("is-invalid");
    instance.form.find("[data-error]").html(``);
    instance.form.find(".invalid-feedback").html(``);
    
    if(response.errors) {
        var attr = instance.form.data('errors') ? instance.form.data('errors') : 'name';
        for(const [key, value] of Object.entries(response.errors)) {
            var input = instance.form.find(`[${attr}="${key}"]`);
            input.toggleClass('is-invalid');
            input.parent().children('.invalid-feedback').html(value);
            instance.form.find(`[data-error="${key}"]`).html(value);
        }
    }
});

DynamicForm.setDefaultActions(function (instance) {
    instance.form.find("input, textarea, select").change(function () {
        var id = $(this).attr('id');
        $(this).removeClass('is-invalid');
        $(this).parent().children('.invalid-feedback').html(``);
        instance.form.find(`[data-error="${id}"]`).html(``);
    });
});