<?php
/**
 * 配置文件操作类
 * @author lihongwu <lihongwu@weizaojiao.cn>
 * @since   1.0
 */
class Config
{
        public $config;
        public $configfile;

        /**
         * 获取配置文件内容
         * @access public
         * @return array
         * @since  1.0
         */
        public function getconfig(){
            if(!$this->configfile){
                $this->configfile = include APP_PATH.'/config/sconfig.php';
            }
            return $this->configfile;
        }

        /**
         * 根据下标获取具体的配置文件内容，多维数组下标用 . 连接，如：mysql.conn1.master
         * @param  String $key 要获取的配置下标，如：mysql.conn1.master
         * @return Array、String  配置结果       
         */
        public function get($key){
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
}
?>