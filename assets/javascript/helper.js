/* 
 ###############################################################################
 vars
 ###############################################################################
 */
var resultId = 0;
var buttonToChange;
var nbClickRemoveGameAccount = 0;
var nbClickRemoveDuo = 0;


$(document).ready(function() {
    $('select').selectpicker();
    if ($('#infoModal').length !== 0) {
        $('#infoModal').modal({
            keyboard: true
        });
    }

});

function setLane(userName, matchResultId, button) {
    $('#laneModal').modal({
        keyboard: true,
        backdrop: false
    });
    document.getElementById('myModalLaneLabel').innerHTML = "Set the " + userName + "'s lane";
    resultId = matchResultId;
    buttonToChange = button;
}



/* 
 ###############################################################################
 PHP POST CALL
 ###############################################################################
 */

/*
 * this function send a post to set a player's lane
 */
function requestAjaxSetLane(button) {
    var laneName = 0;
    if (document.getElementById('option1').checked) {
        laneName = document.getElementById('option1').value;
    } else if (document.getElementById('option2').checked) {
        laneName = document.getElementById('option2').value;
    } else if (document.getElementById('option3').checked) {
        laneName = document.getElementById('option3').value;
    } else if (document.getElementById('option4').checked) {
        laneName = document.getElementById('option4').value;
    } else if (document.getElementById('option5').checked) {
        laneName = document.getElementById('option5').value;
    }
    $.post('/DuoQ/controller/Ajax.php', {function: "setSumLane", resultId: resultId, laneName: laneName}, function(e) {
        buttonToChange.innerHTML = e;
        buttonToChange.className = "btn btn-default btn-xs";
    });
}
/*
 * This function send a post to remove a user's game account
 */
function requestAjaxUnLinkSum(sumId, button) {
    if (nbClickRemoveGameAccount === 1) {
        $.post('/DuoQ/controller/Ajax.php', {function: "unlinkSum", sumId: sumId}, function(e) {
            if (e == "ok") {
                $('#' + sumId).fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });
        nbClickRemoveGameAccount = 0;
    } else {
        button.innerHTML = "Sure ??";
        nbClickRemoveGameAccount++;
    }
}

function requestAjaxRemoveDuo(duoId, button) {
    if (nbClickRemoveDuo === 1) {
        $.post('/DuoQ/controller/Ajax.php', {function: "removeDuo", duoId: duoId}, function(e) {
            if (e === "1") {
                $('#' + duoId).fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });
        nbClickRemoveDuo = 0;
    } else {
        button.innerHTML = "Sure ??";
        nbClickRemoveDuo++;
    }
}


function requestAjaxUpdateDuo(sumName1, sumName2, button) {
    var buttonHtml = button.innerHTML;
    var buttonClass = button.className;
    button.disabled = true;
    $.post('/DuoQ/controller/Ajax.php', {function: "updateDuo", sumName1: sumName1, sumName2: sumName2}, function(e) {
        console.log(e);
        if (e === "refresh Ranked OK") {
            button.innerHTML = '<span class="glyphicon glyphicon-ok"></span> Refreshed';
            button.className = "btn btn-success";
            requestAjaxDisplayUpdatedDuo(sumName1, sumName2);
            setTimeout(function() {
                button.innerHTML = buttonHtml;
                button.className = buttonClass;
                button.disabled = false;
            }, 3000);
        }
    });
}

function requestAjaxDisplayUpdatedDuo(sumName1, sumName2) {
    var matchArea = document.getElementById('GameArea');
    $.post('/DuoQ/controller/Ajax.php', {function: "displayAllRefreshedDuo", sumName1: sumName1, sumName2: sumName2}, function(e) {
        if (e !== "") {
            matchArea.innerHTML = e;
        }
    });
}