<form id="save-competitor">
    <div class="form-group">
        <label><?= _('Nome') ?></label>
        <input type="text" name="name" class="form-control" 
            placeholder="<?= _('Digite o nome do competidor...') ?>" maxlength="100">
        <div class="invalid-feedback"></div>
    </div>

    <div class="form-group">
        <label><?= _('Jogo') ?></label>
        <div id="img-area"></div>
        <small class="text-danger" data-error="img"></small>
    </div>
</form>