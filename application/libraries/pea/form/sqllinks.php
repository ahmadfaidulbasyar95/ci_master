<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_sqllinks extends lib_pea_frm_text
{	
	public $link      = '';
	public $getName   = 'id';
	public $toolModal = '';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setPlainText();
		$this->setLinks($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}

	public function setModal()
	{
		$this->toolModal .= 'modal_processing';
		$this->setIncludes('modal.min', 'js');
	}

	public function setModalReload()
	{
		$this->toolModal .= ' modal_reload';
	}

	public function setModalLarge()
	{
		$this->toolModal .= ' modal_large';
	}

	public function setLinks($link = '')
	{
		if ($link) {
			if (strpos($link, '://') === false) {
				$link = $this->_url.$link;
			}
			$this->link = $link;
		}
	}

	public function getLinks($index = '')
	{
		$link = $this->link;
		$link .= (strpos($link, '?') === false) ? '?'.$this->getName.'='.urlencode($this->getValueID($index)) : '&'.$this->getName.'='.urlencode($this->getValueID($index)) ;
		return $link.'&return='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}

	public function setGetName($getName = '')
	{
		if ($getName) $this->getName = $getName;
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
		$value = '<a class="'.$this->toolModal.' '.$this->attr_class.'" href="'.$this->getLinks($index).'" '.$this->attr.'>'.$value.'</a>';
		$form .= ($this->init == 'roll' or $this->isMultiinput) ? $value : '<p>'.$value.'</p>';
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}