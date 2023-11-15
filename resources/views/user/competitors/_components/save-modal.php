<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-competitor-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" modal-info="title"><?= _('Cadastrar Competidor') ?></h5>
            </div>
            
            <div class="modal-body">
                <?php $this->insert('user/competitors/_components/save-form') ?>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-competitor" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <?= _('Voltar') ?>
                </button>
            </div>
        </div>
    </div>
</div>