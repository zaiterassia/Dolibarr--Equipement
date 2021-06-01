<?php
/* Copyright (c) 2002-2007  Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2004       Benoit Mortier          <benoit.mortier@opensides.be>
 * Copyright (C) 2004       Sebastien Di Cintio     <sdicintio@ressource-toi.org>
 * Copyright (C) 2004       Eric Seigne             <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2017  Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2006       Andre Cianfarani        <acianfa@free.fr>
 * Copyright (C) 2006       Marc Barilley/Ocebo     <marc@ocebo.com>
 * Copyright (C) 2007       Franky Van Liedekerke   <franky.van.liedekerker@telenet.be>
 * Copyright (C) 2007       Patrick Raguin          <patrick.raguin@gmail.com>
 * Copyright (C) 2010       Juanjo Menent           <jmenent@2byte.es>
 * Copyright (C) 2010-2019  Philippe Grand          <philippe.grand@atoo-net.com>
 * Copyright (C) 2011       Herve Prot              <herve.prot@symeos.com>
 * Copyright (C) 2012-2016  Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2012       Cedric Salvador         <csalvador@gpcsolutions.fr>
 * Copyright (C) 2012-2015  Raphaël Doursenaud      <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2014       Alexandre Spangaro      <aspangaro@open-dsi.fr>
 * Copyright (C) 2018       Ferran Marcet           <fmarcet@2byte.es>
 * Copyright (C) 2018-2019  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2018       Nicolas ZABOURI	        <info@inovea-conseil.com>
 * Copyright (C) 2018       Christophe Battarel     <christophe@altairis.fr>
 * Copyright (C) 2018       Josep Lluis Amador      <joseplluis@lliuretic.cat>
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
 */

/**
 *	\file       htdocs/custom/equipement/class/html.equipementform.class.php
 *  \ingroup    core
 *	\brief      File of class with all html predefined components
 */


/**
 *	Class to manage generation of HTML components
 *	Only common components must be here.
 *
 *  TODO Merge all function load_cache_* and loadCache* (except load_cache_vatrates) into one generic function loadCacheTable
 */
class EquipementForm
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var string[]    Array of error strings
	 */
	public $errors = array();

	public $num;


	/**
	 * Constructor
	 *
	 * @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
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
	public function select_contacts($socid, $selected = '', $htmlname = 'contactid', $showempty = 0, $exclude = '', $limitto = '', $showfunction = 0, $moreclass = '', $showsoc = 0, $forcecombo = 0, $events = array(), $options_only = false, $moreparam = '', $htmlid = '')
	{
		// phpcs:enable
		print $this->selectcontacts($socid, $selected, $htmlname, $showempty, $exclude, $limitto, $showfunction, $moreclass, $options_only, $showsoc, $forcecombo, $events, $moreparam, $htmlid);
		return $this->num;
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
	public function selectcontacts($socid, $selected = '', $htmlname = 'contactid', $showempty = 0, $exclude = '', $limitto = '', $showfunction = 0, $moreclass = '', $options_only = false, $showsoc = 0, $forcecombo = 0, $events = array(), $moreparam = '', $htmlid = '', $multiple = false)
	{
		global $conf, $langs, $hookmanager, $action;

		$langs->load('companies');

		if (empty($htmlid)) $htmlid = $htmlname;

		if ($selected === '') $selected = array();
		elseif (!is_array($selected)) $selected = array($selected);
		$out = '';

		if (!is_object($hookmanager))
		{
			include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
			$hookmanager = new HookManager($this->db);
		}

		// We search third parties
		$sql = "SELECT sp.rowid, sp.lastname, sp.statut, sp.firstname, sp.poste";
		if ($showsoc > 0) $sql .= " , s.nom as company";
		$sql .= " FROM ".MAIN_DB_PREFIX."ticket_contact as sp";
		if ($showsoc > 0) $sql .= " LEFT OUTER JOIN  ".MAIN_DB_PREFIX."societe as s ON s.rowid=sp.fk_soc";
		$sql .= " WHERE sp.entity IN (".getEntity('ticket_contact').")";
		if ($socid > 0 || $socid == -1) $sql .= " AND sp.fk_soc=".$socid;
		if (!empty($conf->global->CONTACT_HIDE_INACTIVE_IN_COMBOBOX)) $sql .= " AND sp.statut <> 0";
		$sql .= " ORDER BY sp.lastname ASC";

		dol_syslog(get_class($this)."::select_contacts", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);

			if ($conf->use_javascript_ajax && !$forcecombo && !$options_only)
			{
				include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
				$out .= ajax_combobox($htmlid, $events, $conf->global->CONTACT_USE_SEARCH_TO_SELECT);
			}

			if ($htmlname != 'none' && !$options_only) $out .= '<select class="flat'.($moreclass ? ' '.$moreclass : '').'" id="'.$htmlid.'" name="'.$htmlname.($multiple ? '[]' : '').'" '.($multiple ? 'multiple' : '').' '.(!empty($moreparam) ? $moreparam : '').'>';
			if (($showempty == 1 || ($showempty == 3 && $num > 1)) && !$multiple) $out .= '<option value="0"'.(in_array(0, $selected) ? ' selected' : '').'>&nbsp;</option>';
			if ($showempty == 2) $out .= '<option value="0"'.(in_array(0, $selected) ? ' selected' : '').'>-- '.$langs->trans("Internal").' --</option>';

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
				$contactstatic = new Contact($this->db);

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);

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

			$reshook = $hookmanager->executeHooks('afterSelectContactOptions', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks

			if ($htmlname != 'none' && !$options_only)
			{
				$out .= '</select>';
			}

			$this->num = $num;
			return $out;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Output html form to select a third party
	 *
	 *	@param	string	$selected       		Preselected type
	 *	@param  string	$htmlname       		Name of field in form
	 *  @param  string	$filter         		Optional filters criteras. WARNING: To avoid SQL injection, only few chars [.a-z0-9 =<>] are allowed here (example: 's.rowid <> x', 's.client IN (1,3)')
	 *	@param	string	$showempty				Add an empty field (Can be '1' or text key to use on empty line like 'SelectThirdParty')
	 * 	@param	int		$showtype				Show third party type in combolist (customer, prospect or supplier)
	 * 	@param	int		$forcecombo				Force to load all values and output a standard combobox (with no beautification)
	 *  @param	array	$events					Ajax event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *	@param	int		$limit					Maximum number of elements
	 *  @param	string	$morecss				Add more css styles to the SELECT component
	 *	@param  string	$moreparam      		Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
	 *	@param	string	$selected_input_value	Value of preselected input text (for use with ajax)
	 *  @param	int		$hidelabel				Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
	 *  @param	array	$ajaxoptions			Options for ajax_autocompleter
	 * 	@param  bool	$multiple				add [] in the name of element and add 'multiple' attribut (not working with ajax_autocompleter)
	 * 	@return	string							HTML string with select box for thirdparty.
	 */
	public function select_company($selected = '', $htmlname = 'socid', $filter = '', $showempty = '', $showtype = 0, $forcecombo = 0, $events = array(), $limit = 0, $morecss = 'minwidth100', $moreparam = '', $selected_input_value = '', $hidelabel = 1, $ajaxoptions = array(), $multiple = false)
	{
		// phpcs:enable
		global $conf, $user, $langs;

		$out = '';

		if (!empty($conf->use_javascript_ajax) && !empty($conf->global->COMPANY_USE_SEARCH_TO_SELECT) && !$forcecombo)
		{
			// No immediate load of all database
			$placeholder = '';
			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
				$societetmp = new Societe($this->db);
				$societetmp->fetch($selected);
				$selected_input_value = $societetmp->name;
				unset($societetmp);
			}
			// mode 1
			$urloption = 'htmlname='.urlencode($htmlname).'&outjson=1&filter='.urlencode($filter).($showtype ? '&showtype='.urlencode($showtype) : '');
			$out .= ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/societe/ajax/company.php', $urloption, $conf->global->COMPANY_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			$out .= '<style type="text/css">.ui-autocomplete { z-index: 250; }</style>';
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
			elseif ($hidelabel > 1) {
				$placeholder = ' placeholder="'.$langs->trans("RefOrLabel").'"';
				if ($hidelabel == 2) {
					$out .= img_picto($langs->trans("Search"), 'search');
				}
			}
			$out .= '<input type="text" class="'.$morecss.'" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' '.(!empty($conf->global->THIRDPARTY_SEARCH_AUTOFOCUS) ? 'autofocus' : '').' />';
			if ($hidelabel == 3) {
				$out .= img_picto($langs->trans("Search"), 'search');
			}
		}
		else
		{
			// Immediate load of all database
			$out .= $this->select_thirdparty_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, '', 0, $limit, $morecss, $moreparam, $multiple);
		}

		return $out;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Output html form to select a third party.
	 *  Note, you must use the select_company to get the component to select a third party. This function must only be called by select_company.
	 *
	 *	@param	string	$selected       Preselected type
	 *	@param  string	$htmlname       Name of field in form
	 *  @param  string	$filter         Optional filters criteras (example: 's.rowid <> x', 's.client in (1,3)')
	 *	@param	string	$showempty		Add an empty field (Can be '1' or text to use on empty line like 'SelectThirdParty')
	 * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
	 * 	@param	int		$forcecombo		Force to use standard HTML select component without beautification
	 *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *  @param	string	$filterkey		Filter on key value
	 *  @param	int		$outputmode		0=HTML select string, 1=Array
	 *  @param	int		$limit			Limit number of answers
	 *  @param	string	$morecss		Add more css styles to the SELECT component
	 *	@param  string	$moreparam      Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
	 *	@param  bool	$multiple       add [] in the name of element and add 'multiple' attribut
	 * 	@return	string					HTML string with
	 */
	public function select_thirdparty_list($selected = '', $htmlname = 'socid', $filter = '', $showempty = '', $showtype = 0, $forcecombo = 0, $events = array(), $filterkey = '', $outputmode = 0, $limit = 0, $morecss = 'minwidth100', $moreparam = '', $multiple = false)
	{
		// phpcs:enable
		global $conf, $user, $langs;

		$out = '';
		$num = 0;
		$outarray = array();

		if ($selected === '') $selected = array();
		elseif (!is_array($selected)) $selected = array($selected);

		// Clean $filter that may contains sql conditions so sql code
		if (function_exists('testSqlAndScriptInject')) {
			if (testSqlAndScriptInject($filter, 3) > 0) {
				$filter = '';
			}
		}

		// On recherche les societes
		$sql = "SELECT s.rowid, s.nom as name, s.name_alias, s.client, s.fournisseur, s.code_client, s.code_fournisseur";
		if ($conf->global->COMPANY_SHOW_ADDRESS_SELECTLIST) {
			$sql .= ", s.address, s.zip, s.town";
			$sql .= ", dictp.code as country_code";
		}
		$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
		if ($conf->global->COMPANY_SHOW_ADDRESS_SELECTLIST) {
			$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as dictp ON dictp.rowid = s.fk_pays";
		}
		if (!$user->rights->societe->client->voir && !$user->socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql .= " WHERE s.entity IN (".getEntity('societe').")";
		if (!empty($user->socid)) $sql .= " AND s.rowid = ".$user->socid;
		if ($filter) $sql .= " AND (".$filter.")";
		if (!$user->rights->societe->client->voir && !$user->socid) $sql .= " AND s.rowid = sc.fk_soc AND sc.fk_user = ".$user->id;
		if (!empty($conf->global->COMPANY_HIDE_INACTIVE_IN_COMBOBOX)) $sql .= " AND s.status <> 0";
		// Add criteria
		if ($filterkey && $filterkey != '')
		{
			$sql .= " AND (";
			$prefix = empty($conf->global->COMPANY_DONOTSEARCH_ANYWHERE) ? '%' : ''; // Can use index if COMPANY_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i = 0;
			if (count($scrit) > 1) $sql .= "(";
			foreach ($scrit as $crit) {
				if ($i > 0) $sql .= " AND ";
				$sql .= "(s.nom LIKE '".$this->db->escape($prefix.$crit)."%')";
				$i++;
			}
			if (count($scrit) > 1) $sql .= ")";
			if (!empty($conf->barcode->enabled))
			{
				$sql .= " OR s.barcode LIKE '".$this->db->escape($prefix.$filterkey)."%'";
			}
			$sql .= " OR s.code_client LIKE '".$this->db->escape($prefix.$filterkey)."%' OR s.code_fournisseur LIKE '".$this->db->escape($prefix.$filterkey)."%'";
			$sql .= ")";
		}
		$sql .= $this->db->order("nom", "ASC");
		$sql .= $this->db->plimit($limit, 0);

		// Build output string
		dol_syslog(get_class($this)."::select_thirdparty_list", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			if (!$forcecombo)
			{
				include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
				$out .= ajax_combobox($htmlname, $events, $conf->global->COMPANY_USE_SEARCH_TO_SELECT);
			}

			// Construct $out and $outarray
			$out .= '<select id="'.$htmlname.'" class="flat'.($morecss ? ' '.$morecss : '').'"'.($moreparam ? ' '.$moreparam : '').' name="'.$htmlname.($multiple ? '[]' : '').'" '.($multiple ? 'multiple' : '').'>'."\n";

			$textifempty = (($showempty && !is_numeric($showempty)) ? $langs->trans($showempty) : '');
			if (!empty($conf->global->COMPANY_USE_SEARCH_TO_SELECT))
			{
				// Do not use textifempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
				//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
				if ($showempty && !is_numeric($showempty)) $textifempty = $langs->trans($showempty);
				else $textifempty .= $langs->trans("All");
			}
			if ($showempty) $out .= '<option value="-1">'.$textifempty.'</option>'."\n";

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label = '';
					if ($conf->global->SOCIETE_ADD_REF_IN_LIST) {
						if (($obj->client) && (!empty($obj->code_client))) {
							$label = $obj->code_client.' - ';
						}
						if (($obj->fournisseur) && (!empty($obj->code_fournisseur))) {
							$label .= $obj->code_fournisseur.' - ';
						}
						$label .= ' '.$obj->name;
					}
					else
					{
						$label = $obj->name;
					}

					if (!empty($obj->name_alias)) {
						$label .= ' ('.$obj->name_alias.')';
					}

					if ($showtype)
					{
						if ($obj->client || $obj->fournisseur) $label .= ' (';
						if ($obj->client == 1 || $obj->client == 3) $label .= $langs->trans("Customer");
						if ($obj->client == 2 || $obj->client == 3) $label .= ($obj->client == 3 ? ', ' : '').$langs->trans("Prospect");
						if ($obj->fournisseur) $label .= ($obj->client ? ', ' : '').$langs->trans("Supplier");
						if ($obj->client || $obj->fournisseur) $label .= ')';
					}

					if ($conf->global->COMPANY_SHOW_ADDRESS_SELECTLIST) {
						$label .= '-'.$obj->address.'-'.$obj->zip.' '.$obj->town;
						if (!empty($obj->country_code)) {
							$label .= ' '.$langs->trans('Country'.$obj->country_code);
						}
					}

					if (empty($outputmode))
					{
						if (in_array($obj->rowid, $selected))
						{
							$out .= '<option value="'.$obj->rowid.'" selected>'.$label.'</option>';
						}
						else
						{
							$out .= '<option value="'.$obj->rowid.'">'.$label.'</option>';
						}
					}
					else
					{
						array_push($outarray, array('key'=>$obj->rowid, 'value'=>$label, 'label'=>$label));
					}

					$i++;
					if (($i % 10) == 0) $out .= "\n";
				}
			}
			$out .= '</select>'."\n";
		}
		else
		{
			dol_print_error($this->db);
		}

		$this->result = array('nbofthirdparties'=>$num);

		if ($outputmode) return $outarray;
		return $out;
	}



}