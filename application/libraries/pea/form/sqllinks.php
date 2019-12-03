<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_sqllinks extends lib_pea_frm_text
{	
	public $link    = '';
	public $getName = 'id';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setPlainText();
		$this->setLinks($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}

	public function setLinks($link = '')
	{
		if ($link) $this->link = $link;
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
		if ($this->init == 'roll') $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
		$value = '<a href="'.$this->getLinks($index).'">'.$value.'</a>';
		$form .= ($this->init == 'roll') ? $value : '<p>'.$value.'</p>';
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}