<?php 
/**
*	微信公众号接口
*	来源：LEGEND开发团队，xh829.com
*	禁止抄袭盗版	
*/
class wxgzhClassAction extends apiAction{

	public function getsignAction()
	{
		$bo		= m('wxgzh:signjssdk');
		$appid 	= $bo->readwxset();
		if(isempt($appid)){
			$arr['appId'] = '';
		}else{
			$url = $this->getvals('url');
			$arr = $bo->getsignsdk($url);
		}
		$this->showreturn($arr);
	}
	
}