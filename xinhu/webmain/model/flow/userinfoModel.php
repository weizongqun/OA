<?php
class flow_userinfoClassModel extends flowModel
{
	public  $uidfields = 'id';
	
	public function initModel()
	{
		$this->statearr 	= explode(',','试用期,正式,实习生,兼职,临时工,离职');
		$this->birtypearr 	= explode(',','阳历,农历');
		$this->calendar		= c('calendar');
	}

	public function flowrsreplace($rs)
	{
		if($rs['state']==5)$rs['ishui']=1;
		$rs['state']		= $this->getuserstate($rs['state']);
		
		$rs['shengri']		= '';
		if(!isempt($rs['birthday'])){
			if($rs['birtype']==0){
				$rs['shengri'] = $rs['birthday'];
			}else{
				$days			= $this->calendar->daytocal($rs['birthday']);
				$rs['shengri'] = ''.$days['year'].'年农历'.$days['month'].'月'.$days['day'].'';
			}
		}
		
		$rs['birtype']		= $this->birtypearr[$rs['birtype']];
		return $rs;
	}
	
	public function getuserstate($zt)
	{
		return $this->rock->arrvalue($this->statearr,$zt,'未知');
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$s 		= '';
		$key 	= $this->rock->post('key');
		$state 	= $this->rock->post('state');
		if($key!=''){
			$s = " and (`name` like '%$key%' or `ranking` like '%$key%' or `deptname` like '%$key%') ";
		}
		if($state!='')$s.=" and `state`='$state'";
		return array(
			'keywhere'	=> $s,
			'fields'	=> 'id,name,deptname,ranking,state,tel,sex,mobile,workdate,quitdt,positivedt,birtype,birthday,num'
		);
	}
	
	/**
	*	员工生日提醒
	*/
	public function birthdaytodo()
	{
		$dt 	= date('m-d');
		$cala   = $this->calendar->toCalday($this->rock->date);
		$nongli	= $cala['cal'];
		$rows 	= $this->db->getall("select a.`birthday`,b.`id`,a.`birtype`,b.`name` from `[Q]".$this->mtable."` a left join `[Q]admin` b on a.`id`=b.`id` where b.`status`=1 and a.`state`<>5 and ((a.`birthday` like '%".$dt."' and a.`birtype`=0) or (a.`birthday` like '%".$nongli."' and a.`birtype`=1) )");
		foreach($rows as $k=>$rs){
			$cont = '今天是'.date('Y年m月d日').',农历'.$cala['month'].''.$cala['day'].'，是你的生日，我们在这里祝你生日快乐。';
			$this->push($rs['id'],'', $cont, '生日祝福');
		}
	}
}