<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Smartytest extends Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Load the Smarty Parser library. Usually you would autoload this library instead.
        $this->load->library('smartyp');
    }

    public function index()
    {    
        // Some example data
        $data['title'] = "The Smarty parser works!";
        $data['body']  = "This is body text to show that the Smarty Parser works!";
        
        // Load the template from the views directory
        $this->load->view("smartytest", $data);
    }
    
    /**
     * Showing off Smarty 3 template inheritance features
     *
     */
    public function inheritance()
    {
        // Some example data
        $data['title'] = "The Smarty parser works with template inheritance!";
        $data['body']  = "This is body text to show that Smarty 3 template inheritance works with Smarty Parser.";
        
        // Load the template from the views directory
        $this->load->view("inheritancetest", $data);
        
    }

}