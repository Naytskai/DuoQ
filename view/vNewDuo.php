<div class="row centeredContent">
    <div class="page-header">
        <h1>Create a new Duo Queue</h1>
    </div>
    <form class="form-horizontal" method="Post" role="form">
        <div class="form-group">
            <label for="sumName" class="col-sm-2 control-label">You</label>
            <div class="col-sm-6">
                <?php echo $sumSelect; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="matesSumName" class="col-sm-2 control-label">Your mate</label>
            <div class="col-sm-6">
                <input type="text" name="matesSumName" class="form-control" id="mateUserName" placeholder="Summoner's name">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
                <button type="submit" name="submitDuo" id="submitDuo" class="btn btn-default">Create</button>
            </div>
        </div>
    </form>  
</div>