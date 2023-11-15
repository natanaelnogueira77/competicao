<?php 
    $theme->title = sprintf(_('%s | %s'), $dbChampionship->name, $appData['app_name']);
    $theme->has_left = false;
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);
?>

<?php 
    $this->start('css');
    $this->insert('user/championships/_styles/single.css');
    $this->end(); 
?>

<div class="card shadow mb-4 br-15 border border-primary">
    <div class="card-body">
        <p class="card-text text-center">
            <strong>
                <?php 
                    if(!$dbChampionship->isFinished()) {
                        echo sprintf(
                            _('%s - Confronto Atual: %s'), 
                            $dbChampionship->name, 
                            $dbClash ? $dbClash->getClashName() : _('Aguardando')
                        );
                    } else {
                        echo sprintf(
                            _('%s - Grande Vencedor'),
                            $dbChampionship->name
                        );
                    }
                ?>
            </strong>
        </p>
    </div>

    <?php if($dbChampionship->isAwaiting()): ?>
    <hr class="my-0">
    <div class="card-body">
        <form id="set-as-in-progress" method="patch"  
            action="<?= $router->route('user.championships.setAsInProgress', ['championship_id' => $dbChampionship->id]) ?>">
            <input type="submit" class="btn btn-lg btn-block btn-outline-success" style="height: 400px; font-size: 5rem;" value="<?= _('Iniciar') ?>">
        </form>
    </div>
    <?php endif; ?>
</div>

<?php if($dbChampionship->isInProgress()): ?>
<form id="set-clash-winner" action="<?= $router->route('user.championships.setClashWinner', [
    'championship_id' => $dbChampionship->id,
    'clash_id' => $dbClash->id
    ]) ?>" method="patch">
    <?php $this->insert('user/championships/_components/clash-area', ['dbClash' => $dbClash]); ?>
</form>
<?php elseif($dbChampionship->isFinished()): ?>
<audio id="winner-song" autoplay loop>
    <source src="<?= url('public/audio/winner-song.mp3') ?>" type="audio/mpeg">
</audio>
<?php $this->insert('user/championships/_components/winner-area', ['dbCompetitor' => $dbCompetitor]); ?>
<?php endif; ?>

<div class="card card-body d-flex align-items-center justify-content-around mb-4 br-15 shadow">
    <div id="elimination-brackets"></div>
</div>

<?php 
    $this->start('scripts'); 
    $this->insert('user/championships/_scripts/single.js', [
        'dbChampionship' => $dbChampionship,
        'bracketsData' => $bracketsData,
        'roundLabels' => $roundLabels
    ]);
    $this->end();
?>