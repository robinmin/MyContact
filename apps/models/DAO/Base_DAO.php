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
		$this->CI = & get_instance ();
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
		$this->CI->db->trans_begin();
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
		if (!$bSucc || ($this->CI->db->trans_status() === FALSE)) {
			$bTran = false;
		    $this->CI->db->trans_rollback();
		} else {
		    $this->CI->db->trans_commit();
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
		return $this->CI->db->table_exists($tableName);
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
	
	/**
	 * getItems : get items from sys_dict
	 *
	 * @param mixed $category	category
	 * @param mixed $key		key value
	 * @param mixed $value		value
	 * @param mixed $cond 		more conditions, default by null
	 * @access public
	 * @return mixed
	 */
	public function getItems($category, $key = null, $value = null, $cond = null) {
		$strWhere = '';
		if( is_array($category) ){
			$strWhere .= "cat.C_VALUE in('".implode("','",$category)."')";
		}else if( is_string($category) && !empty($category) ){
			$strWhere .= "cat.C_VALUE = '".$category."'";
		}else if( is_int($category) ){
			$strWhere .= "cat.C_VALUE = ".$category;
		}
		
		if( is_array($key) ){
			if(strlen($strWhere)>0)	$strWhere .= ' and ';
			$strWhere .= "dt.N_KEY in(".implode(",",$key).")";
		}else if( !empty($key) ){
			if(strlen($strWhere)>0)	$strWhere .= ' and ';
			$strWhere .= "dt.N_KEY = ".$key;
		}
		
		if( is_array($value) ){
			if(strlen($strWhere)>0)	$strWhere .= ' and ';
			$strWhere .= "dt.C_VALUE in('".implode("','",$category)."')";
		}else if( !empty($value) ){
			if(strlen($strWhere)>0)	$strWhere .= ' and ';
			$strWhere .= "dt.C_VALUE = '".$value."'";
		}
		
		if(strlen($strWhere)>0)	$strWhere .= ' and ';
		if( is_array($cond) ){
			$strWhere .= implode(' and ',$cond);
		}else if( is_string($cond) && !empty($cond) ){
			$strWhere .= $cond;
		}else{
			$strWhere .= 'dt.N_INUSE = 1';
		}
		
		if(strlen($strWhere)<=0)	$strWhere = '1=1';
		$strSQL =<<<ENDSQL
select
	ifnull(cat.C_VALUE,'') as C_CATEGORY,
	dt.N_KEY,
	dt.C_VALUE
from SYS_DICT as dt
left join(
	select N_KEY,C_VALUE,N_ORDER from SYS_DICT where N_INUSE=1 and N_CAT=0
) as cat on cat.N_KEY = dt.N_CAT
where $strWhere
order by cat.N_ORDER,dt.N_ORDER,dt.N_KEY
ENDSQL;
		return $this->executeReadSql($strSQL);
	}
}