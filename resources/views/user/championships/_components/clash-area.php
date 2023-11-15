<div class="row mb-4">
    <div class="col-4">
        <div id="competitor-1" class="card border border-primary shadow" style="width: 100%; left: -50vw;">
            <button type="button" class="btn btn-lg btn-outline-success btn-block" style="height: 80px; font-size: 1.3rem;" 
                data-act="winner-button" data-clash-id="<?= $dbClash->com1_id ?>">
                <?= _('Vencedor') ?>
            </button>
            <img src="<?= $dbClash->competitor1->getImageURL() ?>" class="card-img-top" alt="">
            <div class="card-body">
                <h5 class="text-center"><strong><?= $dbClash->competitor1->name ?></strong></h5>
            </div>
        </div>
    </div>

    <div id="versus-img" class="col-4" style="bottom: -50vh;">
        <input type="submit" class="btn btn-lg btn-primary btn-block" 
            style="height: 80px; font-size: 1.3rem;" value="<?= _('Encerrar') ?>">
        <div class="d-flex align-items-center">
            <img src="<?= url('public/imgs/championship/versus.png') ?>" 
                class="card-img-top"  alt="">
        </div>
    </div>

    <div class="col-4">
        <div id="competitor-2" class="card border border-primary shadow" style="width: 100%; right: -50vw;">
            <button type="button" class="btn btn-lg btn-outline-success btn-block" style="height: 80px; font-size: 1.3rem;"
                data-act="winner-button" data-clash-id="<?= $dbClash->com2_id ?>">
                <?= _('Vencedor') ?>
            </button>
            <img src="<?= $dbClash->competitor2->getImageURL() ?>" class="card-img-top" alt="">
            <div class="card-body">
                <h5 class="text-center"><strong><?= $dbClash->competitor2->name ?></strong></h5>
            </div>
        </div>
    </div>
</div>