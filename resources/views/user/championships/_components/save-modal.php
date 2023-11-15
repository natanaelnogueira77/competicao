<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-championship-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" modal-info="title"><?= _('Cadastrar Campeonato') ?></h5>
            </div>
            
            <div class="modal-body">
                <?php $this->insert('user/championships/_components/save-form', ['dbGames' => $dbGames]) ?>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-championship" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <?= _('Voltar') ?>
                </button>
            </div>
        </div>
    </div>
</div>