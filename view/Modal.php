<!-- Error's Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $_SESSION['errorContext']; ?></h4>
            </div>
            <div class="modal-body">
                <?php
                echo $_SESSION['errorForm'];
                $_SESSION['errorForm'] = "";
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- --------------- -->

<!-- Lane's Modal -->
<div class="modal fade" id="laneModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $_SESSION['errorContext']; ?></h4>
            </div>
            <div class="modal-body">
                <div class="btn-group col-sm-4" data-toggle="buttons">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- --------------- -->