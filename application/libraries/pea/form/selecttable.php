<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/select.php';
class lib_pea_frm_selecttable extends lib_pea_frm_select
{	
	public $referenceTable      = '';
	public $referenceFieldKey   = '';
	public $referenceFieldValue = '';
	public $referenceCondition  = '';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function setReferenceTable($referenceTable = '')
	{
		if ($referenceTable) $this->referenceTable = $referenceTable;
	}

	public function setReferenceField($key = '', $value = '')
	{
		if ($key) $this->referenceFieldKey     = $key;
		if ($value) $this->referenceFieldValue = $value;
	}

	public function setReferenceCondition($referenceCondition = '')
	{
		if ($referenceCondition) $this->referenceCondition = 'WHERE '.$referenceCondition;
	}

	public function getForm($index = '')
	{
		$option = array();
		if (isset($GLOBALS['pea_selecttable_'.$this->table.'_'.$this->init.'_'.$this->fieldNameDb])) {
			$option = $GLOBALS['pea_selecttable_'.$this->table.'_'.$this->init.'_'.$this->fieldNameDb];
		}else{
			$option = $this->db->getAll('SELECT '.$this->referenceFieldKey.' AS `key`, '.$this->referenceFieldValue.' AS `value` FROM '.$this->referenceTable.' '.$this->referenceCondition);
			$GLOBALS['pea_selecttable_'.$this->table.'_'.$this->init.'_'.$this->fieldNameDb] = $option;
		}
		foreach ($option as $value) {
			$this->addOption($value['key'], $value['value']);
		}
		return parent::getForm($index);
	}
}