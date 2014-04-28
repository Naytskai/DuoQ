<div class="row centeredContent">
    <form class="form-horizontal" method="Post" role="form">
        <div class="form-group">
            <div class="col-sm-offset-1">
                <h2>Create a new Duo Queue</h2>
            </div>
        </div>
        <div class="form-group">
            <label for="sumName" class="col-sm-2 control-label">You</label>
            <div class="col-sm-4">
                <?php echo $sumSelect; ?>
            </div>
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-primary">
                    <input type="radio" name="lane" id="option1" value="Top">Top
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="lane" id="option2" value="Mid">Mid
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="lane" id="option3" value="Jungle">Jungle
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="lane" id="option4" value="ADC">ADC
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="lane" id="option5" value="Support">Support
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="matesSumName" class="col-sm-2 control-label">Your mate</label>
            <div class="col-sm-4">
                <input type="text" name="matesSumName" class="form-control" id="mateUserName" placeholder="Summoner's name">
            </div>
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-primary">
                    <input type="radio" name="mateslane" id="option1" value="Top">Top
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="mateslane" id="option2" value="Mid">Mid
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="mateslane" id="option3" value="Jungle">Jungle
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="mateslane" id="option4" value="ADC">ADC
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="mateslane" id="option5" value="Support">Support
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
                <button type="submit" name="submitDuo" id="submitDuo" class="btn btn-default">Create</button>
            </div>
        </div>
    </form>  
</div>