<?php 
class loginClassAction extends ActionNot{
	
	public function defaultAction()
	{
		
	}
	
	/**
	*	微信快捷登录
	*/
	public function wxloginAction()
	{
		$this->display= false;
		if($this->rock->isqywx){
			m('weixinqy:oauth')->login();
		}else{
			m('weixin:oauth')->login();
		}
	}
	
	public function wxlogincodeAction()
	{
		$this->display= false;
		if($this->rock->isqywx){
			m('weixinqy:oauth')->logincode();
		}else{
			m('weixin:oauth')->logincode();
		}
	}
	
	
	/**
	*	微信授权
	*/
	public function oauthtoAction()
	{
		m('wxgzh:oauth')->oauthto();
	}
	public function oauthbackAction()
	{
		m('wxgzh:oauth')->oauthback();
	}
}