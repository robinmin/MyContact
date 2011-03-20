<?php
/**
 * Module Description:
 *
 * MetaFactory : Model for meta data factory
 *
 * PHP versions 5
 *
 * LICENSE Declaration:
 *    Any unauthorized using will be prohibitted.
 *
 * @category   Model
 * @package    model
 * @author     $Author: Robin Min $
 * @copyright  1997-2010
 * @version    $Id$
 */
/*****************************************************************************/
/**
 * MetaFactory : Model for meta data factory
 *
 * @category   Model
 * @package    MetaFactory
 * @author     Robin Min
 * @version    Ver 0.1
 */
require_once ('Base_Model' . EXT);

if(!defined('META_TYPE_GRID')) 		define('META_TYPE_GRID',		'grid');
if(!defined('META_TYPE_SQL')) 		define('META_TYPE_SQL',			'sql');

if(!defined('META_KEY_ATTRI')) 		define('META_KEY_ATTRI',		'@attributes');
if(!defined('META_KEY_METADATA')) 	define('META_KEY_METADATA',		'metadata');
if(!defined('META_KEY_DATA')) 		define('META_KEY_DATA',			'data');
if(!defined('META_KEY_EXTRA')) 		define('META_KEY_EXTRA',		'extra');

if(!defined('META_KEY_COLUMN')) 	define('META_KEY_COLUMN',		'column');
if(!defined('META_KEY_ROWID')) 		define('META_KEY_ROWID',		'rowid');
if(!defined('META_KEY_ID')) 		define('META_KEY_ID',			'id');
if(!defined('META_KEY_NAME')) 		define('META_KEY_NAME',			'name');
if(!defined('META_KEY_HEADER')) 	define('META_KEY_HEADER',		'header');
if(!defined('META_KEY_TYPE')) 		define('META_KEY_TYPE',			'type');
if(!defined('META_KEY_USETPL')) 	define('META_KEY_USETPL',		'usetpl');
if(!defined('META_KEY_USEL18N')) 	define('META_KEY_USEL18N',		'usel18n');
if(!defined('META_KEY_DFTORDER'))	define('META_KEY_DFTORDER',		'defaultorder');
if(!defined('META_KEY_DBFIELD')) 	define('META_KEY_DBFIELD',		'dbfield');

if(!defined('META_SQL_ROWID')) 		define('META_SQL_ROWID',		'SQL_ROWID');
if(!defined('META_SQL_ALL')) 		define('META_SQL_ALL',			'SQL_ALL');
if(!defined('META_SQL_WHERE')) 		define('META_SQL_WHERE',		'SQL_WHERE');
if(!defined('META_SQL_ORDER')) 		define('META_SQL_ORDER',		'SQL_ORDER');
if(!defined('META_SQL_FILTER')) 	define('META_SQL_FILTER',		'SQL_FILTER');
if(!defined('META_SQL_START')) 		define('META_SQL_START',		'SQL_START');
if(!defined('META_SQL_LIMIT')) 		define('META_SQL_LIMIT',		'SQL_LIMIT');

class MetaData_Grid{
	private $total				= 0;
	private $results			= array();
	private $success			= false;
	private $defaultSortable	= false;
	private $sort				= '';
	private $dir				= '';
	private $fields				= array();
	private $id					= '';
	private $start				= 0;
	private $limit				= 25;
	private $message			= '';
	private $extra				= array();
	
	//setter
	public function setTotal($total)		{$this->total	= $total;}
	public function setResults(&$results)	{$this->results = &$results;}
	public function setSuccess($success)	{$this->success = $success;}
	public function setFields(&$fields)		{$this->fields = &$fields;}
	public function setId($id)				{$this->id = $id;}
	public function setStart($start)		{$this->start = $start;}
	public function setLimit($limit)		{$this->limit = $limit;}
	public function setMessage($message)	{$this->message = $message;}
	public function setExtra($extra)		{$this->extra = $extra;}
	public function setSortInfo($sort,$dir)	{
		$this->sort = $sort;
		$this->dir	= $dir;
		$this->defaultSortable = true;
	}
	
	//getter
	public function getStart()				{return $this->start;}
	public function getLimit()				{return $this->limit;}
	
	/**
	* getData : retrieve data array
	* 
	* @return array			data
	*/
	public function &getData($blMeta){
		$arrTmp = array(
			'total'		=> $this->total,
			'results'	=> &$this->results,
			'success'	=> $this->success,
			'message'	=> $this->message,
			'extra'		=> $this->extra
		);
		if($blMeta){
			$arrTmp['metaData'] = array(
				'defaultSortable'	=> $this->defaultSortable,
				'sortInfo'			=> array('field'=>$this->sort,'direction'=>$this->dir),
				'fields'			=> &$this->fields,
				'id'				=> $this->id,
				'start'				=> $this->start,
				'limit'				=> $this->limit,
				'root'				=> 'results',
				'successProperty'	=> 'success',
				'totalProperty'		=> 'total'
			);
		}
		return $arrTmp;
	}
}

class MetaFactory extends Base_Model {
    
    private $daoIns			= Null;		//instance object for ADO
    private	$arrTplDefault	= null;		//template variable
    private	$objResult		= null;		//result object
    private $reconfigure	= null;		//flag for need column metadata or not
    private $arrUDV			= array();	//template variable defined by user
    private $whereUD		= '';		//user defined condition
    
    /**
     * __construct : ctor
     *
     * @access public
     * @return none
     */
    public function __construct($reconfigure = true) {
        parent::__construct();
        $this->reconfigure	= $reconfigure;
        
        $this->addDAO ( array ('MetaFactory_DAO' ) );
        $this->daoIns = $this->getDAOInstance ( 'MetaFactory_DAO' );
    }

    /**
     * __destruct : dtor
     *
     * @access public
     * @return none
     */
	function __destruct() {
		$this->release();
	}
	
    public function setUDVariable($arr)	{$this->arrUDV = $arr;}
    public function setUDWhere($where)	{$this->whereUD = $where;}

    /**
     * release : release internal array
     *
     * @access public
     * @return none
     */
    public function release() {
		if(!empty($this->objResult))		unset($this->objResult);
		if(!empty($this->arrUDV))			unset($this->arrUDV);
		if( !empty($this->arrTplDefault) )	unset($this->arrTplDefault);
		
		$this->objResult	= Null;
		$this->whereUD	 	= '';
		$this->arrTplDefault= Null;
		$this->arrUDV 		= array();
    }
	
	/**
	* createGrid : get meta data & data for specified grid
	* 
	* @param string $strGridName	grid name
	* @param bool 	$reconfigure	reconfigure flag
	* @param number $start			start row number
	* @param number $limit			limit number
	* @param array 	$filters		filter(s) condition
	* @param array 	$order			order number
	* @return array or bool
	*/
	public function &createGrid($strGridName, $reconfigure, $start, $limit, $filters=null, $order=null) {
		//1,prepare return object
		$this->objResult = new MetaData_Grid($reconfigure);
		$this->objResult->setStart($start);
		$this->objResult->setLimit($limit);
		
		//2,load configuration information
		$arrConfig = &$this->loadConfig($strGridName,META_TYPE_GRID);
		if(!is_array($arrConfig)){
			$this->objResult->setMessage('Failed to load configuration information.');
			log_message('ERROR','Failed to load configuration information.');
			return $this->objResult->getData($reconfigure);
		}
		
		//3,build where condition
		if(empty($this->whereUD)){
			$this->whereUD = '1=1';
		}
		
		$blRtn 		= true;
		$strOrder 	= '';
		$arrHiddenFld = array(META_KEY_DBFIELD,META_KEY_NAME);
		foreach($arrConfig as $idx => &$arrTmpGrid){
			$arrMeta = &$arrTmpGrid[META_KEY_METADATA];
			$arrAttr = &$arrTmpGrid[META_KEY_ATTRI];
			$arrExtra= &$arrTmpGrid[META_KEY_EXTRA];
			$rowid = (isset($arrAttr[META_KEY_ROWID]) && !empty($arrAttr[META_KEY_ROWID]))?$arrAttr[META_KEY_ROWID]:META_KEY_ROWID;
			
			$this->objResult->setId($rowid);
			$this->objResult->setExtra($arrExtra);
			
			//4,build filter condition
			$strFilters = '';
			if(is_array($filters) && count($filters)>0){
				foreach($filters as $idxFilter => $valFilter){
					//sanity check
					if( !isset($valFilter['data']) || !isset($valFilter['data']['type']) || !isset($valFilter['field']) )	continue;
					
					//incorrect field specified
					$field = '';
					if(!isset($arrMeta[$valFilter['field']])){
						log_message('ERROR','Incorrect filter field have been provided : '.$valFilter['field']);
						continue;
					}
					$field = $arrMeta[$valFilter['field']];
					if(isset($field['dbfield'])){
						$field = $field['dbfield'];
					}else{
						$field = $field['id'];
					}
					
					//fix string value
		       		if( isset($valFilter['data']['value']) ){
						if (is_string($valFilter['data']['value'])) {
							$valFilter['data']['value'] = escape_str($valFilter['data']['value']);
						}
						$valFilter['data']['value'] = conv_string($valFilter['data']['value'], false);
		       		}else{
		       			$valFilter['data']['value'] = '';
		       		}
		       		
		       		//type mapping
		       		$strCond = '';
		       		switch($valFilter['data']['type']){
		       			case 'numeric':
		       			case 'date':
							if('gt' == $valFilter['data']['comparison']) {
			                    $comp = ">";
			                }else if('lt' == $valFilter['data']['comparison']) {
			                    $comp = "<";
			                }else{
			                	$comp = "=";
			                }
			                if ('date' == $valFilter['data']['type']) {
			                	$valFilter['data']['value'] = date('Ymd',strtotime($valFilter['data']['value']));
			                	$strCond .= $field . $comp ."'". $valFilter['data']['value']."'";
			                }else{//numeric
			                    $strCond .= $field . $comp . $valFilter['data']['value'];
			                }
		       				break;
		       			case 'string':
		       				$strCond .= $field . " LIKE '%{$valFilter['data']['value']}%' ";
		       				break;
		       			case 'clist':
		       			case 'list':
			                $values = explode(',', $valFilter['data']['value']);
			                $strCond .= "$field IN ('".implode("','",$values)."') ";
			                unset($values);
		       				break;
		       			case 'nlist':
			                $values = explode(',', $valFilter['data']['value']);
			                $strCond .= "$field IN (".implode(",",$values).") ";
			                unset($values);
		       				break;
		       			default:
		       				log_message('ERROR','Unkown type have been provided : '.$valFilter['data']['type']);
		       				break;
		       		}
		       		if( strlen($strCond )>0 ){
		       			if(strlen($strFilters)>0)	$strFilters .= ' AND ';
		       			$strFilters .= $strCond;
		       		}
				}
			}
			if(strlen($strFilters)<=0){
				$strFilters = '1=1';
			}
			
			//5,prepare data for template replacing
			$this->arrTplDefault = array(	'C_UID'			=> 'get_UID',
											META_SQL_WHERE	=> $this->whereUD,
											META_SQL_FILTER	=> $strFilters,
											META_SQL_START	=> $start,
											META_SQL_LIMIT	=> $limit
									 );
			//6,prepare strOrder
			if(is_array($order) && isset($order['sort']) && !empty($order['sort'])){
				$this->objResult->setSortInfo($order['sort'],isset($order['dir'])?$order['dir']:'ASC');
				$strOrder = $order['sort'];
				if(isset($arrMeta[$strOrder]) && isset($arrMeta[$strOrder][META_KEY_DBFIELD])){
					$strOrder = $arrMeta[$strOrder][META_KEY_DBFIELD];
				}
				$strOrder .= ' '.(isset($order['dir'])?$order['dir']:'ASC');
			/*}elseif(isset($arrAttr[META_KEY_DFTORDER]) && !empty($arrAttr[META_KEY_DFTORDER])){
				$this->objResult->setSortInfo($arrAttr[META_KEY_DFTORDER],'ASC');
				$strOrder = $arrAttr[META_KEY_DFTORDER];*/
			}else{
				//find the first column as the default order column
				foreach($arrMeta as $key => $arrCol){
					if(isset($arrCol[META_KEY_DBFIELD])){
						$strOrder = $arrCol[META_KEY_DBFIELD].' ASC';
					}else{
						$strOrder = $key.' ASC';
					}
					$this->objResult->setSortInfo($key,'ASC');
					
					break;//juest need to get the first one
				}
			}
			$this->arrTplDefault[META_SQL_ORDER] = $strOrder;
			
			//7,prepare rowid
			$this->arrTplDefault[META_SQL_ROWID] = "cast(row_number() over (order by $strOrder) as int) as $rowid";
			
			//8,prepare 'all' & fields
			$strCols = '';
			$fields = array();
			/*if( !isset($arrMeta[$rowid]) ){
				$fields[] = array(META_KEY_ID=>$rowid,META_KEY_NAME=>$rowid,META_KEY_HEADER=>'ID','width'=>8);
			}*/
			foreach($arrMeta as $key => $arrCol){
				//prepare columns list(string)
				if(strlen($strCols)>0)	$strCols .= ',';
				if(isset($arrCol[META_KEY_DBFIELD]) && strcasecmp($arrCol[META_KEY_DBFIELD],$key) !=0 ){
					$strCols .= $arrCol[META_KEY_DBFIELD].' as '.$key;
				}else{
					$strCols .= $key;
				}
				
				//prepare columns list(array)
				$arrTmpFld = array();
				foreach($arrCol as $key => $val){
					if(!in_array($key,$arrHiddenFld)){
						$arrTmpFld[$key] = $val;
					}
				}
				if( !isset($arrTmpFld[META_KEY_NAME]) )		$arrTmpFld[META_KEY_NAME] 		= $arrCol[META_KEY_ID];
				if( !isset($arrTmpFld[META_KEY_HEADER]) )	$arrTmpFld[META_KEY_HEADER] 	= $arrCol[META_KEY_ID];
				
				$fields[] = $arrTmpFld;
				unset($arrTmpFld);
			}
			$this->arrTplDefault[META_SQL_ALL]	= $strCols;
			
			$this->objResult->setFields($fields);
			unset($fields);
			
			//9,merge user provide data with pre-defined data
			if(is_array($this->arrUDV) && count($this->arrUDV)>0){
				$this->arrTplDefault = array_merge($this->arrTplDefault,$this->arrUDV);
			}
			
			//10,template replacing
			if($arrAttr[META_KEY_USETPL]){
				$arrTmpGrid[META_KEY_DATA]	= preg_replace_callback('/(.?)\{([^\}]+)\}/i',array('self','replaceTpl'),$arrTmpGrid[META_KEY_DATA]);
			}
			
			//11,call relevant sub-function to retrieve data
			$mth = __FUNCTION__.'_'.$arrAttr[META_KEY_TYPE];
			if( !method_exists($this,$mth) ){
				$this->objResult->setMessage('Invalide type had been provided : '.$strType);
				log_message('ERROR','Invalide type had been provided : '.$strType);
				$blRtn = false;
				break;
			}else{
				if( !call_user_func_array( array($this,$mth), array(&$arrAttr,&$arrMeta,$arrTmpGrid[META_KEY_DATA])) ){
					$this->objResult->setMessage('Failed to load data.');
					log_message('ERROR','Failed to call sub-function : '.$mth);
					$blRtn = false;
					break;
				}else{
					$this->objResult->setSuccess(true);
				}
			}
		}
		if( !$blRtn ){
			return $this->objResult->getData($reconfigure);
		}

		return $this->objResult->getData($reconfigure);
	}

	/**
	* convert_type : converts an input string into bool, int or float depending on its content
	* 
	* @param string $var	input string
	* @return mixed  value
	*/
	function convert_type( $var ){
	    if( preg_match('/^((-?[0-9]*\.[0-9]+|[[:space:]]*[+-]?[0-9]+(\.[0-9]*)?)([eE][+-]?[0-9]+)?|0x[0-9a-fA-F]+)$/i', $var ) ){
	        if( (float)$var != (int)$var ){
	            return (float)$var;
	        }else{
	            return (int)$var;
	        }
	    }
	    
	    if( strtolower($var) == "true" )	return true;
	    if( strtolower($var) == "false" )	return false;
	    
	    return $var;
	}

	/**
	* object2array : convert object to array
	* 
	* @param object $object	object
	* @return array
	*/
	protected function object2array($object){
		$return = NULL;

		if(is_array($object)){
			$return = array();
			foreach($object as $key => $value){
				$return[$key] = $this->object2array($value);
			}
		}elseif(is_string($object)){
			$return = $object;
		}else{
			$var = @get_object_vars($object);
			if($var){
				$return = array();
				foreach($var as $key => $value){
					$return[$key] = $this->object2array($value);
				}
			}else{
				$return = strval($object);
			}
		}
		return $return;
	}
	
	/**
	* object2array : convert object to array
	* 
	* @param object $object	object
	* @return array
	*/
	protected function &loadConfig($strName, $strType){
		$blResult = false;
		//get configuration file name by grid name
		$strConfigDir = $this->config->item('reports_config');
		if(empty($strConfigDir)){
			$strFileName = '';
		}else{
			$strFileName = $strConfigDir."\\";
		}
		$strFileName .= $strName.'.'.$strType.'.xml';
		
		//check file existing or not
		if( !file_exists($strFileName) ){
			log_message('ERROR','Specified file is not exist : '.$strFileName);
			return $blResult;
		}else{
			log_message('DEBUG','Start to analyze file :'.$strFileName);
		}
		
		//load configuration data
		$objXml = simplexml_load_file($strFileName,'SimpleXMLElement',LIBXML_NOCDATA);
		if( false === $objXml ){
			log_message('ERROR','Failed to load xml file : '.$strFileName);
			return $blResult;
		}
		$arrRtn = $this->object2array($objXml);

		//traverse whole tree
		if( !is_array($arrRtn) || !isset($arrRtn[META_TYPE_GRID]) || !is_array($arrRtn[META_TYPE_GRID]) ){
			log_message('ERROR','Invalide XML layout for '.$strGridName.'(grid)[1].');
			return $blResult;
		}
		
		//fix the unconsistency between single grid and mutiples
		if(isset($arrRtn[META_TYPE_GRID][META_KEY_ATTRI])){
			$arrRtn = array(&$arrRtn[META_TYPE_GRID]);	//single grid
		}else{
			$arrRtn = &$arrRtn[META_TYPE_GRID];			//mutiple grids
		}
		
		foreach($arrRtn as $idx =>&$arrTmpGrid){
			if( !isset($arrTmpGrid[META_KEY_ATTRI]) || !is_array($arrTmpGrid[META_KEY_ATTRI])
				|| !isset($arrTmpGrid[META_KEY_METADATA]) || !isset($arrTmpGrid[META_KEY_METADATA][META_KEY_COLUMN]) 
				|| !isset($arrTmpGrid[META_KEY_DATA]) ){
				log_message('ERROR','Invalide XML layout for '.$strGridName.'(grid)[2].');
				return $blResult;
			}
			
			//fix the unconsistency between single column and mutiples
			if(isset($arrTmpGrid[META_KEY_METADATA][META_KEY_COLUMN][META_KEY_ATTRI])){
				$arrTmpGrid[META_KEY_METADATA][META_KEY_COLUMN] = array($arrTmpGrid[META_KEY_METADATA][META_KEY_COLUMN]);
			}
			
			//grid type
			$arrTmpGrid[META_KEY_ATTRI][META_KEY_TYPE] = strtolower($arrTmpGrid[META_KEY_ATTRI][META_KEY_TYPE]);
			
			//use template or not
			$usetpl = false;
			if(isset($arrTmpGrid[META_KEY_ATTRI][META_KEY_USETPL])
				 && in_array(strtolower(trim($arrTmpGrid[META_KEY_ATTRI][META_KEY_USETPL])),array('1','true'))){
				$usetpl = true;
			}
			$arrTmpGrid[META_KEY_ATTRI][META_KEY_USETPL] = $usetpl;
			
			//use localization or not
			$usel18n = false;
			if(isset($arrTmpGrid[META_KEY_ATTRI][META_KEY_USEL18N])
				 && in_array(strtolower(trim($arrTmpGrid[META_KEY_ATTRI][META_KEY_USEL18N])),array('1','true'))){
				$usel18n = true;
			}
			$arrTmpGrid[META_KEY_ATTRI][META_KEY_USEL18N] = $usel18n;
			
			//gather column meta information under metadata directly(ignore all duplicate columns)
			foreach($arrTmpGrid[META_KEY_METADATA][META_KEY_COLUMN] as $idxCol => &$arrCol){
				if( !isset($arrCol[META_KEY_ATTRI]) ){
					continue;
				}
				
				//convert all attributes with '_' as the sub-array
				$arrTmpCols = array();
				foreach($arrCol[META_KEY_ATTRI] as $keyAttr =>$valAttr ){
					$valAttr = $this->convert_type($valAttr);	//change to right type for int/float/bool
					
					if( preg_match('/^([\w]+)_([\w]+)$/i',$keyAttr,$matches )){
						if(!isset($arrTmpCols[$matches[1]]))	$arrTmpCols[$matches[1]] = array();
						$arrTmpCols[$matches[1]][$matches[2]] = $valAttr;
					}else{
						$arrTmpCols[$keyAttr] = $valAttr;
					}
				}
				
				if(isset($arrTmpCols[META_KEY_ID])){
					$strID = $arrTmpCols[META_KEY_ID];
				}else{
					$strID = strval(count($arrTmpCols)+1);	//missing 'id' in XML
				}
				//ensure 'name' & 'header' had been defined
				if( !isset($arrTmpCols[META_KEY_NAME]) )	$arrTmpCols[META_KEY_NAME] 		= $strID;
				if( !isset($arrTmpCols[META_KEY_HEADER]) )	$arrTmpCols[META_KEY_HEADER] 	= $strID;

				//localize grid name
				$arrMch = null;
				if($usel18n){
					if(preg_match("/^\{(.*)\}$/i",$arrTmpCols[META_KEY_HEADER],$arrMch)){
						if(isset($this->arrUDV[$arrMch[1]])){
							$arrTmpCols[META_KEY_HEADER] = $this->arrUDV[$arrMch[1]];
						}
					}else{
						$strLang = $this->lang->line($arrTmpCols[META_KEY_HEADER]);
						if(!empty($strLang)){
							$arrTmpCols[META_KEY_HEADER] = $strLang;
						}
					}
				}

				$arrTmpGrid[META_KEY_METADATA][$strID] = $arrTmpCols;
				unset($arrTmpCols);
			}
			//delete 'column' attribute under 'metadata'
			unset($arrTmpGrid[META_KEY_METADATA][META_KEY_COLUMN]);
			
			//trim blank for data
			$arrTmpGrid[META_KEY_DATA] = trim($arrTmpGrid[META_KEY_DATA]);
			
			//fix extra information
			if( isset($arrTmpGrid[META_KEY_EXTRA]) && isset($arrTmpGrid[META_KEY_EXTRA][META_KEY_ATTRI]) ){
				$arrTmpGrid[META_KEY_EXTRA] = $arrTmpGrid[META_KEY_EXTRA][META_KEY_ATTRI];
			}else{
				$arrTmpGrid[META_KEY_EXTRA] = array();
			}
		}
		$blResult = true;
		return $arrRtn;
	}
	
	/**
	* createGrid_table : get meta data & data for specified grid
	* 
	* @param $arrAttr		attributes for 'grid' level
	* @param $arrMeta		meta data array
	* @param $strData		data definition
	* @return array			data
	*/
	public function createGrid_table($arrAttr, $arrMeta, $strData) {
		//count rows
		$strWhere = $this->arrTplDefault[META_SQL_WHERE];
		$strSQL = <<<EOSQL
select count(1) as n_count from $strData where $strWhere
EOSQL;
		$arrTmp = $this->daoIns->executeReadSql($strSQL);
		if(false === $arrTmp || !isset($arrTmp[0]) || !isset($arrTmp[0]['n_count']) ){
			log_message('ERROR','Failed to count table : '.$strData);
			return false;
		}
		$this->objResult->setTotal($arrTmp[0]['n_count']);
		unset($arrTmp);
		
		//build SQL statement
		$start		= $this->arrTplDefault[META_SQL_START];
		$to 		= $start + $this->arrTplDefault[META_SQL_LIMIT];
		$strRowId	= $this->arrTplDefault[META_SQL_ROWID];
		$strCols	= $this->arrTplDefault[META_SQL_ALL];
		$rowid		= $arrAttr[META_KEY_ROWID];
		if(!empty($strCols)){
			$strCols = $strRowId.','.$strCols;
		}else{
			$strCols = $strRowId;
		}
		$strSQL = <<<EOSQL
with tmp_grid_data as(
	select top $to $strCols from $strData where $strWhere
)
select * from tmp_grid_data where $rowid >= $start order by keyid
EOSQL;
		$results = $this->daoIns->executeReadSql($strSQL);
		if(false === $results ){
			log_message('ERROR','Failed to retrieve data from table : '.$strData);
			return false;
		}
		$this->objResult->setResults($results);
		return true;
	}
	
	/**
	* createGrid_sql : get meta data & data for specified grid
	* 
	* @param $arrAttr		attributes for 'grid' level
	* @param $arrMeta		meta data array
	* @param $strData		data definition
	* @param $data			template data
	* @param $strWhere		pre-defined condition
	* @param $filters		filter array
	* @param $order			order array
	* @return array			data
	*/
	public function createGrid_sql($arrAttr, $arrMeta, $strData) {
		//retrieve data
		$results = $this->daoIns->executeReadSql($strData);
		if(false === $results ){
			log_message('ERROR','Failed to retrieve data');
			return false;
		}
		
		$this->objResult->setTotal(count($results));
		//mimic limit function for MySQL
		if( count($results)>0 ){
			$results = array_slice($results, $this->objResult->getStart(), $this->objResult->getLimit());
		}
		$this->objResult->setResults($results);
		
		return true;
	}
	
	/**
	* replaceTpl : replace toolkit for template
	* 
	* @param $matches		matched array object
	* @return string		data
	*/
	protected function replaceTpl($matches){
		//remove the escape character '\'
		if("\\" == $matches[1])	return '{'.$matches[2].'}';
		
		//normal case,return the head-first character and the mached data in pre-defined array
		if(isset($this->arrTplDefault[$matches[2]]))
			return $matches[1].$this->arrTplDefault[$matches[2]];
		
		//return the same in case of no defined data can be retrieved
		return $matches[0];
	}
}
