<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Module Description:
 *
 * Sys_Model : model for system
 *
 * PHP 5 only
 *
 * LICENSE Declaration:
 *	It is the private resources,and any unauthorized using will be prohibitted.
 *
 * @category   models
 * @package    Sys_Model
 * @author     $Author$
 * @version    $Id$
 */
/*****************************************************************************/
require_once ('Base_Model' . EXT);

/**
 * Sys_Model : model for system administration
 * 
 * @category dashboard
 * @package dashboard
 * @author Robin Min
 * @version 0.01
 */
class Sys_Model extends Base_Model {
	/**
	 * __construct : ctor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	/**
	 * __destruct : dector
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
	}
	
	/**
	 * getToolList : get all tool-kit list
	 * 
	 * @param none
	 * @access public
	 * @return array	tool-kit list
	 */
	/*public function getToolList(){
		$strBackendSvr = 'http://'.$_SERVER['SERVER_ADDR'].':6789/';
		$strFrontendSvr = 'http://'.$_SERVER['SERVER_ADDR'];
		$strFrontendEx = '';*/
        /*$strNetDvr  = $this->config->item('NET_DRIVER');
        $strNetURI  = $this->config->item('NET_URI');
        if(file_exists($strNetDvr."\\") && is_writable($strNetDvr."\\")){
            $blNetConnected = true;
        }else{
            $blNetConnected = false;
        }*/
		/*return array(
			'ASS'	=> array( 'icon'=>'images/icon/ass.png',	'title'=>'Server Status',		'url'=>$strFrontendSvr.'/server-status/','newwind'=>'1',	'desc'=>'Server status on Apache platform.'),
			'BlcMgr'=> array( 'icon'=>'images/icon/blcmgr.png',	'title'=>'Balancer Manager',	'url'=>$strFrontendSvr.'/balancer-manager/','newwind'=>'1',	'desc'=>'Balance manager on Apache platform.'),
			'Monitor'=> array( 'icon'=>'images/icon/monitor.png','title'=>'System Monitor',		'url'=>$strBackendSvr.'tools/monitor.php','newwind'=>'1',	'desc'=>'Server side information.'),
			'SysEnv'=> array( 'icon'=>'images/icon/sysenv.png',	'title'=>'System Environment',	'url'=>$strBackendSvr.'tools/testphp.php','newwind'=>'1',	'desc'=>'Output of phpinfo().'),
			'Memcahe'=> array( 'icon'=>'images/icon/cache.png',	'title'=>'Memcahe Monitor',		'url'=>$strBackendSvr.'tools/memcache.php','newwind'=>'1',	'desc'=>'Monitor tool for memcahe.'),
			'Spark' => array( 'icon'=>'images/icon/sparkle.png','title'=>'Spark Management',	'url'=>$strBackendSvr.'SparkAdmin/SparkManage/',	'newwind'=>'1',	'desc'=>'Spark management toolkit.'),
			'Menu'	=> array( 'icon'=>'images/icon/menu.png',	'title'=>'Menu Management',		'url'=>$strBackendSvr.'SparkAdmin/SparkManage/showMenu/','newwind'=>'1',	'desc'=>'Menu management.'),

			'Shell' => array( 'icon'=>'images/icon/shell.png',	'title'=>'Backend Shell',		'url'=>$strBackendSvr.'tools/bkcmd.php',	'newwind'=>'1',	'desc'=>'Backend shell for iRM maintenance.'),
			'SDL2'	=> array( 'icon'=>'images/icon/folder.png',	'title'=>'Simple Directory List','url'=>$strBackendSvr.'tools/SDL2.php','newwind'=>'1',	'desc'=>'Server side file or folder explorer.'),
			'RunSQL'=> array( 'icon'=>'images/icon/sql.png',	'title'=>'Run SQL Statement',	'url'=>$strBackendSvr.'runSQL/RunSQL_Controller/','newwind'=>'1',	'desc'=>'Execute SQL statement online.'),
			'Upload'=> array( 'icon'=>'images/icon/upload.png',	'title'=>'Upload File(s)',		'url'=>$strBackendSvr.'application/helpers/upload.php','newwind'=>'1',	'desc'=>'Upload data file(s) to server.'),
			
			'PdtCatMtn'=> array( 'icon'=>'images/icon/PdtCatMtn.png',	'title'=>'Product Category Maintenance',	'url'=>$strFrontendEx.'SysAdmin/showPdtRecomm/','newwind'=>'1',	'desc'=>'Product Recommendation Category Maintenance.'),
            'FilePort'=> array( 'icon'=>($blNetConnected)?'images/icon/netdrv_open.png':'images/icon/netdrv_close.png',
                                'title'=>'File Server Port',
                                'url'=>$strFrontendEx.'SysAdmin/port_mgmt/'.(($blNetConnected)?'0/':'1/'),
                                'newwind'=>'0',
                                'action'=>'refreshNetDrvStatus',
                                'desc'=>'File Server : '.$strNetURI)
		);
	}*/
}
