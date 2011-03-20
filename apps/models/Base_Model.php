<?php
/**
 * Module Description:
 *
 * Base_Model : all models extends the Model
 *
 * PHP 5 only
 *
 *
 * @category   Base_Model
 * @package    Model
 * @author     Robin Min
 * @version    $Id:Base_Model.php 2011-01-29$
 */
/*****************************************************************************/

/**
 * Base_Model : all models extends the Base_Model.
 *
 * @category   Base_Model
 * @package    Model
 * @author     Robin Min
 * @version    1.0
 */
class Base_Model extends CI_Model {

	/**
	 * @var    $m_arrDAOs DAO container
	 * @access protected
	 */
	protected $m_arrDAOs = array ();

	/**
	 * @var    $m_ObjectDAO 
	 * @access protected
	 */
	protected  $m_ObjectDAO = null;

	/**
	 * __construct : constructor
	 *
	 * @access public
	 * @return  none
	 */
	public function __construct() {
		parent::__construct ();
	}

	/**
	 * __destruct : destructor
	 *
	 * @access public
	 * @return  none
	 */
    function __destruct() {
        foreach($this->m_arrDAOs as $alias => &$dao){
        	unset($dao);
        }
    }

	/**
	 * getDAOInstance : get the DAO Instance 
	 *
	 * @param  $dao		DAO name
	 * @param  $alias	alias for DAO
	 * @access public
	 * @return DAO Instance
	 */
	public function getDAOInstance($alias) {
		if( empty($alias) )	return false;
		if( !isset($this->m_arrDAOs[$alias]) ){
			return false;
		}else{
			if( is_string($this->m_arrDAOs[$alias]) ){
				$this->m_arrDAOs[$alias] = new $this->m_arrDAOs[$alias]();
			}
		}
		return $this->m_arrDAOs[$alias];
	}

	/**
	 * destroyDAO : cleanup for specifiled DAO Object
	 *
	 * @param  $alias	alias for DAO
	 * @access public
	 * @return none
	 */
	public function destroyDAO($alias) {
		if( isset($this->m_arrDAOs[$alias]) ){
			unset($this->m_arrDAOs[$alias]);
		}
	}

	/**
	 * addDAO : add the DAO to the m_arrDAOs array
	 *
	 * @param  $dao		DAO name
	 * @param  $alias	alias for DAO
	 * @access public
	 * @return none
	 */
	public function addDAO($dao,$alias = null) {
		if( empty($alias) )	$alias = $dao;
		if( empty($dao) || isset($this->m_arrDAOs[$alias]) ) return false;
		
		$this->m_arrDAOs[$alias] = strval($dao);
		@require_once(APPPATH.'modules/dao/'.$dao.EXT);
		return true;
	}
}