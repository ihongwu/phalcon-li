<?php
/**
 * 日志记录类
 * @author lihongwu <lihongwu@weizaojiao.cn>
 */
use Phalcon\Mvc\Controller;
class Log extends ControllerBase
{
	private $application = 'app';
	private static $_instance = null;
	private $db;
    private $request_log_table;  ##请求日志表名
    private $error_log_table;    ##错误日志表名

	function onConstruct(){
		if(strpos($_SERVER['CONTENT_TYPE'], 'application/json')!==false){
            $this->get = array_merge($_GET,$_POST,array('token'=>$_SERVER['HTTP_TOKEN'],'json'=>json_decode(str_replace(["\n","\r","\t"], '', file_get_contents('php://input')),true)));
        }else{
            $this->get = array_merge($_GET,$_POST,array('token'=>$_SERVER['HTTP_TOKEN']));
        }
        $this->get['_url'] = !empty($this->get['_url']) ? $this->get['_url'] : '/Index/index';
		$this->db = Mysql::start();
        $this->application       = $this->c->get('application');
        $this->request_log_table = $this->c->get('request_log_table');
        $this->error_log_table   = $this->c->get('error_log_table');
	}

	/**
	 * 单例，避免过多占用资源
	 * @return [type] [description]
	 */
	static public function start() {
        if (self::$_instance==null) {
            self::$_instance = new self ();
        }
        return self::$_instance;
    }

    /**
     * 错误日志
     * @return [type] [description]
     */
    public function error($error){
        if (!$this->c->get('error_log')) {
            return false;
        }
    	$errorfn = $this->get_error_type($error).'_error';
    	return $this->$errorfn($error);
    }

    /**
     * 获取错误类型：SQL错误或者其他错误
     * @param  [type] $error [description]
     * @return [type]        [description]
     */
    private function get_error_type($error){
    	if (strpos(strtolower($error),'sqlstate')!==false) {
    		return 'sql';
    	}else{
    		return 'other';
    	}
    }

    /**
     * SQL错误记录-私有方法，只能自调用
     * @param  [type] $error [description]
     * @return [type]        [description]
     */
    private function sql_error($error){
    	$data['type'] = 'sql';
    	$data['action'] = $this->get['_url'];
    	$data['sqlstr'] = $this->db->getlastsql();
    	$data['message'] = $error;
    	$data['application'] = $this->application;
    	$data['ctime'] = date('Y-m-d H:i:s',time());
    	$data['utime'] = date('Y-m-d H:i:s',time());
    	return $this->db->table($this->error_log_table)->add($data);
    }

    /**
     * 其他错误记录-私有方法，只能自调用
     * @param  [type] $error [description]
     * @return [type]        [description]
     */
    private function other_error($error){
    	$data['type'] = 'other';
    	$data['action'] = $this->get['_url'];
    	$data['message'] = $error;
    	$data['application'] = $this->application;
    	$data['ctime'] = date('Y-m-d H:i:s',time());
    	$data['utime'] = date('Y-m-d H:i:s',time());
    	return $this->db->table($this->error_log_table)->add($data);
    }

    /**
     * 检测是否需要记录请求日志
     * @return [type] [description]
     */
    private function check_request_status(){
        if (!$this->c->get('request_log')) {
            return false;
        }
        $action = explode('/', $this->get['_url']);
        unset($action['0']);
        $action = array_values($action);
        $request_log_list = $this->c->get('request_log_list');
        $result = false;
        ##如果日志记录配置列表非数组或者数组长度为0，则记录所有请求日志
        if (!is_array($request_log_list) || count($request_log_list)==0) {
            $result = true;
        }else{
            ##如果日志记录配置列表有值，则只记录有配置的请求日志
            foreach ($request_log_list as $key => $value) {
                $listconfig = explode('/',$value);
                if ($action['0']===$listconfig['0'] && ($listconfig['1']==='*' || $listconfig['1']===$action['1'])) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * 请求日志
     * @param  [string] $requestid   当前请求ID
     * @param  [array] $requestdata  请求数据
     * @param  [array] $returndata   返回数据
     * @return [type]              [description]
     */
    public function request($requestid,$requestdata,$returndata){
        if (!$this->check_request_status()) {
            return false;
        }
    	$data['action'] = $this->get['_url'];
    	$data['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    	$data['requestid'] = $requestid;
    	$data['requestdata'] = json_encode($requestdata);
    	$data['returndata'] = json_encode($returndata);
    	$data['application'] = $this->application;
    	$data['ctime'] = date('Y-m-d H:i:s',time());
    	$data['utime'] = date('Y-m-d H:i:s',time());
    	return $this->db->table($this->request_log_table)->add($data);
    }
}
?>