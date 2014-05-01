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
    <div class="col-md-12">
        <div class="jumbotron">
            <h1>Global stats</h1>
            <div class="row">
                <div class="col-md-4">
                    <h4>Total gaming time with this duo:</h4> 
                    <?php echo $totalGameTime; ?>
                </div>
                <div class="col-md-4">
                    <div class="col-md-6">
                        <h4>Total wins: </h4>
                        <?php echo $totalWins; ?> 
                    </div>
                    <div class="col-md-6">
                        <h4>Total Defeat: </h4>
                        <?php echo $totalDefeat; ?> 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-md-6">
                        <h4>Total Damage dealt: </h4>
                        <?php // echo $totalDefeat; ?> 
                    </div>
                    <div class="col-md-6">
                        <h4>Total Defeat: </h4>
                        <?php echo $totalDefeat; ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <p>
            <?php echo $matches; ?>
        </p>
    </div>
</div>