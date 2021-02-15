<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_textarea extends lib_pea_frm_text
{	
	public $toolHtmlEditor = '';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function setHtmlEditor()
	{
		$this->toolHtmlEditor .= ' textarea_html_editor" id="textarea_html_editor_{name}';
		$this->setIncludes('ckeditor/ckeditor', 'js');
		$this->setIncludes('ckeditor.min', 'js');
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '<td>';
		$form .= $this->formBefore;
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= ($this->init == 'roll' or $this->isMultiinput) ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			// $name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<textarea name="'.$name.'" class="form-control'.str_replace('{name}', $this->name.'_'.$index, $this->toolHtmlEditor).' '.$this->attr_class.'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.'>'.$this->getValue($index).'</textarea>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}