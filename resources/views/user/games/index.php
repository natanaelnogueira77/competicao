<?php 
    $theme->title = sprintf(_('Jogos | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Lista de Jogos'),
        'subtitle' => _('Segue abaixo a lista de jogos cadastrados no sistema'),
        'icon' => 'pe-7s-play',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-play icon-gradient bg-info"> </i>
            <?= _('Jogos') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <button type="button" id="create-game" class="btn btn-lg btn-primary" data-method="post" 
                    data-action="<?= $router->route('user.games.store') ?>">
                    <i class="icofont-plus"></i>
                    <?= _('Cadastrar Jogo') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="filters">
            <?php $this->insert('_components/data-table-filters', ['formId' => 'filters']); ?>
        </form>
        <div id="games" data-action="<?= $router->route('user.games.list') ?>">
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
    $this->insert('user/games/_scripts/index.js');
    $this->end();
    
    $this->start('modals');
    $this->insert('user/games/_components/save-modal');
    $this->end();
?>