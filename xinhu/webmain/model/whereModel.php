<?php
class whereClassModel extends Model
{
	private $moders	= array();
	
	public function initModel()
	{
		$this->settable('flow_where');
	}
	
	public function getstrwhere($str, $uid=0, $fid='')
	{
		if($uid==0)$uid = $this->adminid;
		$sw1		= $this->rock->dbinstr('superid',$uid);
		$super		= "select `id` from `[Q]admin` where $sw1";//我的直属下属
		$allsuper	= "select `id` from `[Q]admin` where instr(`superpath`,'[$uid]')>0"; //我所有下属的下属
		$str 	= str_replace(
			array('{uid}','{date}','[date]','{now}','{super}','{allsuper}'), 
			array($uid, "'".$this->rock->date."'", $this->rock->date ,"'".$this->rock->now."'", $super,$allsuper), 
		$str);
		
		//未读替换
		if(contain($str,'{unread}')){
			$rstr = '';
			if($this->moders){
				$ydid  = m('log')->getread($this->moders['table'], $uid); 
				$rstr  = '{asqom}`id` not in('.$ydid.')';
			}
			$str = str_replace('{unread}', $rstr, $str);
		}
		//已读替换
		if(contain($str,'{read}')){
			$rstr = '';
			if($this->moders){
				$ydid  = m('log')->getread($this->moders['table'], $uid); 
				$rstr  = '{asqom}`id` in('.$ydid.')';
			}
			$str = str_replace('{read}', $rstr, $str);
		}
		//receid
		if(contain($str,'{receid}')){
			$rstr= m('admin')->getjoinstr('receid', $uid, 1);
			$str = str_replace('{receid}', '('.$rstr.')', $str);
		}
		//本周一{weekfirst}
		if(contain($str,'{weekfirst}')){
			$rstr= c('date')->getweekfirst($this->rock->date);
			$str = str_replace('{weekfirst}', $rstr, $str);
		}
		//本周日{weeklast}
		if(contain($str,'{weeklast}')){
			$rstr= c('date')->getweeklast($this->rock->date);
			$str = str_replace('{weeklast}', $rstr, $str);
		}
		return $str;
	}
	
	public function getflowwhere($id, $uid=0, $fid='')
	{
		if(is_array($id)){
			$rs 		= $id;
		}else{
			$swhe		= "`num`='$id'";
			if(is_numeric($id))$swhe=$id;
			$rs 		= $this->getone($swhe);
		}
		if(!$rs)return false;
		if($fid=='')$fid='`uid`';
		$modeid 		= (int)$rs['setid'];
		$this->moders 	= m('flow_set')->getone($modeid);
		
		$wheresstr 	= $this->getstrwhere($this->rock->jm->base64decode($rs['wheresstr']), $uid, $fid);
		$whereustr 	= $this->getstrwhere($this->rock->jm->base64decode($rs['whereustr']), $uid, $fid);
		$wheredstr 	= $this->getstrwhere($this->rock->jm->base64decode($rs['wheredstr']), $uid, $fid);
		$str 		= $wheresstr;if(isempt($str))$str='';
		$ustr 		= $nstr = '';
		if(!isempt($rs['receid'])){
			$tsrt 	= m('admin')->gjoin($rs['receid'],'ud', 'where');
			if($tsrt=='all'){
				$tsrt 	= '1=1';
			}else{
				$tsrt 	= '('.$tsrt.')';
			}
			$ustr = $tsrt;
		}
		if(!isempt($whereustr)){
			if($ustr!='')$ustr.=' and ';
			$ustr .= $whereustr;
		}
		
		if(!isempt($rs['nreceid'])){
			$tsrt 	= m('admin')->gjoin($rs['nreceid'],'ud', 'where');
			if($tsrt=='all'){
				$tsrt 	= '1=1';
			}
			$nstr = $tsrt;
		}
		if(!isempt($wheredstr)){
			if($nstr!='')$nstr.=' or ';
			$nstr .= $wheredstr;
		}
		$astr 	= $str;
		if($ustr != '' || $nstr != ''){
			$_sar= '1=1';
			if($ustr!='')$_sar.=' and '.$ustr.'';
			if($nstr!='')$_sar.=' and not ('.$nstr.')';
			if(!isempt($astr))$astr.=' and ';
			$astr .= ''.$fid.' in(select `id` from `[Q]admin` where '.$_sar.')';
		}
		return array(
			'str'	=> $str,
			'utr'	=> $ustr,
			'ntr'	=> $nstr,
			'atr'	=> $astr
		);
	}
	
	public function getwherestr($id, $uid=0, $fid='', $lx=0)
	{
		$where 	= '';
		$arr 	= $this->getflowwhere($id, $uid, $fid);
		if($arr){
			$where = $arr['atr'];
			if($lx==0)$where = ' and '.$where;
		}
		return $where;
	}
}