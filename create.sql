-- phalcon修改版需要建立3张表，错误日志表、请求记录表、用户登录token表
-- 导出  表 phalcon.phalcon_error_log 结构
CREATE TABLE IF NOT EXISTS `phalcon_error_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `sqlstr` varchar(500) DEFAULT NULL,
  `message` varchar(1000) NOT NULL,
  `application` varchar(20) NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='错误日志：SQL错误、其他错误';

-- 正在导出表  phalcon.phalcon_error_log 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `phalcon_error_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `phalcon_error_log` ENABLE KEYS */;

-- 导出  表 phalcon.phalcon_request_log 结构
CREATE TABLE IF NOT EXISTS `phalcon_request_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL COMMENT '请求方法',
  `url` varchar(300) NOT NULL COMMENT '请求url',
  `requestid` varchar(32) NOT NULL DEFAULT '0' COMMENT '请求ID',
  `requestdata` varchar(1000) NOT NULL DEFAULT '0' COMMENT '请求数据',
  `returndata` text NOT NULL COMMENT '返回数据',
  `application` varchar(50) NOT NULL COMMENT '应用标志',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='请求记录表';


-- 导出  表 phalcon.phalcon_user_token 结构
CREATE TABLE IF NOT EXISTS `phalcon_user_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `token` varchar(32) DEFAULT NULL,
  `application` varchar(32) DEFAULT NULL COMMENT '应用标志',
  `loginnum` int(11) DEFAULT '1' COMMENT '登录次数（不允许多设备同时登录时该字段有效）',
  `ip` varchar(20) DEFAULT '' COMMENT '登录IP地址',
  `islogout` int(1) DEFAULT '0' COMMENT '是否退出（0未退出，1已退出）',
  `endtime` datetime DEFAULT NULL COMMENT '有效期：什么时间该token失效',
  `logouttime` datetime DEFAULT '0000-00-00 00:00:00',
  `ctime` datetime DEFAULT NULL,
  `utime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登录token表';
