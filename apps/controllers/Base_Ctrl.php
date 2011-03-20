<?php
/**
 * Module Description:
 *
 * Base_Ctrl : all controllers extends from this controller.
 *
 * PHP 5 only
 *
 * @category   Base_Ctrl
 * @package    Controller
 * @author     Robin Min
 * @version    $Id:Base_Ctrl.php 2010-01-27 $
 */
/*****************************************************************************/
/**
 * Base_Ctrl : all controllers extends from this controller.
 *
 * @category   Base_Ctrl
 * @package    Controller
 * @author     Robin Min
 * @version    1.0
 */
class Base_Ctrl extends CI_Controller {
	/**
	 * @var    $m_data : data container.
	 * @access protected
	 */
	protected $m_data = array ();
	
	/**
	 * __construct : the __construct.
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct ();
        
        // Ideally we need to autoload the parser
        $this->load->library('parser');
        
		$this->loadCommonInfo();
	}

	/**
	 * loadTpl : load template file and rend it by smarty engine
	 *
	 * @param  $template	template name
	 * @param  $data	data
	 * @param  $return	true:output, false:return data
	 * @access public
	 * @return none
	 */
	protected function loadTpl($template, $data = null, $return = FALSE) {
//		$this->load->library('smartyp');
//		return $this->load->view($this->router->fetch_class().'/'.$template, empty($data)?$this->m_data:$data, $return);
		return $this->parser->parse($this->router->fetch_class().'/'.$template, empty($data)?$this->m_data:$data, $return);
	}
	
	/**
	 * loadAll : load template file and rende it by smarty engine
	 *
	 * @param  $template	template name
	 * @param  $data	data
	 * @param  $return	true:output, false:return data
	 * @access public
	 * @return none
	 */
	protected function loadAll($data = null, $return = FALSE) {
//		$this->load->library('smartyp');
//		return $this->load->view('full_page.tpl', empty($data)?$this->m_data:$data, $return);
		return $this->parser->parse('full_page.tpl', empty($data)?$this->m_data:$data, $return);
	}
	
	/**
	 * loadFrame : load frame template file and rende it by smarty engine
	 *
	 * @param  $template	template name
	 * @param  $data	data
	 * @param  $return	true:output, false:return data
	 * @access public
	 * @return none
	 */
	protected function loadFrame($data = null, $return = FALSE) {
//		$this->load->library('smartyp');
//		return $this->load->view('frame_page.tpl', empty($data)?$this->m_data:$data, $return);
		return $this->parser->parse('frame_page.tpl', empty($data)?$this->m_data:$data, $return);
	}

	/**
	 * protected : output no -cache HTTP HEADERS
	 *
	 * @access protected
	 */
	protected function noCache(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 						// Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 			// always modified
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");// HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); 											// HTTP/1.0
	}

	/**
	 * loadCommonInfo : load all page info(JS,CSS...).
	 *
	 * @access protected
	 * @return none
	 */
	protected function loadCommonInfo() {
		//copy config setting from config file to page data
		$arrTemp = array(	'sys_asset',
							'sys_js',
							'sys_css',
							'sys_encoding'	=> 'charset',		//data index name diffrent with config setting
							'sys_title',
							'sys_version',
							'sys_i18n_msg'	=> 'language' );
		foreach($arrTemp as $nIdx => $strValue){
			if(!is_string($nIdx)){
				$strTemp = $strValue;
			}else{
				$strTemp = $nIdx;
			}
			$strConf = $strValue;
			$strTemp2 = $this->config->item ( $strConf );
			if (! empty ( $strTemp2 )) {
				$this->m_data[ $strTemp ] = $strTemp2;
			}else{
				$this->m_data[ $strTemp ] = '';
			}
		}
		if(!is_array($this->m_data['sys_js']))	$this->m_data['sys_js'] = array($this->m_data['sys_js']);
		if(!is_array($this->m_data['sys_css']))	$this->m_data['sys_css'] = array($this->m_data['sys_css']);
		
		$this->m_data ['sys_base_url'] 	= base_url();
		$this->m_data ['sys_body_ext'] 	= '';	//add extends attributes on body element
		$this->includeJS($this->getMsgFileName());
		$this->currentPage('','','');
	}
	
	/**
	 * setData : Interface to set data
	 *
	 * @param  $varName
	 * @param  $varValue
	 * @access protected
	 * @return none
	 */
	protected function setData($varName, $varValue) {
		if(empty($this->m_data))	$this->m_data = array();
		$this->m_data[$varName] = $varValue;
	}
	
	/**
	 * includeJS : Add javascript file into init array
	 *
	 * @param  $varName
	 * @param  $varValue
	 * @access protected
	 * @return none
	 */
	protected function includeJS($jsName,$strAsset = '') {
		$keyName = 'sys_js';
		if(empty($this->m_data))	$this->m_data = array();
		if(empty($strAsset))		$strAsset = $this->config->item('sys_asset').'js/';
		if(!isset($this->m_data[$keyName])){
			$this->m_data[$keyName] = array();
		}
		if(!in_array($jsName,$this->m_data[$keyName])){
			$this->m_data[$keyName][] = $strAsset.$jsName;
		}
	}
	
	/**
	 * includeCSS : Add css file into init array
	 *
	 * @param  $varName
	 * @param  $varValue
	 * @access protected
	 * @return none
	 */
	protected function includeCSS($cssName,$strAsset = '') {
		$keyName = 'sys_css';
		if(empty($this->m_data))	$this->m_data = array();
		if(empty($strAsset))		$strAsset = $this->config->item('sys_asset').'css/';
		if(!isset($this->m_data[$keyName])){
			$this->m_data[$keyName] = array();
		}
		if(!in_array($cssName,$this->m_data[$keyName])){
			$this->m_data[$keyName][] = $strAsset.$cssName;
		}
	}
	
	/**
	 * currentPage : set default value for page layout
	 *
	 * @param  $css
	 * @access protected
	 * @return none
	 */
	protected function currentPage($body,$js='',$css=''){
		$this->m_data ['this_body']	= $body;
		$this->m_data ['this_css']	= $css;
		$this->m_data ['this_js']	= $js;
	}
	
	/**
	 * getMsgFileName : interface for message file name
	 *
	 * @param  $css
	 * @access protected
	 * @return none
	 */
	protected function getMsgFileName(){
		return 'msg.'.$this->config->config['language'].'.js';
	}
	
	/**
	 * jsonResponse : interface for JSON response
	 *
	 * @param  $sucess
	 * @param  $msg
	 * @param  $arrExtra
	 * @access protected
	 * @return none
	 */
	protected function jsonResponse($sucess,$msg='',$arrExtra = null){
		$this->noCache();
		$arrRtn = array(	'success'	=> $sucess,
							'action'	=> '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/',
							'message'	=> $msg );
		if(is_array($arrExtra) && count($arrExtra)>0){
			$arrRtn = array_merge($arrRtn,$arrExtra);
		}
		echo json_encode($arrRtn);
	}
}
