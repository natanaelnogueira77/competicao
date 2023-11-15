<?php $this->insert('user/competitors/_scripts/save.js') ?>
<script>
    $(function () {
        const table = $("#competitors");
        const filters_form = $("#filters");

        const save_competitor_modal = $("#save-competitor-modal");
        const create_competitor_btn = $("#create-competitor");

        const saveCompetitor = new SaveCompetitor(
            $("#save-competitor"), 
            new MediaLibrary(),
            function (fileSelector, elem, id) {
                save_competitor_modal.modal('toggle');
                setTimeout(function () {
                    fileSelector.mediaLibrary.setSuccess(function (path) {
                        fileSelector.addToSelector(path);
                        setTimeout(function () {
                            save_competitor_modal.modal('show');
                        }, 500);
                    }).open();
                }, 500);
            },
            function (fileSelector, elem, id) {
                save_competitor_modal.modal('toggle');
                setTimeout(function () {
                    fileSelector.mediaLibrary.setSuccess(function (path) {
                        fileSelector.updateOnSelector(elem, id, path);
                        setTimeout(function () {
                            save_competitor_modal.modal('show');
                        }, 500);
                    }).open();
                }, 500);
            }
        );

        const dataTable = App.table(table, table.data('action'));
        dataTable.defaultParams(App.form(filters_form).objectify()).filtersForm(filters_form)
        .setMsgFunc((msg) => App.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir este competidor?')) ?>)) {
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
                        saveCompetitor.dynamicForm.form.attr('action', response.save.action);
                        saveCompetitor.dynamicForm.form.attr('method', response.save.method);
                        saveCompetitor.clean();

                        if(response.content) {
                            saveCompetitor.loadData(response.content);
                        }

                        save_competitor_modal.find("[modal-info=title]").text(
                            <?php echo json_encode(sprintf(_('Editar Competidor - %s'), '{competitor_name}')) ?>
                            .replace('{competitor_name}', data.competitorName)
                        );
                        save_competitor_modal.modal('show');
                    }
                });
            });
        }).load();

        create_competitor_btn.click(function () {
            var data = $(this).data();

            saveCompetitor.dynamicForm.form.attr('action', data.action);
            saveCompetitor.dynamicForm.form.attr('method', data.method);
            saveCompetitor.clean();

            save_competitor_modal.find("[modal-info=title]").text(
                <?php echo json_encode(_('Cadastrar Competidor')) ?>
            );
            save_competitor_modal.modal('show');
        });

        saveCompetitor.setSuccess(function (instance, response) {
            dataTable.load();
            save_competitor_modal.modal('toggle');
        }).load();
    });
</script>