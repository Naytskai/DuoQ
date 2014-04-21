<div class="row centeredContent">
    <form class="form-horizontal" method="Post" role="form">
        <div class="form-group">
            <div class="col-sm-offset-1">
                <h2>Create a new Duo Queue</h2>
            </div>
        </div>
        <div class="form-group">
            <label for="userName" class="col-sm-2 control-label">You</label>
            <div class="col-sm-4">
                <input type="text" name="userName" value="<?php echo $userName ?>" class="form-control" id="userName" placeholder="Summoner's name">
            </div>
            <div class="col-sm-4">
                <div class="btn-group btn-group-sm">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Top</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Mid</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Jungle</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Bot</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="mateUserName" class="col-sm-2 control-label">Your mate</label>
            <div class="col-sm-4">
                <input type="text" name="mateUserName" class="form-control" id="mateUserName" placeholder="Summoner's name">
            </div>
            <div class="col-sm-4">
                <div class="btn-group btn-group-sm">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Top</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Mid</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Jungle</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Bot</button>
                    </div>
                </div>
            </div>
        </div>
    </form>  
</div>