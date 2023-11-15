<?php 
    $theme->title = sprintf(_('Campeonatos | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Lista de Campeonatos'),
        'subtitle' => _('Segue abaixo a lista de campeonatos cadastrados no sistema'),
        'icon' => 'pe-7s-star',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-star icon-gradient bg-info"> </i>
            <?= _('Campeonatos') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="create-championship" class="btn btn-lg btn-primary" data-method="post" 
                    data-action="<?= $router->route('user.championships.store') ?>">
                    <i class="icofont-plus"></i>
                    <?= _('Cadastrar Campeonato') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="filters">
            <?php $this->insert('_components/data-table-filters', ['formId' => 'filters']); ?>
        </form>
        <div id="championships" data-action="<?= $router->route('user.championships.list') ?>">
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
    $this->insert('user/championships/_scripts/index.js');
    $this->end();
    
    $this->start('modals');
    $this->insert('user/championships/_components/save-modal', [
        'dbGames' => $dbGames
    ]);
    $this->end();
?>