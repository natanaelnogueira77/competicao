<?php 
    $theme->title = sprintf(_('%s - Chaveamentos | %s'), $dbChampionship->name, $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => sprintf(_('%s - Chaveamentos'), $dbChampionship->name),
        'subtitle' => sprintf(_('Segue abaixo o chaveamento do campeonato "%s"'), $dbChampionship->name),
        'icon' => 'pe-7s-network',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<?php 
    $this->start('css');
    $this->insert('user/championships/_styles/single.css');
    $this->end(); 
?>

<div class="card card-body d-flex align-items-center justify-content-around mb-4 br-15 shadow">
    <div id="elimination-brackets"></div>
</div>

<?php 
    $this->start('scripts'); 
    $this->insert('user/championships/_scripts/brackets.js', [
        'dbChampionship' => $dbChampionship,
        'bracketsData' => $bracketsData,
        'roundLabels' => $roundLabels
    ]);
    $this->end();
?>