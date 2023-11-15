<?php $this->insert('user/games/_scripts/save.js') ?>
<script>
    $(function () {
        const saveGame = new SaveGame($("#save-game"));
        const table = $("#games");
        const filters_form = $("#filters");

        const save_game_modal = $("#save-game-modal");
        const create_game_btn = $("#create-game");

        const dataTable = App.table(table, table.data('action'));
        dataTable.defaultParams(App.form(filters_form).objectify()).filtersForm(filters_form)
        .setMsgFunc((msg) => App.showMessage(msg.message, msg.type)).loadOnChange().addAction((table) => {
            table.find("[data-act=delete]").click(function () {
                var data = $(this).data();

                if(confirm(<?php echo json_encode(_('Deseja realmente excluir este jogo?')) ?>)) {
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
                        saveGame.dynamicForm.form.attr('action', response.save.action);
                        saveGame.dynamicForm.form.attr('method', response.save.method);
                        saveGame.clean();

                        if(response.content) {
                            saveGame.loadData(response.content);
                        }

                        save_game_modal.find("[modal-info=title]").text(
                            <?php echo json_encode(sprintf(_('Editar Jogo - %s'), '{game_name}')) ?>
                            .replace('{game_name}', data.gameName)
                        );
                        save_game_modal.modal('show');
                    }
                });
            });
        }).load();

        create_game_btn.click(function () {
            var data = $(this).data();

            saveGame.dynamicForm.form.attr('action', data.action);
            saveGame.dynamicForm.form.attr('method', data.method);
            saveGame.clean();

            save_game_modal.find("[modal-info=title]").text(
                <?php echo json_encode(_('Cadastrar Jogo')) ?>
            );
            save_game_modal.modal('show');
        });

        saveGame.setSuccess(function (instance, response) {
            dataTable.load();
            save_game_modal.modal('toggle');
        }).load();
    });
</script>