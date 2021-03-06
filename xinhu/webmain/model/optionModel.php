<?php
class optionClassModel extends Model
{
	private	$getypsarr = array();
	
	/**
		获取选项
	*/
	public function getval($num, $dev='', $lx=0)
	{
		$val= '';
		$rs = $this->getone("`num`='$num'", '`name`,`value`,`id`,`optdt`');
		if($rs){
			if($lx==0)$val=$rs['value'];
			if($lx==1)$val=$rs['name'];
			if($lx==2)$val=$rs['id'];
			if($lx==3)$val=$rs['optdt'];
		}
		if(isempt($val))$val=$dev;
		return $val;
	}
	
	public function getdata($num, $whe='')
	{
		if(!is_numeric($num)){
			$id  = (int)$this->getmou('id', "`num`='$num'");
			if($id == 0)$id = -829;
		}else{
			$id = $num;
		}
		return $this->getall("`pid`='$id' and `valid`=1 $whe order by `sort`,`id`");
	}
	
	public function getmnum($num)
	{
		return $this->getdata($num);
	}
	
	public function getselectdata($num, $tbo=false)
	{
		$arr = $this->getdata($num);
		$rows= array();
		foreach($arr as $k=>$rs){
			$rows[] = $rs;
			if($tbo){
				$sarr = $this->getdata($rs['id']);
				foreach($sarr as $k1=>$rs1){
					$rs1['name'] = '&nbsp;&nbsp;├'.$rs1['name'].'';
					$rows[] = $rs1;
				}
			}
		}
		return $rows;
	}
	
	public function setval($num, $val='', $name=null, $isub=true)
	{
		$numa	= explode('@', $num);
		$num 	= $numa[0];
		$where  = "`num`='$num'";
		$id 	= (int)$this->getmou('id', $where);
		if($id==0)$where='';
		$arr 	= array(
			'num'	=> $num,
			'value'	=> $val,
			'optid'	=> $this->adminid,
			'optdt'	=> $this->rock->now
		);
		if(isset($numa[1]))$arr['pid'] 	= $numa[1];
		if($name!=null)$arr['name'] 	= $name;
		if($id==0 || $isub)$this->record($arr, $where);
		if($id==0)$id = $this->db->insert_id();
		return $id;
	}
	
	
	public function gettreedata($pid)
	{
		$rows 	= $this->getfoldrowsss($pid);
		return $rows;
	}
	
	private function getfoldrowsss($pid)
	{
		$rows 	= $this->db->getall("select `id`,`pid`,`name`,`optdt`,`sort`,`receid`,`recename` from [Q]option where `pid`='$pid' and `valid`=1 order by `sort`,`id`");
		foreach($rows as $k=>$rs){
			$rows[$k]['expanded']	= true;
			$rows[$k]['children'] 	= $this->getfoldrowsss($rs['id']);
		}
		return $rows;
	}
	
	public function getnumtoid($num, $name='', $isub=true)
	{
		$idd = $this->setval($num,'', $name, $isub);
		return $idd;
	}
	
	//获取一组设置
	public function getpidarr($pid, $lx=0)
	{
		$rows = $this->getall("`pid`='$pid'");
		$barr = array();
		foreach($rows as $k=>$rs){
			$barr[$rs['num']] = $rs['value'];
		}
		return $barr;
	}
	
	//获取所有下级Id
	public function getalldownid($id)
	{
		$str  = $id;
		$rows = $this->getall('`pid`='.$id.' and `valid`=1','`id`');
		foreach($rows as $k=>$rs){
			$str1= $this->getalldownid($rs['id']);
			$str.=','.$str1.'';
		}
		return $str;
	}
	
	//根据receid获取记录 $type=0，默认1其他
	public function getreceiddownall($uid, $optid=0, $type=0)
	{
		$rstr = m('admin')->getjoinstr('`receid`', $uid, 1,1);
		$whe  = '';
		if($optid>0)$whe=' and `optid`='.$optid.'';
		$rows = $this->getall('`valid`=1 and `type`='.$type.' and ('.$rstr.') '.$whe.'','`id`');
		$strs = '';
		foreach($rows as $k=>$rs){
			$str1 = $this->getalldownid($rs['id']);
			$strs.=','.$str1.'';
		}
		if($strs!='')$strs = substr($strs, 1);
		return $strs;
	}
	
	/**
	*	根据名称如：技术姿势/PHP知识 得到对应ID
	*/
	public function gettypeid($djnum,$s)
	{
		if(isset($this->getypsarr[$s]))return $this->getypsarr[$s];
		$sid = 0;
		$s 	 = str_replace(',','/', $s);
		$djid= $this->getval($djnum,'0',2);
		if(isempt($djid)){
			$djid = $this->insert(array('name' => '分类','num' => $djnum,'pid'=> 0,'valid'=> 1));
		}
		$dsja= $djid;
		$sarr= explode('/', $s);
		foreach($sarr as $safs){
			$pid 	= $djid;
			$djid 	= (int)$this->getmou('id', "`pid`='$pid' and `name`='$safs'");
			if($djid==0){
				$djid = $this->insert(array(
					'name' => $safs,
					'pid'  => $pid,
					'valid'  => 1,
				));
			}
		}
		if($djid != $dsja)$sid 	= $djid;
		$this->getypsarr[$s] 	= $sid;
		return $sid;
	}
}