<?php
/**
 * Module Description:
 *
 * Base_DAO : all daos extends the Base_DAO.
 *
 * PHP 5 only
 *
 * @category   Base_DAO
 * @package    modules/dao
 * @author     Robin Min
 * @version    $Id:Base_DAO.php 2008-11-24$
 */
/*****************************************************************************/

/**
 * Base_DAO : all daos extends the Base_DAO.
 *
 * @category   Base_DAO
 * @package    modules/dao
 * @author     Robin Min
 * @version    1.0
 */
class Base_DAO {

	/**
	 * @var    $m_strTabName : table Name for dao use
	 * @access protected
	 */
	protected  $m_strTabName = '';

	/**
	 * @var    $m_arrPK : table PK array.
	 * @access protected
	 */
	private $m_arrPK = array();

	/**
	 * @var    $CI 
	 * @access protected
	 */
	protected $CI;

	/**
	 * __construct : __construct.
	 *
	 * @access public
	 * @return  none
	 */
	public function __construct($daoName = ''){
	}
	
	/**
	 * getTabName : get the table's name for the DAO.
	 *
	 * @access public
	 * @return  m_strTabName
	 */
	public function getTabName() {
		return $this->m_strTabName;
	}

	/**
	 * setTabName : set the table's name for the DAO.
	 *
	 * @param $tabName table's name
	 * @access public
	 * @return none
	 */
	public  function setTabName($tabName){
		$this->m_strTabName = $tabName;
	}

	/**
	 * getPK : get the table's PK for the DAO.
	 *
	 * @access public
	 * @return  array  table's PK list
	 */
	public function getPK() {
		return $this->m_arrPK;
	}
	
	public function transBegin(){
		$CI = & get_instance ();
		$CI->db->trans_begin();
	}
	
	/**
	 * transCommit : commit or rollback transaction.
	 *
	 * @param boolean $bSucc : status of transaction process
	 * @access public
	 * @return boolean
	 */
	public function transCommit($bSucc = true){
		
		$bTran = true;
		$CI = & get_instance ();
		if (!$bSucc || ($CI->db->trans_status() === FALSE)) {
			$bTran = false;
		    $CI->db->trans_rollback();
		} else {
		    $CI->db->trans_commit();
		}
		
		return $bTran;
	}

	/**
	 * checkTable : check the table is exit or not in the database.
	 *
	 * @param  $tableName table name
	 * @access public
	 * @return boolean table exit or not
	 */
	public function checkTable($tableName){
		$CI = & get_instance ();
		return $CI->db->table_exists($tableName);
	}

	/**
	 * executeReadSql : query sql.
	 *
	 * @param $sql SQL
	 * @access public
	 * @return  array() query sql result
	 */
	public function executeReadSql($sql) {
		$arrRes = array();
		$query = $this->CI->db->query ( $sql );
		if (FALSE === $query) {
			return FALSE;
		}else{
			$arrRes = $query->result_array ();
			$query->free_result ();
			return $arrRes;
		}
	}
	
	/**
	 * executeWriteSql : query sql.
	 *
	 * @param $sql SQL
	 * @access public
	 * @return  array() query sql result
	 */
	public function executeWriteSql($sql) {
		$query = $this->CI->db->simple_query( $sql );
		if (false === $query) {
			return false;
		}else{
			return true;
		}
	}
}