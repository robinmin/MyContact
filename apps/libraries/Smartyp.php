<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class Smartyp extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        
        $CI =& get_instance();
        $this->template_dir = $CI->config->item('template_dir');
        $this->compile_dir  = $CI->config->item('compile_dir');
        $this->cache_dir    = $CI->config->item('cache_dir');
        $this->config_dir   = $CI->config->item('config_dir');
    }

    public function _assign_variables($variables = array())
    {
        foreach ($variables as $name => $val) 
        {
            $this->assign($name, $val);
        }
    }

}