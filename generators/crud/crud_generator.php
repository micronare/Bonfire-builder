<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crud_Generator extends BF_Generator {

	public $title		= 'CRUD Generator';
	public $description	= 'Creates a controller and all of the views needed for basic Create, Read, Update and Delete functionality, from an existing database table';
	public $templates	= array(
		'controller'	=> 'crud_controller.php',
		'create'		=> 'crud_form.php',
		'update'		=> 'crud_form.php',
		'delete'		=> 'crud_delete.php',
		'list'			=> 'crud_list.php'
	);
	
	protected $before_replace_vars 	= array();
	protected $after_replace_vars	= array('render_validation_rules', 'render_save_data_array');

	// Our form fields and validation needs
	protected $fields	= array(
		// Context
		'context'	=> array(
			'type'		=> 'context',
			'help'		=> 'What context does this controller belong to?',
			'display_name'	=> 'Context',
			'rules'			=> 'required|trim|strip_tags|xss_clean'
		),
		// Database Table
		'table'		=> array(
			'type'		=> 'db_table',
			'help'		=> 'The existing Database table to use.',
			'display_name'	=> 'DB Table Name',
			'rules'			=> 'required|trim|strip_tags|xss_clean',
			'form_type'		=> 'db_table_select'
		),
	);

	// The files to create
	protected $files = array(
		'{context}.php'	=> array(
			'template'	=> 'crud_controller',
			'folder'	=> 'controllers'
		),
		'index.php'	=> array(
			'template'	=> 'crud_index',
			'folder'	=> 'views/{context}'
		),
		'create.php'	=> array(
			'template'	=> 'crud_create',
			'folder'	=> 'views/{context}'
		),
		'edit.php'	=> array(
			'template'	=> 'crud_edit',
			'folder'	=> 'views/{context}'
		),
		'delete.php'	=> array(
			'template'	=> 'crud_delete',
			'folder'	=> 'views/{context}'
		),
	);

	//--------------------------------------------------------------------

	public function get_vars($params)
	{
		$vars = array(
			'module'		=> $params['module'],
			'context'		=> $params['context'],
			'context_ucf'	=> ucfirst($params['context']),
			'extend'		=> $params['context'] != 'public' ? 'Admin_Controller' : 'Front_Controller',
		);
		
		$vars['fields']	= parent::get_vars($params);

		return $vars;
	}

	//--------------------------------------------------------------------

	public function render_validation_rules($params) 
	{
		$filename	= isset($params['filename']) ? $params['filename'] : '';
		$tpl		= isset($params['tpl']) ? $params['tpl'] : '';
		$vars		= isset($params['vars']) ? $params['vars'] : '';
	
		$rules = '';
		
		foreach ($vars['fields'] as $fieldname => $opts)
		{
			$rules .= "\t\t\$this->form_validation->set_rules('{$fieldname}', '{$opts['display_name']}', '{$opts['rules']}');\n";
		}
		
		$tpl = str_replace('{validation_rules}', $rules, $tpl);
	
		return $tpl;
	}
	
	//--------------------------------------------------------------------
	
	public function render_save_data_array($params) 
	{
		$filename	= isset($params['filename']) ? $params['filename'] : '';
		$tpl		= isset($params['tpl']) ? $params['tpl'] : '';
		$vars		= isset($params['vars']) ? $params['vars'] : '';
		
		return $tpl;
	}
	
	//--------------------------------------------------------------------
}