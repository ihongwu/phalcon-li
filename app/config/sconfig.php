<?php
/**
 * 配置文件数组
 * @author lihongwu <lihongwu@weizaojiao.cn>
 */
return array(
	'application'       => 'app2',
	'mysql' => array(
		'connection' => 'conn1',
		'conn1' => array(
			'host' 		=> '127.0.0.1,127.0.0.1,127.0.0.1',
		    'username'  => 'root',
		    'password'  => 'root',
		    'database'  => 'phalcon',
		    'prefix'    => 'phalcon_',
		),
		'conn2' => array(
			'host' 		=> '127.0.0.1,127.0.0.1',
		    'username'  => 'root',
		    'password'  => 'root',
		    'database'  => 'phalcon',
		    'prefix'    => 'phalcon_',
		),
	),
	'open_view'			=> false,           ##是否开启视图，如果开启视图，方法名称需要加Action，反之则不需要加
	'login_ip_limit'	=> false,            ##是否开启登录信息区分IP地址，如果开启，用户更换IP后将会被强制下线，建议false
	'token_table'		=> 'user_token',  	##用户登录token存储表名，不含前缀
	'request_log_table'	=> 'request_log', 	##请求日志记录表名，不含前缀
	'error_log_table'	=> 'error_log',     ##错误日志表名，不含前缀

	'error_log'         => true,       ##错误日志记录开关
	'request_log'       => true,       ##请求日志记录开关
	##请求日志记录列表（request_log=true有效）,方法名称支持*通配符，如Index/*,代表Index控制器下面所有方法都记录，区分大小写，留空代表全部要记录
	'request_log_list'  => array(
							 // 'Index/index'
						  ),
	'token'				=> 'mysql',    ##token存储类型：mysql、redis等，暂时只支持mysql
	'token_type'		=> '1',        ##token类型，1：支持多点登录（多个设备登录同一个账号），2：只能一个设备登录

	##需要登录验证的方法数组列表,方法名称支持*通配符，如Index/*,代表Index控制器下面所有方法都需要验证，区分大小写，留空代表全部不用登录
	'must_login_method' => array(
							'Index/index',
						),

	/**
	 * 不需要登录验证的方法数组列表，不支持通配符，通常用于当前控制器所有方法被登录拦截，其中的某一个方法不想被拦截的情况
	 */
	'no_login_method'	=> array(
							'Index/login'
						),
);
?>