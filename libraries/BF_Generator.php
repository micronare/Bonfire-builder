<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2012, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

/**
 * The base class for all generators to extend from. Contains a number of
 * stock methods that can be used while generating new code.
 *
 * @package    Bonfire
 * @subpackage Modules_Builder
 * @category   Libraries
 * @author     Bonfire Dev Team
 * @link       http://cibonfire.com/guides/core_modules/modulebuilder.html
 */
class BF_Generator {

	/**
	 * The title of the module. Used for GUI display only.
	 *
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * The description of the generator. Used for GUI display only.
	 *
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * An array of fields for our form.
	 *
	 * @access protected
	 * @var   array
	 */
	protected $fields = array();

	/**
	 * An array of files to be created.
	 *
	 * @access  protected
	 * @var   array
	 */
	protected $files = array();

	/**
	 * The name of the module that we're currently creating for.
	 *
	 * @access protected
	 * @var string
	 */
	protected $module = '';

	/**
     * Contains various settings from the modulebuilder config file
     *
     * @access protected
     * @var array
     */
	protected $options = array();

	/**
	 * The path to the generators folder.
	 *
	 * @access protected
	 * @var string
	 */
	protected $self_path;

	/**
	 * A pointer to the CI superglobal.
	 *
	 * @access protected
	 * @var Object
	 */
	protected $ci;

	/**
	 * The core database tables that we can ignore...
	 *
	 */
	protected $core_tables = array('activities', 'email_queue', 'login_attempts', 'permissions', 'role_permissions', 'roles', 'schema_version', 'sessions', 'settings', 'user_cookies', 'user_meta', 'users');

	//--------------------------------------------------------------------

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->config('modulebuilder');
        $this->options = $this->ci->config->item('modulebuilder');

        $this->self_path = str_replace('libraries', '', dirname(__FILE__)) .'generators/';
	}

	//--------------------------------------------------------------------

	/**
	 * Gets the contents of the index view for this generator. This method
	 * handles most of the work for child generator classes of displaying the form
	 * performing validation and running the generate method of the generator.
	 *
	 * @return string 	The contents of the rendered view.
	 */
	public function render()
	{
		$this->ci->load->library('form_validation');

		$name = $this->ci->uri->segment(5);
		$this->name = $name;

		// If a form was submitted, it's either a Preview or Generate action
		if ($this->ci->input->post('form-submit-preview'))
		{
			$action = 'preview';
		}
		else if ($this->ci->input->post('form-submit-generate'))
		{
			$action = 'generate';
		}

		// Was a form submitted?
		if (isset($action))
		{
			// We go ahead an validate no matter what the action,
			// since it's possible that they've modified data since
			// we previewed.
			if ($this->validate() === true)
			{
				// Time to run the actual generator method!
				$params = array(
					'module'	=> $this->ci->input->post('module')
				);

				foreach ($this->fields as $field => $options)
				{
					if ($this->ci->input->post($field))
					{
						$params[$field] = $this->ci->input->post($field);
					}
				}

				// Handle our actions
				if ($action == 'preview')
				{
					$preview = $this->build_preview($name, $params);
				}
				else if ($action == 'generate')
				{
					// Call the child class' generate method to
					// handle the heavy lifting.
					// We should get back an array of status messages
					// from the method for each action/file
					// that was to be generated.
					$status = $this->generate($params);
				}
			}
			else
			{
				//die('invalid');
			}
		}

		$data = array(
			'name' 			=> $this->name,
			'title'			=> $this->title,
			'description'	=> $this->description,
			'form'			=> $this->render_form(),
		);

		if (isset($preview))
		{
			$data['preview'] = $preview;
		}
		if (isset($status))
		{
			$data['status'] = $status;
		}

		// View file is expected to be at views/index.php
		$view = $this->ci->load->view('gen_form', $data, true);

		return $view;
	}

	//--------------------------------------------------------------------

	/**
	 * Looks at the information and builds out a preview of the
	 * files that will be built by this generator.
	 *
	 * @param string $module The name of the module
	 * @param string $name The name of the generator we're running
	 * @param array $params An array of $_POST values for this module.
	 *
	 * @return string 	The HTML to insert into the view.
	 */
	protected function build_preview($name, $params)
	{
		$module = $params['module'];
		$files = $this->determine_files($module, $params);

		$preview = '<table class="preview"><tbody>';

		foreach ($files as $file) {
			$preview .= "<tr><td>{$file['path']}<span style='color:yellow'>{$file['filename']}</span></td>";

			// does the file exist?
			if (is_file($file['path'] . $file['filename']))
			{
				$preview .= '<td><span class="label label-warning">Overwrite</span></td>';
			}
			else
			{
				$preview .= '<td><span class="label label-success">Create</span></td>';
			}

			$preview .= '</tr>';
		}

		$preview .= '</tbody></table>';

		return $preview;
	}

	//--------------------------------------------------------------------

	protected function generate($params)
	{
		// We need to retrieve an array of values from
		// the generator of info to replace.
		$vars = $this->get_vars($params);

		$files = $this->determine_files($params['module'], $params);

		$results = array();

		// Now, loop through each of our files, rendering each one
		// in turn.
		foreach ($files as $file)
		{
			// Replaces info in filename with any of the generator vars.
			$filename = $this->replace_vars($file['filename'], $vars);

			$tpl = $this->load_template($file['template'], $this->name);

			$tpl = $this->replace_vars($tpl, $vars);

			$results[] = $this->write_file(realpath($file['path']) .'/', $file['filename'], $tpl);
		}
		die('<pre>'. print_r($results, true));

		return $results;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !Private Methods
	//--------------------------------------------------------------------

	/**
	 * Replaces any existence of {var} within the given string.
	 *
	 * @param  string $str  The string to replace the vars in
	 * @param  array $vars An array of name/value pairs to replace in $str.
	 *
	 * @access private
	 *
	 * @return string The modified string.
	 */
	private function replace_vars($str, $vars)
	{
		if (!is_array($vars) || (is_array($vars) && !count($vars)) )
		{
			return $str;
		}

		foreach ($vars as $key => $value)
		{
			$str = str_ireplace('{'. $key .'}', $value, $str);
		}

		return $str;
	}

	//--------------------------------------------------------------------


	/**
	 * Builds an array of filenames with paths, based on the $files array.
	 *
	 * @param string $module The name of the module to be built into.
	 * @param array $params The array of information pulled form $_POST
	 *
	 * @return $array
	 */
	protected function determine_files($module, $params)
	{
		$files = array();

		if (!is_array($this->files) || !count($this->files))
		{
			return $files;
		}

		// All we're determing here is determining the file path/name
		foreach ($this->files as $name_format => $options)
		{
			$name = $name_format;

			/*
				Time to do some field replacement in our
				name_format to determine our name. If the
				$field is found within curly braces in our
				name_fromat, then replace it.

				So, {name} would be replaced by $name, etc.
			 */
			foreach ($this->fields as $field => $opts)
			{
				if (strpos($name_format, '{'. $field .'}') !== false)
				{
					$name = strtolower( str_replace('{'. $field .'}', $params[$field], $name_format) );
					break;
				}
			}

			/*
				Alternatively, check for the {context} tag and replace it in the name
				for context controllers.
			 */
			if ($this->ci->input->post('context'))
			{
				$name = str_replace('{context}', $this->ci->input->post('context'), $name);
				$options['folder'] = str_replace('{context}', $this->ci->input->post('context'), $options['folder']);
			}

			$path = str_replace('application/', '', APPPATH) .'modules/'. $module .'/'. $options['folder'] .'/';

			$files[] = array(
				'filename'	=> $name,
				'path'		=> $path,
				'template'	=> $options['template']
			);
		}

		return $files;
	}

	//--------------------------------------------------------------------


	/**
	 * Runs the validation of the generator's form. Inserts a required
	 * check for the module field.
	 *
	 * @access private
	 *
	 * @return boolean
	 */
	private function validate()
	{
		$rules = $this->form_validation_array();

		// This Code Builder is only intended to work on code from
		// custom modules, not the core code, so we'll make sure we have a module
		$rules[] = array(
			'field'	=> 'module',
			'label' => 'Module',
			'rules'	=> 'required|trim|xss_clean'
		);

		$this->ci->form_validation->set_rules($rules);

		return $this->ci->form_validation->run();
	}

	//--------------------------------------------------------------------

	/**
	 * Renders the contents of a form based on the $fields array in the
	 * child generator class.
	 *
	 * Supported field types:
	 * 		db_prefix	- Info block to show current database prefix
	 * 		input 		- A standard text input, Bootstrap-style
	 * 		checkbox 	- A standard checkbox field, Bootstrap-style.
	 *
	 * @return string The rendered form
	 */
	private function render_form()
	{
		$form = '';

		/*
			Add a module selector for all forms...
		 */
		$modules = module_list(TRUE);

		$form .= '<div class="control-group">';
		$form .= '<label class="control-label">Module</label>';
		$form .= '<div class="controls">';
		$form .= '<select name="module"><option value=""></option>';
		foreach ($modules as $module)
		{
			$selected = $this->ci->input->post('module') == $module ? 'selected="selected"' : '';
			$form .= "<option value='$module' $selected>". ucwords(str_replace('_', ' ', $module)) ."</option>";
		}
		$form .= '</select>';
		$form .= '</div></div>';

		foreach ($this->fields as $field => $options)
		{
			$default = isset($options['default']) ? $options['default'] : '';
			$selected = $this->ci->input->post($field) ? $this->ci->input->post($field) : $default;

			switch ($options['type'])
			{
				// Display the list of contexts - does not allow creating new ones.
				case 'context':
					$contexts = $this->ci->config->item('contexts');

					$form .= '<div class="control-group">';
					$form .= '<label class="control-label">'. $options['display_name'] .'</label>';
					$form .= '<div class="controls">';
					$form .= "<select name='{$field}'><option value=''></option>";
					foreach ($contexts as $context)
					{
						$selected = $selected == $context ? 'selected="selected"' : '';
						$form .= '<option value="'. strtolower($context) ."\" $selected>". ucwords($context) .'</option>';
					}
					$form .= '</select>';
					$form .= "<span class='help-block'>{$options['help']}</span>";
					$form .= '</div></div>';
					break;

				// Display the current database prefix
				case 'db_prefix':
					$prefix  = $this->ci->db->dbprefix;
					$form 	.= '<div class="control-group">';
					$form 	.= '<label class="control-label">Database Prefix</label>';
					$form 	.= "<div class='controls'><div class='alert alert-info'>{$prefix}</div></div>";
					$form 	.= '</div>';
					break;

				// List of database tables
				case 'db_table':
					$tables = $this->ci->db->list_tables();
					$prefix = $this->ci->db->dbprefix;

					// Used as the class for the select that attaches
					// to the
					$form_type = isset($options['form_type']) ? $options['form_type'] : '';

					$selected = $this->ci->input->post('table') ? 'selected="selected"' : '';

					$form .= '<div class="control-group">';
					$form .= '<label class="control-label">Database Table</label>';
					$form .= '<div class="controls">';
					$form .= "<select name='{$field}' class='{$form_type}'><option value=''></option>";

					// App-specific tables
					$form .= '<optgroup label="Application Tables">';
					foreach ($tables as $table)
					{
						if (in_array(str_replace($prefix, '', $table), $this->core_tables))
						{
							continue;
						}
						$table = str_replace($prefix, '', $table);
						$form .= "<option value='$table' $selected>$table</option>";
					}
					$form .= '</optgroup>';

					// Core tables
					$form .= '<optgroup label="Core Tables">';
					foreach ($this->core_tables as $table)
					{
						$table = str_replace($prefix, '', $table);
						$form .= "<option value='$table' $selected>$table</option>";
					}
					$form .= '</optgroup>';

					$form .= '</select>';
					$form .= "<span class='help-block'>{$options['help']}</span>";
					$form .= '</div>';

					// Allow for table details
					$form .= '<div id="db_table_form_details"></div>';

					$form .= '</div>';

					break;

				case 'input':
					$required = strpos($options['rules'], 'required') !== false ? 'required' : '';
					$error = form_error($field) ? 'error' : '';

					$form .= "<div class='control-group $error'>";
					$form .= "<label class='control-label $required'>{$options['display_name']}</label>";
					$form .= '<div class="controls">';
					$form .= "<input type='text' class='input-xlarge' name='{$field}' value='$selected' $required />";
					if (isset($options['help']) && !empty($options['help']))
					{
						$form .= "<span class='help-block'>{$options['help']}</span>";
					}
					$form .= '</div>';
					$form .= '</div>';
					break;

				case 'checkbox':
					$form .= '<div class="control-group"><div class="controls">';
					$form .= '<label class="checkbox">';
					$form .= "<input type='checkbox' name='$field' {$options['default']} />";
					$form .= $options['display_name'];
					$form .= '</label>';
					if (isset($options['help']) && !empty($options['help']))
					{
						$form .= "<span class='help-block'>{$options['help']}</span>";
					}
					$form .= '</div></div>';
					break;
			}
		}

		Assets::add_module_js('builder', 'generators.js');

		return $form;
	}

	//--------------------------------------------------------------------

	/**
	 * Takes the $fields array and turns it into the array needed
	 * by the form validation class.
	 *
	 * @return array
	 */
	private function form_validation_array()
	{
		if (!is_array($this->fields) || !count($this->fields))
		{
			return array();
		}

		$valids = array();

		foreach ($this->fields as $field => $options)
		{
			if (!isset($options['rules']))
			{
				continue;
			}

			$valids[] = array(
				'field'	=> $field,
				'label'	=> isset($options['display_name']) && !empty($options['display_name']) ? $options['display_name'] : $name,
				'rules' => $options['rules']
			);
		}

		return $valids;
	}

	//--------------------------------------------------------------------

	/**
	 * Loads the template file in so that our generator can
	 * do all of it's fancy schmany formatting and text replacement on it.
	 *
	 * @param  string $template_name The name of the template file to load.
	 * @return string
	 */
	public function load_template($template_name, $gen_name)
	{
		$path = $this->self_path .'/'. $gen_name .'/templates/'. $template_name .'_tpl.php';

		if (is_file($path))
		{
			$this->ci->load->helper('file');
			$tpl = read_file($path);
		}
		else
		{
			return '';
		}

		// Replace some standard fields
		$tpl = str_replace('{generate_date}', date('Y-m-d h:ia'), $tpl);

		if (isset($this->ci->load->_ci_cached_vars['current_user']))
		{
			$uname = isset($this->ci->load->_ci_cached_vars['current_user']->username) ? $this->ci->load->_ci_cached_vars['current_user']->username : $this->ci->load->_ci_cached_vars['current_user']->email;
		}
		$tpl = str_replace('{generate_user}', $uname, $tpl);

		return $tpl;
	}

	//--------------------------------------------------------------------

	public function write_file($path, $filename, $content='')
	{
		$this->ci->load->helper('file');

		// Does the path exist?
		if (!is_dir($path))
		{
			mkdir($path, 0755, true);
		}

		if (is_file($path . $filename))
		{
			$results[$path . $filename] = 'EXISTS';
		}
		else if (write_file($path . $filename, $content))
		{
			$results[$path . $filename] = 'CREATED';
		}
		else
		{
			$results[$path . $filename] = 'FAILED';
		}

		return $results;
	}

	//--------------------------------------------------------------------

}
