<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

// ------------------------------------------------------------------------

/**
 * Module Builder Developer Context Controller
 *
 * This controller displays the list of current modules in the bonfire/modules folder
 * and also allows the users to create new modules.
 *
 * This code is originally based on Ollie Rattue's http://formigniter.org/ project
 *
 * @package    Bonfire
 * @subpackage Modules_ModuleBuilder
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/core_modules/modulebuilder.html
 *
 */
class Developer extends Admin_Controller {

    //---------------------------------------------------------------

    /**
     * Setup restrictions and load configs, libraries and language files
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->auth->restrict('Site.Developer.View');
        $this->load->config('modulebuilder');
        $this->lang->load('builder');

        $this->options = $this->config->item('modulebuilder');

        $this->load->library('BF_Generator');

        Template::set('toolbar_title', 'Code Builder');

    }//end __construct

    //---------------------------------------------------------------

    /**
     * Displays a list of installed modules with the option to create
     * a new one.
     *
     * @access public
     *
     * @return void
     */
    public function index()
    {
        $modules = module_list(true);
        $configs = array();

        // check that the modules folder is writeable
        Template::set('writeable', $this->_check_writeable());

        // Get a list of available generators
        Template::set('generators', $this->get_generators());

        Template::render();

    }//end index()

    //--------------------------------------------------------------------

    public function generate($gen_name)
    {
        $class_name = $this->load_generator($gen_name);
        $class = new $class_name();

        Template::set('content', $class->render());
        Template::render('two_left');
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // !PRIVATE METHODS
    //--------------------------------------------------------------------

    public function load_generator($name)
    {
        $path = str_replace('controllers', 'generators', dirname(__FILE__)) .'/'. $name .'/'. $name .'_generator.php';

        require $path;

        $class_name = ucfirst($name) .'_Generator';

        return $class_name;
    }

    //--------------------------------------------------------------------


    private function get_generators()
    {
        $this->load->helper('directory');

        $path = str_replace('controllers', 'generators', dirname(__FILE__));
        $map = directory_map($path);

        $gens = array();

        /*
            Loop through each folder and see if we have a valid generator.

            If we do, then pull out the gen name, the title and the description.
         */
        foreach ($map as $name => $files)
        {
            $file = $path .'/'. $name .'/'. $name .'_generator.php';
            if (!is_file($file))
            {
                continue;
            }

            $class_name = ucfirst($name) .'_Generator';

            require $file;

            $class = new $class_name();

            $map[$name] = array(
                'title'         => $class->title,
                'description'   => $class->description
            );

            unset($class);
        }

        return $map;
    }

    //--------------------------------------------------------------------


    /**
     * Check that the Modules folder is writeable
     *
     * @access  private
     *
     * @return  bool
     */
    public function _check_writeable()
    {
        return is_writeable($this->options['output_path']);

    }//end _check_writeable()


}//end Developer
