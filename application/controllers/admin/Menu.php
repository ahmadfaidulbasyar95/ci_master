<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');
		$this->load->library('session');

		$this->_tpl_model->setTemplate('admin');
	}

	function index()
	{
		$id = @intval($_GET['id']);
		include_once $this->_tpl_model->_root.'application/libraries/tabs.php';

		$form = $this->_pea_model->newForm('menu');
		$form->initSearch();
		
		$form->search->addInput('position_id', 'selecttable');
		$form->search->input->position_id->setTitle('Menu Position');
		$form->search->input->position_id->setReferenceTable('menu_position');
		$form->search->input->position_id->setReferenceField( 'title', 'id' );
		$form->search->input->position_id->addOption( 'Admin', '' );
		$form->search->input->position_id->setAttr( 'onchange=\'$(this).parents("form").find("[type=\"submit\"]").trigger("click");\'' );
		
		$form->search->formWrap('<div style="float:right">','</div>');
		
		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		
		echo $form->search->getForm();
		
		$_GET['position_id'] = @intval($keyword['position_id']);
		if (isset($_POST[$form->search->input->position_id->getName()]) and $id) {
			redirect($form->_url.'admin/menu');
		}

		$_GET['id']     = 0;
		$_GET['par_id'] = $id;
		$add            = $this->form();
		
		$_GET['id'] = $id;
		$list       = $this->list();

		if ($id) {
			$edit = $this->form();
			
			echo lib_tabs(array(
				'Edit Menu'    => $edit,
				'Sub Menu'     => $list,
				'Add Sub Menu' => $add,
			));
		}else{
			echo lib_tabs(array(
				'Menu'     => $list,
				'Add Menu' => $add,
			));
		}

		$this->_tpl_model->js('controllers/admin/menu.js');
		$this->_tpl_model->show();
	}

	function list()
	{
		$id          = @intval($_GET['id']);
		$position_id = @intval($_GET['position_id']);
		$form        = $this->_pea_model->newForm('menu');

		$form->initRoll('WHERE `par_id`='.$id.' AND `position_id`='.$position_id.' ORDER BY `orderby` ASC');

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setLinks('admin/menu');

		$form->roll->addInput('url', 'sqlplaintext');
		$form->roll->input->url->setTitle('Real URL');
		$form->roll->input->url->setDisplayFunction(function($value='')
		{
			if (strpos($value, '://') === false) {
				return $value;
			}
			return '<a target="_BLANK" href="'.$value.'.html">'.$value.'</a>';
		});

		if ($position_id) {
			$form->roll->addInput('uri', 'sqlplaintext');
			$form->roll->input->uri->setTitle('SEO URL');
			$form->roll->input->uri->setDisplayFunction(function($value='')
			{
				return ($value and $value != '-') ? '<a target="_BLANK" href="'.$this->_pea_model->_url.$value.'.html">'.$value.'</a>' : '';
			});
		}else{
			$form->roll->addInput('shortcut', 'checkbox');
			$form->roll->input->shortcut->setTitle('Shortcut');
			$form->roll->input->shortcut->setCaption('show');
		}

		$form->roll->addInput('orderby', 'orderby');
		$form->roll->input->orderby->setTitle('Ordered');
		
		$form->roll->addInput('active', 'checkbox');
		$form->roll->input->active->setTitle('Active');
		$form->roll->input->active->setCaption('yes');
		
		$form->roll->action();
		return $form->roll->getForm();
	}

	function form()
	{
		$id          = @intval($_GET['id']);
		$position_id = @intval($_GET['position_id']);
		$par_id      = @intval($_GET['par_id']);
		$form        = $this->_pea_model->newForm('menu');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		$form->edit->setHeader(!empty($id) ? 'Edit Menu' : 'Add Menu');
		
		$form->edit->addInput('title','text');
		$form->edit->input->title->setTitle('Title');
		$form->edit->input->title->setRequire();
		$form->edit->input->title->setAttr('rel="menu_title"');

		$form->edit->addInput('icon','text');
		$form->edit->input->icon->setTitle('Icon Class');

		$form->edit->addInput('url','text');
		$form->edit->input->url->setTitle('Real URL');
		$form->edit->input->url->setRequire();
		$form->edit->input->url->setAttr('rel="menu_url"');
		$form->edit->input->url->addTip('This is the real link in the system, normal format will be [controller]/[method] you can also copy from URL bar and the system will automatically find out the real Link is.');
		
		$form->edit->addInput('url_type','hidden');
		$form->edit->input->url_type->setTitle('Real URL Type');
		$form->edit->input->url_type->setAttr('rel="menu_url_type"');

		if ($position_id) {
			$form->edit->addInput('uri_wrap', 'multiinput');
			$form->edit->input->uri_wrap->setTitle('Search Engine Optimization URL');

			$form->edit->input->uri_wrap->addInput('uri_1','plaintext');
			$form->edit->input->uri_wrap->element->uri_1->setDefaultValue($form->_url);

			$form->edit->input->uri_wrap->addInput('uri','text');
			$form->edit->input->uri_wrap->element->uri->setTitle('SEO URL');
			$form->edit->input->uri_wrap->element->uri->setRequire();
			$form->edit->input->uri_wrap->element->uri->setAttr('rel="menu_uri" data-id="'.$id.'"');

			if (isset($_POST[$form->edit->input->url_type->getName()])) {
				if ($_POST[$form->edit->input->url_type->getName()] == 1) {
					$_POST[$form->edit->input->uri_wrap->element->uri->getName()] = '-';
				}
			}
			
			$form->edit->input->uri_wrap->addInput('uri_2','plaintext');
			$form->edit->input->uri_wrap->element->uri_2->setDefaultValue('.html');
		}else{
			$form->edit->addInput('shortcut', 'checkbox');
			$form->edit->input->shortcut->setTitle('Shortcut');
			$form->edit->input->shortcut->setCaption('show');
			$form->edit->input->shortcut->setDefaultValue('');
		}

		$form->edit->addInput('active', 'checkbox');
		$form->edit->input->active->setTitle('Active');
		$form->edit->input->active->setCaption('yes');

		if (!$id and $par_id) {
			$form->edit->addExtraField('par_id', $par_id);
			$form->edit->addExtraField('position_id', $form->db->getOne('SELECT `position_id` FROM `menu` WHERE `id`='.$par_id));
		}elseif ($position_id) {
			$form->edit->addExtraField('position_id', $position_id);
		}
		
		$form->edit->action();
		return $form->edit->getForm();
	}

	function position()
	{
		$form = $this->_pea_model->newForm('menu_position');

		$form->initEdit();

		$form->edit->setHeader('Add Menu Position');

		$form->edit->addInput('title', 'text');
		$form->edit->input->title->setTitle('Title');
		
		$form->edit->action();
	
		$form->initRoll('WHERE 1 ORDER BY `title` ASC');

		$form->roll->setHeader('Menu Position');

		$form->roll->addInput('title', 'text');
		$form->roll->input->title->setTitle('Title');
		
		$form->roll->action();
		echo $form->roll->getForm();
		
		echo $form->edit->getForm();

		$this->_tpl_model->show();
	}
}
