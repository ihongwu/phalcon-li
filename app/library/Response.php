<?php
/**
 * 数据返回类
 * @author lihongwu <lihongwu@weizaojiao.cn>
 */
use Phalcon\Mvc\Controller;
class Response extends ControllerBase
{
	private static $_instance = null;

	function onConstruct()
	{
		$this->log = Log::start();
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

    public function data_encode($data){

    }

    /**
     * 数据返回方法
     * @param  [int]    $code    状态码
     * @param  [string] $message 提示信息
     * @param  [array]  $data    返回数据
     * @return [json]            返回结果
     */
	public function show($code,$message,$data){
		$requestid = md5(uniqid().time().rand(100000,999999));
		if (!is_numeric($code)) {return '';}
        if (is_array($data)) {
            if (count($data)==0) {
                $result = array('code' => $code,'requestid' => $requestid,'message' => $message,'data' => NULL);
            }else{
                $result = array('code' => $code,'requestid' => $requestid,'message' => $message,'data' => $data);
            }
        }else{
            $result = array('code' => $code,'requestid' => $requestid,'message' => $message);
        }
        $requestdata = array(
            'TOKEN'=>$_SERVER['HTTP_TOKEN'],
			'GET' => $_GET,
			'POST'=> $_POST,
		);
		$this->log->request($requestid,$requestdata,$result);
        echo json_encode($result);
        exit();
	}

}
?>