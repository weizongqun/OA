<?php
class flow_dailyClassModel extends flowModel
{
	public function initModel()
	{
		$this->typearr = explode(',','日报,周报,月报,年报');
		$this->logobj = m('log');
	}
	
	protected function flowchangedata()
	{
		$this->rs['typess'] 	= $this->typearr[$this->rs['type']];
	}
	
	public function flowrsreplace($rs, $lx=0)
	{
		if($rs['mark']=='0')$rs['mark'] = '';
		if($lx==2){
			if(isset($rs['optdt']))$rs['optdt']	= str_replace(' ','<br>', $rs['optdt']);
			if(isset($rs['adddt']))$rs['adddt']	= str_replace(' ','<br>', $rs['adddt']);
			
			$zt = $this->logobj->isread('daily', $rs['id'], $this->adminid);
			$status = 1;
			if($zt>0)$status=0;
			$rs['status']		= $status;
			
			$dt 	= $rs['dt'];
			if($rs['type']!=0 && !isempt($rs['enddt'])){
				$dt.='<br><font color="#aaaaaa">'.$rs['enddt'].'</font>';
			}
			$rs['dt'] = $dt;
		}
		$rs['content'] 		= str_replace("\n",'<br>', $rs['content']);
		$rs['plan'] 		= str_replace("\n",'<br>', $rs['plan']);
		$rs['type'] 		= $this->typearr[$rs['type']];
		return $rs;
	}
	
	//提交保存完日报通知上级
	protected function flowsubmit($na, $sm)
	{
		$uparr = m('admin')->getsuperman($this->uid);
		$recid = $this->rock->arrvalue($uparr, 0);
		$typea = $this->typearr[$this->rs['type']];
		$title = ''.$this->rs['optname'].'的'.$typea.'';
		$cont  = c('html')->substrstr($this->rs['content'],0, 100);
		$this->push($recid, '', "".$typea."日期：{dt}\n".$cont, $title);
	}
	
	protected function flowaddlog($a)
	{
		if($a['name'] == '日报评分'){
			$fenshu	 = (int)$this->rock->post('fenshu','0');
			$this->push($this->rs['uid'], '工作日报', ''.$this->adminname.'评分你[{dt}]的{typess},分数('.$fenshu.')','工作日报评分');
			$this->update(array(
				'mark' => $fenshu
			), $this->id);
		}
	}
	
	protected function flowdatalog($arr)
	{
		$ispingfen	= 0;
		$barr 		= m('admin')->getsuperman($this->uid); //获取我的上级主管
		if($barr){
			$hes 	= $barr[0];
			if(contain(','.$hes.',',','.$this->adminid.','))$ispingfen = 1; //是否可以评分
		}
		$arr['ispingfen'] 	= $ispingfen;
		$arr['mark'] 		= $this->rs['mark'];
		return $arr;
	}
	
	protected function flowgetoptmenu($opt)
	{
		if($this->uid==$this->adminid)return false;
		$to = m('log')->isread($this->mtable, $this->id);
		return $to<=0;
	}
	
	protected function flowoptmenu($ors, $crs)
	{
		$table 	= $this->mtable;
		$mid	= $this->id;
		$uid	= $this->adminid;
		$lx 	= $ors['num'];
		$log 	= m('log');
		if($lx=='yd'){
			$log->addread($table, $mid, $uid);
		}
		if($lx=='allyd'){
			$ydid  = $log->getread($table, $uid);	
			$where = m('view')->viewwhere($this->modeid, $uid);
			$where = "((1=1 $where) or (`uid`='$uid') )";
			$where = "`id` not in($ydid) and $where";
			
			$rows 	= m($table)->getrows($where,'id');
			foreach($rows as $k=>$rs)$log->addread($table, $rs['id'], $uid);
		}
	}
	
	
	protected function flowprintrows($rows)
	{
		foreach($rows as $k=>$rs){
			$rows[$k]['plan_style']		= 'text-align:left';
			$rows[$k]['content']		= str_replace("\n",'<br>', $rs['content']);
			$rows[$k]['plan']			= str_replace("\n",'<br>', $rs['plan']);
			$rows[$k]['type']			= $this->typearr[$rs['type']];
		}
		return $rows;
	}
	
	//条件过滤已从流程模块条件下设置
	protected function flowbillwhere($uid, $lx)
	{
		$type 	= $this->rock->post('type');
		$key 	= $this->rock->post('key');
		$dt 	= $this->rock->post('dt');
		$where 		= '';
		$keywhere 	= '';
		

		if(!isempt($type))$where.=" and a.`type`='$type'";
		if(!isempt($dt))$where.=" and a.`dt` like '$dt%'";
		
		if(!isempt($key))$keywhere=m('admin')->getkeywhere($key, 'b.', "or a.`content` like '%$key%'");
		
		return array(
			'table' => '`[Q]daily` a left join `[Q]admin` b on a.`uid`=b.`id`',
			'fields'=> 'a.*,b.`name`,b.`deptname`',
			'where' => $where,
			'keywhere' => $keywhere,
			'asqom' => 'a.', //主表别名
			'order' => 'a.`optdt` desc'
		);
	}
	
}