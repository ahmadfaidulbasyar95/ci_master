<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_keyword extends lib_pea_frm_text
{	
	public $searchField  = array();
	public $is_full_text = 0;

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function addSearchField($field = '', $is_full_text = 0)
	{
		$this->searchField  = explode(',', $field);
		$this->is_full_text = ($is_full_text) ? 1 : 0;
	}

	public function getSearchSql()
	{
		$value = $this->getValue();
		if (!$value and $value != '0') return '';
		else {
			$sql = array();
			foreach ($this->searchField as $field) {
				$sql[] = ($this->is_full_text) ? '`'.$field.'` = "'.addslashes($value).'"' : '`'.$field.'` LIKE "%'.addslashes($value).'%"';
			}
			return ($sql) ? (count($sql) > 1) ? '('.implode(' OR ', $sql).')' : implode(' OR ', $sql) : '';
		}
	}
}