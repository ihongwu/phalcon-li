<?php
/**
 * Curl模拟请求类
 * @author lihongwu <lihongwu@weizaojiao.cn>
 */
class Curl {    
    private $url = '';           ## 访问的url
    private $oriUrl = '';        ## referer url
    private $data = array();     ## 可能发出的数据 post,put
    private $header = array();   ##header头
    private $method;             ## 访问方式，默认是GET请求
    private static $_instance = null;
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

    private function send($url, $data = array(), $method = 'get',$header=array()) {
        if (!$url) exit('URL不能为空');
        $this->url = $url;
        $this->method = $method;
        $urlArr = parse_url($url);
        $this->oriUrl = $urlArr['scheme'] .'://'. $urlArr['host'];
        $this->data = $data;
        if ( !in_array($this->method,array('get', 'post', 'put', 'delete'))) {
            exit('错误的请求方法!');
        }
        $this->setHeader($header);  ##设置header头
        $func = $this->method . 'Request';
        return $this->$func($this->url);
    }

    /**
     * 设置header头
     * @param array $data [description]
     */
    private function setHeader($data = array()){
        $header = array();
        foreach ($data as $key => $value) {
            $header[] = ''.$key.': '.''.$value.'';
        }
        $this->header = $header;
    }
    /**
     * 基础发起curl请求函数
     * @param int $is_post 是否是post请求
     */
    private  function doRequest($is_post = 0) {
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $this->url); ##抓取指定网页
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        // 来源设置成来自本站
        curl_setopt($ch, CURLOPT_REFERER, $this->oriUrl);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); ##要求结果为字符串且输出到屏幕上
        if($is_post == 1) curl_setopt($ch, CURLOPT_POST, $is_post);//post提交方式
        if (!empty($this->data)) {
            $this->data = $this->dealPostData($this->data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        $data = curl_exec($ch); ##运行curl    
        curl_close($ch);
        return $data;
    }

    /**
     * 处理发起非get请求的传输数据
     * 
     * @param array $postData
     */
    private function dealPostData($postData) {
        if (!is_array($postData)) exit('post data should be array');
        foreach ($postData as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $postData = substr($o, 0, -1);
        return $postData;
    }

    /**
     * 发起get请求
     */
    private function getRequest() {
        return $this->doRequest(0);
    }
    /**
     * 发起post请求
     */
    private function postRequest() {
        return $this->doRequest(1);
    }
    
    /**
     * 发起put请求
     */
    private function putRequest($param) {
        return $this->doRequest(2);
    }
    
    /**
     * 发起delete请求
     */
    private function deleteRequest($param) {
        return $this->doRequest(3);
    }

    /**
     * 对外提供的get请求方法
     * @param  [type] $url    要请求的URL
     * @param  array  $data   参数数组
     * @param  array  $header header头数组
     * @return [type]         请求结果
     */
    public function get($url,$data=array(),$header=array()){
        return $this->send($url,$data,'get',$header);
    }

    /**
     * 对外提供的post请求方法
     * @param  [type] $url    要请求的URL
     * @param  array  $data   参数数组
     * @param  array  $header header头数组
     * @return [type]         请求结果
     */
    public function post($url,$data=array(),$header=array()){
        return $this->send($url,$data,'post',$header);
    }
}