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
 * @author     Robin Min
 * @copyright  1997-2011
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
	 * heartbeat : heartbeat checking
	 *
	 * @access public
	 * @return  none
	 */
	public function heartbeat(){
		$this->jsonResponse(true,'');
	}
}
