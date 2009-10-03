<?php

/*
 * phpMyEdit - instant MySQL table editor and code generator
 *
 * extensions/phpMyEdit-report.class.php - phpMyEdit report extension
 * ____________________________________________________________
 *
 * Developed by Ondrej Jombik <nepto@platon.sk>
 * Copyright (c) 2002-2006 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpMyEdit/
 */

/* $Platon: phpMyEdit/extensions/phpMyEdit-report.class.php,v 1.11 2004/12/30 19:59:00 nepto Exp $ */

/* Extension TODO:

   - allow user to enable/disable particular field in reporting (maybe 'X' flag
     for indicating that field is forbidden is good idea)
   - support for ['help'] in select fields screen
   - make extension's option for selecting "Select fields" link or button
 */

require_once dirname(__FILE__).'/../phpMyEdit.class.php';

class phpMyEdit_report extends phpMyEdit
{

	function phpMyEdit_report($opts) /* {{{ */
	{
		$opts['options'] = 'L';
		$execute = 1;
		isset($opts['execute']) && $execute = $opts['execute'];
		$opts['execute'] = 0;
		parent::phpMyEdit($opts);
		$execute && $this->execute();
	} /* }}} */

	function make_language_labels($language) /* {{{ */
	{
		$ret = parent::make_language_labels($language);
		strlen($ret['Make report'])        <= 0 && $ret['Make report']        = 'Make report';
		strlen($ret['Select fields'])      <= 0 && $ret['Select fields']      = 'Select fields';
		strlen($ret['Records per screen']) <= 0 && $ret['Records per screen'] = 'Records per screen';
		return $ret;
	} /* }}} */

	function get_cgi_cookie_var($name, $default_value = null) /* {{{ */
	{
		$ret = $this->get_cgi_var($name, null);
		if ($ret === null) {
			global $HTTP_COOKIE_VARS;
			$ret = @$HTTP_COOKIE_VARS[$name.'_'.$this->tb.'_cookie'];
			if (! isset($ret)) {
				$ret = $default_value;
			}
		}
		return $ret;
	} /* }}} */

	function display_list_table_buttons($total_recs, $position) /* {{{ */
	{	/* This is mostly copy/paste from core class. */
		$listall = $this->inc <= 0; // Are we doing a listall?
		echo '<table class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<td class="',$this->getCSSclass('buttons', $position),'">',"\n";
		echo '<input class="',$this->getCSSclass('fields-select', $position);
		echo '" type="submit" name="fields_select" value="',$this->labels['Select fields'],'">&nbsp;';
		// Note that <input disabled isn't valid HTML, but most browsers support it
		$disabled = ($this->fm > 0 && ! $listall) ? '' : ' disabled';
		echo '<input',$disabled,' class="',$this->getCSSclass('prev', $position);
		echo '" type="submit" name="',ltrim($disabled),'prev" value="',$this->labels['Prev'],'">&nbsp;';
		$disabled = ($this->fm + $this->inc < $total_recs && ! $listall) ? '' : ' disabled';
		echo '<input',$disabled,' class="',$this->getCSSclass('next', $position);
		echo '" type="submit" name="',ltrim($disabled),'next" value="',$this->labels['Next'],'">';
		// Message is now written here
		echo '</td>',"\n";
		if (strlen(@$this->message) > 0) {
			echo '<td class="',$this->getCSSclass('message', $position),'">',$this->message,'</td>',"\n";
		}
		// Display page and records statistics
		echo '<td class="',$this->getCSSclass('stats', $position),'">',"\n";
		if ($listall) {
			echo $this->labels['Page'],':&nbsp;1&nbsp;',$this->labels['of'],'&nbsp;1';
		} else {
			echo $this->labels['Page'],':&nbsp;',($this->fm / $this->inc) + 1;
			echo '&nbsp;',$this->labels['of'],'&nbsp;',max(1, ceil($total_recs / abs($this->inc)));
		}
		echo '&nbsp; ',$this->labels['Records'],':&nbsp;',$total_recs;
		echo '</td></tr></table>',"\n";
	} /* }}} */

	function display_report_selection_buttons($position) /* {{{ */
	{
		echo '<table class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<td class="',$this->getCSSclass('buttons', $position),'">',"\n";
		echo '<input class="',$this->getCSSclass('make-report', $position);
		echo '" type="submit" name="prepare_filter" value="',$this->labels['Make report'],'">',"\n";
		echo '</td></tr></table>',"\n";
	} /* }}} */

	function get_select_fields_link() /* {{{ */
	{
		$link = '<a href="'.htmlspecialchars($this->page_name).'?fields_select=1';
		for ($i = 0; $i < count($table_cols); $i++) {
			$varname = 'qf'.$i;
			$value   = $this->get_cgi_cookie_var($varname);
			if (! empty($value)) {
				$link .= htmlspecialchars(
						'&'.rawurlencode($varname).
						'='.rawurlencode($value));
			}
		}
		$link .= htmlspecialchars($this->cgi['persist']);
		$link .= '">'.$this->labels['Select fields'].'</a>';
		return $link;
	} /* }}} */

	function execute() /* {{{ */
	{
		global $HTTP_GET_VARS;
		global $HTTP_POST_VARS;

		/*
		 * Extracting field names
		 */

		$table_cols     = array();
		$all_table_cols = array();

		if ($this->connect() == false) {
			return false;
		}
		$query_parts = array(
				'type'   => 'select',
				'select' => '*',
				'from'   => $this->tb,
				'limit'  => '1');
		$result = $this->myquery($this->get_SQL_query($query_parts), __LINE__);
		$all_table_cols = array_keys(@mysql_fetch_array($result, MYSQL_ASSOC));
		if (count($all_table_cols) <= 0) {
			$this->error('database fetch error');
			return false;
		}
		foreach (array_keys($this->fdd) as $field_name) {
			if (preg_match('/^\d*$/', $field_name))
				continue;
			if (($idx = array_search($field_name, $all_table_cols)) !== false)
				$table_cols[$field_name] = mysql_field_len($result, $idx);
		}
		@mysql_free_result($result);
		unset($all_table_cols);

		/*
		 * Preparing variables
		 */

		$fields_select  = $this->get_cgi_var('fields_select');
		$filter         = $this->get_cgi_var('filter');
		$prepare_filter = $this->get_cgi_var('prepare_filter');
		$this->inc      = intval($this->get_cgi_cookie_var('inc'));
		$force_select   = true;
		$none_displayed = true;
		$expire_time    = time() + (3600 * 24 * 30 * 12 * 5); // five years
		$headers_sent   = @headers_sent();

		foreach (array_merge(array('@inc'), array_keys($table_cols)) as $col) {
			$varname = ($col[0] == '@' ? substr($col, 1) : 'have_'.$col);
			if (isset($HTTP_POST_VARS[$varname]) || isset($HTTP_GET_VARS[$varname])) {
				$value = $HTTP_POST_VARS[$varname];
				if (isset($HTTP_GET_VARS[$varname])) {
					$value = $HTTP_GET_VARS[$varname];
				}
				if ($varname != 'inc' && ! empty($value)) {
					$force_select = false;
				}
				$headers_sent || setcookie($varname.'_'.$this->tb.'_cookie', $value, $expire_time);
				$this->cgi['persist'] .= '&'.urlencode($varname);
				$this->cgi['persist'] .= '='.urlencode($value);
			} else {
				$headers_sent || setcookie($varname.'_'.$this->tb.'_cookie', '', time() - 10000);
			}
		}

		$i = -1;
		foreach (array_keys($this->fdd) as $key) {
			$i++;
			if (preg_match('/^\d*$/', $key))
				continue;
			$varname = 'have_'.$key;
			$value   = @$this->get_cgi_cookie_var($varname, '');
			$options = @$value ? 'LV' : '';
			$this->fdd[$i]['options']   = $options;
			$this->fdd[$key]['options'] = $options;
			$this->displayed[$i] = @$value ? true : false;
			$value && $none_displayed = false;
		}

		/*
		 * Redirecting when neccessary
		 * (hackity hack with unregistering/unchecking fields)
		 */

		if ($prepare_filter && ! $headers_sent) {
			$this->execute_redirect();
			exit;
		}

		/*
		 * Check if field selection report screen has to be displayed
		 */

		if (isset($fields_select) || $force_select || $none_displayed) {
			$this->execute_report_screen($table_cols);
			return true;
		}

		if (0) {
			$this->message .= $this->get_select_fields_link();
		}

		// parent class call
		return parent::execute();
	} /* }}} */

	function execute_redirect() /* {{{ */
	{
		global $HTTP_SERVER_VARS;
		global $HTTP_GET_VARS;
		global $HTTP_POST_VARS;
		$redirect_url = 'http://'.$HTTP_SERVER_VARS['HTTP_HOST'].$HTTP_SERVER_VARS['SCRIPT_NAME'];
		$delim = '?';
		foreach ($HTTP_POST_VARS + $HTTP_GET_VARS as $cgi_var_name => $cgi_var_value) {
			$cgi_var_name == 'prepare_filter' && $cgi_var_name = 'filter';
			$redirect_url .= $delim;
			$redirect_url .= rawurlencode($cgi_var_name).'='.rawurlencode($cgi_var_value);
			$delim == '?' && $delim = '&';
		}
		$redirect_url .= $this->cgi['persist'];
		header('Location: '.$redirect_url);
		exit;
	} /* }}} */

	function execute_report_screen($table_cols) /* {{{ */
	{
		echo '<form class="',$this->getCSSclass('form'),'" action="';
		echo htmlspecialchars($this->page_name),'" method="POST">',"\n";
		if ($this->nav_up()) {
			$this->display_report_selection_buttons('up');
			echo '<hr class="',$this->getCSSclass('hr', 'up'),'">',"\n";
		}
		echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";

		$i = 0;
		foreach ($table_cols as $key => $val) {
			$css_postfix    = @$this->fdd[$key]['css']['postfix'];
			$css_class_name = $this->getCSSclass('input', null, true, $css_postfix);
			$varname        = 'have_'.$key;
			$value          = $this->get_cgi_cookie_var($varname);
			$checked        = @$value ? ' checked' : ''; 
			echo '<tr class="',$this->getCSSclass('row', null, 'next', $css_postfix),'">',"\n";
			echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'">';
			echo $this->fdd[$i]['name'],'</td>',"\n";
			echo '<td class="',$this->getCSSclass('check', null, true, $css_postfix),'">';
			echo '<input class="',$css_class_name,'" type="checkbox" name="';
			echo htmlspecialchars($varname),'"',$checked,'>';
			echo '</td>',"\n";
			echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
			echo $this->getColAttributes($key),">\n";
			$varname = 'qf'.$i;
			$value   = $this->get_cgi_cookie_var($varname);
			if ($this->fdd[$key]['select'] == 'D' || $this->fdd[$key]['select'] == 'M') {
				$from_table = ! $this->col_has_values($key) || isset($this->fdd[$key]['values']['table']);
				$selected   = $value;
				$value      = $this->set_values($key, array('*' => '*'), null, $from_table);
				$multiple   = $this->col_has_multiple_select($key);
				$multiple  |= $this->fdd[$key]['select'] == 'M';
				$readonly   = false;
				$strip_tags = true;
				$escape     = true;
				echo $this->htmlSelect($varname.'_id', $css_class_name, $value, $selected,
						$multiple, $readonly, $strip_tags, $escape);
			} else {
				echo '<input class="',$css_class_name,'" type=text name="';
				echo htmlspecialchars($varname),'" value="',htmlspecialchars($value),'" size="';
				echo min(40, $val),'" maxlength="',min(40, max(10, $val)),'">';
			}
			echo '</td>',"\n",'</tr>',"\n";
			$i++;
		}
		echo '<tr class="',$this->getCSSclass('row', null, 'next', $css_postfix),'">',"\n";
		echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'" colspan="2">';
		echo $this->labels['Records per screen'],'</td>';
		echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'">';
		echo '<input class="',$css_class_name,'" type="text" name="inc" value="',$this->inc.'">';
		echo '</td></tr>',"\n";
		echo '</table>',"\n";
		if ($this->nav_down()) {
			echo '<hr class="',$this->getCSSclass('hr', 'down'),'">',"\n";
			$this->display_report_selection_buttons('down');
		}
		echo '</form>';
	} /* }}} */

}

/* Modeline for ViM {{{
 * vim:set ts=4:
 * vim600:fdm=marker fdl=0 fdc=0:
 * }}} */

?>
