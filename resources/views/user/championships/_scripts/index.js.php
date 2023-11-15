<?php $this->insert('user/championships/_scripts/save.js') ?>
<script>
    $(function () {
        const saveChampionship = new SaveChampionship($("#save-championship"));
        const table = $("#championships");
        const filters_form = $("#filters");

        const save_championship_modal = $("#save-championship-modal");
        const create_championship_btn = $("#create-championship");

        const dataTable = App.table(table, table.data('action'));
        dataTable.defaultParams(App.form(filters_form).objectify()).filtersForm(filters_form)
        .setMsgFunc((msg) => App.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir este campeonato?')) ?>)) {
                    App.callAjax({
                        url: data.action,
                        type: data.method,
                        success: function (response) {
                            dataTable.load();
                        }
                    });
                }
            });
        }).addAction((table) => {
            table.find("[data-act=edit]").click(function () {
                const data = $(this).data();

                App.callAjax({
                    url: data.action,
                    type: data.method,
                    success: function (response) {
                        saveChampionship.dynamicForm.form.attr('action', response.save.action);
                        saveChampionship.dynamicForm.form.attr('method', response.save.method);
                        saveChampionship.clean();

                        if(response.content) {
                            saveChampionship.loadData(response.content);
                        }

                        save_championship_modal.find("[modal-info=title]").text(
                            <?php echo json_encode(sprintf(_('Editar Campeonato - %s'), '{championship_name}')) ?>
                            .replace('{championship_name}', data.championshipName)
                        );
                        save_championship_modal.modal('show');
                    }
                });
            });
        }).addAction((table) => {
            table.find("[data-act=create-clashes]").click(function () {
                const data = $(this).data();

                App.callAjax({
                    url: data.action,
                    type: data.method
                });
            });
        }).load();

        create_championship_btn.click(function () {
            var data = $(this).data();

            saveChampionship.dynamicForm.form.attr('action', data.action);
            saveChampionship.dynamicForm.form.attr('method', data.method);
            saveChampionship.clean();

            save_championship_modal.find("[modal-info=title]").text(
                <?php echo json_encode(_('Cadastrar Campeonato')) ?>
            );
            save_championship_modal.modal('show');
        });

        saveChampionship.setSuccess(function (instance, response) {
            dataTable.load();
            save_championship_modal.modal('toggle');
        }).load();
    });
</script>