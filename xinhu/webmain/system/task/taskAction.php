<?php
class taskClassAction extends Action
{
	
	public function getrunlistAjax()
	{
		$barr = m('task')->getlistrun($this->date);
		$this->returnjson($barr);
	}
	public function starttaskAjax()
	{
		$url 	= getconfig('localurl');
		if($url=='')exit('请先设置系统本地地址');
		$mtask = m('task');
		$mtask->createjson();
		$msg = $mtask->starttask();
		if(contain($msg, 'ok')){
			echo 'ok';
		}else{
			echo '无法启动可能未开启服务端';
		}
	}
	
	public function clearztAjax()
	{
		m('task')->update('state=0,lastdt=null,lastcont=null','1=1');
	}
	
	
	public function downbatAjax()
	{
		$ljth = str_replace('/','\\',ROOT_PATH);
		echo '<title>计划任务开启方法</title>';
		
		echo '<font color="red">如您有安装信呼服务端，就不用根据下面来开启计划任务了</font><br><a target="_blank" style="color:blue" href="http://xh829.com/view_taskrun.html">查看官网上帮助</a><br>';
		echo '计划任务的运行时间需要设置为5的倍数才可以运行到。<br>';
		if(!$this->contain(PHP_OS,'WIN')){
			echo '您的服务器系统是：Linux，可用根据以下设置定时任务<br>';
			echo '根据以下命令设置运行：<br><br>';
			echo 'crontab -e<br>';
			echo '#每5分钟运行一次<br>';
			echo '*/5 * * * * '.getconfig('phppath','php').' '.ROOT_PATH.'/task.php runt,task<br>';
			exit;
		}
		
		echo '您的服务器系统是：Windows，可根据以下设置定时任务<br>';
		$str1 = '@echo off
'.getconfig('phppath','php.exe').' '.$ljth.'\task.php runt,task';
		$this->rock->createtxt(''.UPDIR.'/xinhutaskrun.bat', $str1);
		echo '1、打开文件：'.UPDIR.'/xinhutaskrun.bat将php.exe换成你当前php环境的目录如：F:\php\php-5.6.22\php.exe<br>2、在您的win服务器上，开始菜单→运行 输入 cmd 回车(管理员身份运行)，输入以下命令(每5分钟运行一次)：<br><br>';
		echo 'schtasks /create /sc DAILY /mo 1 /du "24:00" /ri 5 /sd "2017/04/01" /st "00:00:00"  /tn "信呼计划任务" /tr '.$ljth.'\\'.UPDIR.'\xinhutaskrun.bat';
	}
}