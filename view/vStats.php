<div class="row centeredContent">
    <div class="col-md-12">
        <div class="page-header">
            <h1>Your Duo-queue</h1>
            <div class="col-sm-offset-1">
                <form class="form-inline" role="form" method="POST">
                    <div class="form-group">
                        <label for="duoLane">Select a duo queue</label>
                        <?php echo $duoSelect ?> 
                    </div>
                    <button type="submit" name="submitDuo" class="btn btn-default">Display</button>
                </form>
            </div>
        </div>
    </div>
    <?php if (isset($_POST['submitDuo']) || isset($_GET['duoId']) || isset($_GET['gameId'])) { ?>
        <div class="col-md-12">
            <div class="jumbotron">
                <h2><?php echo $headerTitle; ?>'s stats</h2>
                <button id="share-button" data-clipboard-text="<?php echo $shareURL; ?>" class="btn btn-default">Clipboard it !</button>
                <div class="row">
                    <div class="col-md-4 centeredText">
                        <h4>Total gaming time</h4>
                        <span class="label label-primary"><?php echo round($totalGameTime) . " mins"; ?></span>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-6 centeredText">
                            <h4>Wins</h4>
                            <span class="label label-success"><?php echo $totalWins; ?></span>
                        </div>
                        <div class="col-md-6 centeredText">
                            <h4>Defeats</h4>
                            <span class="label label-danger"><?php echo $totalDefeat; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-6 centeredText">
                            <h4>Damage dealt</h4>
                            <span id="damageLabel" class="label label-info" onmouseover="$('#damageLabel').tooltip('show');" data-toggle="tooltip" title="Average Duo's member damages dealt by game"><?php echo $totalDomDealt; ?></span>
                        </div>
                        <div class="col-md-6 centeredText">
                            <h4>Gold</h4>
                            <span id="goldLabel" class="label label-warning" onmouseover="$('#goldLabel').tooltip('show');" data-toggle="tooltip" title="Average Duo's member gold gain by game"><?php echo $totalGold; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col-md-12">
        <p>
            <?php echo $matches; ?>
        </p>
    </div>
</div>