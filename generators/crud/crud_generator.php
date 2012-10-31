<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crud_Generator extends BF_Generator {

	public $title		= 'CRUD Generator';
	public $description	= 'Creates a controller and all of the views needed for basic Create, Read, Update and Delete functionality, from an existing database table';
	public $templates	= array(
		'controller'	=> 'crud_controller.php',
		'create'		=> 'crud_form.php',
		'update'		=> 'crud_form.php',
		'list'			=> 'crud_list.php'
	);

	protected $before_replace_vars 	= array();
	protected $after_replace_vars	= array('render_validation_rules', 'render_views');

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
		'{module}_lang.php'	=> array(
			'template'	=> 'crud_lang',
			'folder'	=> 'language/english'
		)
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
		$vars		= isset($params['vars']) ? $params['vars'] : '';

		if ($filename !== $vars['context'] .'.php')
			return;

		$rules = '';

		if (isset($vars['fields']) && is_array($vars['fields']))
		{
			foreach ($vars['fields'] as $fieldname => $opts)
			{
				$rules .= "\t\t\$this->form_validation->set_rules('{$fieldname}', '{$opts['display_name']}', '{$opts['rules']}');\n";
			}
		}

		$this->tpl = str_replace('{validation_rules}', $rules, $this->tpl);
	}

	//--------------------------------------------------------------------

	public function render_views($params)
	{
		$filename	= isset($params['filename']) ? $params['filename'] : '';
		$vars		= isset($params['vars']) ? $params['vars'] : '';

		//if (!isset($vars['fields']) || empty($vars['fields']) || !is_array($vars['fields']))
		//	return;

		$form_fields = '';

die('<pre>'. print_r($vars['fields'], true));
		$this->ci->load->helper('form');

		foreach ($vars['fields'] as $name => $opts)
		{
			switch (strtolower($opts['field_type']))
			{
				case 'checkbox':
					$form_fields .= form_checkbox($name, 1);
					break;
				case 'date':
					$form_fields .= form_date( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'datetime':
					$form_fields .= form_datetime( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'dropdown':
					$options = isset($opts['options']) ? $opts['options'] : array();
					$form_fields .= form_dropdown( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>"), $options );
					break;
				case 'email':
					$form_fields .= form_email( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'input':
					$form_fields .= form_input( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'month':
					$form_fields .= form_month( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'number':
					$form_fields .= form_number( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'range':
					$form_fields .= form_range( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
				case 'textarea':
					$rows = 10;
					$cols = 40;
					$form_fields .= form_input( array('name'=>$name, 'class'=>'input-xxlarge', 'value'=>"<?php echo set_value('$name') ?>", 'rows'=>$rows, 'cols'=>$cols) );
					break;
				case 'url':
					$form_fields .= form_url( array('name'=>$name, 'class'=>'input-xlarge', 'value'=>"<?php echo set_value('$name') ?>") );
					break;
			}
		}


		//echo '<pre>';
		//die(print_r($params));

		$this->tpl = str_replace('{form_fields}', $form_fields, $this->tpl);
	}

	//--------------------------------------------------------------------

}