<?php
/**
* 数据库操作类
* @author lihongwu <lihongwu@weizaojiao.cn>
*/
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
class Mysql
{	
	public $dbconn;  //链接数据库对象
	public $wdbconn;
	public $conn;
	public $master = false;
	public $dbconfig;
	public $table;
	public $field;
	public $where;
	public $group;
	public $having;
	public $order;
	public $limit;
	public $switchd_db;
	public $prefix;
	public $configfile;
	public $lastsql;
	private static $_instance = null;
	function __construct(){
		$this->conn  = $this->get('mysql.connection');
		$this->master= false;
		$this->field = '*';
		$this->where = 1;
		$this->group = '';
		$this->having='';
		$this->order = '';
		$this->limit = '';
		$this->switchd_db = false;
		$this->lastsql = '';
	}

	/**
	 * 单例，避免过多占用资源
	 * @return [type] [description]
	 */
	public static function start() {
        if (self::$_instance==null) {
            self::$_instance = new self ();
        }
        return self::$_instance;
    }

	/**
	 * 获取数据库配置项，如果是查询，将获取到所有数据库中的其中的一个配置，如果是写数据，则只读取写库的配置
	 * @return [type] [description]
	 */
	private function getdataconf(){
		$result = array();
		if ($this->conn=='') {
			$this->conn = $this->get('mysql.connection');
		}
		$config = $this->get('mysql.'.$this->conn);
		$this->prefix = $config['prefix'];
		if (is_array($config) && isset($config['host'])) {
			$hostkey   = 0;
			$userkey   = 0;
			$passkey   = 0;
			$datakey   = 0;
			$prefixkey = 0;
			$host      = explode(',',$config['host']);
			$user      = explode(',',$config['username']);
			$pass      = explode(',',$config['password']);
			$data      = explode(',',$config['database']);
			$prefix    = explode(',',$config['prefix']);
			if (!$this->master) {  //如果没有指定需要使用主数据库
				$hostkey = rand(0,count($host)-1);
				if (isset($user[$hostkey])) {
					$userkey = $hostkey;
				}
				if (isset($pass[$hostkey])) {
					$passkey = $hostkey;
				}
				if (isset($data[$hostkey])) {
					$datakey = $hostkey;
				}
				if(isset($prefix[$hostkey])){
					$prefixkey = $hostkey;
				}
			}
			
			$result['host'] = trim($host[$hostkey]);
			$result['username'] = $user[$userkey];
			$result['password'] = $pass[$passkey];
			$result['database'] = $data[$datakey];
			$result['prefix']   = $prefix[$prefixkey];
		}
		return $this->dbconfig = $result;
	}

	/**
	 * 获取数据库链接（读数据使用该方法）
	 * @return [source] 数据库链接资源
	 */
	private function getconnect(){
		if ($this->dbconn && $this->switchd_db==false) {
            return $this->dbconn;
        } else {
        	$config = $this->getdataconf();
            $this->dbconn = $nowdb = new PdoMysql(
                [
                    'host'     =>$config['host'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'dbname'   => $config['database'],
                ]
            );

            //如果切换数据库状态为true
            if ($this->switchd_db==true) {
            	//切换指定的数据库以后需要将状态切换为false，方便下一个sql执行切换到默认库
            	$this->switchd_db = false;
            	//将当前的连接资源设为false，避免下次调用时获取得是上一个切换的库
            	$this->dbconn = false;
            	//将数据库链接项设为空，下次调用不指定数据库时使用默认数据库来链接
            	$this->conn = '';
            }
            return $nowdb;
        }
	}

	/**
	 * 获取数据库链接（写数据使用该方法）
	 * @return [source] 数据库链接资源
	 */
	private function getwriteconnect(){
		$this->master = true;
		if ($this->wdbconn && $this->switchd_db==false) {
            return $this->wdbconn;
        } else {
        	$config = $this->getdataconf();
            $this->wdbconn = $nowdb = new PdoMysql(
                [
                    'host'     =>$config['host'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'dbname'   => $config['database'],
                ]
            );
            //如果切换数据库状态为true
            if ($this->switchd_db==true) {
            	//切换指定的数据库以后需要将状态切换为false，方便下一个sql执行切换到默认库
            	$this->switchd_db = false;
            	//将当前的连接资源设为false，避免下次调用时获取得是上一个切换的库
            	$this->wdbconn = false;
            	//将数据库链接项设为空，下次调用不指定数据库时使用默认数据库来链接
            	$this->conn = '';
            }
            $this->master = false;
            return $nowdb;
        }
	}

	/**
	 * 设置数据库配置的链接（使用哪一个数据库配置项）
	 * @return [type] [description]
	 */
	public function connect($conn=''){
		$this->conn = $conn;
		// 若果调用了该方法，将是否切换数据库设置为true，告诉获取数据库连接的方法不要使用之前的连接了，该切换了
		$this->switchd_db = true;
		return $this;
	}

	/**
	 * 设置是否使用主数据库
	 * @param  boolean $master [description]
	 * @return [type]          [description]
	 */
	public function master($master=false){
		$this->master = $master;
		return $this;
	}

	/**
	 * 设置表名，必须调用
	 * @param  string $table [description]
	 * @return [type]        [description]
	 */
	public function table($table=''){
		$this->table = $table;
		return $this;
	}

	/**
	 * 设置查询字段
	 * @param  string $field [description]
	 * @return [type]        [description]
	 */
	public function field($field='*'){
		$field = trim($field);
		$field = empty($field) ? '*' : $field;
		$this->field = $field;
		return $this;
	}

	/**
	 * 设置查询或修改条件，修改时必须调用
	 * @param  integer $where [description]
	 * @return [type]         [description]
	 */
	public function where($where=1){
		$this->where = $where;
		return $this;
	}

	/**
	 * 设置查询的分组
	 * @param  string $group 分组规则
	 * @return [type]        [description]
	 */
	public function group($group=''){
		$this->group = $group;
		return $this;
	}

	public function having($having=''){
		$this->having = $having;
		return $this;
	}

	/**
	 * 设置查询排序
	 * @param  string $order [description]
	 * @return [type]        [description]
	 */
	public function order($order=''){
		$this->order = $order;
		return $this;
	}

	/**
	 * 设置查询limit限制
	 * @param  string $limit [description]
	 * @return [type]        [description]
	 */
	public function limit($limit=''){
		$this->limit = $limit;
		return $this;
	}

	/**
	 * 初始化数据库方法参数，执行完sql语句后调用本方法还原
	 * @return [type] [description]
	 */
	private function resetdb(){
		$this->master();
		$this->table();
		$this->field();
		$this->where();
		$this->group();
		$this->order();
		$this->limit();
	}

	private function getgroup(){
		if ($this->group=='') {
			return '';
		}else{
			return 'GROUP BY '.$this->group;
		}
	}

	private function gethaving(){
		if ($this->having=='') {
			return '';
		}else{
			return 'HAVING '.$this->having;
		}
	}

	/**
	 * 获取排序组装的字符串
	 * @return [type] [description]
	 */
	private function getorder(){
		if ($this->order=='') {
			return '';
		}else{
			return 'ORDER BY '.$this->order;
		}
	}

	/**
	 * 获取limit组装的字符串
	 * @return [type] [description]
	 */
	private function getlimit(){
		if ($this->limit=='') {
			return '';
		}else{
			return 'LIMIT '.$this->limit;
		}
	}

	/**
	 * 获取表名
	 * @return [type] [description]
	 */
	private function gettable(){
		return $this->dbconfig['prefix'].$this->table;
	}

	public function getlastsql(){
		return $this->lastsql;
	}

	/**
	 * 单条查询
	 * @return [type] [description]
	 */
	public function find(){
		$dbconn = $this->getconnect();
		$group  = $this->getgroup();
		$having  = $this->gethaving();
        $order  = $this->getorder();
        $table = $this->gettable();
        $this->lastsql = "SELECT {$this->field} FROM {$table} WHERE {$this->where} {$group} {$having} {$order} LIMIT 1";
        $result = $dbconn->prepare($this->lastsql);
        $result->execute();
        $this->resetdb();
        return $result->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * 查询多条
	 * @return [type] [description]
	 */
	public function select(){
		$dbconn = $this->getconnect();
		$group  = $this->getgroup();
		$having  = $this->gethaving();
		$order  = $this->getorder();
		$limit = $this->getlimit();
        $table = $this->gettable();
        $this->lastsql    = "SELECT {$this->field} FROM {$table} WHERE {$this->where} {$group} {$having} {$order} {$limit}";
        $result = $dbconn->prepare($this->lastsql);
        $result->execute();
        $this->resetdb();
        return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * 获取某一个字段值
	 * @param  [String] $field 字段名称
	 * @return [any]        [字段结果]
	 */
	public function getField($field){
		$dbconn = $this->getconnect();
		$group  = $this->getgroup();
		$having  = $this->gethaving();
        $order  = $this->getorder();
        $table = $this->gettable();
        $this->lastsql    = "SELECT {$field} FROM {$table} WHERE {$this->where} {$group} {$having} {$order} LIMIT 1";
        $result = $dbconn->prepare($this->lastsql);
        $result->execute();
        $this->resetdb();
        $result = $result->fetch(PDO::FETCH_ASSOC);
        return $result[$field];
	}

	/**
	 * 新增数据
	 * @param array $data 一维数组的键值对数据
	 */
	public function add($data = array()){
		$datalen = count($data);
		if (!is_array($data) || $datalen==0) {
			echo '数据不能为空';exit;
		}
        $field = implode(',', array_keys($data));
        $value = '';
        foreach ($data as $v) {
        	$value .= "'".str_replace("'","\'",$v)."'".',';
        }
        $value = substr($value, 0, -1);

        $dbconn = $this->getwriteconnect();
        $order  = $this->getorder();
        $table = $this->gettable();
        $this->lastsql    = "INSERT INTO {$table} ({$field}) VALUES ({$value})";
        $result = $dbconn->prepare($this->lastsql);
        $res = $result->execute();
        $this->resetdb();
        if($result->rowCount()>0){
        	return $dbconn->lastInsertId();
        }else{
        	return false;
        }
	}

	/**
	 * 修改数据--调用该方法必须调用where()方法，$data为数组且不能为空
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function save($data = array()){
		$datalen = count($data);
		if (!is_array($data) || $datalen==0) {
			echo '数据不能为空';exit;
		}
		if ($this->where==1) {
			echo '修改数据时必须调用where()方法';exit;
		}
		$condtion = '';
        foreach ($data as $k => $v) {
            $condtion .= $condtion ? ",$k='".str_replace("'","\'",$v)."'" : " $k='".str_replace("'","\'",$v)."'";
        }
        $dbconn = $this->getwriteconnect();
        $table = $this->gettable();
        $this->lastsql = "UPDATE {$table} SET {$condtion} WHERE {$this->where}";
        $result = $dbconn->prepare($this->lastsql);
        $res = $result->execute();
        $this->resetdb();
        //execute方法只要sql语句正确总是返回true，所以需要根据影响的行数来判断是否执行成功
        if($result->rowCount()>0){
        	return true;
        }else{
        	return false;
        }
	}

	public function delete(){
		if ($this->where==1 || strpos(trim($this->where),'1=1') !== false) {
			echo '删除数据时必须调用where()方法且不能含有危险条件';exit;
		}
		$dbconn = $this->getwriteconnect();
        $table = $this->gettable();
        $this->lastsql = "DELETE FROM {$table} WHERE {$this->where}";
        $result = $dbconn->prepare($this->lastsql);
        $result->execute();
        $this->resetdb();
        //execute方法只要sql语句正确总是返回true，所以需要根据影响的行数来判断是否执行成功
        if($result->rowCount()>0){
        	return true;
        }else{
        	return false;
        }
	}

	/**
	 * 设置字段增减数字值-只能内部调用
	 * @param [type]  $field  [description]
	 * @param integer $number [description]
	 * @param [type]  $type   [description]
	 */
	private function setFieldNumber($field,$number=1,$type){
		if ($this->where==1) {
			echo '必须调用where方法';exit;
		}
		$dbconn = $this->getwriteconnect();
        $table = $this->gettable();
        $number = (int)$number;
        $condtion = "{$field}={$field}{$type}{$number}";
        $this->lastsql = "UPDATE {$table} SET {$condtion} WHERE {$this->where}";
        $result = $dbconn->prepare($this->lastsql);
        $res = $result->execute();
        $this->resetdb();
        //execute方法只要sql语句正确总是返回true，所以需要根据影响的行数来判断是否执行成功
        if($result->rowCount()>0){
        	return true;
        }else{
        	return false;
        }
	}

	/**
	 * 设置字段加值
	 * @param [type]  $field  字段名称
	 * @param integer $number 要增加的数值
	 */
	public function setInc($field,$number=1){
		return $this->setFieldNumber($field,$number,'+');
	}

	/**
	 * 设置字段减值
	 * @param [type]  $field  字段名称
	 * @param integer $number 要减掉的数值
	 */
	public function setDec($field,$number=1){
		return $this->setFieldNumber($field,$number,'-');
	}

	/**
	 * 通用查询方法，用于复杂sql
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function query($sql){
		$sqltype = $this->getSqlType($sql);
		$this->lastsql = $sql;
		if($sqltype=='select'){
			$dbconn = $this->getconnect();
	        $result = $dbconn->prepare($sql);
	        $result->execute();
	        $this->resetdb();
	        return $result->fetchAll(PDO::FETCH_ASSOC);
		}elseif($sqltype=='insert'){
			$dbconn = $this->getwriteconnect();
			$result = $dbconn->prepare($sql);
	        $res = $result->execute();
	        $this->resetdb();
	        if($result->rowCount()>0){
	        	return $dbconn->lastInsertId();
	        }else{
	        	return false;
	        }
		}elseif($sqltype=='update' || $sqltype=='delete'){
			$dbconn = $this->getwriteconnect();
			$result = $dbconn->prepare($sql);
	        $res = $result->execute();
	        $this->resetdb();
	        if($result->rowCount()>0){
	        	return true;
	        }else{
	        	return false;
	        }
		}else{
			return false;
		}
	}

	private function getSqlType($sql){
		$sqltrim = str_replace(' ', '', strtolower($sql));
		if (strpos($sqltrim,'select') === 0 && in_array('select', explode(' ',$sql))) {
			return 'select';
		}elseif(strpos($sqltrim,'insert') === 0 && in_array('insert', explode(' ',$sql))){
			return 'insert';
		}elseif(strpos($sqltrim,'delete') === 0 && in_array('delete', explode(' ',$sql))){
			return 'delete';
		}elseif(strpos($sqltrim,'update') === 0 && in_array('update', explode(' ',$sql))){
			return 'update';
		}else{
			return '';
		}
	}


	private function getconfig(){
        if(!$this->configfile){
            $this->configfile = include APP_PATH.'/config/wzjconfig.php';
        }
        return $this->configfile;
    }

    /**
     * 根据下标获取具体的配置文件内容，多维数组下标用 . 连接，如：Config::get('mysql.conn1.master');
     * @param  String $key 要获取的配置下标，如：mysql.conn1.master
     * @return Array、String  配置结果       
     */
    private function get($key){
        $configres = $this->getconfig();
        $pointer   = &$configres;
        if(trim($key)!=''){
            $key = explode('.',$key);
        }
        foreach($key as $key=>$value){
            if (isset($pointer[$value])) {
                $pointer = &$pointer[$value];
            } else {
                $pointer = '';
                break;
            }
        }
        return $pointer;
    }

    function __destruct(){
    	$this->wdbconn = null;
    	$this->dbconn = null;
    }

}
?>