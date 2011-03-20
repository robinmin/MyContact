<?php
if(isset($_SERVER['REMOTE_ADDR']))	die('Command Line Only!');

//$objCurrent = null;
$blReturn = true;
try{
	//prepare system environment
	set_time_limit(0);
	$_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'] = $argv[1];	//tricky to simulate HTTP request
	$arrNewConf = array(
		'max_execution_time'=> 0,
		'memory_limit'		=> '512M',
		'mssql.timeout'		=> 3600,
		'mssql.textlimit'	=> 2147483647,
		'mssql.textsize'	=> 2147483647
	);
	$arrOldConf = array();
	foreach($arrNewConf as $key => $val){
		$arrOldConf[$key] = ini_get($key);
		ini_set($key,$val);
	}
	$dtBegin = microtime();
	//simulate HTTP request to index.php
	require dirname(__FILE__) . '/index.php';
	$dtEnd = microtime();
	//recover system settings
	foreach($arrOldConf as $key => $val){
		ini_set($key,$val);
	}
	
	//calculate time cost
	list($sm, $ss) = explode(' ', $dtBegin);
	list($em, $es) = explode(' ', $dtEnd);
	$tmCost = number_format(($em + $es) - ($sm + $ss),4);
	echo "\r\nTime Cost(s) : $tmCost";
}catch(Exception $exp) {
	$blReturn = false;
	echo '[Exception] : '.$exp->getMessage().'. @('.basename($exp->getFile()).' ['.$exp->getLine().'])';
}
if($blReturn){
	exit(0);
}else{
	exit(-1);
}

///////////////////////////////////////////////////////////////////////////////
/**
 * executeReadSql : query sql.
 *
 * @param $sql SQL
 * @access public
 * @return  array() query sql result
 */
function executeReadSql($sql) {
	global $CI;
	$arrRes = array();
	$query = $CI->db->query ( $sql );
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
function executeWriteSql($sql) {
	global $CI;
	$query = $CI->db->query ( $sql );
	if (false === $query) {
		return false;
	}else{
		return true;
	}
}

/**
 * executeWriteSql2 : query sql.
 *
 * @param $sql SQL
 * @access public
 * @return  array() query sql result
 */
function executeWriteSql2($sql) {
	global $CI;
	$query = $CI->db->simple_query( $sql );
	if (false === $query) {
		return false;
	}else{
		return true;
	}
}