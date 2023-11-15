<script>
    $(function () {
        const table = $("#users");
        const filters_form = $("#filters");

        const dataTable = App.table(table, table.data('action'));
        dataTable.defaultParams(App.form(filters_form).objectify()).filtersForm(filters_form)
        .setMsgFunc((msg) => App.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir este usuário?')) ?>)) {
                    App.callAjax({
                        url: data.action,
                        type: data.method,
                        success: function (response) {
                            dataTable.load();
                        }
                    });
                }
            });
        }).load();
    });
</script>