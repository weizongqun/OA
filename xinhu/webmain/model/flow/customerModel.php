<?php
class flow_customerClassModel extends flowModel
{
	public function initModel()
	{
		$this->statearr		 = c('array')->strtoarray('停用|#888888,启用|green');
		$this->statarr		 = c('array')->strtoarray('否|#888888,是|#ff6600');
	}
	

	
	public function flowrsreplace($rs, $lx=0)
	{
		if(isset($rs['status'])){
			if($rs['status']==0)$rs['ishui'] = 1;
			$zt 	= $this->statearr[$rs['status']];
			$rs['status']	= '<font color="'.$zt[1].'">'.$zt[0].'</font>';
		}
		
		if(isset($rs['isstat'])){
			$stat 	= $this->statarr[$rs['isstat']];
			$rs['isstat']	= '<font color="'.$stat[1].'">'.$stat[0].'</font>';
		}
		
		if(isset($rs['isgys'])){
			$gys 	= $this->statarr[$rs['isgys']];
			$rs['isgys']	= '<font color="'.$gys[1].'">'.$gys[0].'</font>';
		}
		if($this->rock->arrvalue($rs,'htshu','0')==0)$rs['htshu']='';
		if($this->rock->arrvalue($rs,'moneyz','0')==0)$rs['moneyz']='';
		if($this->rock->arrvalue($rs,'moneyd','0')==0)$rs['moneyd']='';
		return $rs;
	}
	
	
	
	//是否有查看权限
	protected function flowisreadqx()
	{
		$bo = false;
		$shateid = ','.$this->rs['shateid'].',';
		if(contain($shateid,','.$this->adminid.','))$bo=true;
		return $bo;
	}
	
	protected function flowgetfields_qiyong($lx)
	{
		$arr = array();
		if($this->uid==$this->adminid){
			$arr['mobile'] 		= '手机号';
			$arr['tel'] 		= '电话';
			$arr['email'] 		= '邮箱';
			$arr['routeline'] 	= '交通路线';
		}
		return $arr;
	}

	
	protected function flowoptmenu($ors, $crs)
	{
		$zt  = $ors['statusvalue'];
		$num = $ors['num'];
		if($num=='ztqh'){
			$this->update('`status`='.$zt.'', $this->id);
		}
		
		//共享
		if($num=='shate'){
			$cname 	 = $crs['cname'];
			$cnameid = $crs['cnameid'];
			$this->update(array(
				'shateid' 	=> $cnameid,
				'shate' 	=> $cname,
			), $this->id);
			$this->push($cnameid, '客户管理', ''.$this->adminname.'将一个客户【{name}】共享给你');
		}
		
		//取消共享
		if($num=='unshate'){
			$this->update(array(
				'shateid' 	=> '',
				'shate' 	=> '',
			), $this->id);
		}	
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		return array(
			'order' => 'status desc,optdt desc'
		);
	}
}