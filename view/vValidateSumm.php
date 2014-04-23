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
            <label for="sumName" class="col-sm-4 control-label">Your chat's secret</label>
            <div class="col-sm-4">
                <input type="text" name="sumName" value="<?php echo $userName ?>" class="form-control" id="userName" placeholder="chat's secret">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-6">
                <button type="submit" name="submitDuo" id="submitDuo" class="btn btn-default">Finish</button>
            </div>
        </div>
    </form>  
</div>