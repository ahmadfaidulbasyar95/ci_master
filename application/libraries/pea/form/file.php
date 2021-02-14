<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../../../../system/libraries/Upload.php';
include_once __DIR__.'/../../../../system/libraries/Image_lib.php';
include_once __DIR__.'/../../path.php';
include_once __DIR__.'/text.php';
class lib_pea_frm_file extends lib_pea_frm_text
{	
	public $fileFolder          = '';
	public $fileExtAllowed      = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
	public $fileImgSize         = 0;
	public $fileImgSizeThumb    = 0;
	public $fileImgSizeThumbPre = 0;
	public $imageClick          = 0;
	public $newValue            = '';
	public $newValue_roll       = array();
	public $oldValue            = '';
	public $oldValue_roll       = array();
	public $toolModal           = '';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->upload    = new CI_Upload();
		$this->image_lib = new CI_Image_lib();
		$this->setFolder('files/uploads/');
	}

	public function setNewValue($newValue = '', $index = '')
	{
		if (is_numeric($index)) $this->newValue_roll[$index] = $newValue;
		else $this->newValue = $newValue;
	}

	public function getNewValue($index = '')
	{
		$newValue = (is_numeric($index)) ? @$this->newValue_roll[$index] : $this->newValue;
		return $newValue;
	}

	public function setOldValue($oldValue = '', $index = '')
	{
		if (is_numeric($index)) $this->oldValue_roll[$index] = $oldValue;
		else $this->oldValue = $oldValue;
	}

	public function getOldValue($index = '')
	{
		$oldValue = (is_numeric($index)) ? @$this->oldValue_roll[$index] : $this->oldValue;
		return $oldValue;
	}

	public function getValue($index = '')
	{
		$value = (is_numeric($index)) ? @$this->value_roll[$index] : $this->value;
		return $value;
	}

	public function setFolder($fileFolder='')
	{
		$fileFolder = $this->_root.str_replace($this->_root, '', $fileFolder);  
		if (lib_path_create($fileFolder)) {
			$this->fileFolder = $fileFolder;
		}
	}

	public function setAllowedExtension($fileExtAllowed = array())
	{
		if ($fileExtAllowed and is_array($fileExtAllowed)) $this->fileExtAllowed = $fileExtAllowed;
	}

	public function setResize($fileImgSize = 0)
	{
		$this->fileImgSize = intval($fileImgSize);
	}

	public function setThumbnail($fileImgSizeThumb = 0, $fileImgSizeThumbPre = 'thumb')
	{
		$this->fileImgSizeThumb    = intval($fileImgSizeThumb);
		$this->fileImgSizeThumbPre = $fileImgSizeThumbPre;
	}

	public function setImageClick()
	{
		$this->imageClick = 1;
		$this->toolModal .= 'image_viewer" data-magnify="gallery" data-group="'.$this->table.'_'.$this->init;
		$this->setIncludes('image_viewer/css/jquery.magnify.min', 'css');
		$this->setIncludes('image_viewer/js/jquery.magnify.min', 'js');
		$this->setIncludes('image_viewer.min', 'js');
	}

	public function getPostValue($index = '')
	{
		$config      = array();
		$upload_name = (is_numeric($index)) ? $this->getName().'__'.$index : $this->getName();
		if (@$_FILES[$upload_name]['name']) {
			if ($this->fileFolder) $config['upload_path']       = $this->fileFolder;
			if ($this->fileExtAllowed) $config['allowed_types'] = implode('|', $this->fileExtAllowed);
			$this->upload->initialize($config);
			if ($this->upload->do_upload($upload_name)) {
				$this->setNewValue($this->upload->data()['file_name'], $index);
				$this->setOldValue($this->getValue($index), $index);
			}else{
				$this->msg = str_replace('{msg}', $this->upload->display_errors(), $this->failMsgTpl);
			}
		}
		$value              = $this->getNewValue($index);
		if (!$value) $value = $this->getValue($index);
		if ($this->getRequire()) {
			if (!$value and !$this->msg) {
				$this->msg = str_replace('{msg}', str_replace('{title}', $this->title, @$this->failMsg['require']), $this->failMsgTpl);
			}
		}
		return $value;
	}

	public function onSaveSuccess($index = '')
	{
		$newValue = $this->getNewValue($index);
		if ($newValue) {
			if ($this->fileImgSize) {
				$config                   = array();
				$config['source_image']   = $this->fileFolder.$newValue;
				$config['width']          = $this->fileImgSize;
				$config['height']         = $this->fileImgSize;
				$config['maintain_ratio'] = TRUE;
				$this->image_lib->initialize($config);
				$this->image_lib->resize();
				if ($this->fileImgSizeThumb) {
					if (preg_match('~\/$~', $this->fileImgSizeThumbPre)) {
						lib_path_create($this->fileFolder.$this->fileImgSizeThumbPre);
					}
					copy($this->fileFolder.$newValue, $this->fileFolder.$this->fileImgSizeThumbPre.$newValue);
					$config['source_image']   = $this->fileFolder.$this->fileImgSizeThumbPre.$newValue;
					$config['width']          = $this->fileImgSizeThumb;
					$config['height']         = $this->fileImgSizeThumb;
					$this->image_lib->initialize($config);
					$this->image_lib->resize();
				}
			}
			$oldValue = $this->getOldValue($index);
			if (is_file($this->fileFolder.$oldValue)) unlink($this->fileFolder.$oldValue);
			if ($this->fileImgSizeThumb) {
				if (is_file($this->fileFolder.$this->fileImgSizeThumbPre.$oldValue)) unlink($this->fileFolder.$this->fileImgSizeThumbPre.$oldValue);
			}
		}
	}

	public function onSaveFailed($index = '')
	{
		$newValue = $this->getNewValue($index);
		if (is_file($this->fileFolder.$newValue)) unlink($this->fileFolder.$newValue);
	}

	public function onDeleteSuccess($index = '')
	{
		$value = $this->getValue($index);
		if (is_file($this->fileFolder.$value)) unlink($this->fileFolder.$value);
		if ($this->fileImgSizeThumb) {
			if (is_file($this->fileFolder.$this->fileImgSizeThumbPre.$value)) unlink($this->fileFolder.$this->fileImgSizeThumbPre.$value);
		}
	}

	public function getReportOutput($value = '')
	{
		if ($value and is_file($this->fileFolder.$value)) {
			return str_replace($this->_root, $this->_url, $this->fileFolder).$value;
		}
		return '';
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		$value = $this->getValue($index);
		if ($value) $this->isRequire = '';
		$value_text = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
		if ($value and is_file($this->fileFolder.$value)) {
			$link = str_replace($this->_root, $this->_url, $this->fileFolder).$value;
			if ($this->imageClick) {
				$value_text = '<img class="img-thumbnail" style="height: 25px;" src="'.$link.'">'.$value_text;
			}
			if ($this->init != 'roll') {
				$value_text = '<p>'.$value_text.'</p>';
			}
			$form .= '<a class="'.$this->toolModal.'" href="'.$link.'" target="_BLANK">'.$value_text.'</a>';
		}
		if (!$this->isPlainText) {
			$name = (is_numeric($index)) ? $this->name.'__'.$index : $this->name;
			// $name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<input type="file" name="'.$name.'" class="form-control '.$this->attr_class.'" value="" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.'>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}