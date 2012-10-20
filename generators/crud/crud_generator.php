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

}