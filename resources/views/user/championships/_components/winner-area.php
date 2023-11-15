<div class="row mb-4 d-flex justify-content-around">
    <div class="col-4">
        <div id="competitor-winner" class="card border border-primary shadow" style="width: 100%; left: -50vw;">
            <button type="button" id="winner-song-btn" class="btn btn-lg btn-outline-primary btn-block">
                <?= _('Tocar Música') ?>
            </button>
            <img src="<?= $dbCompetitor->getImageURL() ?>" class="card-img-top" alt="">
            <div class="card-body">
                <h5 class="text-center"><strong><?= $dbCompetitor->name ?></strong></h5>
            </div>
        </div>
    </div>

    <div id="trophy-img" class="col-4" style="right: -50vw;">
        <div class="d-flex align-items-center">
            <img src="<?= url('public/imgs/championship/trophy.png') ?>" 
                class="card-img-top" alt="">
        </div>
    </div>
</div>