<?php 
class loginClassAction extends ActionNot{
	
	public function defaultAction()
	{
		//判断是否可以快捷登录
		$iskj = 0;
		if($this->rock->web == 'wxbro'){
			if($this->rock->isqywx){
				if(!isempt($this->option->getval('weixinqy_corpid')))$iskj=2;
			}else{
				$coppid = $this->option->getval('weixin_corpid');
				if(!isempt($coppid))$iskj=1;
			}
		}
		$this->smartydata['iskj'] = $iskj;
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