<div class="row centeredContent">
    <form class="form-horizontal" method="Post" role="form">
        <div class="form-group">
            <div class="col-sm-offset-1">
                <h2>Link a League of Legends account</h2>
            </div>
        </div>
        <div class="form-group">
            <label for="sumName" class="col-sm-4 control-label">Your summoner's name</label>
            <div class="col-sm-4">
                <input type="text" name="sumName" value="<?php echo $userName ?>" class="form-control" id="userName" placeholder="Summoner's name">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-6">
                <button type="submit" name="submitSumm" id="submitDuo" class="btn btn-default">Link</button>
            </div>
        </div>
    </form>  
</div>