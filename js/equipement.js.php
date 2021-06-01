<?php
/* Copyright (C) 2021 Assia Zaiter <assia.zaiter@exher.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Library javascript to enable Browser notifications
 */

if (!defined('NOREQUIREUSER'))  define('NOREQUIREUSER', '1');
if (!defined('NOREQUIREDB'))    define('NOREQUIREDB', '1');
if (!defined('NOREQUIRESOC'))   define('NOREQUIRESOC', '1');
if (!defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN', '1');
if (!defined('NOCSRFCHECK'))    define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1);
if (!defined('NOLOGIN'))        define('NOLOGIN', 1);
if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', 1);
if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', 1);
if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');


/**
 * \file    equipement/js/equipement.js.php
 * \ingroup equipement
 * \brief   JavaScript file for module Equipement.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/../main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/../main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

// Define js type
header('Content-Type: application/javascript');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');
$path = dol_buildpath('/custom/equipement/ajax/', 1);
?>

/* Javascript library of module Equipement */

function addMarque() {
	ele= "mark";
	action = "marque";
    doModal(action, 'Nom de la marque à ajouter');
    var modal = document.getElementById(action);
    modal.style.display = "block";
    do_action(ele, action);
}

function addModel() {
	ele= "model";
	action = "modele";
    doModal(action, 'Nom du modèle à ajouter');
    var modal = document.getElementById(action);
    modal.style.display = "block";
    do_action(ele, action);
}

function addOs() {
	ele= "os_type";
	action = "os";
    doModal(action, 'Nom de l\'Os à ajouter');
    var modal = document.getElementById(action);
    modal.style.display = "block";
    do_action(ele, action);
}



function doModal(id, heading)
{
	var input = document.createElement("input");
    input.type = "text";
    input.name = "data-" + id;
    input.id = "data-" + id;
    input.className = "modal-input";
    var html =  '<div id="'+id+'" class="modal">';
    html += '<div class="modal-content">';
    html += '<div class="modal-header">';
    html += '<span class="close">&times;</span>';
    html += '<h2>'+heading+'</h2>'
    html += '</div>';
    html += '<div class="modal-body" id="modal-body">';
    html += '</div>';
    html += '<div class="modal-footer">';
    html += '<button id="submitBut" class="butAction" >';
    html += 'Ajouter';
    html += '</button>'; // close button
    html += '<button id="undoneBut" class="butActionDelete">';
    html += 'Anuller';
    html += '</button>'; 
    html += '</div>';  
	html += '</div>';  
    html += '</div>'; 
    let body = document.querySelector("body");
    $("body").append(html);
    var container = document.getElementById("modal-body");
    container.appendChild(input);
}

function do_action(ele,action) {
	$eleSelect = $("#"+ele);
	var modal = document.getElementById(action);
	// Get the button that save
    var submit = document.getElementById("submitBut");    

    // Get the button that undone
    var undone = document.getElementById("undoneBut");    

    // Get the element that closes the modal
    var span = document.getElementsByClassName("close")[0];    

    // Get the data
    var data = document.getElementById("data-"+action);    

    // When the user clicks on (x), close the modal
    span.onclick = function() {
      modal.style.display = "none";
      $(".modal").remove();
    }    

    // When the user clicks undone button, close the modal
    undone.onclick = function() {
      modal.style.display = "none";
      $(".modal").remove();
	}
	// When the user clicks submit button, save data
   	submit.onclick = function() {
   		value = data.value;
      	if (value == null || value == ''){
        	console.log('User cancel bran adding');
    	}else{
            $.getJSON("<?php echo $path ?>" +"add"+action+".php", {eleName: value})
                .done(function(data){
                    console.log(data);
                    if (data){
                        $eleSelect.append(new Option(data.name, data.value, true, true));
                    }    

                })
    	}

    	modal.style.display = "none";
    	$(".modal").remove();
    	console.log(value);
	};
}





