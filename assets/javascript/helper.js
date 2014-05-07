/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    $('select').selectpicker();
    if ($('#infoModal').length !== 0) {
        $('#infoModal').modal({
            keyboard: true
        });
    }

});