<?php
use Phalcon\Mvc\Controller;
class ControllerBase extends Controller
{	
	public $get;
    public $action;
	public function onConstruct(){
		##试探请求直接返回ok，不返回其他数据（跨域发送的试探请求）
        if ($_SERVER['REQUEST_METHOD']=='OPTIONS') {
            echo 'ok';exit;
        }
        ##统一获取参数
        if(strpos($_SERVER['CONTENT_TYPE'], 'application/json')!==false){
            $this->get = array_merge($_GET,$_POST,array('token'=>$_SERVER['HTTP_TOKEN'],'json'=>json_decode(str_replace(["\n","\r","\t"], '', file_get_contents('php://input')),true)));
        }else{
            $this->get = array_merge($_GET,$_POST,array('token'=>$_SERVER['HTTP_TOKEN']));
        }

        $this->action = $this->getAction();
        $this->get['_url'] = implode('/',$this->action);

		##获取需要登录的控制器方法名称列表
		$must_login_method = $this->c->get('must_login_method');

        ##获取视图是否开启配置
        $open_view = $this->c->get('open_view');

		##验证token
		##需要验证登录的方法进入token验证逻辑
		if($this->checkIslogin() && $open_view===false){
			if (!$this->token->check($this->get['token'])) {
				$this->result->show(400,'token验证失败');
			}
		}
        
        ##如果视图配置是关闭的，则在基类里直接调用子类方法，子类方法无需加框架要求的Action
        if ($open_view===false) {
            $action = $this->action['1'];
            $this->$action();
            exit;
        }
	}

    /**
     * 获取访问的控制器/方法
     * @return [array] 控制器方法数组
     */
	public function getAction(){
		$action      = explode('/', $this->get['_url']);
        unset($action['0']);
        $action      = array_values($action);
        $action_arr  = array();
        $action_arr['0'] = !empty($action['0']) ? $action['0'] : 'Index';
        $action_arr['1'] = !empty($action['1']) ? $action['1'] : 'index';
        return $action_arr;
	}

	/**
     * 检测是否需要登录
     * @return [boolean] 检测结果
     */
    private function checkIslogin(){
        $must_login_method = $this->c->get('must_login_method');
        $result = false;
        ##如果登录配置列表非数组或者数组长度为0，则表示所有都不需要登录直接放行
        if (!is_array($must_login_method) || count($must_login_method)==0) {
            $result = false;
        }elseif(in_array(implode('/', $this->action), $this->c->get('no_login_method'))){
        	$result = false;
        }else{
            ##如果需要登录验证的方法数组列表有值，则只拦截有配置的方法
            foreach ($must_login_method as $key => $value) {
                $listconfig = explode('/',$value);
                if ($this->action['0']===$listconfig['0'] && ($listconfig['1']==='*' || $listconfig['1']===$this->action['1'])) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

}
