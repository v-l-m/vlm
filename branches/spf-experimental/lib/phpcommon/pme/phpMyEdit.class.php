<?php

/*
 * phpMyEdit - instant MySQL table editor and code generator
 *
 * phpMyEdit.class.php - main table editor class definition file
 * ____________________________________________________________
 *
 * Copyright (c) 1999-2002 John McCreesh <jpmcc@users.sourceforge.net>
 * Copyright (c) 2001-2002 Jim Kraai <jkraai@users.sourceforge.net>
 * Versions 5.0 and higher developed by Ondrej Jombik <nepto@php.net>
 * Copyright (c) 2002-2006 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpMyEdit/
 */

/* $Platon: phpMyEdit/phpMyEdit.class.php,v 1.204 2007-09-16 12:57:07 nepto Exp $ */

/*  This is a generic table editing program. The table and fields to be
	edited are defined in the calling program.

	This program works in three passes.
	* Pass 1 (the last part of the program) displays the selected SQL
	  table in a scrolling table on the screen. Radio buttons are used to
	  select a record for editing or deletion. If the user chooses Add,
	  Change, Copy, View or Delete buttons.
	* Pass 2 starts, displaying the selected record. If the user chooses
	  the Save button from this screen.
	* Pass 3 processes the update and the display returns to the
	  original table view (Pass 1).
*/

class phpMyEdit_timer /* {{{ */
{
	var $startTime;
	var $started;

	function phpMyEdit_timer($start = true)
	{
		$this->started = false;
		if ($start) {
			$this->start();
		}
	}

	function start()
	{
		$startMtime      = explode(' ', microtime());
		$this->startTime = (double) $startMtime[0] + (double) $startMtime[1];
		$this->started   = true;
	}

	function end($iterations = 1)
	{
		// get the time, check whether the timer was started later
		$endMtime = explode(' ', microtime());
		if ($this->started) {
			$endTime = (double)($endMtime[0])+(double)($endMtime[1]);
			$dur = $endTime - $this->startTime;
			$avg = 1000 * $dur / $iterations;
			$avg = round(1000 * $avg) / 1000;
			return $avg;
		} else {
			return 'phpMyEdit_timer ERROR: timer not started';
		}
	}
} /* }}} */

if (! function_exists('array_search')) { /* {{{ */
	function array_search($needle, $haystack)
	{
		foreach ($haystack as $key => $value) {
			if ($needle == $value)
				return $key;
		}
		return false;
	}
} /* }}} */

if (! function_exists('realpath')) { /* {{{ */
	function realpath($path)
	{
		return $path;
	}
} /* }}} */

class phpMyEdit
{
	// Class variables {{{

	// Database handling
	var $hn;		// hostname
	var $un;		// user name
	var $pw;		// password
	var $tb;		// table
	var $db;		// database
	var $dbp;		// database with point and delimiters
	var $dbh;		// database handle
	var $close_dbh;	// if database handle should be closed

	// Record manipulation
	var $key;		// name of field which is the unique key
	var $key_num;	// number of field which is the unique key
	var $key_type;	// type of key field (int/real/string/date etc.)
	var $key_delim;	// character used for key value quoting
	var $rec;		// number of record selected for editing
	var $inc;		// number of records to display
	var $fm;		// first record to display
	var $fl;		// is the filter row displayed (boolean)
	var $fds;		// sql field names
	var $fdn;		// sql field names => $k
	var $num_fds;	// number of fields
	var $options;	// options for users: ACDFVPI
	var $fdd;		// field definitions
	var $qfn;		// value of all filters used during the last pass
	var $sfn;		// sort field number (- = descending sort order)
	var $cur_tab;	// current selected tab

	// Operation
	var $navop;		// navigation buttons/operations
	var $sw;		// filter display/hide/clear button
	var $operation;	// operation to do: Add, Change, Delete
	var $saveadd;
	var $moreadd;
	var $canceladd;
	var $savechange;
	var $morechange;
	var $cancelchange;
	var $savecopy;
	var $cancelcopy;
	var $savedelete;
	var $canceldelete;
	var $cancelview;

	// Additional features
	var $labels;		// multilingual labels
	var $cgi;			// CGI variable features array
	var $js;			// JS configuration array
	var $dhtml;			// DHTML configuration array
	var $url;			// URL array
	var $message;		// informational message to print
	var $notify;		// change notification e-mail adresses
	var $logtable;		// name of optional logtable
	var $navigation;	// navigation style
	var $tabs;			// TAB names
	var $timer = null;	// phpMyEdit_timer object
	var $sd; var $ed;	// sql start and end delimiters '`' in case of MySQL

	// Predefined variables
	var $comp_ops  = array('<'=>'<','<='=>'<=','='=>'=','>='=>'>=','>'=>'>');
	var $sql_aggrs = array(
			'sum'   => 'Total',
			'avg'   => 'Average',
			'min'   => 'Minimum',
			'max'   => 'Maximum',
			'count' => 'Count');
	var $page_types = array(
			'L' => 'list',
			'F' => 'filter',
			'A' => 'add',
			'V' => 'view',
			'C' => 'change',
			'P' => 'copy',
			'D' => 'delete'
			);
	var $default_buttons = array(
			'L' => array('<<','<','add','view','change','copy','delete','>','>>','goto','goto_combo'),
			'F' => array('<<','<','add','view','change','copy','delete','>','>>','goto','goto_combo'),
			'A' => array('save','more','cancel'),
			'C' => array('save','more','cancel'),
			'P' => array('save', 'cancel'),
			'D' => array('save','cancel'),
			'V' => array('change','cancel')
			);
	// }}}

	/*
	 * column specific functions
	 */

	function col_has_sql($k)    { return isset($this->fdd[$k]['sql']); }
	function col_has_sqlw($k)   { return isset($this->fdd[$k]['sqlw']) && !$this->virtual($k); }
	function col_has_values($k) { return isset($this->fdd[$k]['values']) || isset($this->fdd[$k]['values2']); }
	function col_has_php($k)    { return isset($this->fdd[$k]['php']); }
	function col_has_URL($k)    { return isset($this->fdd[$k]['URL'])
		|| isset($this->fdd[$k]['URLprefix']) || isset($this->fdd[$k]['URLpostfix']); }
	function col_has_multiple($k)
	{ return $this->col_has_multiple_select($k) || $this->col_has_checkboxes($k); }
	function col_has_multiple_select($k)
	{ return $this->fdd[$k]['select'] == 'M' && ! $this->fdd[$k]['values']['table']; }
	function col_has_checkboxes($k)
	{ return $this->fdd[$k]['select'] == 'C' && ! $this->fdd[$k]['values']['table']; }
	function col_has_radio_buttons($k)
	{ return $this->fdd[$k]['select'] == 'O' && ! $this->fdd[$k]['values']['table']; }
	function col_has_datemask($k)
	{ return isset($this->fdd[$k]['datemask']) || isset($this->fdd[$k]['strftimemask']); }

	/*
	 * functions for indicating whether navigation style is enabled
     */

	function nav_buttons()       { return stristr($this->navigation, 'B'); }
	function nav_text_links()    { return stristr($this->navigation, 'T'); }
	function nav_graphic_links() { return stristr($this->navigation, 'G'); }
	function nav_up()            { return (stristr($this->navigation, 'U') && !($this->buttons[$this->page_type]['up'] === false)); }
	function nav_down()          { return (stristr($this->navigation, 'D') && !($this->buttons[$this->page_type]['down'] === false)); }

	/*
	 * functions for indicating whether operations are enabled
	 */

	function add_enabled()    { return stristr($this->options, 'A'); }
	function change_enabled() { return stristr($this->options, 'C'); }
	function delete_enabled() { return stristr($this->options, 'D'); }
	function filter_enabled() { return stristr($this->options, 'F'); }
	function view_enabled()   { return stristr($this->options, 'V'); }
	function copy_enabled()   { return stristr($this->options, 'P') && $this->add_enabled(); }
	function tabs_enabled()   { return $this->display['tabs'] && count($this->tabs) > 0; }
	function hidden($k)       { return stristr($this->fdd[$k]['input'],'H'); }
	function password($k)     { return stristr($this->fdd[$k]['input'],'W'); }
	function readonly($k)     { return stristr($this->fdd[$k]['input'],'R') || $this->virtual($k);     }
	function virtual($k)      { return stristr($this->fdd[$k]['input'],'V') && $this->col_has_sql($k); }

	function add_operation()    { return $this->operation == $this->labels['Add']    && $this->add_enabled();    }
	function change_operation() { return $this->operation == $this->labels['Change'] && $this->change_enabled(); }
	function copy_operation()   { return $this->operation == $this->labels['Copy']   && $this->copy_enabled();   }
	function delete_operation() { return $this->operation == $this->labels['Delete'] && $this->delete_enabled(); }
	function view_operation()   { return $this->operation == $this->labels['View']   && $this->view_enabled();   }
	function filter_operation() { return $this->fl && $this->filter_enabled() && $this->list_operation(); }
	function list_operation()   { /* covers also filtering page */ return ! $this->change_operation()
										&& ! $this->add_operation()    && ! $this->copy_operation()
										&& ! $this->delete_operation() && ! $this->view_operation(); }
	function next_operation()	{ return ($this->navop == $this->labels['Next']) || ($this->navop == '>'); }
	function prev_operation()	{ return ($this->navop == $this->labels['Prev']) || ($this->navop == '<'); }
	function first_operation()	{ return ($this->navop == $this->labels['First']) || ($this->navop == '<<'); }
	function last_operation()	{ return ($this->navop == $this->labels['Last']) || ($this->navop == '>>'); }
	function clear_operation()	{ return $this->sw == $this->labels['Clear'];  }

	function add_canceled()    { return $this->canceladd    == $this->labels['Cancel']; }
	function view_canceled()   { return $this->cancelview   == $this->labels['Cancel']; }
	function change_canceled() { return $this->cancelchange == $this->labels['Cancel']; }
	function copy_canceled()   { return $this->cancelcopy   == $this->labels['Cancel']; }
	function delete_canceled() { return $this->canceldelete == $this->labels['Cancel']; }

	function is_values2($k, $val = 'X') /* {{{ */
	{
		return $val === null ||
			(isset($this->fdd[$k]['values2']) && !isset($this->fdd[$k]['values']['table']));
	} /* }}} */

	function processed($k) /* {{{ */
	{
		if ($this->virtual($k)) {
			return false;
		}
		$options = @$this->fdd[$k]['options'];
		if (! isset($options)) {
			return true;
		}
		return
			($this->saveadd    == $this->labels['Save']  && stristr($options, 'A')) ||
			($this->moreadd    == $this->labels['More']  && stristr($options, 'A')) ||
			($this->savechange == $this->labels['Save']  && stristr($options, 'C')) ||
			($this->morechange == $this->labels['Apply'] && stristr($options, 'C')) ||
			($this->savecopy   == $this->labels['Save']  && stristr($options, 'P')) ||
			($this->savedelete == $this->labels['Save']  && stristr($options, 'D'));
	} /* }}} */

	function displayed($k) /* {{{ */
	{
		if (is_numeric($k)) {
			$k = $this->fds[$k];
		}
		$options = @$this->fdd[$k]['options'];
		if (! isset($options)) {
			return true;
		}
		return
			($this->add_operation()    && stristr($options, 'A')) ||
			($this->view_operation()   && stristr($options, 'V')) ||
			($this->change_operation() && stristr($options, 'C')) ||
			($this->copy_operation()   && stristr($options, 'P')) ||
			($this->delete_operation() && stristr($options, 'D')) ||
			($this->filter_operation() && stristr($options, 'F')) ||
			($this->list_operation()   && stristr($options, 'L'));
	} /* }}} */
	
	function debug_var($name, $val) /* {{{ */
	{
		if (is_array($val) || is_object($val)) {
			echo "<pre>$name\n";
			ob_start();
			//print_r($val);
			var_dump($val);
			$content = ob_get_contents();
			ob_end_clean();
			echo htmlspecialchars($content);
			echo "</pre>\n";
		} else {
			echo 'debug_var()::<i>',htmlspecialchars($name),'</i>';
			echo '::<b>',htmlspecialchars($val),'</b>::',"<br />\n";
		}
	} /* }}} */

	/*
	 * sql functions
     */
	function sql_connect() /* {{{ */
	{
		$this->dbh = @ini_get('allow_persistent')
			? @mysql_pconnect($this->hn, $this->un, $this->pw)
			: @mysql_connect($this->hn, $this->un, $this->pw);
	} /* }}} */
		

	function sql_disconnect() /* {{{ */
	{
		if ($this->close_dbh) {
			@mysql_close($this->dbh);
			$this->dbh = null;
		}
	} /* }}} */

	function sql_fetch(&$res, $type = 'a') /* {{{ */
	{
		if($type == 'n') $type = MYSQL_NUM;
		else $type = MYSQL_ASSOC;
		return @mysql_fetch_array($res, $type);
	} /* }}} */

	function sql_free_result(&$res) /* {{{ */
	{
		return @mysql_free_result($res);
	} /* }}} */

	function sql_affected_rows(&$dbh) /* {{{ */
	{
		return @mysql_affected_rows($dbh);
	} /* }}} */

	function sql_field_len(&$res,$field) /* {{{ */
	{
		return @mysql_field_len($res, $field);
	} /* }}} */

	function sql_insert_id() /* {{{ */
	{
		return mysql_insert_id($this->dbh);
	} /* }}} */

	function sql_limit($start, $more) /* {{{ */
	{
		return ' LIMIT '.$start.', '.$more.' ';
	} /* }}} */

	function sql_delimiter() /* {{{ */
	{
		$this->sd = '`'; $this->ed='`';
		return $this->sd;
	} /* }}} */


	function myquery($qry, $line = 0, $debug = 0) /* {{{ */
	{
		global $debug_query;
		if ($debug_query || $debug) {
			$line = intval($line);
			echo '<h4>MySQL query at line ',$line,'</h4>',htmlspecialchars($qry),'<hr size="1" />',"\n";
		}
		if (isset($this->db)) {
			$ret = @mysql_db_query($this->db, $qry, $this->dbh);
		} else {
			$ret = @mysql_query($qry, $this->dbh);
		}
		if (! $ret) {
			echo '<h4>MySQL error ',mysql_errno($this->dbh),'</h4>';
			echo htmlspecialchars(mysql_error($this->dbh)),'<hr size="1" />',"\n";
		}
		return $ret;
	} /* }}} */

	/* end of sql functions */ 

	function make_language_labels($language) /* {{{ */
	{
		// just try the first language and variant
		// this isn't content-negotiation rfc compliant
		$language = strtoupper($language);

		// try the full language w/ variant
		$file = $this->dir['lang'].'PME.lang.'.$language.'.inc';
		while (! file_exists($file)) {
			$pos = strrpos($language, '-');
			if ($pos === false) {
				$file = $this->dir['lang'].'PME.lang.EN.inc';
				break;
			}
			$language = substr($language, 0, $pos);
			$file = $this->dir['lang'].'PME.lang.'.$language.'.inc';
		}
		$ret = @include($file);
		if (! is_array($ret)) {
			return $ret;
		}
		$small = array(
				'Search' => 'v',
				'Hide'   => '^',
				'Clear'  => 'X',
				'Query'  => htmlspecialchars('>'));
		if ((!$this->nav_text_links() && !$this->nav_graphic_links())
				|| !isset($ret['Search']) || !isset($ret['Query'])
				|| !isset($ret['Hide'])   || !isset($ret['Clear'])) {
			foreach ($small as $key => $val) {
				$ret[$key] = $val;
			}
		}
		return $ret;
	} /* }}} */

	function set_values($field_num, $prepend = null, $append = null, $strict = false) /* {{{ */
	{
		return (array) $prepend + (array) $this->fdd[$field_num]['values2']
			+ (isset($this->fdd[$field_num]['values']['table']) || $strict
					? $this->set_values_from_table($field_num, $strict)
					: array())
			+ (array) $append;
	} /* }}} */

	function set_values_from_table($field_num, $strict = false) /* {{{ */
	{
		$db    = &$this->fdd[$field_num]['values']['db'];
		$table = $this->sd.$this->fdd[$field_num]['values']['table'].$this->ed;
		$key   = &$this->fdd[$field_num]['values']['column'];
		$desc  = &$this->fdd[$field_num]['values']['description'];
		$dbp   = isset($db) ? $this->sd.$db.$this->ed.'.' : $this->dbp;
		$qparts['type'] = 'select';
		if ($table != $this->sd.$this->ed) {
			$qparts['select'] = 'DISTINCT '.$table.'.'.$this->sd.$key.$this->ed;
			if ($desc && is_array($desc) && is_array($desc['columns'])) {
				$qparts['select'] .= ',CONCAT('; // )
				$num_cols = sizeof($desc['columns']);
				if (isset($desc['divs'][-1])) {
					$qparts['select'] .= '"'.addslashes($desc['divs'][-1]).'",';
				}
				foreach ($desc['columns'] as $key => $val) {
					if ($val) {
						$qparts['select'] .= 'IFNULL(CAST('.$this->sd.$val.$this->ed.' AS CHAR),"")';
						if ($desc['divs'][$key]) {
							$qparts['select'] .= ',"'.addslashes($desc['divs'][$key]).'"';
						}
						$qparts['select'] .= ',';
					}
				}
				$qparts['select']{strlen($qparts['select']) - 1} = ')';
				$qparts['select'] .= ' AS '.$this->sd.'PMEalias'.$field_num.$this->ed;
				$qparts['orderby'] = $this->sd.'PMEalias'.$field_num.$this->ed;
			} else if ($desc && is_array($desc)) {
				// TODO
			} else if ($desc) {
				$qparts['select'] .= ','.$table.'.'.$this->sd.$desc.$this->ed;
				$qparts['orderby'] = $this->sd.$desc.$this->ed;
			} else if ($key) {
				$qparts['orderby'] = $this->sd.$key.$this->ed;
			}
			$qparts['from'] = $dbp.$table;
			$ar = array(
					'table'       => $table,
					'column'      => $column,
					'description' => $desc);
			$qparts['where'] = $this->substituteVars($this->fdd[$field_num]['values']['filters'], $ar);
			if ($this->fdd[$field_num]['values']['orderby']) {
				$qparts['orderby'] = $this->substituteVars($this->fdd[$field_num]['values']['orderby'], $ar);
			}
		} else { /* simple value extraction */
			$key = &$this->fds[$field_num];
			$this->virtual($field_num) && $key = $this->fqn($field_num);
			$qparts['select']  = 'DISTINCT '.$this->sd.$key.$this->ed.' AS PMEkey';
			$qparts['orderby'] = 'PMEkey';
			$qparts['from']    = $this->dbp.$this->sd.$this->tb.$this->ed;
		}
		$values = array();
		$res    = $this->myquery($this->get_SQL_query($qparts), __LINE__);
		while ($row = $this->sql_fetch($res, 'n')) {
			$values[$row[0]] = $desc ? $row[1] : $row[0];
		}
		return $values;
	} /* }}} */

	function fqn($field, $dont_desc = false, $dont_cols = false) /* {{{ */
	{
		is_numeric($field) || $field = array_search($field, $this->fds);
		// if read SQL expression exists use it
		if ($this->col_has_sql($field) && !$this->col_has_values($field))
			return $this->fdd[$field]['sql'];
		// on copy/change always use simple key retrieving
		if ($this->add_operation()
				|| $this->copy_operation()
				|| $this->change_operation()) {
				$ret = $this->sd.'PMEtable0'.$this->ed.'.'.$this->sd.$this->fds[$field].$this->ed;
		} else {
			if ($this->fdd[$this->fds[$field]]['values']['description'] && ! $dont_desc) {
				$desc = &$this->fdd[$this->fds[$field]]['values']['description'];
				if (is_array($desc) && is_array($desc['columns'])) {
					$ret      = 'CONCAT('; // )
					$num_cols = sizeof($desc['columns']);
					if (isset($desc['divs'][-1])) {
						$ret .= '"'.addslashes($desc['divs'][-1]).'",';
					}
					foreach ($desc['columns'] as $key => $val) {
						if ($val) {
							$ret .= 'IFNULL(CAST('.$this->sd.'PMEjoin'.$field.$this->ed.'.'.$this->sd.$val.$this->ed.' AS CHAR),"")';
							if ($desc['divs'][$key]) {
								$ret .= ',"'.addslashes($desc['divs'][$key]).'"';
							}
							$ret .= ',';
						}
					}
					$ret{strlen($ret) - 1} = ')';
				} else if (is_array($desc)) {
					// TODO
				} else {
					$ret = $this->sd.'PMEjoin'.$field.$this->ed.'.'.$this->sd.$this->fdd[$this->fds[$field]]['values']['description'].$this->ed;
				}
			// TODO: remove me
			} elseif (0 && $this->fdd[$this->fds[$field]]['values']['column'] && ! $dont_cols) {
				$ret = $this->sd.'PMEjoin'.$field.$this->ed.'.'.$this->fdd[$this->fds[$field]]['values']['column'];
			} else {
				$ret = $this->sd.'PMEtable0'.$this->ed.'.'.$this->sd.$this->fds[$field].$this->ed;
			}
			// TODO: not neccessary, remove me!
			if (is_array($this->fdd[$this->fds[$field]]['values2'])) {
			}
		}
		return $ret;
	} /* }}} */

	function get_SQL_main_list_query($qparts) /* {{{ */
	{
 		return $this->get_SQL_query($qparts);
 	} /* }}} */
 


	function get_SQL_query($parts) /* {{{ */
	{
		foreach ($parts as $k => $v) {
			$parts[$k] = trim($parts[$k]);
		}
		switch ($parts['type']) {
			case 'select':
				$ret  = 'SELECT ';
				if ($parts['DISTINCT'])
					$ret .= 'DISTINCT ';
				$ret .= $parts['select'];
				$ret .= ' FROM '.$parts['from'];
				if ($parts['where'] != '')
					$ret .= ' WHERE '.$parts['where'];
				if ($parts['groupby'] != '')
					$ret .= ' GROUP BY '.$parts['groupby'];
				if ($parts['having'] != '')
					$ret .= ' HAVING '.$parts['having'];
				if ($parts['orderby'] != '')
					$ret .= ' ORDER BY '.$parts['orderby'];
				if ($parts['limit'] != '')
					$ret .= ' '.$parts['limit'];
				if ($parts['procedure'] != '')
					$ret .= ' PROCEDURE '.$parts['procedure'];
				break;
			case 'update':
				$ret  = 'UPDATE '.$parts['table'];
				$ret .= ' SET '.$parts['fields'];
				if ($parts['where'] != '')
					$ret .= ' WHERE '.$parts['where'];
				break;
			case 'insert':
				$ret  = 'INSERT INTO '.$parts['table'];
				$ret .= ' VALUES '.$parts['values'];
				break;
			case 'delete':
				$ret  = 'DELETE FROM '.$parts['table'];
				if ($parts['where'] != '')
					$ret .= ' WHERE '.$parts['where'];
				break;
			default:
				die('unknown query type');
				break;
		}
		return $ret;
	} /* }}} */

	function get_SQL_column_list() /* {{{ */
	{
		$fields = array();
		for ($k = 0; $k < $this->num_fds; $k++) {
			if (! $this->displayed[$k] && $k != $this->key_num) {
				continue;
			}
			$fields[] = $this->fqn($k).' AS '.$this->sd.'qf'.$k.$this->ed; // no delimiters here, or maybe some yes
			if ($this->col_has_values($k)) {
				if($this->col_has_sql($k)) $fields[] = $this->fdd[$k]['sql'].' AS '.$this->sd.'qf'.$k.'_idx'.$this->ed;
				else $fields[] = $this->fqn($k, true, true).' AS '.$this->sd.'qf'.$k.'_idx'.$this->ed;
			}
			if ($this->col_has_datemask($k)) {
				$fields[] = 'UNIX_TIMESTAMP('.$this->fqn($k).') AS '.$this->sd.'qf'.$k.'_timestamp'.$this->ed;
			}
		}
		return join(',', $fields);
	} /* }}} */

	function get_SQL_join_clause() /* {{{ */
	{
		$main_table  = $this->sd.'PMEtable0'.$this->ed;
		$join_clause = $this->sd.$this->tb.$this->ed." AS $main_table";
		for ($k = 0, $numfds = sizeof($this->fds); $k < $numfds; $k++) {
			$main_column = $this->fds[$k];
			if($this->fdd[$main_column]['values']['db']) {
				$dbp = $this->sd.$this->fdd[$main_column]['values']['db'].$this->ed.'.';
			} else {
				//$dbp = $this->dbp;
			}
			$table       = $this->sd.$this->fdd[$main_column]['values']['table'].$this->ed;
			$join_column = $this->sd.$this->fdd[$main_column]['values']['column'].$this->ed;
			$join_desc   = $this->sd.$this->fdd[$main_column]['values']['description'].$this->ed;
			if ($join_desc != $this->sd.$this->ed && $join_column != $this->sd.$this->ed) {
				$join_table = $this->sd.'PMEjoin'.$k.$this->ed;
				$ar = array(
						'main_table'       => $main_table,
						'main_column'      => $this->sd.$main_column.$this->ed,
						'join_table'       => $join_table,
						'join_column'      => $join_column,
						'join_description' => $join_desc);
				$join_clause .= " LEFT OUTER JOIN $dbp".$table." AS $join_table ON (";
				$join_clause .= isset($this->fdd[$main_column]['values']['join'])
					? $this->substituteVars($this->fdd[$main_column]['values']['join'], $ar)
					: "$join_table.$join_column = $main_table.".$this->sd.$main_column.$this->ed;
				$join_clause .= ')';
			}
		}
		return $join_clause;
	} /* }}} */

	function get_SQL_where_from_query_opts($qp = null, $text = 0) /* {{{ */
	{
		if ($qp == null) {
			$qp = $this->query_opts;
		}
		$where = array();
		foreach ($qp as $field => $ov) {
			if (is_numeric($field)) {
				$tmp_where = array();
				foreach ($ov as $field2 => $ov2) {
					$tmp_where[] = sprintf('%s %s %s', $field2, $ov2['oper'], $ov2['value']);
				}
				$where[] = '('.join(' OR ', $tmp_where).')';
			} else {
				if (is_array($ov['value'])) {
					$tmp_ov_val = '';
					foreach ($ov['value'] as $ov_val) {
						strlen($tmp_ov_val) > 0 && $tmp_ov_val .= ' OR ';
						$tmp_ov_val .= sprintf('FIND_IN_SET("%s",%s)', $ov_val, $field);
					}
					$where[] = "($tmp_ov_val)";
				} else {
					$where[] = sprintf('%s %s %s', $field, $ov['oper'], $ov['value']);
				}
			}
		}
		// Add any coder specified filters
		if (! $text && $this->filters) {
			$where[] = '('.$this->filters.')';
		}
		if (count($where) > 0) {
			if ($text) {
				return str_replace('%', '*', join(' AND ',$where));
			} else {
				return join(' AND ',$where);
			}
		}
		return ''; /* empty string */
	} /* }}} */

	function gather_query_opts() /* {{{ */
	{
		$this->query_opts = array();
		$this->prev_qfn   = $this->qfn;
		$this->qfn        = '';
		if ($this->clear_operation()) {
			return;
		}
		// gathers query options into an array, $this->query_opts
		$qo = array();
		for ($k = 0; $k < $this->num_fds; $k++) {
			$l    = 'qf'.$k;
			$lc   = 'qf'.$k.'_comp';
			$li   = 'qf'.$k.'_id';
			$m    = $this->get_sys_cgi_var($l);
			$mc   = $this->get_sys_cgi_var($lc);
			$mi   = $this->get_sys_cgi_var($li);
			if (! isset($m) && ! isset($mi)) {
				continue;
			}
			if (is_array($m) || is_array($mi)) {
				if (is_array($mi)) {
					$m = $mi;
					$l = $li;
				}
				if (in_array('*', $m)) {
					continue;
				}
				if ($this->col_has_values($k) && $this->col_has_multiple($k)) {
					foreach (array_keys($m) as $key) {
						$m[$key] = addslashes($m[$key]);
					}
					$qo[$this->fqn($k)] = array('value' => $m);
				} else {
					$qf_op = '';
					foreach (array_keys($m) as $key) {
						if ($qf_op == '') {
							$qf_op   = 'IN';
							$qf_val  = '"'.addslashes($m[$key]).'"';
							$afilter = ' IN ("'.addslashes($m[$key]).'"'; // )
						} else {
							$afilter = $afilter.',"'.addslashes($m[$key]).'"';
							$qf_val .= ',"'.addslashes($m[$key]).'"';
						}
						$this->qfn .= '&'.$this->cgi['prefix']['sys'].$l.'['.rawurlencode($key).']='.rawurlencode($m[$key]);
					}
					$afilter = $afilter.')';
					// XXX: $dont_desc and $dont_cols hack
					$dont_desc = isset($this->fdd[$k]['values']['description']);
					$dont_cols = isset($this->fdd[$k]['values']['column']);
					$qo[$this->fqn($k, $dont_desc, $dont_cols)] =
						array('oper'  => $qf_op, 'value' => "($qf_val)"); // )
				}
			} else if (isset($mi)) {
				if ($mi == '*') {
					continue;
				}
				if ($this->fdd[$k]['select'] != 'M' && $this->fdd[$k]['select'] != 'D' && $mi == '') {
					continue;
				}
				$afilter = addslashes($mi);
				$qo[$this->fqn($k, true, true)] = array('oper'  => '=', 'value' => "'$afilter'");
				$this->qfn .= '&'.$this->cgi['prefix']['sys'].$li.'='.rawurlencode($mi);
			} else if (isset($m)) {
				if ($m == '*') {
					continue;
				}
				if ($this->fdd[$k]['select'] != 'M' && $this->fdd[$k]['select'] != 'D' && $m == '') {
					continue;
				}
				$afilter = addslashes($m);
				if ($this->fdd[$k]['select'] == 'N') {
					$mc = in_array($mc, $this->comp_ops) ? $mc : '=';
					$qo[$this->fqn($k)] = array('oper' => $mc, 'value' => "'$afilter'");
					$this->qfn .= '&'.$this->cgi['prefix']['sys'].$l .'='.rawurlencode($m);
					$this->qfn .= '&'.$this->cgi['prefix']['sys'].$lc.'='.rawurlencode($mc);
				} else {
					$afilter = '%'.str_replace('*', '%', $afilter).'%';
					$ids  = array();
					$ar   = array();
					$ar[$this->fqn($k)] = array('oper' => 'LIKE', 'value' => "'$afilter'");
					if (is_array($this->fdd[$k]['values2'])) {
						foreach ($this->fdd[$k]['values2'] as $key => $val) {
							if (strlen($m) > 0 && stristr($val, $m)) {
								$ids[] = '"'.addslashes($key).'"';
							}
						}
						if (count($ids) > 0) {
							$ar[$this->fqn($k, true, true)]
								= array('oper'  => 'IN', 'value' => '('.join(',', $ids).')');
						}
					}
					$qo[] = $ar;
					$this->qfn .= '&'.$this->cgi['prefix']['sys'].$l.'='.rawurlencode($m);
				}
			}
		}
		$this->query_opts = $qo;
	} /* }}} */

	/*
	 * Create JavaScripts
	 */

	function form_begin() /* {{{ */
	{
		$page_name = htmlspecialchars($this->page_name);
		if ($this->add_operation() || $this->change_operation() || $this->copy_operation()
				|| $this->view_operation() || $this->delete_operation()) {
			$field_to_tab = array();
			for ($tab = $k = $this->cur_tab = 0; $k < $this->num_fds; $k++) {
				if (isset($this->fdd[$k]['tab'])) {
					if ($tab == 0 && $k > 0) {
						$this->tabs[0] = 'PMEtab0';
						$this->cur_tab = 1;
						$tab++;
					}
					if (is_array($this->fdd[$k]['tab'])) {
						$this->tabs[$tab] = @$this->fdd[$k]['tab']['name'];
						$this->fdd[$k]['tab']['default'] && $this->cur_tab = $tab;
					} else {
						$this->tabs[$tab] = @$this->fdd[$k]['tab'];
					}
					$tab++;
				}
				$field_to_tab[$k] = max(0, $tab - 1);
			}
			if (preg_match('/^'.$this->dhtml['prefix'].'tab(\d+)$/', $this->get_sys_cgi_var('cur_tab'), $parts)) {
				$this->cur_tab = $parts[1];
			}
			if ($this->tabs_enabled()) {
				// initial TAB styles
				echo '<style type="text/css" media="screen">',"\n";
				for ($i = 0; $i < count($this->tabs); $i++) {
					echo '	#'.$this->dhtml['prefix'].'tab',$i,' { display: ';
					echo (($i == $this->cur_tab || $this->tabs[$i] == 'PMEtab0' ) ? 'block' : 'none') ,'; }',"\n";
				}
				echo '</style>',"\n";
				// TAB javascripts
				echo '<script type="text/javascript"><!--',"\n\n";
				$css_class_name1 = $this->getCSSclass('tab', $position);
				$css_class_name2 = $this->getCSSclass('tab-selected', $position);
				echo 'var '.$this->js['prefix'].'cur_tab  = "'.$this->dhtml['prefix'].'tab',$this->cur_tab,'";

function '.$this->js['prefix'].'show_tab(tab_name)
{';
				if ($this->nav_up()) {
					echo '
	document.getElementById('.$this->js['prefix'].'cur_tab+"_up_label").className = "',$css_class_name1,'";
	document.getElementById('.$this->js['prefix'].'cur_tab+"_up_link").className = "',$css_class_name1,'";
	document.getElementById(tab_name+"_up_label").className = "',$css_class_name2,'";
	document.getElementById(tab_name+"_up_link").className = "',$css_class_name2,'";';
				}
				if ($this->nav_down()) {
					echo '
	document.getElementById('.$this->js['prefix'].'cur_tab+"_down_label").className = "',$css_class_name1,'";
	document.getElementById('.$this->js['prefix'].'cur_tab+"_down_link").className = "',$css_class_name1,'";
	document.getElementById(tab_name+"_down_label").className = "',$css_class_name2,'";
	document.getElementById(tab_name+"_down_link").className = "',$css_class_name2,'";';
				}
				echo '
	document.getElementById('.$this->js['prefix'].'cur_tab).style.display = "none";
	document.getElementById(tab_name).style.display = "block";
	'.$this->js['prefix'].'cur_tab = tab_name;
	document.'.$this->cgi['prefix']['sys'].'form.'.$this->cgi['prefix']['sys'].'cur_tab.value = tab_name;
}',"\n\n";
				echo '// --></script>', "\n";
			}
		}

		if ($this->add_operation() || $this->change_operation() || $this->copy_operation()) {
			$first_required = true;
			for ($k = 0; $k < $this->num_fds; $k++) {
				if ($this->displayed[$k] && ! $this->readonly($k) && ! $this->hidden($k)
						&& ($this->fdd[$k]['js']['required'] || isset($this->fdd[$k]['js']['regexp']))) {
					if ($first_required) {
				 		$first_required = false;
						echo '<script type="text/javascript"><!--',"\n";
						echo '
function '.$this->js['prefix'].'trim(str)
{
	while (str.substring(0, 1) == " "
			|| str.substring(0, 1) == "\\n"
			|| str.substring(0, 1) == "\\r")
	{
		str = str.substring(1, str.length);
	}
	while (str.substring(str.length - 1, str.length) == " "
			|| str.substring(str.length - 1, str.length) == "\\n"
			|| str.substring(str.length - 1, str.length) == "\\r")
	{
		str = str.substring(0, str.length - 1);
	}
	return str;
}

function '.$this->js['prefix'].'form_control(theForm)
{',"\n";
					}
					if ($this->col_has_values($k)) {
						$condition = 'theForm.'.$this->cgi['prefix']['data'].$this->fds[$k].'.selectedIndex == -1';
						$multiple  = $this->col_has_multiple_select($k);
					} else {
						$condition = '';
						$multiple  = false;
						if ($this->fdd[$k]['js']['required']) {
							$condition = $this->js['prefix'].'trim(theForm.'.$this->cgi['prefix']['data'].$this->fds[$k].'.value) == ""';
						}
						if (isset($this->fdd[$k]['js']['regexp'])) {
							$condition .= (strlen($condition) > 0 ? ' || ' : '');
							$condition .= sprintf('!(%s.test('.$this->js['prefix'].'trim(theForm.%s.value)))',
									$this->fdd[$k]['js']['regexp'], $this->cgi['prefix']['data'].$this->fds[$k]);
						}
					}

					/* Multiple selects have their name like ''name[]''.
					   It is not possible to work with them directly, because
					   theForm.name[].something will result into JavaScript
					   syntax error. Following search algorithm is provided
					   as a workaround for this.
					 */
					if ($multiple) {
						echo '
	multiple_select = null;
	for (i = 0; i < theForm.length; i++) {
		if (theForm.elements[i].name == "',$this->cgi['prefix']['data'].$this->fds[$k],'[]") {
			multiple_select = theForm.elements[i];
			break;
		}
	}
	if (multiple_select != null && multiple_select.selectedIndex == -1) {';
					} else {
						echo '
	if (',$condition,') {';
					}
					echo '
		alert("';
					if (isset($this->fdd[$k]['js']['hint'])) {
						echo htmlspecialchars($this->fdd[$k]['js']['hint']);
					} else {
						echo $this->labels['Please enter'],' ',$this->fdd[$k]['name'],'.';
					}
					echo '");';
					if ($this->tabs_enabled() && $field_to_tab[$k] >= $this->cur_tab) {
						echo '
		'.$this->js['prefix'].'show_tab("'.$this->dhtml['prefix'].'tab',$field_to_tab[$k],'");';
					}
					echo '
		theForm.',$this->cgi['prefix']['data'].$this->fds[$k],'.focus();
		return false;
	}',"\n";
				}
			}
			if (! $first_required) {
				echo '
	return true;
}',"\n\n";
				echo '// --></script>', "\n";
			}
		}

		if ($this->filter_operation()) {
				echo '<script type="text/javascript"><!--',"\n";
				echo '
function '.$this->js['prefix'].'filter_handler(theForm, theEvent)
{
	var pressed_key = null;
	if (theEvent.which) {
		pressed_key = theEvent.which;
	} else {
		pressed_key = theEvent.keyCode;
	}
	if (pressed_key == 13) { // enter pressed
		theForm.submit();
		return false;
	}
	return true;
}',"\n\n";
				echo '// --></script>', "\n";
		}

		if ($this->display['form']) {
			echo '<form class="',$this->getCSSclass('form'),'" method="post"';
			echo ' action="',$page_name,'" name="'.$this->cgi['prefix']['sys'].'form">',"\n";
		}
		return true;
	} /* }}} */

	function form_end() /* {{{ */
	{
		if ($this->display['form']) {
			echo '</form>',"\n";
		}
	} /* }}} */

	function display_tab_labels($position) /* {{{ */
	{
		if (! is_array($this->tabs)) {
			return false;
		}
		echo '<table summary="labels" class="',$this->getCSSclass('tab', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('tab', $position),'">',"\n";
		for ($i = ($this->tabs[0] == 'PMEtab0' ? 1 : 0); $i < count($this->tabs); $i++) {
			$css_class_name = $this->getCSSclass($i != $this->cur_tab ? 'tab' : 'tab-selected', $position);
			echo '<td class="',$css_class_name,'" id="'.$this->dhtml['prefix'].'tab',$i,'_',$position,'_label">';
			echo '<a class="',$css_class_name,'" id="'.$this->dhtml['prefix'].'tab',$i,'_',$position,'_link';
			echo '" href="javascript:'.$this->js['prefix'].'show_tab(\''.$this->dhtml['prefix'].'tab',$i,'\')">';
			echo $this->tabs[$i],'</a></td>',"\n";
		}
		echo '<td class="',$this->getCSSclass('tab-end', $position),'">&nbsp;</td>',"\n";
		echo '</tr>',"\n";
		echo '</table>',"\n";
	} /* }}} */

	/*
	 * Display functions
	 */

	function display_add_record() /* {{{ */
	{
		for ($tab = 0, $k = 0; $k < $this->num_fds; $k++) {
			if (isset($this->fdd[$k]['tab']) && $this->tabs_enabled() && $k > 0) {
				$tab++;
				echo '</table>',"\n";
				echo '</div>',"\n";
				echo '<div id="'.$this->dhtml['prefix'].'tab',$tab,'">',"\n";
				echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";
			}
			if (! $this->displayed[$k]) {
				continue;
			}
			if ($this->hidden($k)) {
				echo $this->htmlHiddenData($this->fds[$k], $this->fdd[$k]['default']);
				continue;
			}
			$css_postfix    = @$this->fdd[$k]['css']['postfix'];
			$css_class_name = $this->getCSSclass('input', null, 'next', $css_postfix);
			$escape			= isset($this->fdd[$k]['escape']) ? $this->fdd[$k]['escape'] : true;
			echo '<tr class="',$this->getCSSclass('row', null, true, $css_postfix),'">',"\n";
			echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'">';
			echo $this->fdd[$k]['name'],'</td>',"\n";
			echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
			echo $this->getColAttributes($k),">\n";
			if ($this->col_has_values($k)) {
				$vals       = $this->set_values($k);
				$selected   = @$this->fdd[$k]['default'];
				$multiple   = $this->col_has_multiple($k);
				$readonly   = $this->readonly($k);
				$strip_tags = true;
				//$escape     = true;
				if ($this->col_has_checkboxes($k) || $this->col_has_radio_buttons($k)) {
					echo $this->htmlRadioCheck($this->cgi['prefix']['data'].$this->fds[$k],
							$css_class_name, $vals, $selected, $multiple, $readonly,
							$strip_tags, $escape);
				} else {
					echo $this->htmlSelect($this->cgi['prefix']['data'].$this->fds[$k],
							$css_class_name, $vals, $selected, $multiple, $readonly,
							$strip_tags, $escape);
				}
			} elseif (isset ($this->fdd[$k]['textarea'])) {
				echo '<textarea class="',$css_class_name,'" name="',$this->cgi['prefix']['data'].$this->fds[$k],'"';
				echo ($this->readonly($k) ? ' disabled' : '');
				if (intval($this->fdd[$k]['textarea']['rows']) > 0) {
					echo ' rows="',$this->fdd[$k]['textarea']['rows'],'"';
				}
				if (intval($this->fdd[$k]['textarea']['cols']) > 0) {
					echo ' cols="',$this->fdd[$k]['textarea']['cols'],'"';
				}
				if (isset($this->fdd[$k]['textarea']['wrap'])) {
					echo ' wrap="',$this->fdd[$k]['textarea']['wrap'],'"';
				} else {
					echo ' wrap="virtual"';
				}
				echo '>';
				if($escape) echo htmlspecialchars($this->fdd[$k]['default']);
				else echo $this->fdd[$k]['default'];
				echo '</textarea>',"\n";
			} elseif ($this->col_has_php($k)) {
				echo include($this->fdd[$k]['php']);
			} else {
				// Simple edit box required
				$len_props = '';
				$maxlen = intval($this->fdd[$k]['maxlen']);
				$size   = isset($this->fdd[$k]['size']) ? $this->fdd[$k]['size'] : min($maxlen, 60); 
				if ($size > 0) {
					$len_props .= ' size="'.$size.'"';
				}
				if ($maxlen > 0) {
					$len_props .= ' maxlength="'.$maxlen.'"';
				}
				echo '<input class="',$css_class_name,'" ';
				echo ($this->password($k) ? 'type="password"' : 'type="text"');
				echo ($this->readonly($k) ? ' disabled' : '');
				echo ' name="',$this->cgi['prefix']['data'].$this->fds[$k],'"';
				echo $len_props,' value="';
				if($escape) echo htmlspecialchars($this->fdd[$k]['default']);
			    else echo $this->fdd[$k]['default'];
				echo '" />';
			}
			echo '</td>',"\n";
			if ($this->guidance) {
				$css_class_name = $this->getCSSclass('help', null, true, $css_postfix);
				$cell_value     = $this->fdd[$k]['help'] ? $this->fdd[$k]['help'] : '&nbsp;';
				echo '<td class="',$css_class_name,'">',$cell_value,'</td>',"\n";
			}
			echo '</tr>',"\n";
		}
	} /* }}} */

	function display_copy_change_delete_record() /* {{{ */
	{
		/*
		 * For delete or change: SQL SELECT to retrieve the selected record
		 */

		$qparts['type']   = 'select';
		$qparts['select'] = $this->get_SQL_column_list();
		$qparts['from']   = $this->get_SQL_join_clause();
		$qparts['where']  = '('.$this->fqn($this->key).'='
			.$this->key_delim.$this->rec.$this->key_delim.')';

		$res = $this->myquery($this->get_SQL_query($qparts),__LINE__);
		if (! ($row = $this->sql_fetch($res))) {
			return false;
		}
		for ($tab = 0, $k = 0; $k < $this->num_fds; $k++) {
			if (isset($this->fdd[$k]['tab']) && $this->tabs_enabled() && $k > 0) {
				$tab++;
				echo '</table>',"\n";
				echo '</div>',"\n";
				echo '<div id="'.$this->dhtml['prefix'].'tab',$tab,'">',"\n";
				echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";
			}
			if (! $this->displayed[$k]) {
				continue;
			}
			if ($this->copy_operation() || $this->change_operation()) {
				if ($this->hidden($k)) {
					if ($k != $this->key_num) {
						echo $this->htmlHiddenData($this->fds[$k], $row["qf$k"]);
					}
					continue;
				}
				$css_postfix = @$this->fdd[$k]['css']['postfix'];
				echo '<tr class="',$this->getCSSclass('row', null, 'next', $css_postfix),'">',"\n";
				echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'">';
				echo $this->fdd[$k]['name'],'</td>',"\n";
				/* There are two possibilities of readonly fields handling:
				   1. Display plain text for readonly timestamps, dates and URLs.
				   2. Display disabled input field
				   In all cases particular readonly field will NOT be saved. */
				if ($this->readonly($k) && ($this->col_has_datemask($k) || $this->col_has_URL($k))) {
					echo $this->display_delete_field($row, $k);
				} elseif ($this->password($k)) {
					echo $this->display_password_field($row, $k);
				} else {
					echo $this->display_change_field($row, $k);
				}
				if ($this->guidance) {
					$css_class_name = $this->getCSSclass('help', null, true, $css_postfix);
					$cell_value     = $this->fdd[$k]['help'] ? $this->fdd[$k]['help'] : '&nbsp;';
					echo '<td class="',$css_class_name,'">',$cell_value,'</td>',"\n";
				}
				echo '</tr>',"\n";
			} elseif ($this->delete_operation() || $this->view_operation()) {
				$css_postfix = @$this->fdd[$k]['css']['postfix'];
				echo '<tr class="',$this->getCSSclass('row', null, 'next', $css_postfix),'">',"\n";
				echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'">';
				echo $this->fdd[$k]['name'],'</td>',"\n";
				if ($this->password($k)) {
					echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
					echo $this->getColAttributes($k),'>',$this->labels['hidden'],'</td>',"\n";
				} else {
					$this->display_delete_field($row, $k);
				}
				if ($this->guidance) {
					$css_class_name = $this->getCSSclass('help', null, true, $css_postfix);
					$cell_value     = $this->fdd[$k]['help'] ? $this->fdd[$k]['help'] : '&nbsp;';
					echo '<td class="',$css_class_name,'">',$cell_value,'</td>',"\n";
				}
				echo '</tr>',"\n";
			}
		}
	} /* }}} */

	function display_change_field($row, $k) /* {{{ */ 
	{
		$css_postfix    = @$this->fdd[$k]['css']['postfix'];
		$css_class_name = $this->getCSSclass('input', null, true, $css_postfix);
		$escape         = isset($this->fdd[$k]['escape']) ? $this->fdd[$k]['escape'] : true;
		echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
		echo $this->getColAttributes($k),">\n";
		if ($this->col_has_values($k)) {
			$vals       = $this->set_values($k);
			$multiple   = $this->col_has_multiple($k);
			$readonly   = $this->readonly($k);
			$strip_tags = true;
			//$escape     = true;
			if ($this->col_has_checkboxes($k) || $this->col_has_radio_buttons($k)) {
				echo $this->htmlRadioCheck($this->cgi['prefix']['data'].$this->fds[$k],
						$css_class_name, $vals, $row["qf$k"], $multiple, $readonly,
						$strip_tags, $escape);
			} else {
				echo $this->htmlSelect($this->cgi['prefix']['data'].$this->fds[$k],
						$css_class_name, $vals, $row["qf$k"], $multiple, $readonly,
						$strip_tags, $escape);
			}
		} elseif (isset($this->fdd[$k]['textarea'])) {
			echo '<textarea class="',$css_class_name,'" name="',$this->cgi['prefix']['data'].$this->fds[$k],'"';
			echo ($this->readonly($k) ? ' disabled' : '');
			if (intval($this->fdd[$k]['textarea']['rows']) > 0) {
				echo ' rows="',$this->fdd[$k]['textarea']['rows'],'"';
			}
			if (intval($this->fdd[$k]['textarea']['cols']) > 0) {
				echo ' cols="',$this->fdd[$k]['textarea']['cols'],'"';
			}
			if (isset($this->fdd[$k]['textarea']['wrap'])) {
				echo ' wrap="',$this->fdd[$k]['textarea']['wrap'],'"';
			} else {
				echo ' wrap="virtual"';
			}
			echo '>';
			if($escape) echo htmlspecialchars($row["qf$k"]);
			else echo $row["qf$k"];
			echo '</textarea>',"\n";
		} elseif ($this->col_has_php($k)) {
			echo include($this->fdd[$k]['php']);
		} else {
			$len_props = '';
			$maxlen = intval($this->fdd[$k]['maxlen']);
			$size   = isset($this->fdd[$k]['size']) ? $this->fdd[$k]['size'] : min($maxlen, 60); 
			if ($size > 0) {
				$len_props .= ' size="'.$size.'"';
			}
			if ($maxlen > 0) {
				$len_props .= ' maxlength="'.$maxlen.'"';
			}
			echo '<input class="',$css_class_name,'" type="text"';
			echo ($this->readonly($k) ? ' disabled' : '');
			echo ' name="',$this->cgi['prefix']['data'].$this->fds[$k],'" value="';
			if($escape) echo htmlspecialchars($row["qf$k"]);
			else echo $row["qf$k"];
			echo '"',$len_props,' />',"\n";
		}
		echo '</td>',"\n";
	} /* }}} */

	function display_password_field($row, $k) /* {{{ */
	{
		$css_postfix = @$this->fdd[$k]['css']['postfix'];
		echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
		echo $this->getColAttributes($k),">\n";
		$len_props = '';
		$maxlen = intval($this->fdd[$k]['maxlen']);
		$size   = isset($this->fdd[$k]['size']) ? $this->fdd[$k]['size'] : min($maxlen, 60); 
		if ($size > 0) {
			$len_props .= ' size="'.$size.'"';
		}
		if ($maxlen > 0) {
			$len_props .= ' maxlength="'.$maxlen.'"';
		}
		echo '<input class="',$this->getCSSclass('value', null, true, $css_postfix),'" type="password"';
		echo ($this->readonly($k) ? ' disabled' : '');
		echo ' name="',$this->cgi['prefix']['data'].$this->fds[$k],'" value="';
		echo htmlspecialchars($row["qf$k"]),'"',$len_props,' />',"\n";
		echo '</td>',"\n";
	} /* }}} */

	function display_delete_field($row, $k) /* {{{ */
	{
		$css_postfix    = @$this->fdd[$k]['css']['postfix'];
		$css_class_name = $this->getCSSclass('value', null, true, $css_postfix);
		echo '<td class="',$css_class_name,'"',$this->getColAttributes($k),">\n";
		echo $this->cellDisplay($k, $row, $css_class_name);
		echo '</td>',"\n";
	} /* }}} */

	/**
	 * Returns CSS class name
	 */
	function getCSSclass($name, $position  = null, $divider = null, $postfix = null) /* {{{ */
	{
		static $div_idx = -1;
		$elements = array($this->css['prefix'], $name);
		if ($this->page_type && $this->css['page_type']) {
			if ($this->page_type != 'L' && $this->page_type != 'F') {
				$elements[] = $this->page_types[$this->page_type];
			}
		}
		if ($position && $this->css['position']) {
			$elements[] = $position;
		}
		if ($divider && $this->css['divider']) {
			if ($divider === 'next') {
				$div_idx++;
				if ($this->css['divider'] > 0 && $div_idx >= $this->css['divider']) {
					$div_idx = 0;
				}
			}
			$elements[] = $div_idx;
		}
		if ($postfix) {
			$elements[] = $postfix;
		}
		return join($this->css['separator'], $elements);
	} /* }}} */

	/**
	 * Returns field cell HTML attributes
	 */
	function getColAttributes($k) /* {{{ */
	{
		$colattrs = '';
		if (isset($this->fdd[$k]['colattrs'])) {
			$colattrs .= ' ';
			$colattrs .= trim($this->fdd[$k]['colattrs']);
		}
		if (isset($this->fdd[$k]['nowrap'])) {
			$colattrs .= ' nowrap';
		}
		return $colattrs;
	} /* }}} */

	/**
	 * Substitutes variables in string
	 * (this is very simple but secure eval() replacement)
	 */
	function substituteVars($str, $subst_ar) /* {{{ */
	{
		$array = preg_split('/(\\$\w+)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		$count = count($array);
		for ($i = 1; $i < $count; $i += 2) {
			$key = substr($array[$i], 1);
			if (isset($subst_ar[$key])) {
				$array[$i] = $subst_ar[$key];
			}
		}
		return join('', $array);
	} /* }}} */

	/**
	 * Print URL
	 */
	function urlDisplay($k, $link_val, $disp_val, $css, $key) /* {{{ */
	{
		$escape = isset($this->fdd[$k]['escape']) ? $this->fdd[$k]['escape'] : true;
		$ret  = '';
		$name = $this->fds[$k];
		$page = $this->page_name;
		$url  = $this->cgi['prefix']['sys'].'rec'.'='.$key.'&'.$this->cgi['prefix']['sys'].'fm'
			.'='.$this->fm.'&'.$this->cgi['prefix']['sys'].'fl'.'='.$this->fl;
		$url .= '&'.$this->cgi['prefix']['sys'].'qfn'.'='.rawurlencode($this->qfn).$this->qfn;
		$url .= '&'.$this->get_sfn_cgi_vars().$this->cgi['persist'];
		$ar   = array(
				'key'   => $key,
				'name'  => $name,
				'link'  => $link_val,
				'value' => $disp_val,
				'css'   => $css,
				'page'  => $page,
				'url'   => $url
				);
		$urllink = isset($this->fdd[$k]['URL'])
			?  $this->substituteVars($this->fdd[$k]['URL'], $ar)
			: $link_val;
		$urldisp = isset($this->fdd[$k]['URLdisp'])
			?  $this->substituteVars($this->fdd[$k]['URLdisp'], $ar)
			: $disp_val;
		$target = isset($this->fdd[$k]['URLtarget'])
			? 'target="'.htmlspecialchars($this->fdd[$k]['URLtarget']).'" '
			: '';
		$prefix_found  = false;
		$postfix_found = false;
		$prefix_ar     = @$this->fdd[$k]['URLprefix'];
		$postfix_ar    = @$this->fdd[$k]['URLpostfix'];
		is_array($prefix_ar)  || $prefix_ar  = array($prefix_ar);
		is_array($postfix_ar) || $postfix_ar = array($postfix_ar);
		foreach ($prefix_ar as $prefix) {
			if (! strncmp($prefix, $urllink, strlen($prefix))) {
				$prefix_found = true;
				break;
			}
		}
		foreach ($postfix_ar as $postfix) {
			if (! strncmp($postfix, $urllink, strlen($postfix))) {
				$postfix_found = true;
				break;
			}
		}
		$prefix_found  || $urllink = array_shift($prefix_ar).$urllink;
		$postfix_found || $urllink = $urllink.array_shift($postfix_ar);
		if (strlen($urllink) <= 0 || strlen($urldisp) <= 0) {
			$ret = '&nbsp;';
		} else {
			if ($escape) {
				$urldisp = htmlspecialchars($urldisp);
			}
			$urllink = htmlspecialchars($urllink);
			$ret = '<a '.$target.'class="'.$css.'" href="'.$urllink.'">'.$urldisp.'</a>';
		}
		return $ret;
	} /* }}} */

	function cellDisplay($k, $row, $css) /* {{{ */
	{
		$escape  = isset($this->fdd[$k]['escape']) ? $this->fdd[$k]['escape'] : true;
		$key_rec = $row['qf'.$this->key_num];
		if (@$this->fdd[$k]['datemask']) {
			$value = intval($row["qf$k".'_timestamp']);
			$value = $value ? @date($this->fdd[$k]['datemask'], $value) : '';
		} else if (@$this->fdd[$k]['strftimemask']) {
			$value = intval($row["qf$k".'_timestamp']);
			$value = $value ? @strftime($this->fdd[$k]['strftimemask'], $value) : '';
		} else if ($this->is_values2($k, $row["qf$k"])) {
			$value = $row['qf'.$k.'_idx'];
			if ($this->fdd[$k]['select'] == 'M') {
				$value_ar  = explode(',', $value);
				$value_ar2 = array();
				foreach ($value_ar as $value_key) {
					if (isset($this->fdd[$k]['values2'][$value_key])) {
						$value_ar2[$value_key] = $this->fdd[$k]['values2'][$value_key];
						$escape = false;
					}
				}
				$value = join(', ', $value_ar2);
			} else {
				if (isset($this->fdd[$k]['values2'][$value])) {
					$value  = $this->fdd[$k]['values2'][$value];
					$escape = false;
				}
			}
		} elseif (isset($this->fdd[$k]['values2'][$row["qf$k"]])) {
			$value = $this->fdd[$k]['values2'][$row["qf$k"]];
		} else {
			$value = $row["qf$k"];
		}
		$original_value = $value;
		if (@$this->fdd[$k]['strip_tags']) {
			$value = strip_tags($value);
		}
		if ($num_ar = @$this->fdd[$k]['number_format']) {
			if (! is_array($num_ar)) {
				$num_ar = array($num_ar);
			}
			if (count($num_ar) == 1) {
				list($nbDec) = $num_ar;
				$value = number_format($value, $nbDec);
			} else if (count($num_ar) == 3) {
				list($nbDec, $decPoint, $thSep) = $num_ar;
				$value = number_format($value, $nbDec, $decPoint, $thSep);
			}
		}
		if (intval($this->fdd[$k]['trimlen']) > 0 && strlen($value) > $this->fdd[$k]['trimlen']) {
			$value = ereg_replace("[\r\n\t ]+",' ',$value);
			$value = substr($value, 0, $this->fdd[$k]['trimlen'] - 3).'...';
		}
		if (@$this->fdd[$k]['mask']) {
			$value = sprintf($this->fdd[$k]['mask'], $value);
		}
		if ($this->col_has_php($k)) {
			return include($this->fdd[$k]['php']);
		}
		if ($this->col_has_URL($k)) {
			return $this->urlDisplay($k, $original_value, $value, $css, $key_rec);
		}
		if (strlen($value) <= 0) {
			return '&nbsp;';
		}
		if ($escape) {
			$value = htmlspecialchars($value);
		}
		return nl2br($value);
	} /* }}} */

	/**
	 * Creates HTML submit input element
	 *
	 * @param	name			element name
	 * @param	label			key in the language hash used as label
	 * @param	css_class_name	CSS class name
	 * @param	js_validation	if add JavaScript validation subroutine to button
	 * @param	disabled		if mark the button as disabled
	 * @param	js		any extra text in tags
	 */
	function htmlSubmit($name, $label, $css_class_name, $js_validation = true, $disabled = false, $js = NULL) /* {{{ */
	{
		// Note that <input disabled> isn't valid HTML, but most browsers support it
		if($disabled == -1) return;
		$markdisabled = $disabled ? ' disabled' : '';
		$ret = '<input'.$markdisabled.' type="submit" class="'.$css_class_name
			.'" name="'.$this->cgi['prefix']['sys'].ltrim($markdisabled).$name
			.'" value="'.(isset($this->labels[$label]) ? $this->labels[$label] : $label);
		if ($js_validation) {
			$ret .= '" onclick="return '.$this->js['prefix'].'form_control(this.form);';
		}
		$ret .='"';
		if(isset($js)) $ret .= ' '.$js;
		$ret .= ' />';
		return $ret;
	} /* }}} */

	/**
	 * Creates HTML hidden input element
	 *
	 * @param	name	element name
	 * @param	value	value
	 */

	function htmlHiddenSys($name, $value) /* {{{ */
	{
		return $this->htmlHidden($this->cgi['prefix']['sys'].$name, $value);
	} /* }}} */

	function htmlHiddenData($name, $value) /* {{{ */
	{
		return $this->htmlHidden($this->cgi['prefix']['data'].$name, $value);
	} /* }}} */

	function htmlHidden($name, $value) /* {{{ */
	{
		return '<input type="hidden" name="'.htmlspecialchars($name)
			.'" value="'.htmlspecialchars($value).'" />'."\n";
	} /* }}} */

	/**
	 * Creates HTML select element (tag)
	 *
	 * @param	name		element name
	 * @param	css			CSS class name
	 * @param	kv_array	key => value array
	 * @param	selected	selected key (it can be single string, array of
	 *						keys or multiple values separated by comma)
	 * @param	multiple	bool for multiple selection
	 * @param	readonly	bool for readonly/disabled selection
	 * @param	strip_tags	bool for stripping tags from values
	 * @param	escape		bool for HTML escaping values
	 * @param	js		string to be in the <select >, ususally onchange='..';
	 */
	function htmlSelect($name, $css, $kv_array, $selected = null, /* ...) {{{ */
			/* booleans: */ $multiple = false, $readonly = false, $strip_tags = false, $escape = true, $js = NULL)
	{
		$ret = '<select class="'.htmlspecialchars($css).'" name="'.htmlspecialchars($name);
		if ($multiple) {
			$ret  .= '[]" multiple size="'.$this->multiple;
			if (! is_array($selected) && $selected !== null) {
				$selected = explode(',', $selected);
			}
		}
		$ret .= '"'.($readonly ? ' disabled ' : ' ').$js.">\n";
		if (! is_array($selected)) {
			$selected = $selected === null ? array() : array((string)$selected);
		} else {
			foreach($selected as $val) $selecte2[]=(string)$val;
			$selected = $selected2;
		}
		$found = false;
		foreach ($kv_array as $key => $value) {
			$ret .= '<option value="'.htmlspecialchars($key).'"';
			if ((! $found || $multiple) && in_array((string)$key, $selected, 1)
					|| (count($selected) == 0 && ! $found && ! $multiple)) {
				$ret  .= ' selected="selected"';
				$found = true;
			}
			$strip_tags && $value = strip_tags($value);
			$escape     && $value = htmlspecialchars($value);
			$ret .= '>'.$value.'</option>'."\n";
		}
		$ret .= '</select>';
		return $ret;
	} /* }}} */

	/**
	 * Creates HTML checkboxes or radio buttons
	 *
	 * @param	name		element name
	 * @param	css			CSS class name
	 * @param	kv_array	key => value array
	 * @param	selected	selected key (it can be single string, array of
	 *						keys or multiple values separated by comma)
	 * @param	multiple	bool for multiple selection (checkboxes)
	 * @param	readonly	bool for readonly/disabled selection
	 * @param	strip_tags	bool for stripping tags from values
	 * @param	escape		bool for HTML escaping values
	 * @param	js		string to be in the <select >, ususally onchange='..';
	 */
	function htmlRadioCheck($name, $css, $kv_array, $selected = null, /* ...) {{{ */
			/* booleans: */ $multiple = false, $readonly = false, $strip_tags = false, $escape = true, $js = NULL)
	{
		$ret = '';
		if ($multiple) {
			if (! is_array($selected) && $selected !== null) {
				$selected = explode(',', $selected);
			}
		}
		if (! is_array($selected)) {
			$selected = $selected === null ? array() : array($selected);
		}
		$found = false;
		foreach ($kv_array as $key => $value) {
			$ret .= '<input type="'.($multiple ? 'checkbox' : 'radio').'" name="';
			$ret .= htmlspecialchars($name).'[]" value="'.htmlspecialchars($key).'"';
			if ((! $found || $multiple) && in_array((string) $key, $selected, 1)
					|| (count($selected) == 0 && ! $found && ! $multiple)) {
				$ret  .= ' checked';
				$found = true;
			}
			if ($readonly) {
				$ret .= ' disabled';
			}
			$strip_tags && $value = strip_tags($value);
			$escape     && $value = htmlspecialchars($value);
			$ret .= '>'.$value.'<br>'."\n";
		}
		return $ret;
	} /* }}} */

    /**
     * Returns original variables HTML code for use in forms or links.
     *
     * @param   mixed   $origvars       string or array of original varaibles
     * @param   string  $method         type of method ("POST" or "GET")
     * @param   mixed   $default_value  default value of variables
     *                                  if null, empty values will be skipped
     * @return                          get HTML code of original varaibles
     */
    function get_origvars_html($origvars, $method = 'post', $default_value = '') /* {{{ */
    {
        $ret    = '';
        $method = strtoupper($method);
        if ($method == 'POST') {
            if (! is_array($origvars)) {
                $new_origvars = array();
                foreach (explode('&', $origvars) as $param) {
                    $parts = explode('=', $param, 2);
                    if (! isset($parts[1])) {
                        $parts[1] = $default_value;
                    }
                    if (strlen($parts[0]) <= 0) {
                        continue;
                    }
                    $new_origvars[$parts[0]] = $parts[1];
                }
                $origvars =& $new_origvars;
            }
            foreach ($origvars as $key => $val) {
                if (strlen($val) <= 0 && $default_value === null) {
                    continue;
                }
                $key = rawurldecode($key);
                $val = rawurldecode($val);
                $ret .= $this->htmlHidden($key, $val);
            }
        } else if (! strncmp('GET', $method, 3)) {
            if (! is_array($origvars)) {
                $ret .= $origvars;
            } else {
                foreach ($origvars as $key => $val) {
                    if (strlen($val) <= 0 && $default_value === null) {
                        continue;
                    }
                    $ret == '' || $ret .= '&amp;';
                    $ret .= htmlspecialchars(rawurlencode($key));
                    $ret .= '=';
                    $ret .= htmlspecialchars(rawurlencode($val));
                }
            }
            if ($method[strlen($method) - 1] == '+') {
                $ret = "?$ret";
            }
        } else {
            trigger_error('Unsupported Platon::get_origvars_html() method: '
                    .$method, E_USER_ERROR);
        }
        return $ret;
    } /* }}} */

	function get_sfn_cgi_vars($alternative_sfn = null) /* {{{ */
	{
		if ($alternative_sfn === null) { // FAST! (cached return value)
			static $ret = null;
			$ret == null && $ret = $this->get_sfn_cgi_vars($this->sfn);
			return $ret;
		}
		$ret = '';
		$i   = 0;
		foreach ($alternative_sfn as $val) {
			$ret != '' && $ret .= '&';
			$ret .= rawurlencode($this->cgi['prefix']['sys'].'sfn')."[$i]=".rawurlencode($val);
			$i++;
		}
		return $ret;
	} /* }}} */

	function get_default_cgi_prefix($type) /* {{{ */
	{
		switch ($type) {
			case 'operation':	return 'PME_op_';
			case 'sys':			return 'PME_sys_';
			case 'data':		return 'PME_data_';
		}
		return '';
	} /* }}} */

	function get_sys_cgi_var($name, $default_value = null) /* {{{ */
	{
		if (isset($this)) {
			return $this->get_cgi_var($this->cgi['prefix']['sys'].$name, $default_value);
		}
		return phpMyEdit::get_cgi_var(phpMyEdit::get_default_cgi_prefix('sys').$name, $default_value);
	} /* }}} */

	function get_data_cgi_var($name, $default_value = null) /* {{{ */
	{
		if (isset($this)) {
			return $this->get_cgi_var($this->cgi['prefix']['data'].$name, $default_value);
		}
		return phpMyEdit::get_cgi_var(phpMyEdit::get_default_cgi_prefix('data').$name, $default_value);
	} /* }}} */

    function get_cgi_var($name, $default_value = null) /* {{{ */
    {
		if (isset($this) && isset($this->cgi['overwrite'][$name])) {
			return $this->cgi['overwrite'][$name];
		}

        static $magic_quotes_gpc = null;
        if ($magic_quotes_gpc === null) {
            $magic_quotes_gpc = get_magic_quotes_gpc();
        }
        $var = @$_GET[$name];
        if (! isset($var)) {
            $var = @$_POST[$name];
        }
        if (isset($var)) {
            if ($magic_quotes_gpc) {
                if (is_array($var)) {
                    foreach (array_keys($var) as $key) {
                        $var[$key] = stripslashes($var[$key]);
                    }
                } else {
                    $var = stripslashes($var);
                }
            }
        } else {
            $var = @$default_value;
        }
		if (isset($this) && $var === null && isset($this->cgi['append'][$name])) {
			return $this->cgi['append'][$name];
		}
        return $var;
    } /* }}} */

	function get_server_var($name) /* {{{ */
	{
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
		global $HTTP_SERVER_VARS;
		if (isset($HTTP_SERVER_VARS[$name])) {
			return $HTTP_SERVER_VARS[$name];
		}
		global $$name;
		if (isset($$name)) {
			return $$name;
		}
		return null;
	} /* }}} */

	/*
	 * Debug functions
	 */

	function print_get_vars ($miss = 'No GET variables found') // debug only /* {{{ */
	{
		// we parse form GET variables
		if (is_array($_GET)) {
			echo "<p> Variables per GET ";
			foreach ($_GET as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $akey => $aval) {
						// $_GET[$k][$akey] = strip_tags($aval);
						// $$k[$akey] = strip_tags($aval);
						echo "$k\[$akey\]=$aval   ";
					}
				} else {
					// $_GET[$k] = strip_tags($val);
					// $$k = strip_tags($val);
					echo "$k=$v   ";
				}
			}
			echo '</p>';
		} else {
			echo '<p>';
			echo $miss;
			echo '</p>';
		}
	} /* }}} */

	function print_post_vars($miss = 'No POST variables found')  // debug only /* {{{ */
	{
		global $_POST;
		// we parse form POST variables
		if (is_array($_POST)) {
			echo "<p>Variables per POST ";
			foreach ($_POST as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $akey => $aval) {
						// $_POST[$k][$akey] = strip_tags($aval);
						// $$k[$akey] = strip_tags($aval);
						echo "$k\[$akey\]=$aval   ";
					}
				} else {
					// $_POST[$k] = strip_tags($val);
					// $$k = strip_tags($val);
					echo "$k=$v   ";
				}
			}
			echo '</p>';
		} else {
			echo '<p>';
			echo $miss;
			echo '</p>';
		}
	} /* }}} */

	function print_vars ($miss = 'Current instance variables')  // debug only /* {{{ */
	{
		echo "$miss   ";
		echo 'page_name=',$this->page_name,'   ';
		echo 'hn=',$this->hn,'   ';
		echo 'un=',$this->un,'   ';
		echo 'pw=',$this->pw,'   ';
		echo 'db=',$this->db,'   ';
		echo 'dbp=',$this->dbp,'   ';
		echo 'dbh=',$this->dbh,'   ';
		echo 'tb=',$this->tb,'   ';
		echo 'key=',$this->key,'   ';
		echo 'key_type=',$this->key_type,'   ';
		echo 'inc=',$this->inc,'   ';
		echo 'options=',$this->options,'   ';
		echo 'fdd=',$this->fdd,'   ';
		echo 'fl=',$this->fl,'   ';
		echo 'fm=',$this->fm,'   ';
		echo 'sfn=',htmlspecialchars($this->get_sfn_cgi_vars()),'   ';
		echo 'qfn=',$this->qfn,'   ';
		echo 'sw=',$this->sw,'   ';
		echo 'rec=',$this->rec,'   ';
		echo 'navop=',$this->navop,'   ';
		echo 'saveadd=',$this->saveadd,'   ';
		echo 'moreadd=',$this->moreadd,'   ';
		echo 'canceladd=',$this->canceladd,'   ';
		echo 'savechange=',$this->savechange,'   ';
		echo 'morechange=',$this->morechange,'   ';
		echo 'cancelchange=',$this->cancelchange,'   ';
		echo 'savecopy=',$this->savecopy,'   ';
		echo 'cancelcopy=',$this->cancelcopy,'   ';
		echo 'savedelete=',$this->savedelete,'   ';
		echo 'canceldelete=',$this->canceldelete,'   ';
		echo 'cancelview=',$this->cancelview,'   ';
		echo 'operation=',$this->operation,'   ';
		echo "\n";
	} /* }}} */

	/*
	 * Display buttons at top and bottom of page
	 */
	function display_list_table_buttons($position, $listall = false) /* {{{ */
	{
		if (($but_str = $this->display_buttons($position)) === null)
			return;
		if($position == 'down') echo '<hr size="1" class="'.$this->getCSSclass('hr', 'down').'" />'."\n";
		echo '<table summary="navigation" class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<td class="',$this->getCSSclass('buttons', $position),'">',"\n";
		echo $but_str,'</td>',"\n";
		// Message is now written here
		if (strlen(@$this->message) > 0) {
			echo '<td class="',$this->getCSSclass('message', $position),'">',$this->message,'</td>',"\n";
		}
		if($this->display['num_pages'] || $this->display['num_records'])
			echo '<td class="',$this->getCSSclass('stats', $position),'">',"\n";
		if($this->display['num_pages']) {
			if ($listall) {
				echo $this->labels['Page'],':&nbsp;1&nbsp;',$this->labels['of'],'&nbsp;1';
			} else {
				$current_page = intval($this->fm / $this->inc) + 1;
				$total_pages  = max(1, ceil($this->total_recs / abs($this->inc)));
				echo $this->labels['Page'],':&nbsp;',$current_page;
				echo '&nbsp;',$this->labels['of'],'&nbsp;',$total_pages;
			}
		}
		if($this->display['num_records'])
			echo '&nbsp; ',$this->labels['Records'],':&nbsp;',$this->total_recs;
		if($this->display['num_pages'] || $this->display['num_records']) echo '</td>';
		echo '</tr></table>',"\n";
		if($position == 'up') echo '<hr size="1" class="'.$this->getCSSclass('hr', 'up').'" />'."\n";
	} /* }}} */

	/*
	 * Display buttons at top and bottom of page
	 */
	function display_record_buttons($position) /* {{{ */
	{
		if (($but_str = $this->display_buttons($position)) === null)
			return;
		if ($position == 'down') {
			if ($this->tabs_enabled()) $this->display_tab_labels('down');
			echo '<hr size="1" class="',$this->getCSSclass('hr', 'down'),'" />',"\n";
		}
		echo '<table summary="navigation" class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<td class="',$this->getCSSclass('buttons', $position),'">',"\n";
		echo $but_str,'</td>',"\n";
		// Message is now written here
		//echo '</td>',"\n";
		if (strlen(@$this->message) > 0) {
			echo '<td class="',$this->getCSSclass('message', $position),'">',$this->message,'</td>',"\n";
		}
		echo '</tr></table>',"\n";
		if ($position == 'up') {
			if ($this->tabs_enabled()) $this->display_tab_labels('up');
			echo '<hr size="1" class="',$this->getCSSclass('hr', 'up'),'" />',"\n";
		}
	} /* }}} */

	function display_buttons($position) /* {{{ */
	{
		$nav_fnc = 'nav_'.$position;
		if(! $this->$nav_fnc())
			return;
		$buttons = (is_array($this->buttons[$this->page_type][$position]))
			? $this->buttons[$this->page_type][$position]
			: $this->default_buttons[$this->page_type];
		foreach ($buttons as $name) {
			$ret .= $this->display_button($name, $position)."\n";
		}
		return $ret;
	} /* }}} */

	function display_button($name, $position = 'up') /* {{{ */
	{
		if (is_array($name)) {
			if (isset($name['code'])) return $name['code'];
			return $this->htmlSubmit($name['name'], $name['value'], $name['css'], $name['js_validation'], $name['disabled'], $name['js']);
		}
		$disabled = 1; // show disabled by default
		if ($name[0] == '+') { $name = substr($name, 1); $disabled =  0; } // always show disabled as enabled
		if ($name[0] == '-') { $name = substr($name, 1); $disabled = -1; } // don't show disabled
		if ($name == 'cancel') {
			return $this->htmlSubmit('cancel'.$this->page_types[$this->page_type], 'Cancel',
					$this->getCSSclass('cancel', $position), false);
		}
		if (in_array($name, array('add','view','change','copy','delete'))) {
			$enabled_fnc = $name.'_enabled';
			$enabled     = $this->$enabled_fnc();
			if ($name != 'add' && ! $this->total_recs && strstr('LF', $this->page_type))
				$enabled = false;
			return $this->htmlSubmit('operation', ucfirst($name),
					$this->getCSSclass($name, $position), false, $enabled ? 0 : $disabled);
		}
		if ($name == 'savedelete') {
			$enabled     = $this->delete_enabled();
			$js = 'onclick="return confirm(\''.$this->labels['Delete'].' ?\');"';
			return $this->htmlSubmit('savedelete', 'Delete',
					$this->getCSSclass('save', $position), false, $enabled ? 0 : $disabled, $js);
		}
		if (in_array($name, array('save','more'))) {
			$validation = true; // if js validation
			if     ($this->page_type == 'D' && $name == 'save' ) { $value = 'Delete'; $validation = false; }
			elseif ($this->page_type == 'C' && $name == 'more' ) { $value = 'Apply'; }
			else $value = ucfirst($name);
			return $this->htmlSubmit($name.$this->page_types[$this->page_type], $value,
					$this->getCSSclass($name, $position), $validation);
		}
		$listall = $this->inc <= 0;
		if ($listall) {
			$disabledprev = true;
			$disablednext = true;
			$total_pages  = 1;
			$current_page = 1;
		} else {
			$disabledprev = $this->fm <= 0;
			$disablednext =  $this->fm + $this->inc >= $this->total_recs;
			$total_pages  = max(1, ceil($this->total_recs / abs($this->inc)));
			$current_page = ceil($this->fm / abs($this->inc)); // must + 1
		}
		$disabledfirst = $disabledprev;
		$disabledlast  = $disablednext;
		// some statistics first
		if ($name == 'total_pages') return $total_pages;
		if ($name == 'current_page') return ($current_page+1);
		if ($name == 'total_recs') return ($this->total_recs);
		// now some goto buttons/dropdowns/inputs...
		if ($name == 'goto_text') {
			$ret = '<input type="text" class="'.$this->getCSSclass('gotopn', $position).'"';
			$ret .= ' name="'.$this->cgi['prefix']['sys'].'navpn'.$position.'" value="'.($current_page+1).'"';
			$ret .= ' size="'.(strlen($total_pages)+1).'" maxlength="'.(strlen($total_pages)+1).'"';
			// TODO some js here.... on enter submit, on click erase ?...
			$ret .=' oneypress="return PE_filter_handler(this.form, event);" />';
			return $ret;
		}
		if ($name == 'goto_combo') {
			$disabledgoto = !($listall || ($disablednext && $disabledprev)) ? '' : ' disabled';
			if ($disablegoto != '' && $disabled < 0) return;
			$kv_array = array();
			for ($i = 0; $i < $total_pages; $i++) {
				$kv_array[$this->inc * $i] = $i + 1;
			}
			// TODO: add onchange="return this.form.submit();" DONE ???
			return $this->htmlSelect($this->cgi['prefix']['sys'].ltrim($disabledgoto).'navfm'.$position,
					$this->getCSSclass('goto', $position), $kv_array, (string)$this->fm, false, $disabledgoto,
					false, true, 'onchange="return this.form.submit();"');
		}
		if ($name == 'goto') {
			return $this->htmlSubmit('navop', 'Go to', $this->getCSSclass('goto', $position),
					false, ($listall || ($disablednext && $disabledprev)) ? $disabled : 0);
		}
		if (in_array($name, array('first','prev','next','last','<<','<','>','>>'))) {
			$disabled_var = 'disabled'.$name;
			$name2 = $name;
			if (strlen($name) <= 2) {
				$nav_values = array('<<' => 'first', '<' => 'prev', '>' => 'next', '>>' => 'last');
				$disabled_var = 'disabled'.$nav_values[$name];
				$name2 = $nav_values[$name];
			}
			return $this->htmlSubmit('navop', ucfirst($name),
					$this->getCSSclass($name2, $position), false, $$disabled_var ? $disabled : 0);
		}
		if(isset($this->labels[$name])) return $this->labels[$name];
		return $name;
	} /* }}} */

	function number_of_recs() /* {{{ */
	{
		$count_parts = array(
				'type'   => 'select',
				'select' => 'count(*)',
				'from'   => $this->get_SQL_join_clause(),
				'where'  => $this->get_SQL_where_from_query_opts());
		$res = $this->myquery($this->get_SQL_main_list_query($count_parts), __LINE__);
		$row = $this->sql_fetch($res, 'n');
		$this->total_recs = $row[0];
	} /* }}} */

	/*
	 * Table Page Listing
	 */
	function list_table() /* {{{ */
	{
		if ($this->fm == '') {
			$this->fm = 0;
		}
		$this->fm = $this->navfm;
		if ($this->prev_operation()) {
			$this->fm = $this->fm - $this->inc;
			if ($this->fm < 0) {
				$this->fm = 0;
			}
		}
		if ($this->first_operation()) {
			$this->fm = 0;
		} // last operation must be performed below, after retrieving total_recs
		if ($this->next_operation()) {
			$this->fm += $this->inc;
		}
		$this->number_of_recs();
		if ($this->last_operation() || $this->fm > $this->total_recs) { // if goto_text is badly set
			$this->fm = (int)(($this->total_recs - 1)/$this->inc)*$this->inc;
		}
		// If sort sequence has changed, restart listing
		$this->qfn != $this->prev_qfn && $this->fm = 0;
		if (0) { // DEBUG
			echo 'qfn vs. prev_qfn comparsion ';
			echo '[<b>',htmlspecialchars($this->qfn),'</b>]';
			echo '[<b>',htmlspecialchars($this->prev_qfn),'</b>]<br />';
			echo 'comparsion <u>',($this->qfn == $this->prev_qfn ? 'proved' : 'failed'),'</u>';
			echo '<hr size="1" />';
		}
		/*
		 * If user is allowed to Change/Delete records, we need an extra column
		 * to allow users to select a record
		 */
		$select_recs = $this->key != '' &&
			($this->change_enabled() || $this->delete_enabled() || $this->view_enabled());
		// Are we doing a listall?
		$listall = $this->inc <= 0;
		/*
		 * Display the SQL table in an HTML table
		 */
		$this->form_begin();
		echo $this->get_origvars_html($this->get_sfn_cgi_vars());
		echo $this->htmlHiddenSys('fl', $this->fl);
		// Display buttons at top and/or bottom of page.
		$this->display_list_table_buttons('up', $listall);
		if ($this->cgi['persist'] != '') {
			echo $this->get_origvars_html($this->cgi['persist']);
		}
		if (! $this->filter_operation()) {
			echo $this->get_origvars_html($this->qfn);
		}
		echo $this->htmlHiddenSys('qfn', $this->qfn);
		echo $this->htmlHiddenSys('fm', $this->fm);
		echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";
		echo '<tr class="',$this->getCSSclass('header'),'">',"\n";
		/*
		 * System (navigation, selection) columns counting
		 */
		$sys_cols  = 0;
		$sys_cols += intval($this->filter_enabled() || $select_recs);
		if ($sys_cols > 0) {
			$sys_cols += intval($this->nav_buttons()
					&& ($this->nav_text_links() || $this->nav_graphic_links()));
		}
		/*
		 * We need an initial column(s) (sys columns)
		 * if we have filters, Changes or Deletes enabled
		 */
		if ($sys_cols) {
			echo '<th class="',$this->getCSSclass('header'),'" colspan="',$sys_cols,'">';
			if ($this->filter_enabled()) {
				if ($this->filter_operation()) {
					echo $this->htmlSubmit('sw', 'Hide', $this->getCSSclass('hide'), false);
					echo $this->htmlSubmit('sw', 'Clear', $this->getCSSclass('clear'), false);
				} else {
					echo $this->htmlSubmit('sw', 'Search', $this->getCSSclass('search'), false);
				}
			} else {
				echo '&nbsp;';
			}
			echo '</th>',"\n";
		}
		for ($k = 0; $k < $this->num_fds; $k++) {
			$fd = $this->fds[$k];
			if (! $this->displayed[$k]) {
				continue;
			}
			$css_postfix    = @$this->fdd[$k]['css']['postfix'];
			$css_class_name = $this->getCSSclass('header', null, null, $css_postfix);
			$fdn = $this->fdd[$fd]['name'];
			if (! $this->fdd[$fd]['sort'] || $this->password($fd)) {
				echo '<th class="',$css_class_name,'">',$fdn,'</th>',"\n";
			} else {
				// Clicking on the current sort field reverses the sort order
				$new_sfn = $this->sfn;
				array_unshift($new_sfn, in_array("$k", $new_sfn, 1) ? "-$k" : $k);
				echo '<th class="',$css_class_name,'">';
				echo '<a class="',$css_class_name,'" href="';
				echo htmlspecialchars($this->page_name.'?'.$this->cgi['prefix']['sys'].'fm'.'=0'
						.'&'.$this->cgi['prefix']['sys'].'fl'.'='.$this->fl
						.'&'.$this->cgi['prefix']['sys'].'qfn'.'='.rawurlencode($this->qfn).$this->qfn
						.'&'.$this->get_sfn_cgi_vars($new_sfn).$this->cgi['persist']);
				echo '">',$fdn,'</a></th>',"\n";
			}
		}
		echo '</tr>',"\n";

		/*
		 * Prepare the SQL Query from the data definition file
		 */
		$qparts['type']   = 'select';
		$qparts['select'] = $this->get_SQL_column_list();
		// Even if the key field isn't displayed, we still need its value
		if ($select_recs) {
			if (!in_array ($this->key, $this->fds)) {
				$qparts['select'] .= ','.$this->fqn($this->key);
			}
		}
		$qparts['from']  = $this->get_SQL_join_clause();
		$qparts['where'] = $this->get_SQL_where_from_query_opts();
		// build up the ORDER BY clause
		if (isset($this->sfn)) {
			$sort_fields   = array();
			$sort_fields_w = array();
			foreach ($this->sfn as $field) {
				if ($field[0] == '-') {
					$field = substr($field, 1);
					$desc  = true;
				} else {
					$field = $field;
					$desc  = false;
				}
				$sort_field   = $this->fqn($field);
				$sort_field_w = $this->fdd[$field]['name'];
				$this->col_has_sql($field) && $sort_field_w .= ' (sql)';
				if ($desc) {
					$sort_field   .= ' DESC';
					$sort_field_w .= ' '.$this->labels['descending'];
				} else {
					$sort_field_w .= ' '.$this->labels['ascending'];
				}
				$sort_fields[]   = $sort_field;
				$sort_fields_w[] = $sort_field_w;
			}
			if (count($sort_fields) > 0) {
				$qparts['orderby'] = join(',', $sort_fields);
			}
		}
		$qparts['limit'] = $listall ? '' : $this->sql_limit($this->fm,$this->inc);

		/*
		 * Main list_table() query
		 *
		 * Each row of the HTML table is one record from the SQL query. We must
		 * perform this query before filter printing, because we want to use
		 * $this->sql_field_len() function. We will also fetch the first row to get
		 * the field names.
		 */
		$query = $this->get_SQL_main_list_query($qparts);
		$res   = $this->myquery($query, __LINE__);
		if ($res == false) {
			$this->error('invalid SQL query', $query);
			return false;
		}
		$row = $this->sql_fetch($res);

		/* FILTER {{{
		 *
		 * Draw the filter and fill it with any data typed in last pass and stored
		 * in the array parameter keyword 'filter'. Prepare the SQL WHERE clause.
		 */
		if ($this->filter_operation()) {
			// Filter row retrieval
			$fields     = false;
			$filter_row = $row;
			if (! is_array($filter_row)) {
				unset($qparts['where']);
				$query = $this->get_SQL_query($qparts);
				$res   = $this->myquery($query, __LINE__);
				if ($res == false) {
					$this->error('invalid SQL query', $query);
					return false;
				}
				$filter_row = $this->sql_fetch($res);
			}
			/* Variable $fields is used to get index of particular field in
			   result. That index can be passed in example to $this->sql_field_len()
			   function. Use field names as indexes to $fields array. */
			if (is_array($filter_row)) {
				$fields = array_flip(array_keys($filter_row));
			}
			if ($fields != false) {
				$css_class_name = $this->getCSSclass('filter');
				echo '<tr class="',$css_class_name,'">',"\n";
				echo '<td class="',$css_class_name,'" colspan="',$sys_cols,'">';
				echo $this->htmlSubmit('filter', 'Query', $this->getCSSclass('query'), false);
				echo '</td>', "\n";
				for ($k = 0; $k < $this->num_fds; $k++) {
					if (! $this->displayed[$k]) {
						continue;
					}
					$css_postfix      = @$this->fdd[$k]['css']['postfix'];
					$css_class_name   = $this->getCSSclass('filter', null, null, $css_postfix);
					$this->field_name = $this->fds[$k];
					$fd               = $this->field_name;
					$this->field      = $this->fdd[$fd];
					$l  = 'qf'.$k;
					$lc = 'qf'.$k.'_comp';
					$li = 'qf'.$k.'_id';
					if ($this->clear_operation()) {
						$m  = null;
						$mc = null;
						$mi = null;
					} else {
						$m  = $this->get_sys_cgi_var($l);
						$mc = $this->get_sys_cgi_var($lc);
						$mi = $this->get_sys_cgi_var($li);
					}
					echo '<td class="',$css_class_name,'">';
					if ($this->password($k)) {
						echo '&nbsp;';
					} else if ($this->fdd[$fd]['select'] == 'D' || $this->fdd[$fd]['select'] == 'M') {
						// Multiple fields processing
						// Default size is 2 and array required for values.
						$from_table = ! $this->col_has_values($k) || isset($this->fdd[$k]['values']['table']);
						$vals       = $this->set_values($k, array('*' => '*'), null, $from_table);
						$selected   = $mi;
						$multiple   = $this->col_has_multiple_select($k);
						$multiple  |= $this->fdd[$fd]['select'] == 'M';
						$readonly   = false;
						$strip_tags = true;
						$escape     = true;
						echo $this->htmlSelect($this->cgi['prefix']['sys'].$l.'_id', $css_class_name,
								$vals, $selected, $multiple, $readonly, $strip_tags, $escape);
					} elseif ($this->fdd[$fd]['select'] == 'N' || $this->fdd[$fd]['select'] == 'T') {
						$len_props = '';
						$maxlen = intval($this->fdd[$k]['maxlen']);
						$maxlen > 0 || $maxlen = intval($this->sql_field_len($res, $fields["qf$k"]));
						$size = isset($this->fdd[$k]['size']) ? $this->fdd[$k]['size']
							: ($maxlen < 30 ? min($maxlen, 8) : 12);
						$len_props .= ' size="'.$size.'"';
						$len_props .= ' maxlength="'.$maxlen.'"';
						if ($this->fdd[$fd]['select'] == 'N') {
							$mc = in_array($mc, $this->comp_ops) ? $mc : '=';
							echo $this->htmlSelect($this->cgi['prefix']['sys'].$l.'_comp',
									$css_class_name, $this->comp_ops, $mc);
						}
						echo '<input class="',$css_class_name,'" value="',htmlspecialchars(@$m);
						echo '" type="text" name="'.$this->cgi['prefix']['sys'].'qf'.$k.'"',$len_props;
						echo ' onkeypress="return '.$this->js['prefix'].'filter_handler(this.form, event);" />';
					} else {
						echo '&nbsp;';
					}
					echo '</td>',"\n";
				}
				echo '</tr>',"\n";
			}
		} // }}}
		
		/*
		 * Display sorting sequence
		 */
		if ($qparts['orderby'] && $this->display['sort']) {
			$css_class_name = $this->getCSSclass('sortinfo');
			echo '<tr class="',$css_class_name,'">',"\n";
			echo '<td class="',$css_class_name,'" colspan="',$sys_cols,'">';
			echo '<a class="',$css_class_name,'" href="';
			echo htmlspecialchars($this->page_name
					.'?'.$this->cgi['prefix']['sys'].'fl'.'='.$this->fl
					.'&'.$this->cgi['prefix']['sys'].'fm'.'='.$this->fm
					.'&'.$this->cgi['prefix']['sys'].'qfn'.'='.rawurlencode($this->qfn)
					.$this->qfn.$this->cgi['persist']);
			echo '">',$this->labels['Clear'],'</a></td>',"\n";
			echo '<td class="',$css_class_name,'" colspan="',$this->num_fields_displayed,'">';
			echo $this->labels['Sorted By'],': ',join(', ', $sort_fields_w),'</td></tr>',"\n";
		}

		/*
		 * Display the current query
		 */
		$text_query = $this->get_SQL_where_from_query_opts(null, true);
		if ($text_query != '' && $this->display['query']) {
			$css_class_name = $this->getCSSclass('queryinfo');
			echo '<tr class="',$css_class_name,'">',"\n";
			echo '<td class="',$css_class_name,'" colspan="',$sys_cols,'">';
			echo '<a class="',$css_class_name,'" href="';
			echo htmlspecialchars($this->get_server_var('PHP_SELF')
					.'?'.$this->cgi['prefix']['sys'].'fl'.'='.$this->fl
					.'&'.$this->cgi['prefix']['sys'].'fm'.'='.$this->fm
					.'&'.$this->cgi['prefix']['sys'].'qfn'.'='.rawurlencode($this->qfn)
					.'&'.$this->get_sfn_cgi_vars().$this->cgi['persist']);
			echo '">',$this->labels['Clear'],'</a></td>',"\n";
			echo '<td class="',$css_class_name,'" colspan="',$this->num_fields_displayed,'">';
			echo $this->labels['Current Query'],': ',htmlspecialchars($text_query),'</td></tr>',"\n";
		}

		if ($this->nav_text_links() || $this->nav_graphic_links()) {
			$qstrparts = array();
			strlen($this->fl)             > 0 && $qstrparts[] = $this->cgi['prefix']['sys'].'fl'.'='.$this->fl;
			strlen($this->fm)             > 0 && $qstrparts[] = $this->cgi['prefix']['sys'].'fm'.'='.$this->fm;
			count($this->sfn)             > 0 && $qstrparts[] = $this->get_sfn_cgi_vars();
			strlen($this->cgi['persist']) > 0 && $qstrparts[] = $this->cgi['persist'];
			$qpview      = $qstrparts;
			$qpcopy      = $qstrparts;
			$qpchange    = $qstrparts;
			$qpdelete    = $qstrparts;
			$qp_prefix   = $this->cgi['prefix']['sys'].'operation'.'='.$this->cgi['prefix']['operation'];
			$qpview[]    = $qp_prefix.'View';
			$qpcopy[]    = $qp_prefix.'Copy';
			$qpchange[]  = $qp_prefix.'Change';
			$qpdelete[]  = $qp_prefix.'Delete';
			$qpviewStr   = htmlspecialchars($this->page_name.'?'.join('&',$qpview).$this->qfn);
			$qpcopyStr   = htmlspecialchars($this->page_name.'?'.join('&',$qpcopy).$this->qfn);
			$qpchangeStr = htmlspecialchars($this->page_name.'?'.join('&',$qpchange).$this->qfn);
			$qpdeleteStr = htmlspecialchars($this->page_name.'?'.join('&',$qpdelete).$this->qfn);
		}

		$fetched  = true;
		$first    = true;
		$rowCount = 0;
		while ((!$fetched && ($row = $this->sql_fetch($res)) != false)
				|| ($fetched && $row != false)) {
			$fetched = false;
			echo '<tr class="',$this->getCSSclass('row', null, 'next'),'">',"\n";
			if ($sys_cols) { /* {{{ */
				$key_rec     = $row['qf'.$this->key_num];
				$queryAppend = htmlspecialchars('&'.$this->cgi['prefix']['sys'].'rec'.'='.$key_rec);
				$viewQuery   = $qpviewStr   . $queryAppend;
				$copyQuery   = $qpcopyStr   . $queryAppend;
				$changeQuery = $qpchangeStr . $queryAppend;
				$deleteQuery = $qpdeleteStr . $queryAppend;
				$viewTitle   = htmlspecialchars($this->labels['View']);
				$changeTitle = htmlspecialchars($this->labels['Change']);
				$copyTitle   = htmlspecialchars($this->labels['Copy']);
				$deleteTitle = htmlspecialchars($this->labels['Delete']);
				$css_class_name = $this->getCSSclass('navigation', null, true);
				if ($select_recs) {
					if (! $this->nav_buttons() || $sys_cols > 1) {
						echo '<td class="',$css_class_name,'">';
					}
					if ($this->nav_graphic_links()) {
						$printed_out = false;
						if ($this->view_enabled()) {
							$printed_out = true;
							echo '<a class="',$css_class_name,'" href="',$viewQuery,'"><img class="';
							echo $css_class_name,'" src="',$this->url['images'];
							echo 'pme-view.png" height="15" width="16" border="0" ';
							echo 'alt="',$viewTitle,'" title="',$viewTitle,'" /></a>';
						}
						if ($this->change_enabled()) {
							$printed_out && print('&nbsp;');
							$printed_out = true;
							echo '<a class="',$css_class_name,'" href="',$changeQuery,'"><img class="';
							echo $css_class_name,'" src="',$this->url['images'];
							echo 'pme-change.png" height="15" width="16" border="0" ';
							echo 'alt="',$changeTitle,'" title="',$changeTitle,'" /></a>';
						}
						if ($this->copy_enabled()) {
							$printed_out && print('&nbsp;');
							$printed_out = true;
							echo '<a class="',$css_class_name,'" href="',$copyQuery,'"><img class="';
							echo $css_class_name,'" src="',$this->url['images'];
							echo 'pme-copy.png" height="15" width="16" border="0" ';
							echo 'alt="',$copyTitle,'" title="',$copyTitle,'" /></a>';
						}
						if ($this->delete_enabled()) {
							$printed_out && print('&nbsp;');
							$printed_out = true;
							echo '<a class="',$css_class_name,'" href="',$deleteQuery,'"><img class="';
							echo $css_class_name,'" src="',$this->url['images'];
							echo 'pme-delete.png" height="15" width="16" border="0" ';
							echo 'alt="',$deleteTitle,'" title="',$deleteTitle,'" /></a>';
						}
					}
					if ($this->nav_text_links()) {
						if ($this->nav_graphic_links()) {
							echo '<br class="',$css_class_name,'">';
						}
						$printed_out = false;
						if ($this->view_enabled()) {
							$printed_out = true;
							echo '<a href="',$viewQuery,'" title="',$viewTitle,'" class="',$css_class_name,'">V</a>';
						}
						if ($this->change_enabled()) {
							$printed_out && print('&nbsp;');
							$printed_out = true;
							echo '<a href="',$changeQuery,'" title="',$changeTitle,'" class="',$css_class_name,'">C</a>';
						}
						if ($this->copy_enabled()) {
							$printed_out && print('&nbsp;');
							$printed_out = true;
							echo '<a href="',$copyQuery,'" title="',$copyTitle,'" class="',$css_class_name,'">P</a>';
						}
						if ($this->delete_enabled()) {
							$printed_out && print('&nbsp;');
							$printed_out = true;
							echo '<a href="',$deleteQuery,'" title="',$deleteTitle,'" class="',$css_class_name,'">D</a>';
						}
					}
					if (! $this->nav_buttons() || $sys_cols > 1) {
						echo '</td>',"\n";
					}
					if ($this->nav_buttons()) {
						echo '<td class="',$css_class_name,'"><input class="',$css_class_name;
						echo '" type="radio" name="'.$this->cgi['prefix']['sys'].'rec';
						echo '" value="',htmlspecialchars($key_rec),'"';
						if (($this->rec == '' && $first) || ($this->rec == $key_rec)) {
							echo ' checked';
							$first = false;
						}
						echo ' /></td>',"\n";
					}
				} elseif ($this->filter_enabled()) {
					echo '<td class="',$css_class_name,'" colspan="',$sys_cols,'">&nbsp;</td>',"\n";
				}
			} /* }}} */
			for ($k = 0; $k < $this->num_fds; $k++) { /* {{{ */
				$fd = $this->fds[$k];
				if (! $this->displayed[$k]) {
					continue;
				}
				$css_postfix    = @$this->fdd[$k]['css']['postfix'];
				$css_class_name = $this->getCSSclass('cell', null, true, $css_postfix);
				if ($this->password($k)) {
					echo '<td class="',$css_class_name,'">',$this->labels['hidden'],'</td>',"\n";
					continue;
				}
				echo '<td class="',$css_class_name,'"',$this->getColAttributes($fd),'>';
				echo $this->cellDisplay($k, $row, $css_class_name);
				echo '</td>',"\n";
			} /* }}} */
			echo '</tr>',"\n";
		}

		/*
		 * Display and accumulate column aggregation info, do totalling query
		 * XXX this feature does not work yet!!!
		 */
		// aggregates listing (if any)
		if ($$var_to_total) {
			// do the aggregate query if necessary
			//if ($vars_to_total) {
				$qp = array();
				$qp['type'] = 'select';
				$qp['select'] = $aggr_from_clause;
				$qp['from']   = $this->get_SQL_join_clause();
				$qp['where']  = $this->get_SQL_where_from_query_opts();
				$tot_query    = $this->get_SQL_query($qp);
				$totals_result = $this->myquery($tot_query,__LINE__);
				$tot_row       = $this->sql_fetch($totals_result);
			//}
			$qp_aggr = $qp;
			echo "\n",'<tr class="TODO-class">',"\n",'<td class="TODO-class">&nbsp;</td>',"\n";
			/*
			echo '<td>';
			echo printarray($qp_aggr);
			echo printarray($vars_to_total);
			echo '</td>';
			echo '<td colspan="'.($this->num_fds-1).'">'.$var_to_total.' '.$$var_to_total.'</td>';
			*/
			// display the results
			for ($k=0;$k<$this->num_fds;$k++) {
				$fd = $this->fds[$k];
				if (stristr($this->fdd[$fd]['options'],'L') or !isset($this->fdd[$fd]['options'])) {
					echo '<td>';
					$aggr_var  = 'qf'.$k.'_aggr';
					$$aggr_var = $this->get_sys_cgi_var($aggr_var);
					if ($$aggr_var) {
						echo $this->sql_aggrs[$$aggr_var],': ',$tot_row[$aggr_var];
					} else {
						echo '&nbsp;';
					}
					echo '</td>',"\n";
				}
			}
			echo '</tr>',"\n";
		}
		echo '</table>',"\n"; // end of table rows listing
		$this->display_list_table_buttons('down', $listall);
		$this->form_end();
	} /* }}} */

	function display_record() /* {{{ */
	{
		// PRE Triggers
		$ret = true;
		if ($this->change_operation()) {
			$ret &= $this->exec_triggers_simple('update', 'pre');
			// if PRE update fails, then back to view operation
			if (! $ret) {
				$this->operation = $this->labels['View'];
				$ret = true;
			}
		}
		if ($this->add_operation() || $this->copy_operation()) {
			$ret &= $this->exec_triggers_simple('insert', 'pre');
		}
		if ($this->view_operation()) {
			$ret &= $this->exec_triggers_simple('select', 'pre');
		}
		if ($this->delete_operation()) {
			$ret &= $this->exec_triggers_simple('delete', 'pre');
		}
		// if PRE insert/view/delete fail, then back to the list
		if ($ret == false) {
			$this->operation = '';
			$this->list_table();
			return;
		}
		$this->form_begin();
		if ($this->cgi['persist'] != '') {
			echo $this->get_origvars_html($this->cgi['persist']);
		}
		echo $this->get_origvars_html($this->get_sfn_cgi_vars());
		echo $this->get_origvars_html($this->qfn);
		echo $this->htmlHiddenSys('cur_tab', $this->dhtml['prefix'].'tab'.$this->cur_tab);
		echo $this->htmlHiddenSys('qfn', $this->qfn);
		echo $this->htmlHiddenSys('rec', $this->copy_operation() ? '' : $this->rec);
		echo $this->htmlHiddenSys('fm', $this->fm);
		echo $this->htmlHiddenSys('fl', $this->fl);
		$this->display_record_buttons('up');
		if ($this->tabs_enabled()) {
			echo '<div id="'.$this->dhtml['prefix'].'tab0">',"\n";
		}
		echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";
		if ($this->add_operation()) {
			$this->display_add_record();
		} else {
			$this->display_copy_change_delete_record();
		}
		echo '</table>',"\n";
 		if ($this->tabs_enabled()) {
		echo '</div>',"\n";
		}		
		$this->display_record_buttons('down');

		$this->form_end();
	} /* }}} */

	/*
	 * Action functions
	 */

	function do_add_record() /* {{{ */
	{
		// Preparing query
		$query       = '';
		$key_col_val = '';
		$newvals     = array();
		for ($k = 0; $k < $this->num_fds; $k++) {
			if ($this->processed($k)) {
				$fd = $this->fds[$k];
				if ($this->readonly($k)) {
					$fn = (string) @$this->fdd[$k]['default'];
				} else {
					$fn = $this->get_data_cgi_var($fd);
				}
				if ($fd == $this->key) {
					$key_col_val = $fn;
				}
				$newvals[$fd] = is_array($fn) ? join(',',$fn) : $fn;
			}
		}
		// Creating array of changed keys ($changed)
		$changed = array_keys($newvals);
		// Before trigger, newvals can be efectively changed
		if ($this->exec_triggers('insert', 'before', $oldvals, $changed, $newvals) == false) {
			return false;
		}
		// Real query (no additional query in this method)
		foreach ($newvals as $fd => $val) {
			if ($fd == '') continue;
			if ($this->col_has_sqlw($this->fdn[$fd])) {
				$val_as  = addslashes($val);
				$val_qas = '"'.addslashes($val).'"';
				$value = $this->substituteVars(
						$this->fdd[$this->fdn[$fd]]['sqlw'], array(
							'val_qas' => $val_qas,
							'val_as'  => $val_as,
							'val'     => $val
							));
			} else {
				$value = "'".addslashes($val)."'";
			}
			if ($query == '') {
				$query = 'INSERT INTO '.$this->sd.$this->tb.$this->ed.' ('.$this->sd.$fd.$this->ed.''; // )
				$query2 = ') VALUES ('.$value.'';
			} else {
				$query  .= ', '.$this->sd.$fd.$this->ed.'';
				$query2 .= ', '.$value.'';
			}
		}
		$query .= $query2.')';
		$res    = $this->myquery($query, __LINE__);
		$this->message = $this->sql_affected_rows($this->dbh).' '.$this->labels['record added'];
		if (! $res) {
			return false;
		}
		$this->rec = $this->sql_insert_id();
		// Notify list
		if (@$this->notify['insert'] || @$this->notify['all']) {
			$this->email_notify(false, $newvals);
		}
		// Note change in log table
		if ($this->logtable) {
			$query = sprintf('INSERT INTO %s'
					.' (updated, user, host, operation, tab, rowkey, col, oldval, newval)'
					.' VALUES (NOW(), "%s", "%s", "insert", "%s", "%s", "", "", "%s")',
					$this->logtable, addslashes($this->get_server_var('REMOTE_USER')),
					addslashes($this->get_server_var('REMOTE_ADDR')), addslashes($this->tb),
					addslashes($key_col_val), addslashes(serialize($newvals)));
			$this->myquery($query, __LINE__);
		}
		// After trigger
		if ($this->exec_triggers('insert', 'after', $oldvals, $changed, $newvals) == false) {
			return false;
		}
		return true;
	} /* }}} */

	function do_change_record() /* {{{ */
	{
		// Preparing queries
		$query_real   = '';
		$query_oldrec = '';
		$newvals      = array();
		$oldvals      = array();
		$changed      = array();
		// Prepare query to retrieve oldvals
		for ($k = 0; $k < $this->num_fds; $k++) {
			if ($this->processed($k) && !$this->readonly($k)) {
				$fd = $this->fds[$k];
				$fn = $this->get_data_cgi_var($fd);
				$newvals[$this->fds[$k]] = is_array($fn) ? join(',',$fn) : $fn;
				if ($query_oldrec == '') {
					$query_oldrec = 'SELECT '.$this->sd.$fd.$this->ed;
				} else {
					$query_oldrec .= ','.$this->sd.$fd.$this->ed;
				}
			}
		}
		$where_part = " WHERE (".$this->sd.$this->key.$this->ed.'='.$this->key_delim.$this->rec.$this->key_delim.')';
		$query_newrec  = $query_oldrec.' FROM ' . $this->tb;
		$query_oldrec .= ' FROM ' . $this->sd.$this->tb.$this->ed . $where_part;
		// Additional query (must go before real query)
		$res     = $this->myquery($query_oldrec, __LINE__);
		$oldvals = $this->sql_fetch($res);
		$this->sql_free_result($res);
		// Creating array of changed keys ($changed)
		foreach ($newvals as $fd => $value) {
			if ($value != $oldvals[$fd])
				$changed[] = $fd;
		}
		// Before trigger
		if ($this->exec_triggers('update', 'before', $oldvals, $changed, $newvals) == false) {
			return false;
		}
		// Build the real query respecting changes to the newvals array
		foreach ($newvals as $fd => $val) {
			if ($fd == '') continue;
			if ($this->col_has_sqlw($this->fdn[$fd])) {
				$val_as  = addslashes($val);
				$val_qas = '"'.addslashes($val).'"';
				$value = $this->substituteVars(
						$this->fdd[$this->fdn[$fd]]['sqlw'], array(
							'val_qas' => $val_qas,
							'val_as'  => $val_as,
							'val'     => $val
							));
			} else {
				$value = "'".addslashes($val)."'";
			}
			if ($query_real == '') {
				$query_real   = 'UPDATE '.$this->sd.$this->tb.$this->ed.' SET '.$this->sd.$fd.$this->ed.'='.$value;
			} else {
				$query_real   .= ','.$this->sd.$fd.$this->ed.'='.$value;
			}
		}
		$query_real .= $where_part;
		// Real query
		$res = $this->myquery($query_real, __LINE__);
		$this->message = $this->sql_affected_rows($this->dbh).' '.$this->labels['record changed'];
		if (! $res) {
			return false;
		}
		// Another additional query (must go after real query)
		if (in_array($this->key, $changed)) {
			$this->rec = $newvals[$this->key]; // key has changed
		}
		$query_newrec .= ' WHERE ('.$this->key.'='.$this->key_delim.$this->rec.$this->key_delim.')';
		$res     = $this->myquery($query_newrec, __LINE__);
		$newvals = $this->sql_fetch($res);
		$this->sql_free_result($res);
		// Creating array of changed keys ($changed)
		$changed = array();
		foreach ($newvals as $fd => $value) {
			if ($value != $oldvals[$fd])
				$changed[] = $fd;
		}
		// Notify list
		if (@$this->notify['update'] || @$this->notify['all']) {
			if (count($changed) > 0) {
				$this->email_notify($oldvals, $newvals);
			}
		}
		// Note change in log table
		if ($this->logtable) {
			foreach ($changed as $key) {
				$qry = sprintf('INSERT INTO %s'
						.' (updated, user, host, operation, tab, rowkey, col, oldval, newval)'
						.' VALUES (NOW(), "%s", "%s", "update", "%s", "%s", "%s", "%s", "%s")',
						$this->logtable, addslashes($this->get_server_var('REMOTE_USER')),
						addslashes($this->get_server_var('REMOTE_ADDR')), addslashes($this->tb),
						addslashes($this->rec), addslashes($key),
						addslashes($oldvals[$key]), addslashes($newvals[$key]));
				$this->myquery($qry, __LINE__);
			}
		}
		// After trigger
		if ($this->exec_triggers('update', 'after', $oldvals, $changed, $newvals) == false) {
			return false;
		}
		return true;
	} /* }}} */

	function do_delete_record() /* {{{ */
	{
		// Additional query
		$query   = 'SELECT * FROM '.$this->sd.$this->tb.$this->ed.' WHERE ('.$this->sd.$this->key.$this->ed.' = '
				.$this->key_delim.$this->rec.$this->key_delim.')'; // )
		$res     = $this->myquery($query, __LINE__);
		$oldvals = $this->sql_fetch($res);
		$this->sql_free_result($res);
		// Creating array of changed keys ($changed)
		$changed = is_array($oldvals) ? array_keys($oldvals) : array();
		$newvals = array();
		// Before trigger
		if ($this->exec_triggers('delete', 'before', $oldvals, $changed, $newvals) == false) {
			return false;
		}
		// Real query
		$query = 'DELETE FROM '.$this->tb.' WHERE ('.$this->key.' = '
				.$this->key_delim.$this->rec.$this->key_delim.')'; // )
		$res = $this->myquery($query, __LINE__);
		$this->message = $this->sql_affected_rows($this->dbh).' '.$this->labels['record deleted'];
		if (! $res) {
			return false;
		}
		// Notify list
		if (@$this->notify['delete'] || @$this->notify['all']) {
			$this->email_notify($oldvals, false);
		}
		// Note change in log table
		if ($this->logtable) {
			$query = sprintf('INSERT INTO %s'
					.' (updated, user, host, operation, tab, rowkey, col, oldval, newval)'
					.' VALUES (NOW(), "%s", "%s", "delete", "%s", "%s", "%s", "%s", "")',
					$this->logtable, addslashes($this->get_server_var('REMOTE_USER')),
					addslashes($this->get_server_var('REMOTE_ADDR')), addslashes($this->tb),
					addslashes($this->rec), addslashes($key), addslashes(serialize($oldvals)));
			$this->myquery($query, __LINE__);
		}
		// After trigger
		if ($this->exec_triggers('delete', 'after', $oldvals, $changed, $newvals) == false) {
			return false;
		}
		return true;
	} /* }}} */

	function email_notify($old_vals, $new_vals) /* {{{ */
	{
		if (! function_exists('mail')) {
			return false;
		}
		if ($old_vals != false && $new_vals != false) {
			$action  = 'update';
			$subject = 'Record updated in';
			$body    = 'An item with '.$this->fdd[$this->key]['name'].' = '
				.$this->key_delim.$this->rec.$this->key_delim .' was updated in';
			$vals    = $new_vals;
		} elseif ($new_vals != false) {
			$action  = 'insert';
			$subject = 'Record added to';
			$body    = 'A new item was added into';
			$vals    = $new_vals;
		} elseif ($old_vals != false) {
			$action  = 'delete';
			$subject = 'Record deleted from';
			$body    = 'An item was deleted from';
			$vals    = $old_vals;
		} else {
			return false;
		}
		$addr  = $this->get_server_var('REMOTE_ADDR');
		$user  = $this->get_server_var('REMOTE_USER');
		$body  = 'This notification e-mail was automatically generated by phpMyEdit.'."\n\n".$body;
		$body .= ' table '.$this->tb.' in SQL database '.$this->db.' on '.$this->page_name;
		$body .= ' by '.($user == '' ? 'unknown user' : "user $user").' from '.$addr;
		$body .= ' at '.date('d/M/Y H:i').' with the following fields:'."\n\n";
		$i = 1;
		foreach ($vals as $k => $text) {
			$name = isset($this->fdd[$k]['name~'])
				? $this->fdd[$k]['name~'] : $this->fdd[$k]['name'];
			if ($action == 'update') {
				if ($old_vals[$k] == $new_vals[$k]) {
					continue;
				}
				$body .= sprintf("[%02s] %s (%s)\n      WAS: %s\n      IS:  %s\n",
						$i, $name, $k, $old_vals[$k], $new_vals[$k]);
			} else {
				$body .= sprintf('[%02s] %s (%s): %s'."\n", $i, $name, $k, $text);
			}
			$i++;
		}
		$body    .= "\n--\r\n"; // \r is needed for signature separating
		$body    .= "phpMyEdit\ninstant SQL table editor and code generator\n";
		$body    .= "http://platon.sk/projects/phpMyEdit/\n\n";
		$subject  = @$this->notify['prefix'].$subject.' '.$this->dbp.$this->tb;
		$subject  = trim($subject); // just for sure
		$wrap_w   = intval(@$this->notify['wrap']);
	   	$wrap_w > 0 || $wrap_w = 72;
		$from     = (string) @$this->notify['from'];
		$from != '' || $from = 'webmaster@'.strtolower($this->get_server_var('SERVER_NAME'));
		$headers  = 'From: '.$from."\n".'X-Mailer: PHP/'.phpversion().' (phpMyEdit)';
		$body     = wordwrap($body, $wrap_w, "\n", 1);
		$emails   = (array) $this->notify[$action] + (array) $this->notify['all'];
		foreach ($emails as $email) {
			if (! empty($email)) {
				mail(trim($email), $subject, $body, $headers);
			}
		}
		return true;
	} /* }}} */

	/*
	 * Apply triggers function
	 * Run a (set of) trigger(s). $trigger can be an Array or a filename
	 * Break and return false as soon as a trigger return false
	 * we need a reference on $newvals to be able to change value before insert/update
	 */
	function exec_triggers($op, $step, $oldvals, &$changed, &$newvals) /* {{{ */
	{
		if (! isset($this->triggers[$op][$step])) {
			return true;
		}
		$ret  = true;
		$trig = $this->triggers[$op][$step];
		if (is_array($trig)) {
			ksort($trig);
			for ($t = reset($trig); $t !== false && $ret != false; $t = next($trig)) {
				$ret = include($t);
			}
		} else {
			$ret = include($trig);
		}
		return $ret;
	} /* }}} */

	function exec_triggers_simple($op, $step) /* {{{ */
	{
		$oldvals = $newvals = $changed = array();
		return $this->exec_triggers($op, $step, $oldvals, $changed, $newvals);
	} /* }}} */
	
	/*
	 * Recreate functions
	 */
	function recreate_fdd($default_page_type = 'L') /* {{{ */
	{
		// TODO: one level deeper browsing
		$this->page_type = $default_page_type;
		$this->filter_operation() && $this->page_type = 'F';
		$this->view_operation()   && $this->page_type = 'V';
		if ($this->add_operation()
				|| $this->saveadd == $this->labels['Save']
				|| $this->moreadd == $this->labels['More']) {
			$this->page_type = 'A';
		}
		if ($this->change_operation()
				|| $this->savechange == $this->labels['Save']
				|| $this->morechange == $this->labels['Apply']) {
			$this->page_type = 'C';
		}
		if ($this->copy_operation() || $this->savecopy == $this->labels['Save']) {
			$this->page_type = 'P';
		}
		if ($this->delete_operation() || $this->savedelete == $this->labels['Delete']) {
			$this->page_type = 'D';
		}
		// Restore backups (if exists)
		foreach (array_keys($this->fdd) as $column) {
			foreach (array_keys($this->fdd[$column]) as $col_option) {
				if ($col_option[strlen($col_option) - 1] != '~')
					continue;

				$this->fdd[$column][substr($col_option, 0, strlen($col_option) - 1)]
					= $this->fdd[$column][$col_option];
				unset($this->fdd[$column][$col_option]);
			}
		}
		foreach (array_keys($this->fdd) as $column) {
			foreach (array_keys($this->fdd[$column]) as $col_option) {
				if (! strchr($col_option, '|')) {
					continue;
				}
				$col_ar = explode('|', $col_option, 2);
				if (! stristr($col_ar[1], $this->page_type)) {
					continue;
				}
				// Make field backups
				$this->fdd[$column][$col_ar[0] .'~'] = $this->fdd[$column][$col_ar[0]];
				$this->fdd[$column][$col_option.'~'] = $this->fdd[$column][$col_option];
				// Set particular field
				$this->fdd[$column][$col_ar[0]] = $this->fdd[$column][$col_option];
				unset($this->fdd[$column][$col_option]);
			}
		}
	} /* }}} */

	function recreate_displayed() /* {{{ */
	{
		$field_num            = 0;
		$num_fields_displayed = 0;
		$this->fds            = array();
		$this->fdn            = array();
		$this->displayed      = array();
		$this->guidance       = false;
		foreach (array_keys($this->fdd) as $key) {
			if (preg_match('/^\d+$/', $key)) { // skipping numeric keys
				continue;
			}
			$this->fds[$field_num] = $key;
			$this->fdn[$key] = $field_num;
			/* We must use here displayed() function, because displayed[] array
			   is not created yet. We will simultaneously create that array as well. */
			if ($this->displayed[$field_num] = $this->displayed($field_num)) {
				$num_fields_displayed++;
			}
			if (is_array(@$this->fdd[$key]['values']) && ! isset($this->fdd[$key]['values']['table'])) {
				foreach ($this->fdd[$key]['values'] as $val) {
					$this->fdd[$key]['values2'][$val] = $val;
				}
				unset($this->fdd[$key]['values']);
			}
			isset($this->fdd[$key]['help']) && $this->guidance = true;
			$this->fdd[$field_num] = $this->fdd[$key];
			$field_num++;
		}
		$this->num_fds              = $field_num;
		$this->num_fields_displayed = $num_fields_displayed;
		$this->key_num              = array_search($this->key, $this->fds);
		/* Adds first displayed column into sorting fields by replacing last
		   array entry. Also remove duplicite values and change column names to
		   their particular field numbers.

		   Note that entries like [0]=>'9' [1]=>'-9' are correct and they will
		   have desirable sorting behaviour. So there is no need to remove them.
		 */
		$this->sfn = array_unique($this->sfn);
		$check_ar = array();
		foreach ($this->sfn as $key => $val) {
			if (preg_match('/^[-]?\d+$/', $val)) { // skipping numeric keys
				$val = abs($val);
				if (in_array($val, $check_ar) || $this->password($val)) {
					unset($this->sfn[$key]);
				} else {
					$check_ar[] = $val;
				}
				continue;
			}
			if ($val[0] == '-') {
				$val = substr($val, 1);
				$minus = '-';
			} else {
				$minus = '';
			}
			if (($val = array_search($val, $this->fds)) === false || $this->password($val)) {
				unset($this->sfn[$key]);
			} else {
				$val = intval($val);
				if (in_array($val, $check_ar)) {
					unset($this->sfn[$key]);
				} else {
					$this->sfn[$key] = $minus.$val;
					$check_ar[] = $val;
				}
			}
		}
		$this->sfn = array_unique($this->sfn);
		return true;
	} /* }}} */

	function backward_compatibility() /* {{{ */
	{
		foreach (array_keys($this->fdd) as $column) {
			// move ['required'] to ['js']['required']
			if (! isset($this->fdd[$column]['js']['required']) && isset($this->fdd[$column]['required'])) {
				$this->fdd[$column]['js']['required'] = $this->fdd[$column]['required'];
			}
			// move 'HWR' flags from ['options'] into ['input']
			if (isset($this->fdd[$column]['options'])) {
				stristr($this->fdd[$column]['options'], 'H') && $this->fdd[$column]['input'] .= 'H';
				stristr($this->fdd[$column]['options'], 'W') && $this->fdd[$column]['input'] .= 'W';
				stristr($this->fdd[$column]['options'], 'R') && $this->fdd[$column]['input'] .= 'R';
			}
		}
	} /* }}} */

	/*
	 * Error handling function
	 */
	function error($message, $additional_info = '') /* {{{ */
	{
		echo '<h1>phpMyEdit error: ',htmlspecialchars($message),'</h1>',"\n";
		if ($additional_info != '') {
			echo '<hr size="1" />',htmlspecialchars($additional_info);
		}
		return false;
	} /* }}} */

	/*
	 * Database connection function
	 */
	function connect() /* {{{ */
	{
		if (isset($this->dbh)) {
			return true;
		}
		if (!isset($this->db)) {
			$this->error('no database defined');
			return false;
		}
		if (!isset ($this->tb)) {
			$this->error('no table defined');
			return false;
		}
		$this->sql_connect();
		if (!$this->dbh) {
			$this->error('could not connect to SQL');
			return false;
		}
		return true;
	} /* }}} */
	
	/*
	 * The workhorse
	 */
	function execute() /* {{{ */
	{
		//  DEBUG -  uncomment to enable
		/*
		//phpinfo();
		$this->print_get_vars();
		$this->print_post_vars();
		$this->print_vars();
		echo "<pre>query opts:\n";
		echo print_r($this->query_opts);
		echo "</pre>\n";
		echo "<pre>get vars:\n";
		echo print_r($this->get_opts);
		echo "</pre>\n";
		 */

		// Let's do explicit quoting - it's safer
		set_magic_quotes_runtime(0);
		// Checking if language file inclusion was successful
		if (! is_array($this->labels)) {
			$this->error('could not locate language files', 'searched path: '.$this->dir['lang']);
			return false;
		}
		// Database connection
		if ($this->connect() == false) {
			return false;
		}

		/*
		 * ======================================================================
		 * Pass 3: process any updates generated if the user has selected
		 * a save or cancel button during Pass 2
		 * ======================================================================
		 */
		// Cancel button - Cancel Triggers
		if ($this->add_canceled() || $this->copy_canceled()) {
			$this->exec_triggers_simple('insert', 'cancel');
		}
		if ($this->view_canceled()) {
			$this->exec_triggers_simple('select', 'cancel');
		}
		if ($this->change_canceled()) {
			$this->exec_triggers_simple('update', 'cancel');
		}
		if ($this->delete_canceled()) {
			$this->exec_triggers_simple('delete', 'cancel');
		}
		// Save/More Button - database operations
		if ($this->saveadd == $this->labels['Save'] || $this->savecopy == $this->labels['Save']) {
			$this->add_enabled() && $this->do_add_record();
			unset($this->saveadd);
			unset($this->savecopy);
			$this->recreate_fdd();
		}
		elseif ($this->moreadd == $this->labels['More']) {
			$this->add_enabled() && $this->do_add_record();
			$this->operation = $this->labels['Add']; // to force add operation
			$this->recreate_fdd();
			$this->recreate_displayed();
			$this->backward_compatibility();
		}
		elseif ($this->savechange == $this->labels['Save']) {
			$this->change_enabled() && $this->do_change_record();
			unset($this->savechange);
			$this->recreate_fdd();
		}
		elseif ($this->morechange == $this->labels['Apply']) {
			$this->change_enabled() && $this->do_change_record();
			$this->operation = $this->labels['Change']; // to force change operation
			$this->recreate_fdd();
			$this->recreate_displayed();
			$this->backward_compatibility();
		}
		elseif ($this->savedelete == $this->labels['Delete']) {
			$this->delete_enabled() && $this->do_delete_record();
			unset($this->savedelete);
			$this->recreate_fdd();
		}

		/*
		 * ======================================================================
		 * Pass 2: display an input/edit/confirmation screen if the user has
		 * selected an editing button on Pass 1 through this page
		 * ======================================================================
		 */
		if ($this->add_operation()
				|| $this->change_operation() || $this->delete_operation()
				|| $this->view_operation()   || $this->copy_operation()) {
			$this->display_record();
		}

		/*
		 * ======================================================================
		 * Pass 1 and Pass 3: display the SQL table in a scrolling window on
		 * the screen (skip this step in 'Add More' mode)
		 * ======================================================================
		 */
		else {
			$this->list_table();
		}

		$this->sql_disconnect();
		if ($this->display['time'] && $this->timer != null) {
			echo $this->timer->end(),' miliseconds';
		}
	} /* }}} */

	/*
	 * Class constructor
	 */
	function phpMyEdit($opts) /* {{{ */
	{
		// Set desirable error reporting level
		$error_reporting = error_reporting(E_ALL & ~E_NOTICE);
		// Database handle variables
		$this->sql_delimiter();
		if (isset($opts['dbh'])) {
			$this->close_dbh = false;
			$this->dbh = $opts['dbh'];
			$this->dbp = '';
		} else {
			$this->close_dbh = true;
			$this->dbh = null;
			$this->dbp = $this->sd.$opts['db'].$this->ed.'.';
			$this->hn  = $opts['hn'];
			$this->un  = $opts['un'];
			$this->pw  = $opts['pw'];
			$this->db  = $opts['db'];
		}
		$this->tb  = $opts['tb'];
		// Other variables
		$this->key       = $opts['key'];
		$this->key_type  = $opts['key_type'];
		$this->inc       = $opts['inc'];
		$this->options   = $opts['options'];
		$this->fdd       = $opts['fdd'];
		$this->multiple  = intval($opts['multiple']);
		$this->multiple <= 0 && $this->multiple = 2;
		$this->filters   = is_array(@$opts['filters']) ? join(' AND ', $opts['filters']) : @$opts['filters'];
		$this->triggers  = @$opts['triggers'];
		$this->notify    = @$opts['notify'];
		$this->logtable  = @$opts['logtable'];
		$this->page_name = @$opts['page_name'];
		if (! isset($this->page_name)) {
			$this->page_name = basename($this->get_server_var('PHP_SELF'));
			isset($this->page_name) || $this->page_name = $this->tb;
		} 
		$this->display['query'] = @$opts['display']['query'];
		$this->display['sort']  = @$opts['display']['sort'];
		$this->display['time']  = @$opts['display']['time'];
		if ($this->display['time']) {
			$this->timer = new phpMyEdit_timer();
		}
		$this->display['tabs'] = isset($opts['display']['tabs'])
			? $opts['display']['tabs'] : true;
		$this->display['form'] = isset($opts['display']['form'])
			? $opts['display']['form'] : true;
		$this->display['num_records'] = isset($opts['display']['num_records'])
			? $opts['display']['num_records'] : true;
		$this->display['num_pages'] = isset($opts['display']['num_pages'])
			? $opts['display']['num_pages'] : true;
		// Creating directory variables
		$this->dir['root'] = dirname(realpath(__FILE__))
			. (strlen(dirname(realpath(__FILE__))) > 0 ? '/' : '');
		$this->dir['lang'] = $this->dir['root'].'lang/';
		// Creating URL variables
		$this->url['images'] = 'images/';
		isset($opts['url']['images']) && $this->url['images'] = $opts['url']['images'];
		// CSS classes policy
		$this->css = @$opts['css'];
		!isset($this->css['separator']) && $this->css['separator'] = '-';
		!isset($this->css['prefix'])    && $this->css['prefix']    = 'pme';
		!isset($this->css['page_type']) && $this->css['page_type'] = false;
		!isset($this->css['position'])  && $this->css['position']  = false;
		!isset($this->css['divider'])   && $this->css['divider']   = 2;
		$this->css['divider'] = intval(@$this->css['divider']);
		// JS overall configuration
		$this->js = @$opts['js'];
		!isset($this->js['prefix']) && $this->js['prefix'] = 'PME_js_';
		// DHTML overall configuration
		$this->dhtml = @$opts['dhtml'];
		!isset($this->dhtml['prefix']) && $this->dhtml['prefix'] = 'PME_dhtml_';
		// Navigation
		$this->navigation = @$opts['navigation'];
		if (! $this->nav_buttons() && ! $this->nav_text_links() && ! $this->nav_graphic_links()) {
			$this->navigation .= 'B'; // buttons are default
		}
		if (! $this->nav_up() && ! $this->nav_down()) {
			$this->navigation .= 'D'; // down position is default
		}
		$this->buttons = $opts['buttons'];
		// Language labels (must go after navigation)
		$this->labels = $this->make_language_labels(isset($opts['language'])
				? $opts['language'] : $this->get_server_var('HTTP_ACCEPT_LANGUAGE'));
		// CGI variables
		$this->cgi = @$opts['cgi'];
		$this->cgi['persist'] = '';
		if (@is_array($opts['cgi']['persist'])) {
			foreach ($opts['cgi']['persist'] as $key => $val) {
				if (is_array($val)) {
					foreach($val as $key2 => $val2) {
						$this->cgi['persist'] .= '&'.rawurlencode($key)
							.'['.rawurlencode($key2).']='.rawurlencode($val2);
					}
				} else {
					$this->cgi['persist'] .= '&'.rawurlencode($key).'='.rawurlencode($val);
				}
			}
		}
		foreach (array('operation', 'sys', 'data') as $type) {
			if (! isset($this->cgi['prefix'][$type])) {
				$this->cgi['prefix'][$type] = $this->get_default_cgi_prefix($type);
			}
		}
		// Sorting variables
		$this->sfn   = $this->get_sys_cgi_var('sfn');
		isset($this->sfn)             || $this->sfn          = array();
		is_array($this->sfn)          || $this->sfn          = array($this->sfn);
		isset($opts['sort_field'])    || $opts['sort_field'] = array();
		is_array($opts['sort_field']) || $opts['sort_field'] = array($opts['sort_field']);
		$this->sfn   = array_merge($this->sfn, $opts['sort_field']);
		// Form variables all around
		$this->fl    = intval($this->get_sys_cgi_var('fl'));
		$this->fm    = intval($this->get_sys_cgi_var('fm'));
//		$old_page = ceil($this->fm / abs($this->inc)) + 1;
		$this->qfn   = $this->get_sys_cgi_var('qfn');
		$this->sw    = $this->get_sys_cgi_var('sw');
		$this->rec   = $this->get_sys_cgi_var('rec', '');
		$this->navop = $this->get_sys_cgi_var('navop');
		$navfmup     = $this->get_sys_cgi_var('navfmup');
		$navfmdown   = $this->get_sys_cgi_var('navfmdown');
		$navpnup     = $this->get_sys_cgi_var('navpnup');
		$navpndown   = $this->get_sys_cgi_var('navpndown');
		if($navfmdown!=NULL && $navfmdown != $this->fm) $this->navfm = $navfmdown;
		elseif($navfmup!=NULL && $navfmup != $this->fm) $this->navfm = $navfmup;
		elseif($navpndown!=NULL && ($navpndown-1)*$this->inc != $this->fm) $this->navfm = ($navpndown-1)*$this->inc;
		elseif($navpnup!=NULL && ($navpnup-1)*$this->inc != $this->fm) $this->navfm = ($navpnup-1)*$this->inc;
		else $this->navfm = $this->fm; 
		$this->operation = $this->get_sys_cgi_var('operation');
		$oper_prefix_len = strlen($this->cgi['prefix']['operation']);
		if (! strncmp($this->cgi['prefix']['operation'], $this->operation, $oper_prefix_len)) {
			$this->operation = $this->labels[substr($this->operation, $oper_prefix_len)];
		}
		$this->saveadd      = $this->get_sys_cgi_var('saveadd');
		$this->moreadd      = $this->get_sys_cgi_var('moreadd');
		$this->canceladd    = $this->get_sys_cgi_var('canceladd');
		$this->savechange   = $this->get_sys_cgi_var('savechange');
		$this->morechange   = $this->get_sys_cgi_var('morechange');
		$this->cancelchange = $this->get_sys_cgi_var('cancelchange');
		$this->savecopy     = $this->get_sys_cgi_var('savecopy');
		$this->cancelcopy   = $this->get_sys_cgi_var('cancelcopy');
		$this->savedelete   = $this->get_sys_cgi_var('savedelete');
		$this->canceldelete = $this->get_sys_cgi_var('canceldelete');
		$this->cancelview   = $this->get_sys_cgi_var('cancelview');
		// Filter setting
		if (isset($this->sw)) {
			$this->sw == $this->labels['Search'] && $this->fl = 1;
			$this->sw == $this->labels['Hide']   && $this->fl = 0;
			//$this->sw == $this->labels['Clear']  && $this->fl = 0;
		}
		// TAB names
		$this->tabs = array();
		// Setting key_delim according to key_type
		if ($this->key_type == 'real') {
			/* If 'real' key_type does not work,
			   try change MySQL datatype from float to double */
			$this->rec = doubleval($this->rec);
			$this->key_delim = '';
		} elseif ($this->key_type == 'int') {
			$this->rec = intval($this->rec);
			$this->key_delim = '';
		} else {
			$this->key_delim = '"';
			// $this->rec remains unmodified
		}
		// Specific $fdd modifications depending on performed action
		$this->recreate_fdd();
		// Extract SQL Field Names and number of fields
		$this->recreate_displayed();
		// Issue backward compatibility
		$this->backward_compatibility();
		// Gathering query options
		$this->gather_query_opts();
		// Call to action
		!isset($opts['execute']) && $opts['execute'] = 1;
		$opts['execute'] && $this->execute();
		// Restore original error reporting level
		error_reporting($error_reporting);
	} /* }}} */

}

/* Modeline for ViM {{{
 * vim:set ts=4:
 * vim600:fdm=marker fdl=0 fdc=0:
 * }}} */

?>
