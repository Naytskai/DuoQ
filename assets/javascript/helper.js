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

    var client = new ZeroClipboard($('#share-button'));
    client.on('ready', function(event) {
        // console.log( 'movie is loaded' );
        client.on('copy', function(event) {
            event.clipboardData.setData('text/plain', event.target.innerHTML);
            alert("1");
        });

        client.on('aftercopy', function(event) {
            updateShareButton();
            console.log('Copied text to clipboard: ' + event.data['text/plain']);
            alert("2");
        });
    });

    client.on('error', function(event) {
        // console.log( 'ZeroClipboard error of type "' + event.name + '": ' + event.message );
        alert("3");
        ZeroClipboard.destroy();
    });
});


function updateShareButton() {
    $('#share-button').html('<b>Copied to clipboard</b>');
}