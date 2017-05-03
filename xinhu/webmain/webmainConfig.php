<?php
if(!defined('HOST'))die('not access');
//[管理员]在2017-05-03 19:23:27通过[系统→系统工具→系统设置]，保存修改了配置文件
return array(
	'title'	=> 'lEGEND协同办公系统',	//系统默认标题
	'apptitle'	=> 'legend OA',	//APP上或PC客户端上的标题
	'url'	=> 'dev.oa.com/',	//系统URL
	'db_drive'	=> 'mysqli',	//操作数据库驱动有mysql,mysqli,pdo三种
	'db_host'	=> '127.0.0.1',	//数据库地址
	'db_user'	=> 'root',	//数据库用户名
	'db_pass'	=> '123456',	//数据库密码
	'db_base'	=> 'rockxinhu',	//数据库名称
	'randkey'	=> '',	//系统随机字符串密钥
	'perfix'	=> 'xinhu_',	//数据库表名前缀
	'localurl'	=> 'dev.oa.com/',	//本地系统URL，用于服务器上浏览地址
	'updir'	=> 'upload',
	'phppath'	=> '',
	'qom'	=> 'xinhu_',	//session、cookie前缀
	'highpass'	=> '',	//超级管理员密码，可用于登录任何帐号
	'asynkey'	=> 'asynkey23sss',	//这是异步任务key
	'openkey'	=> 'openkey23se',	//对外接口openkey
	'asynsend'	=> false,	//是否异步发送提醒消息，为true需开启服务端
	'install'	=> true,	//已安装，不要去掉啊
	'sqllog'	=> false,	//是否记录sql日志保存upload/sqllog下
	'xinhukey'	=> '',	//信呼官网key，用于在线升级使用

);