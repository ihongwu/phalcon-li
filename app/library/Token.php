<?php
/**
 * 生成登录token和校验登录token
 * 可以通过配置文件设置将token存储到不同的位置，如：redis，memcached中
 * 如果需要存储到redis或memcached中需要另行开发相应的功能（实现setTokenRedis方法和checkTokenRedis方法即可）
 * @author lihongwu <lihongwu@weizaojiao.cn>
 */
use Phalcon\Mvc\Controller;
class Token extends ControllerBase
{
	private static $_instance = null;
	private $default_timeout = 30;         ##默认登录token超时时长（天）
	private $application;
	private $token_table;   ##token表名

	function onConstruct()
	{
		$this->db = Mysql::start();
		$this->tokentype 	 = ucfirst(strtolower($this->c->get('token'))); ##要调用的实际存储的方法
		$this->application 	 = $this->c->get('application');
		$this->token_table   = $this->c->get('token_table');
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
     * 设置token的方法
     * @param integer $uid     用户ID
     * @param integer $timeout 超时时间（秒）
     * @return  integer 获取到的token字符串
     */
	public function set($uid=0,$timeout=0){
		if (intval($uid)=='0') {
			return false;
		}
		$setTokenfn = 'setToken'.$this->tokentype;
		return $this->$setTokenfn((int)$uid,(int)trim($timeout));
	}

	/**
	 * 检测token是否有效
	 * @param  [type] $token token字符串
	 * @return [type]        返回用户ID
	 */
	public function check($token){
		$token = strip_tags(trim($token));
		if ($token=='') {
			return false;
		}
		$checkTokenfn = 'checkToken'.$this->tokentype;
		return $this->$checkTokenfn($token);
	}

	/**
	 * 退出登录
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
	public function logout($token){
		$token = strip_tags(trim($token));
		if ($token=='') {
			return false;
		}
		$logoutTokenfn = 'logoutToken'.$this->tokentype;
		return $this->$logoutTokenfn($token);
	}

	/**
	 * 根据用户ID生成token
	 * @param  [type] $uid 用户ID
	 * @return [type]      token字符串
	 */
	private function generateToken($uid){
		return md5($uid.uniqid().time().rand(100000,999999));
	}

	/**
	 * 根据超时时间获取到实际到期的时间
	 * @param  [type] $timeout 超时时间
	 * @return [type]          实际到期的时间
	 */
	private function getTimeout($timeout=0){
		if ($timeout==0) {
			return time()+$this->default_timeout*86400;
		}else{
			return time()+$timeout;
		}
	}

	/**
	 * 将token存储到mysql中
	 * @param [type] $uid     用户ID
	 * @param [type] $timeout 超时时间
	 */
	private function setTokenMysql($uid,$timeout){
		if ($uid==0) {
			return false;
		}
		$data['uid'] 			= $uid;
		$data['token'] 			= $this->generateToken($uid);
		$data['application']    = $this->application;
		$data['ip']				= $_SERVER['REMOTE_ADDR'];
		$data['endtime'] 	    = date('Y-m-d H:i:s',$this->getTimeout($timeout));
		if ($this->c->get('token_type')==1) {
			##支持多设备登录
			$data['ctime'] 			= date('Y-m-d H:i:s',time());
			$res = $this->db->table($this->token_table)->add($data);
		}else{
			$lastlogininfo = $this->db->table($this->token_table)->where("uid='{$uid}'")->order('id desc')->find();
			if ($lastlogininfo) {
				$data['islogout'] = 0;
				$data['logouttime'] = '0000-00-00 00:00:00';
				$res = $this->db->table($this->token_table)->where("uid='{$uid}' and id='{$lastlogininfo['id']}'")->save($data);
				$this->db->table($this->token_table)->where("id='{$lastlogininfo['id']}'")->setInc('loginnum');
			}else{
				$data['ctime'] 			= date('Y-m-d H:i:s',time());
				$res = $this->db->table($this->token_table)->add($data);
			}
			
		}
		if ($res) {
			return $data['token'];
		}else{
			return '';
		}
	}

	/**
	 * 从数据库中检测token是否有效
	 * @param  [type] $token token字符串
	 * @return [type]        用户ID
	 */
	private function checkTokenMysql($token){
		$where = "token='{$token}' and ip='{$_SERVER['REMOTE_ADDR']}' and islogout=0 and application='{$this->application}' and endtime>'".date('Y-m-d H:i:s',time())."'";
		if ($this->c->get('login_ip_limit')===false) {
			$where = "token='{$token}' and islogout=0 and application='{$this->application}' and endtime>'".date('Y-m-d H:i:s',time())."'";
		}
		// echo $where;
		return $this->db->table($this->token_table)->where($where)->order('id desc')->getField('uid');
	}

	/**
	 * 从数据库退出登录，将是否退出字段（islogout）改成1
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
	private function logoutTokenMysql($token){
		$where = "token='{$token}' and ip='{$_SERVER['REMOTE_ADDR']}' and islogout=0 and application='{$this->application}' and endtime>'".date('Y-m-d H:i:s',time())."'";
		if ($this->c->get('login_ip_limit')===false) {
			$where = "token='{$token}' and islogout=0 and application='{$this->application}' and endtime>'".date('Y-m-d H:i:s',time())."'";
		}
		return $this->db->table($this->token_table)->where($where)->order('id desc')->save(array('islogout'=>1,'logouttime'=>date('Y-m-d H:i:s',time())));
	}


}
?>