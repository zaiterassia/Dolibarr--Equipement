<?php
/* Copyright (C) 2017  Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * $form
 */

// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf))
{
	print "Error, template page can't be called as URL";
	exit;
}

?>
<!-- BEGIN PHP TEMPLATE commonfields_add.tpl.php -->
<?php

$object->fields = dol_sort_array($object->fields, 'position');
$form->withfromsocid = $socid ? $socid : $user->socid;
$form->withfromcontactid = $contactid ? $contactid : '';

foreach ($object->fields as $key => $val)
{
	// Discard if extrafield is a hidden field on form
	if (abs($val['visible']) != 1 && abs($val['visible']) != 3) continue;

	if (array_key_exists('enabled', $val) && isset($val['enabled']) && !verifCond($val['enabled'])) continue; // We don't want this field

	print '<tr id="field_'.$key.'">';
	print '<td';
	print ' class="titlefieldcreate';
	if ($val['notnull'] > 0) print ' fieldrequired';
	if ($val['type'] == 'text' || $val['type'] == 'html') print ' tdtop';
	print '"';
	print '>';
	if (!empty($val['help'])) print $form->textwithpicto($langs->trans($val['label']), $langs->trans($val['help']));
	else print $langs->trans($val['label']);
	print '</td>';
	print '<td>';
	if (in_array($val['type'], array('int', 'integer'))) $value = GETPOST($key, 'int');
	elseif ($val['type'] == 'text' || $val['type'] == 'html') $value = GETPOST($key, 'none');
	elseif ($val['type'] == 'date') $value = dol_mktime(12, 0, 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'));
	elseif ($val['type'] == 'datetime') $value = dol_mktime(GETPOST($key.'hour', 'int'), GETPOST($key.'min', 'int'), 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'));
	elseif ($val['type'] == 'boolean') $value = (GETPOST($key) == 'on' ? 1 : 0);
	elseif ($key== 'ref') {
		$value = $object->getNextNumRef();
	}
	else $value = GETPOST($key, 'alphanohtml');
	if ($val['noteditable']) print $object->showOutputField($val, $key, $value, '', '', '', 0);
	elseif ($key == "fk_soc"){
		$events = array();
				$events[] = array('method' => 'getContacts', 'url' => dol_buildpath('/custom/equipement/ajax/contacts.php', 1), 'htmlname' => 'contactid', 'params' => array('add-customer-contact' => 'disabled'));
				print img_picto('', 'company', 'class="paddingright"');
				print $formequipement->select_company($form->withfromsocid, 'fk_soc', '', 1, 0, '', $events, 0, 'minwidth200');
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
		// If no socid, set to -1 to avoid full contacts list
		$selectedCompany = ($form->withfromsocid > 0) ? $form->withfromsocid : -1;
		print img_picto('', 'contact', 'class="paddingright"');
		$nbofcontacts = $formequipement->select_contacts($selectedCompany, $form->withfromcontactid, 'contactid', 3, '', '', 0, 'minwidth200');

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
<!-- END PHP TEMPLATE commonfields_add.tpl.php -->
