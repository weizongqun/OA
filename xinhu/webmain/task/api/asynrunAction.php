<?php
class asynrunClassAction extends apiAction
{
	public function initAction()
	{
		$this->display	= false;
		$uid   			= (int)$this->get('adminid');
		$key   			= $this->get('asynkey');
		$mykey			= getconfig('asynkey');
		if($mykey != ''){
			$wodkey 	= md5(md5($mykey));
			if($wodkey != $key)exit('sorry,asyn');
		}
		$urs 			= m('admin')->getone($uid,'`id`,`name`,`user`');
		if($urs)$this->setNowUser($urs['id'], $urs['name'], $urs['user']);
	}
	
	//测试
	public function indexAction()
	{
		$runtime = $this->get('runtime');
		$this->rock->debugs('hehe:'.time().','.$runtime.'','yibu');
	}
	
	//消息同步到微信企业会话
	public function wxchattbAction()
	{
		$id   = (int)$this->get('id');
		m('weixin:chat')->chattongbu($id);
	}
	
	//消息同步到企业客服消息汇总
	public function wxkefutbAction()
	{
		$id   = (int)$this->get('id');
		m('weixin:kefu')->chattongbu($id);
	}
	
	//薪资发放通知给人员
	public function salaryffAction()
	{
		$id    = (int)$this->get('id');
		m('flow')->initflow('hrsalary', $id)->todouser();
	}
	
	//下载微信发送的图片到服务器
	public function downwxpicAction()
	{
		$picurl = $this->rock->jm->uncrypt($this->get('picurl'));
		$msgid  = $this->get('msgid');
		m('reim')->downwximg($picurl, $msgid);
	}
	
	//异步发送邮件
	public function sendemailAction()
	{
		$id    = (int)$this->get('id');
		m('email')->sendemailcont($id);
	}
	
	//异步微信企业号发送提醒
	public function wxsendmsgAction()
	{
		$body = $this->get('body');
		if($body=='')return;
		$body	= $this->jm->base64decode($body);
		m('weixin:index')->sendbody($body);
	}
	
	//异步企业微信发送提醒
	public function wxqysendmsgAction()
	{
		$body 		= $this->get('body');
		$agentid 	= $this->get('agentid');
		if($body=='')return;
		$body	= $this->jm->base64decode($body);
		$barr 	= m('weixinqy:index')->sendbody($body, $agentid);
	}
	
	//转pdf完成了设置
	public function topdfokAction()
	{
		$id    	= (int)$this->get('id');
		$type 	= $this->get('type','html');
		$frs 	= m('file')->getone($id);
		$pdfpath= str_replace('.'.$frs['fileext'].'','.'.$type.'', $frs['filepath']);
		if(!file_exists($pdfpath))return;
		if($type=='html'){
			$cont = file_get_contents($pdfpath);
			$str1 = '<meta http-equiv=Content-Type content="text/html; charset=gb2312">';
			$cont = str_replace('</title>', '</title>'.$str1.'', $cont);
			$this->rock->createtxt($pdfpath, $cont);
		}
		m('file')->update("`pdfpath`='$pdfpath'", $id);
	}
}