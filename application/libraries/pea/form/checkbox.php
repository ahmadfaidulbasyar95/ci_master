<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_checkbox extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setDefaultValue(1);
		if ($this->init == 'roll') $this->setIncludes('checkall.min', 'js');
	}

	public function getPostValue($index = '')
	{
		return (is_numeric($index)) ? @intval($_POST[$this->getName()][$index]) : @intval($_POST[$this->getName()]);
	}

	public function getRollTitle($sortConfig = array(), $active = '', $is_desc = '')
	{
		return '
<div class="'.lib_bsv('checkbox', 'form-check').' checkall" style="float: left;margin: 0;">
	<label>
		<input type="checkbox" title="'.$this->title.'">
	</label>
</div>'.parent::getRollTitle($sortConfig, $active, $is_desc);
	}

	public function getReportOutput($value = '')
	{
		if ($value) {
			return $this->caption;
		}
		return '';
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= ($this->init == 'roll') ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$value = ($this->getValue($index)) ? 'checked="checked"' : '';
			$form .= '
<div class="'.lib_bsv('checkbox', 'form-check').'">
	<label>
		<input type="checkbox" name="'.$name.'" value="1" title="'.$this->caption.'" '.$this->attr.' '.$value.'>
		'.$this->caption.'
	</label>
</div>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}