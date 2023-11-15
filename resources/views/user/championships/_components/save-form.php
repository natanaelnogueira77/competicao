<form id="save-championship">
    <div class="form-group">
        <label><?= _('Nome') ?></label>
        <input type="text" name="name" class="form-control" 
            placeholder="<?= _('Digite o nome do campeonato...') ?>" maxlength="100">
        <div class="invalid-feedback"></div>
    </div>

    <div class="form-group">
        <label><?= _('Jogo') ?></label>
        <select name="jog_id" class="form-control">
            <option value=""><?= _('Selecionar...') ?></option>
            <?php 
                if($dbGames) {
                    foreach($dbGames as $dbGame) {
                        echo "<option value=\"{$dbGame->id}\">{$dbGame->name}</option>";
                    }
                }
            ?>
        </select>
        <div class="invalid-feedback"></div>
    </div>
</form>