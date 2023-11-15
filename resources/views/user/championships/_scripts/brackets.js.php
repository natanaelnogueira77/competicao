<script src="<?= url('public/assets/js/jquery.gracket.js') ?>"></script>
<script>
    $(function () {
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