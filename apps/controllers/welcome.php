<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require('Base_Ctrl'.EXT);

class Welcome extends Base_Ctrl {

	function __construct(){
		parent::__construct();	
	}
	
	function index()
	{
		$this->load->model('Base_Model');
		$dt = $this->Base_Model->getItems(null,null,null,null);
		if(count($dt)>0){
			$this->load->library('table');
			$this->table->set_heading(array_keys($dt[0]));
			$this->table->set_template(array(
				'table_open'          => '<table border="1" cellpadding="1" cellspacing="1" class="table_blue_border">'
			));
			$this->m_data['this_body'] = $this->table->generate($dt);
			$this->table->clear();
		}
		$this->loadAll();
		//$this->load->view('welcome_message');
//		echo '<pre>';
//		var_dump($this);
//		echo '</pre>';
	}
	
	function smarty()
	{
		$this->m_data['this_body'] = 'Hello ..>'.__FUNCTION__.'!';
		$this->m_data['base_url'] = 'base_url';
		$this->m_data['this_css'] = 'this_css';
		$this->m_data['this_js_data'] = 'this_js_data';
		$this->m_data['this_js_layout'] = 'this_js_layout';
		$this->loadTpl(__FUNCTION__.'.tpl'); 
	}
	
	public function test2(){
//		$this->data['this_body'] = 'Hello ..>'.__FUNCTION__.'!';
//		$this->data['this_body_new'] = '>>>>>>>>>>>'.__FUNCTION__.'<<<<<<<<<<<<<<<';
//		$this->loadFrame('smarty.tpl', $this->data); 
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */