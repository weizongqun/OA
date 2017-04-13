<?php
/**
*	LEGEND服务端运行Linux服务器上
*	主页：http://xxxxxxxx.com/
*	说明：系统是基于Workerman的服务使用(www.workerman.net)
*	作者：雨中磐石(rainrock)
*	【看这里】此源码是运行在您的服务器上的，菜鸟请勿尝试。
*/

define('ROOT_PATH',str_replace('\\','/',dirname(__FILE__)));




//这个建议不要修改(也不允许修改)
define('PATHS', ROOT_PATH.'/Rock/push');

//端口：1000-9999数字都可以，禁止用特殊端口，建议还是不要修改
define('WSPORT', 6552);
//REIM端口IP，默认是不用修改的，外网使用就要写成当前电脑服务器IP了，如内网使用就写192.168.*.*
define('WSIP', '127.0.0.1');
//结合以上两个设置，LEGEND后台【LEGEND-服务器设置】上的通信地址就写：ws://你的真实ip:6552/


//以下设置服务端推送地址IP建议不要修改【LEGEND-服务器设置】服务端推送地址http://127.0.0.1:6553/
define('HTTPIP', '127.0.0.1');
define('HTTPPORT', 6553);


//这个是设置RECID使用的，在【LEGEND-服务器设置】的recID填写对应
define('REIMRECID', 'rockxinhu');

//如果您正式使用，建议改为false
define('DEBUG', false);