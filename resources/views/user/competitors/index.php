<?php 
    $theme->title = sprintf(_('Competidores - %s | %s'), $dbChampionship->name, $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => sprintf(_('Lista de Competidores - %s'), $dbChampionship->name),
        'subtitle' => sprintf(_('Segue abaixo a lista de competidores do campeonato "%s"'), $dbChampionship->name),
        'icon' => 'pe-7s-users',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-users icon-gradient bg-info"> </i>
            <?= _('Competidores') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <a class="btn btn-lg btn-danger" 
                    href="<?= $router->route('user.championships.index', ['championship_id' => $dbChampionship->id]) ?>">
                    <i class="icofont-arrow-left"></i>
                    <?= _('Voltar') ?>
                </a>

                <button type="button" id="create-competitor" class="btn btn-lg btn-primary" data-method="post" 
                    data-action="<?= $router->route('user.competitors.store', [
                        'championship_id' => $dbChampionship->id
                    ]) ?>">
                    <i class="icofont-plus"></i>
                    <?= _('Cadastrar Competidor') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="filters">
            <?php $this->insert('_components/data-table-filters', ['formId' => 'filters']); ?>
        </form>
        <div id="competitors" data-action="<?= $router->route('user.competitors.list', [
            'championship_id' => $dbChampionship->id
            ]) ?>">
            <div class="d-flex justify-content-around p-5">
                <div class="spinner-grow text-secondary" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->start('scripts'); 
    $this->insert('user/competitors/_scripts/index.js');
    $this->end();
    
    $this->start('modals');
    $this->insert('_components/media-library');
    $this->insert('user/competitors/_components/save-modal');
    $this->end();
?>