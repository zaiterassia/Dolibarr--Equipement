<?php
/* Copyright (C) 2017-2019  Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * Need to have following variables defined:
 * $object (invoice, order, ...)
 * $action
 * $conf
 * $langs
 */

// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf))
{
	print "Error, template page can't be called as URL";
	exit;
}
if (!is_object($form)) $form = new Form($db);

?>
<!-- BEGIN PHP TEMPLATE commonfields_edit.tpl.php -->
<?php

$object->fields = dol_sort_array($object->fields, 'position');

foreach ($object->fields as $key => $val)
{
	// Discard if extrafield is a hidden field on form
	if (abs($val['visible']) != 1 && abs($val['visible']) != 3 && abs($val['visible']) != 4) continue;

	if (array_key_exists('enabled', $val) && isset($val['enabled']) && !verifCond($val['enabled'])) continue; // We don't want this field

	print '<tr><td';
	print ' class="titlefieldcreate';
	if ($val['notnull'] > 0) print ' fieldrequired';
	if ($val['type'] == 'text' || $val['type'] == 'html') print ' tdtop';
	print '">';
	if (!empty($val['help'])) print $form->textwithpicto($langs->trans($val['label']), $langs->trans($val['help']));
	else print $langs->trans($val['label']);
	print '</td>';
	print '<td>';
	if (in_array($val['type'], array('int', 'integer'))) $value = GETPOSTISSET($key) ?GETPOST($key, 'int') : $object->$key;
	elseif ($val['type'] == 'text' || $val['type'] == 'html') $value = GETPOSTISSET($key) ?GETPOST($key, 'none') : $object->$key;
	else $value = GETPOSTISSET($key) ?GETPOST($key, 'alpha') : $object->$key;
	//var_dump($val.' '.$key.' '.$value);
	if ($val['noteditable']) print $object->showOutputField($val, $key, $value, '', '', '', 0);
	elseif ($key == "fk_soc"){
		$events = array();
				$events[] = array('method' => 'getContacts', 'url' => dol_buildpath('/custom/equipement/ajax/contacts.php', 1), 'htmlname' => 'contactid', 'params' => array('add-customer-contact' => 'disabled'));
				print img_picto('', 'company', 'class="paddingright"');
				print $formequipement->select_company($value, 'fk_soc', '', 1, 0, '', $events, 0, 'minwidth200');
				print '</td></tr>';
				if (!empty($conf->use_javascript_ajax) && !empty($conf->global->COMPANY_USE_SEARCH_TO_SELECT)) {
					$htmlname = 'fk_soc';
					print '<script type="text/javascript">
                    $(document).ready(function () {
                        jQuery("#'.$htmlname.'").change(function () {
                            var obj = '.json_encode($events).';
                            $.each(obj, function(key,values) {
                                if (values.method.length) {
                                    runJsCodeForEvent'.$htmlname.'(values);
                                }
                            });
                        });

                        function runJsCodeForEvent'.$htmlname.'(obj) {
                            console.log("Run runJsCodeForEvent'.$htmlname.'");
                            var id = $("#'.$htmlname.'").val();
                            var method = obj.method;
                            var url = obj.url;
                            var htmlname = obj.htmlname;
                            var showempty = obj.showempty;
                            $.getJSON(url,
                                    {
                                        action: method,
                                        id: id,
                                        htmlname: htmlname,
                                        showempty: showempty
                                    },
                                    function(response) {
                                        $.each(obj.params, function(key,action) {
                                            if (key.length) {
                                                var num = response.num;
                                                if (num > 0) {
                                                    $("#" + key).removeAttr(action);
                                                } else {
                                                    $("#" + key).attr(action, action);
                                                }
                                            }
                                        });
                                        $("select#" + htmlname).html(response.value);
                                        if (response.num) {
                                            var selecthtml_str = response.value;
                                            var selecthtml_dom=$.parseHTML(selecthtml_str);
                                            $("#inputautocomplete"+htmlname).val(selecthtml_dom[0][0].innerHTML);
                                        } else {
                                            $("#inputautocomplete"+htmlname).val("");
                                        }
                                        $("select#" + htmlname).change();	/* Trigger event change */
                                    }
                            );
                        }
                    });
                    </script>';
				}
	}
	elseif ($key == "contactid"){
		// Contact
		print img_picto('', 'contact', 'class="paddingright"');
		$nbofcontacts = $formequipement->select_contacts($object->fk_soc, $value, 'contactid', 3, '', '', 0, 'minwidth200');
	}
	else print $object->showInputField($val, $key, $value, '', '', '', 0);
    if ($key == 'mark')
        print'<a class="butActionNew" id="addMarque" title="'.$langs->trans("newMark").'" href="#" onclick="addMarque()"><span class="fa fa-plus-circle valignmiddle"></span></a>';
    if ($key == 'model')
        print'<a class="butActionNew" id="addmodel" title="'.$langs->trans("newModel").'" href="#" onclick="addModel()"><span class="fa fa-plus-circle valignmiddle"></span></a>';
    if ($key == 'os_type')
        print'<a class="butActionNew" title="'.$langs->trans("newOs").'" href="#" onclick="addOs()"><span class="fa fa-plus-circle valignmiddle"></span></a>';
    if ($key=="garantee_time"){
        // User of creation
        if ($object->withusercreate > 0) {
            print '<tr><td class="titlefield">'.$langs->trans("CreatedBy").'</td><td>';
            $langs->load("users");
            print $user->getNomUrl(1);
        }
            print ' &nbsp; ';
            //print "</td></tr>\n";
    }
	print '</td>';
	print '</tr>';
}

?>
<!-- END PHP TEMPLATE commonfields_edit.tpl.php -->
