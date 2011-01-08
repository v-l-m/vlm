<?php

/*
 * phpMyEdit - instant MySQL table editor and code generator
 *
 * extensions/phpMyEdit-slide.class.php - slide show extension for phpMyEdit
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

/* $Platon: phpMyEdit/extensions/phpMyEdit-slide.class.php,v 1.11 2007-09-02 22:30:00 nepto Exp $ */

/*
 * Coding elapsed time: from 8:30 to 10:30 at 30th October 2002
 * with heavy patching phpMyEdit core class.
 *
 * Music used: E-Type (Campione, This is the Way and others)
 */

require_once dirname(__FILE__).'/../phpMyEdit.class.php';

class phpMyEdit_slide extends phpMyEdit
{
	// Extension options array
	var $ext;

	function phpMyEdit_slide($opts) /* {{{ */
	{
		$execute = 1;
		isset($opts['execute']) && $execute = $opts['execute'];
		$opts['execute'] = 0;
		parent::phpMyEdit($opts);

		$this->ext = $opts['ext'];

		$execute && $this->execute($opts);
	} /* }}} */

	function execute($opts) /* {{{ */
	{
		if ($this->get_sys_cgi_var('rec_change')
				&& ($this->next_operation() || $this->prev_operation())) {
			$this->operation = $this->labels['Change'];
		}
		if (! $this->change_operation()) {
			$this->operation = $this->labels['View'];
		}
		if ($this->prev_operation()) {
			! $this->ext['prev_disabled'] && $this->rec = $this->get_sys_cgi_var('rec_prev');
			$this->prev = '';
		}
		if ($this->next_operation()) {
			! $this->ext['next_disabled'] && $this->rec = $this->get_sys_cgi_var('rec_next');
			$this->next = '';
		}
		if (! $this->rec) {
			$this->rec = $this->ext['rec'];
		}

		if (! $this->rec
				|| (! $this->ext['prev_disable'] && ! $this->ext['prev'])
				|| (! $this->ext['next_disable'] && ! $this->ext['next'])) {
			if ($this->connect() == false) {
				return false;
			}
			$query_parts = array(
					'type'   => 'select',
					// XXX FIXME - simplify query
					'select' => 'PMEtable0.'.$this->key,
					'from'   => $this->get_SQL_join_clause(),
					'where'  => $this->get_SQL_where_from_query_opts());
			// TODO: order by clausule according to default sort order options
			$res = $this->myquery($this->get_SQL_query($query_parts), __LINE__);
			$ids = array();
			while (($row = @mysql_fetch_array($res, MYSQL_NUM)) !== false) {
				$ids[] = $row[0];
			}
			@mysql_free_result($res);
			if ($this->rec) {
				$idx = array_search($this->rec, $ids);
				$idx === false && $idx = 0;
			} else {
				$idx = 0;
			}

			$this->rec = $ids[$idx];
			! $this->ext['prev'] && $this->ext['prev'] = $ids[$idx - 1];
			! $this->ext['next'] && $this->ext['next'] = $ids[$idx + 1];
		}

		$this->default_buttons['V'] = array('change', 'cancel',
				(isset($this->ext['prev']) ? '+prev' : 'prev'),
				(isset($this->ext['next']) ? '+next' : 'next'),
				array('code'=>'<input type="hidden" name="'
					.$this->cgi['prefix']['sys'].'rec_prev" value="'.$this->ext['prev'].'">'),
				array('code'=>'<input type="hidden" name="'
					.$this->cgi['prefix']['sys'].'rec_next" value="'.$this->ext['next'].'">')
				);
	
		$this->recreate_fdd();
		$this->recreate_displayed();
		parent::execute();
	} /* }}} */

}

/* Modeline for ViM {{{
 * vim:set ts=4:
 * vim600:fdm=marker fdl=0 fdc=0:
 * }}} */

?>
