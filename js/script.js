
function doModal(id, heading, formContent, strSubmitFunc, btnText)
{
    var html =  '<div id="'+myModal+'" class="modal">';
    html += '<div class="modal-header">';
    html += '<span class="close">&times;</span>';
    html += '<h4>'+heading+'</h4>'
    html += '</div>';
    html += '<div class="modal-body">';
    html += '<p>';
    html += formContent;
    html += '</div>';
    html += '<div class="modal-footer">';
    if (btnText!='') {
        html += '<span class="btn btn-success"';
        html += ' onClick="'+strSubmitFunc+'">'+btnText;
        html += '</span>';
    }
    html += '<span class="btn" data-dismiss="modal">';
    html += 'Close';
    html += '</span>'; // close button
    html += '</div>';  // footer
    html += '</div>';  // modalWindow
    // $("#"+placementId).html(html);
    // $("#modalWindow").modal();
    let body = document.querySelector("body");
    body.appendChild(html);
}

$("#addMarque").on('click', function(e){

    $marqueSelect = $("#mark");

    let marque = prompt('Nom de la marque à ajouter');

    if (marque == null || marque == ''){
        console.log('User cancel bran adding');
    }else{
        $.getJSON("<?php echo $path . 'addmarque.php' ?>", {marqueName: marque})
            .done(function(data){
                console.log(data);
                if (data){
                    $marqueSelect.append(new Option(data.name, data.value, true, true));
                }

            })
    }
    console.log(marque);
});
