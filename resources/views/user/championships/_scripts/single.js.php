<script src="<?= url('public/assets/js/jquery.gracket.js') ?>"></script>
<?php if($dbChampionship->isFinished()): ?>
<script src="<?= url('public/assets/js/confetti.js') ?>"></script>]
<?php endif; ?>
<script>
    $(function () {
        <?php if($dbChampionship->isAwaiting()): ?>
        App.form($("#set-as-in-progress"), function (response) {
            window.location.reload();
        }).apply();
        <?php elseif($dbChampionship->isInProgress()): ?>
        const set_clash_winner = $("#set-clash-winner");
        const winner_buttons = $("[data-act=winner-button]");
        winner_buttons.click(function () {
            winner_buttons.removeClass('active');
            $(this).addClass('active');
        });

        $("#competitor-1").animate({ left: "0" }, 500);
        $("#competitor-2").animate({ right: "0" }, 500);
        $("#versus-img").animate({ bottom: "0" }, 500);

        const DFSetClashWinner = App.form(set_clash_winner).setBeforeAjax(function () {
            console.log(this.form.find("[data-act=winner-button].active").data('clashId'));
            this.formData['winner_id'] = this.form.find("[data-act=winner-button].active").data('clashId');
            return this;
        }).setObjectify(true).setSuccessCallback(function (instance, response) {
            window.location.reload();
        }).apply();
        <?php elseif($dbChampionship->isFinished()): ?>
        $("#competitor-winner").animate({ left: "0" }, 500);
        const trophyAnimation = function (elem) {
            elem.animate({ 
                left: "8vw",
                bottom: "8vh", 
                deg: 20
            }, {
                duration: 800,
                step: function (now) {
                    $(this).css({ transform: 'rotate(' + now + 'deg)' });
                }
            }, 'linear').animate({ 
                bottom: "-8vh",
                deg: 0
            }, {
                duration: 800,
                step: function (now) {
                    $(this).css({ transform: 'rotate(' + now + 'deg)' });
                }
            }, 'linear').animate({ 
                left: "-8vw",
                bottom: "8vh",
                deg: -20
            }, {
                duration: 800,
                step: function (now) {
                    $(this).css({ transform: 'rotate(' + now + 'deg)' });
                }
            }, 'linear').animate({ 
                bottom: "-8vh",
                deg: 0
            }, {
                duration: 800,
                step: function (now) {
                    $(this).css({ transform: 'rotate(' + now + 'deg)' });
                },
                complete: function () {
                    trophyAnimation(elem);
                }
            }, 'linear');
        };
        trophyAnimation($("#trophy-img"));
        startConfetti();

        $("#winner-song-btn").click(function () {
            document.getElementById('winner-song').play();
        });
        <?php endif; ?>

        $('#elimination-brackets').gracket({
            src: <?php echo json_encode($bracketsData) ?>,
            gracketClass: "g_gracket",
            gameClass: "g_game",
            roundClass: "g_round",
            roundLabelClass: "g_round_label",
            teamClass: "g_team",
            winnerClass: "g_winner",
            spacerClass: "g_spacer",
            currentClass: "g_current",
            seedClass: "g_seed",
            cornerRadius: 15,
            canvasId: "g_canvas",
            canvasClass: "g_canvas",
            canvasLineColor: "#eee",
            canvasLineCap: "round",
            canvasLineWidth: 2,
            canvasLineGap: 15,
            roundLabels: <?php echo json_encode($roundLabels) ?>
        });
    });
</script>