/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var resultId = 0;
var buttonToChange;
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
    $.post('/DuoQ/controller/Ajax.php', {methode: "setSumLane", resultId: resultId, laneName: laneName}, function(e) {
        buttonToChange.innerHTML = e;
        buttonToChange.className = "btn btn-default btn-xs";
    });
}