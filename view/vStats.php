<div class="row centeredContent">
    <div class="col-md-11 col-sm-offset-1">
        <h1>Your Duo-queue</h1><br>
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
    <div class="col-md-11 col-sm-offset-1">
        <p>
            <?php echo $matches; ?>
        </p>
    </div>
</div>