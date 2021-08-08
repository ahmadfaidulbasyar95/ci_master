<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_zipcode extends lib_pea_frm_text
{	
	public $referenceTable      = '';
	public $referenceFieldValue = '';
	public $token_load          = 0;
	public $dependent           = array();

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setType('number');
	}

	public function setReferenceTable($referenceTable = '')
	{
		if ($referenceTable) $this->referenceTable = $referenceTable;
	}

	public function setReferenceField($value = '')
	{
		if ($value) $this->referenceFieldValue = $value;
	}

	public function setDependent($name = '', $field = '')
	{
		if ($name and $field) {
			$this->dependent = array(
				'name'  => $name,
				'field' => $field,
			);
			$this->setIncludes('zipcode','js');
		}else{
			$this->dependent = array();
		}
	}

	public function getToken()
	{
		if (!$this->token_load) {
			$this->token_load = 1;
			if (!$this->isPlainText and $this->dependent) {
				$token = 'SELECT '.$this->referenceFieldValue.' AS `value` FROM '.$this->referenceTable.' WHERE '.$this->dependent['field'].'="[v]" LIMIT 1';
				$this->db->load->model('_encrypt_model');
				$this->addAttr('data-token="'.$this->db->_encrypt_model->encodeToken($token, 60).'"');
				$this->addAttr('data-dependent="'.$this->dependent['name'].'"');
				$this->addClass('fm_zipcode');
			}
		}
	}

	public function getForm($index = '', $values = array())
	{
		$this->getToken();
		return parent::getForm($index, $values);
	}

}