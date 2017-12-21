<?php

class IndexController extends ControllerBase
{

	function onConstruct(){
        parent::onConstruct();
	}

    public function index()
    {
        // $org = $this->db->table('org')->field('ddd')->select();
        // var_dump($this->get['json']);exit;
        // echo $this->get['json']['age'];
        $this->result->show(200,'hello world',$this->get);
        $this->token->check('token');
    }

    /**
     * 开启视图功能演示方法
     * @return [type] [description]
     */
    public function indexAction(){
        $this->view->disable();
        echo 111;
    }

    public function logout(){
    	$this->result->show(1,'退出结果',array('result'=>$this->token->logout($this->get['token'])));
    }

    public function login(){
    	$this->result->show(1,'登录结果',array('result'=>$this->token->set('1')));
    }
}

