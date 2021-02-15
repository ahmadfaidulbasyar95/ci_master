<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/sqllinks.php';
class lib_pea_frm_editlinks extends lib_pea_frm_sqllinks
{	

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setFieldName($this->table_id);
	}

	public function getLinks($index = '')
	{
		$value = ($this->getFieldName()) ? $this->getValue($index) : $this->getValueID($index);
		$link = $this->link;
		$link .= (strpos($link, '?') === false) ? '?'.$this->getName.'='.urlencode($value) : '&'.$this->getName.'='.urlencode($value);
		return $link.'&return='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}
	
	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '<td>';
		$form .= $this->formBefore;
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		$value = '<a class="'.$this->toolModal.' '.$this->attr_class.'" href="'.$this->getLinks($index).'" '.$this->attr.'>'.$this->caption.'</a>';
		$form .= ($this->init == 'roll' or $this->isMultiinput) ? $value : '<p>'.$value.'</p>';
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}