<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/******************************************************************************
 * Module Description:
 *
 * Sys_Ctrl : the Controller for system.
 *
 * PHP 5 only
 *
 * LICENSE Declaration:
 *	It is the private resources,and any unauthorized using will be prohibitted.
 *
 * @category   controller
 * @package    Sys_Ctrl
 * @author     $Author$
 * @copyright  1997-2007 The SCB Group
 * @version    $Id$
 *
******************************************************************************/

require('Base_Ctrl'.EXT);

class Sys_Ctrl extends Base_Ctrl{
	/**
	 * __construct : __construct.
	 *
	 * @access public
	 * @return  none
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('Sys_Model');
	}
	
	/**
	 * index : show default page
	 *
	 * @access public
	 * @return  none
	 */
	public function index(){
		$this->m_data['url_prefix']	= '../';
		//output
		$this->m_data['this_body']	= $this->loadTpl(__FUNCTION__.'.tpl',null,true);
		$this->loadAll();
	}
	

	/**
	 * login : login page
	 *
	 * @access public
	 * @return  none
	 */
	public function login() {
		 redirect('/'.__CLASS__.'/desktop/', 'refresh');
	}

	/**
	 * logout : logout function
	 *
	 * @access public
	 * @return  none
	 */
	public function logout() {
		$this->load->helper('url');
		
		redirect('/'.__CLASS__.'/index/', 'refresh');
	}
	
	/**
	 * desktop : show the desktop
	 *
	 * @access public
	 * @return  none
	 */
	public function desktop(){
		//add css files
		$this->includeCSS('desktop.css');
		//add js files
		$this->includeJS('StartMenu.js');
		$this->includeJS('TaskBar.js');
		$this->includeJS('Desktop.js');
		$this->includeJS('myContact.js');
		
		//customize main page
		$this->m_data['sys_body_ext']= 'scroll="no"';
		
		//output
		$this->m_data['this_body'] = $this->loadTpl(__FUNCTION__.'.tpl',null,true);
		$this->loadAll();
	}

	/**
	 * getValue : get key/value array
	 *
	 * @param  none
	 * @access public
	 * @return none
	 */
	public function getItem($key){
		$key = explode(',;',$key);
		if(is_array($key) && count($key) == 1)
			$key = $key[0];
		if($key !== false){
			$arrRst = $this->Sys_Model->getItem($key,null);
			echo $this->jsonResponse(true,'',$arrRst);
		}else{
			log_message('ERROR','Invalidate parame is provided @'.__FUNCTION__);
			echo $this->jsonResponse(false,'Invalidate parame is provided @'.__FUNCTION__,array());
		}
	}
	
	/**
	 * heartbeat : heartbeat checking
	 *
	 * @access public
	 * @return  none
	 */
	public function heartbeat(){
		$this->jsonResponse(true,'');
	}
}
