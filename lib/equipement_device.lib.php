<?php
/* Copyright (C) ---Put here your own copyright and developer email---
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
 */

/**
 * \file    lib/equipement_device.lib.php
 * \ingroup equipement
 * \brief   Library files with common functions for Device
 */

/**
 * Prepare array of tabs for Device
 *
 * @param	Device	$object		Device
 * @return 	array					Array of tabs
 */
function devicePrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("equipement@equipement");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/equipement/device_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	if (isset($object->fields['note_public']) || isset($object->fields['note_private']))
	{
		$nbNote = 0;
		if (!empty($object->note_private)) $nbNote++;
		if (!empty($object->note_public)) $nbNote++;
		$head[$h][0] = dol_buildpath('/equipement/device_note.php', 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1] .= (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER) ? '<span class="badge marginleftonlyshort">'.$nbNote.'</span>' : '');
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->equipement->dir_output."/device/".dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
	$nbLinks = Link::count($db, $object->element, $object->id);
	$head[$h][0] = dol_buildpath("/equipement/device_document.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles + $nbLinks) > 0) $head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
	$head[$h][2] = 'document';
	$h++;

	$head[$h][0] = dol_buildpath("/equipement/device_agenda.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Events");
	$head[$h][2] = 'agenda';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@equipement:/equipement/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@equipement:/equipement/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'device@equipement');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'device@equipement', 'remove');

	return $head;
}

// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return list of all contacts (for a third party or all)
	 *
	 *  @param	int		$socid      	Id ot third party or 0 for all
	 *  @param  string	$selected   	Id contact pre-selectionne
	 *  @param  string	$htmlname  	    Name of HTML field ('none' for a not editable field)
	 *  @param  int		$showempty      0=no empty value, 1=add an empty value, 2=add line 'Internal' (used by user edit), 3=add an empty value only if more than one record into list
	 *  @param  string	$exclude        List of contacts id to exclude
	 *  @param	string	$limitto		Disable answers that are not id in this array list
	 *  @param	integer	$showfunction   Add function into label
	 *  @param	string	$moreclass		Add more class to class style
	 *  @param	integer	$showsoc	    Add company into label
	 *  @param	int		$forcecombo		Force to use combo box
	 *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *  @param	bool	$options_only	Return options only (for ajax treatment)
	 *  @param	string	$moreparam		Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
	 *  @param	string	$htmlid			Html id to use instead of htmlname
	 *  @return	int						<0 if KO, Nb of contact in list if OK
	 *  @deprected						You can use selectcontacts directly (warning order of param was changed)
	 */
	function select_contacts($socid, $selected = '', $htmlname = 'contactid', $showempty = 0, $exclude = '', $limitto = '', $showfunction = 0, $moreclass = '', $showsoc = 0, $forcecombo = 0, $events = array(), $options_only = false, $moreparam = '', $htmlid = '')
	{
		// phpcs:enable
		print selectcontacts($socid, $selected, $htmlname, $showempty, $exclude, $limitto, $showfunction, $moreclass, $options_only, $showsoc, $forcecombo, $events, $moreparam, $htmlid);
		return $object->num;
	}

	/**
	 *	Return HTML code of the SELECT of list of all contacts (for a third party or all).
	 *  This also set the number of contacts found into $this->num
	 *
	 * @since 9.0 Add afterSelectContactOptions hook
	 *
	 *	@param	int			$socid      	Id ot third party or 0 for all or -1 for empty list
	 *	@param  array|int	$selected   	Array of ID of pre-selected contact id
	 *	@param  string		$htmlname  	    Name of HTML field ('none' for a not editable field)
	 *	@param  int			$showempty     	0=no empty value, 1=add an empty value, 2=add line 'Internal' (used by user edit), 3=add an empty value only if more than one record into list
	 *	@param  string		$exclude        List of contacts id to exclude
	 *	@param	string		$limitto		Disable answers that are not id in this array list
	 *	@param	integer		$showfunction   Add function into label
	 *	@param	string		$moreclass		Add more class to class style
	 *	@param	bool		$options_only	Return options only (for ajax treatment)
	 *	@param	integer		$showsoc	    Add company into label
	 * 	@param	int			$forcecombo		Force to use combo box (so no ajax beautify effect)
	 *  @param	array		$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *  @param	string		$moreparam		Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
	 *  @param	string		$htmlid			Html id to use instead of htmlname
	 *  @param	bool		$multiple		add [] in the name of element and add 'multiple' attribut
	 *	@return	 int						<0 if KO, Nb of contact in list if OK
	 */
	function selectcontacts($socid, $selected = '', $htmlname = 'contactid', $showempty = 0, $exclude = '', $limitto = '', $showfunction = 0, $moreclass = '', $options_only = false, $showsoc = 0, $forcecombo = 0, $events = array(), $moreparam = '', $htmlid = '', $multiple = false)
	{
		global $object, $db, $conf, $langs, $hookmanager, $action;

		$langs->load('companies');

		if (empty($htmlid)) $htmlid = $htmlname;

		if ($selected === '') $selected = array();
		elseif (!is_array($selected)) $selected = array($selected);
		$out = '';

		// if (!is_object($hookmanager))
		// {
		// 	include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
		// 	$hookmanager = new HookManager($db);
		// }

		// We search third parties
		$sql = "SELECT sp.rowid, sp.lastname, sp.statut, sp.firstname, sp.poste";
		if ($showsoc > 0) $sql .= " , s.nom as company";
		$sql .= " FROM ".MAIN_DB_PREFIX."ticket_contact as sp";
		if ($showsoc > 0) $sql .= " LEFT OUTER JOIN  ".MAIN_DB_PREFIX."societe as s ON s.rowid=sp.fk_soc";
		$sql .= " WHERE sp.entity IN (".getEntity('ticket_contact').")";
		if ($socid > 0 || $socid == -1) $sql .= " AND sp.fk_soc=".$socid;
		if (!empty($conf->global->CONTACT_HIDE_INACTIVE_IN_COMBOBOX)) $sql .= " AND sp.statut <> 0";
		$sql .= " ORDER BY sp.lastname ASC";

		//dol_syslog(get_class($this)."::select_contacts", LOG_DEBUG);
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);

			if ($conf->use_javascript_ajax && !$forcecombo && !$options_only)
			{
				include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
				$out .= ajax_combobox($htmlid, $events, $conf->global->CONTACT_USE_SEARCH_TO_SELECT);
			}

			if ($htmlname != 'none' && !$options_only) $out .= '<select class="flat'.($moreclass ? ' '.$moreclass : '').'" id="'.$htmlid.'" name="'.$htmlname.($multiple ? '[]' : '').'" '.($multiple ? 'multiple' : '').' '.(!empty($moreparam) ? $moreparam : '').'>';
			if (($showempty == 1 || ($showempty == 3 && $num > 1)) && !$multiple) $out .= '<option value="0"'.(in_array(0, $selected) ? ' selected' : '').'>&nbsp;</option>';
			if ($showempty == 2) $out .= '<option value="0"'.(in_array(0, $selected) ? ' selected' : '').'>-- '.$langs->trans("Internal").' --</option>';

			$num = $db->num_rows($resql);
			var_dump($num);
			$i = 0;
			if ($num)
			{
				include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
				$contactstatic = new Contact($db);

				while ($i < $num)
				{
					$obj = $db->fetch_object($resql);

					$contactstatic->id = $obj->rowid;
					$contactstatic->lastname = $obj->lastname;
					$contactstatic->firstname = $obj->firstname;
					if ($obj->statut == 1) {
						if ($htmlname != 'none')
						{
							$disabled = 0;
							if (is_array($exclude) && count($exclude) && in_array($obj->rowid, $exclude)) $disabled = 1;
							if (is_array($limitto) && count($limitto) && !in_array($obj->rowid, $limitto)) $disabled = 1;
							if (!empty($selected) && in_array($obj->rowid, $selected))
							{
								$out .= '<option value="'.$obj->rowid.'"';
								if ($disabled) $out .= ' disabled';
								$out .= ' selected>';
								$out .= $contactstatic->getFullName($langs);
								if ($showfunction && $obj->poste) $out .= ' ('.$obj->poste.')';
								if (($showsoc > 0) && $obj->company) $out .= ' - ('.$obj->company.')';
								$out .= '</option>';
							}
							else
							{
								$out .= '<option value="'.$obj->rowid.'"';
								if ($disabled) $out .= ' disabled';
								$out .= '>';
								$out .= $contactstatic->getFullName($langs);
								if ($showfunction && $obj->poste) $out .= ' ('.$obj->poste.')';
								if (($showsoc > 0) && $obj->company) $out .= ' - ('.$obj->company.')';
								$out .= '</option>';
							}
						}
						else
						{
							if (in_array($obj->rowid, $selected))
							{
								$out .= $contactstatic->getFullName($langs);
								if ($showfunction && $obj->poste) $out .= ' ('.$obj->poste.')';
								if (($showsoc > 0) && $obj->company) $out .= ' - ('.$obj->company.')';
							}
						}
					}
					$i++;
				}
			}
			else
			{
				$out .= '<option value="-1"'.(($showempty == 2 || $multiple) ? '' : ' selected').' disabled>';
				$out .= ($socid != -1) ? ($langs->trans($socid ? "NoContactDefinedForThirdParty" : "NoContactDefined")) : $langs->trans('SelectAThirdPartyFirst');
				$out .= '</option>';
			}

			$parameters = array(
				'socid'=>$socid,
				'htmlname'=>$htmlname,
				'resql'=>$resql,
				'out'=>&$out,
				'showfunction'=>$showfunction,
				'showsoc'=>$showsoc,
			);

			//$reshook = $hookmanager->executeHooks('afterSelectContactOptions', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks

			if ($htmlname != 'none' && !$options_only)
			{
				$out .= '</select>';
			}

			$object->num = $num;
			return $out;
		}
		else
		{
			dol_print_error($db);
			return -1;
		}
	}

/**
 *  Return clicable Thirdparty name to the correct tab (with picto eventually)
 *
 *	@param	int		$withpicto					Include picto into link
 *  @param  string	$mode           			''=Link to card, 'transactions'=Link to transactions card
 *  @param  string  $option         			''=Show ref, 'reflabel'=Show ref+label
 *  @param  int     $save_lastsearch_value    	-1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
 *  @param	int  	$notooltip		 			1=Disable tooltip
 *  @param  int     $socid                      Id of Thirdparty
 *  @param  string  $name                       Name of Thirdparty
 *  @param  boolean $picto                      With picto or not
 *  @param  string  $where                      device/ app : redirect to the correct Thirdparty tab Applications or Devices
 *	@return	string								Chaine avec URL
 */
function goToThirdparty($withpicto=0, $option='', $notooltip=0, $morecss='', $save_lastsearch_value=-1, $socid, $name, $picto, $where)
{
    global $db, $conf, $langs;
    global $dolibarr_main_authentication, $dolibarr_main_demo;
    global $menumanager;

    if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

    $result = '';
    $companylink = '';

    $label = '<u>' . $langs->trans("Thirdparty") . '</u>';
    $label.= '<br>';
    $label.= '<b>' . $langs->trans('name') . ':</b> ' . $name;

    //Redirect to the correct tab
    if ($where == 'device') {
        $url = dol_buildpath('/infoextranet/device.php', 1) . '?socid=' . $socid;
    } elseif ($where == 'app') {
        $url = dol_buildpath('/infoextranet/application.php', 1) . '?socid=' . $socid;
    } else {
        $url = dol_buildpath('/infoextranet/index.php', 1) . '?socid=' . $socid;
    }

    if ($option != 'nolink')
    {
        // Add param to save lastsearch_values or not
        $add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
        if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
        if ($add_save_lastsearch_values) $url.='&save_lastsearch_values=1';
    }

    $linkclose='';
    if (empty($notooltip))
    {
        if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
        {
            $label=$langs->trans("ShowApplication");
            $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
        }
        $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
        $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
    }
    else $linkclose = ($morecss?' class="'.$morecss.'"':'');

    $linkstart = '<a href="'.$url.'"';
    $linkstart.=$linkclose.'>';
    $linkend='</a>';

    $result .= $linkstart;
    if ($withpicto) $result.=img_object(($notooltip?'':$label), ($picto?$picto:'generic'), ($notooltip?(($withpicto != 2) ? 'class="paddingright"' : ''):'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip?0:1);
    if ($withpicto != 2) $result.= $name;
    $result .= $linkend;
    //if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

    return $result;
}
