<?php
class kaoqinClassModel extends Model
{
	private $userarr = array();
	
	public function initModel()
	{
		$this->settable('kqdist');
		$this->admindb = m('admin');
	}
	
	public function adddkjl($uid,$type=0, $dkdt='',$ip='',$mac='')
	{
		$now = $this->rock->now;
		if($dkdt=='')$dkdt = $now;
		if($ip=='')$ip=$this->rock->ip;
		$kqa 	= $this->admindb->getusinfo($uid,'dkip,dkmac');
		$mac	= strtolower($mac);
		if(isempt($kqa['dkip']) && isempt($kqa['dkmac']))return '未设置打卡IP或MAC地址';
		if(!isempt($kqa['dkip']) && $kqa['dkip']!='*'){
			$ass 	= explode(',', $kqa['dkip']);
			if(!in_array($ip, $ass))return '打卡电脑内网IP必须是：'.$kqa['dkip'].'';
		}
		if(!isempt($kqa['dkmac'])){
			if($mac=='')return '无法获取MAC地址,请使用PC客户端';
			$dkmac 	= str_replace('-',':',strtolower($kqa['dkmac']));
			$ass 	= explode(',', $dkmac);
			if(!in_array($mac, $ass))return '打卡电脑MAC地址必须是：'.$kqa['dkmac'].'';
		}
		$this->db->record('[Q]kqdkjl',array(
			'dkdt' 	=> $dkdt,
			'uid'	=> $uid,
			'optdt'	=> $now,
			'ip'	=> $ip,
			'mac'	=> $mac,
			'type'	=> $type
		));
		$dt = substr($dkdt, 0, 10);
		$this->kqanay($uid, $dt);
		return '';
	}
	
	/**
	*	读取考勤时间
	*	$lx0 考勤时段,$lx1上班时间
	*/
	public function getkqsj($uid, $dt, $lx=0)
	{
		$mid 	= $this->getdistid($uid, $dt, 0);
		$rows 	= $this->db->getrows('[Q]kqsjgz','pid='.$mid.'','id,name,stime,etime,qtype','`sort`');
		if($lx==1)return $rows;
		foreach($rows as $k=>$rs){
			$rows[$k]['children'] = $this->db->getrows('[Q]kqsjgz','pid='.$rs['id'].'','id,name,stime,etime,qtype,sort','`sort`');
		}
		return $rows;
	}
	
	/**
	*	读取某人一天考勤类型状态
	*/
	public function getkqztarr($uid, $dt)
	{
		$kqzt 	= $this->getkqsj($uid, $dt);
		$kqsr 	= array(); $xu 	= 0;
		foreach($kqzt as $k=>$rs){
			foreach($rs['children'] as $rs1){
				if(!isset($kqsr[$rs1['name']])){
					$kqsr[$rs1['name']]='state'.$xu.'';
					$xu++;
				}
			}
		}
		return $kqsr;
	}
	
	/**
	*	获取匹配Id
	*	$type 0考勤时间,1休息,2定位
	*/
	public function getdistid($uid, $dt, $type)
	{
		$s 		= $this->admindb->getjoinstr('receid', $uid);
		$rows  	= $this->getall("`status`=1 and `type`=".$type." and '$dt' between `startdt` and `enddt` $s ");
		$mid 	= $this->getpipeimid($uid, $rows);
		return $mid;
	}
	
	/**
	*	是不是工作日
	*/
	public function isworkdt($uid, $dt)
	{
		$mid 	= $this->getdistid($uid, $dt, 1);
		$tos 	= $this->db->rows('[Q]kqxxsj',"`pid`=$mid and `dt`='$dt'");
		$isw 	= ( $tos>0 ) ? 0 : 1;
		return $isw;
	}
	
	/**
	*	读取人员定位定位打卡位置
	*/
	public function dwdkrs($uid, $dt)
	{
		$mid 	= $this->getdistid($uid, $dt, 2);
		return m('kqdw')->getone($mid);
	}
	
	/**
	*	数组匹配人员对应哪个记录
	*/
	public function getpipeimid($uid=0, $garrs, $esfi='mid', $momid=1, $dt='')
	{
		$mid 	= $momid;
		if($uid==0)return $mid;
		$mid 	= 0;
		$deptpath = '';
		if(is_array($uid)){
			if(isset($uid['deptpath']))$deptpath = $uid['deptpath'];
			$uid = $uid['id'];
		}else{
			if(!isset($this->userarr[$uid])){
				$_urs = $this->db->getone('[Q]admin', "`id`='$uid'", '`deptid`,`deptpath`');
				$this->userarr[$uid] = $_urs;
			}
			$deptpath = $this->userarr[$uid]['deptpath'];
		}
		$utid  	= $dtid  =  array();$allars=false;$dttime = 0;
		if($dt!='')$dttime	= strtotime($dt);
		foreach($garrs as $k=>$rs){
			$artid = explode(',', $rs['receid']);
			if($dttime>0){
				if(!isset($rs['starttime'])){
					$rs['starttime'] 	= strtotime($rs['startdt']);
					$rs['endtime'] 		= strtotime($rs['enddt']);
				}
				if($rs['starttime'] > $dttime || $rs['endtime'] < $dttime)continue;
			}	
			foreach($artid as $ssid){
				if($ssid=='')continue;
				if($ssid=='all'){
					$allars = $rs;
					continue;
				}
				$fs  = substr($ssid, 0, 1);
				$sid = str_replace('u','', $ssid);
				$sid = str_replace('d','', $sid);
				if($fs=='d'){
					$dtid[$sid]= $rs;
				}else{
					$utid[$sid]= $rs;
				}					
			}
		}
		if(isset($utid[$uid]))$mid = (int)$utid[$uid][$esfi];
		if($mid == 0 && !$this->isempt($deptpath)){
			$depa = explode(',', str_replace(array('[',']'), array('',''), $deptpath));
			foreach($depa as $depas){
				if(isset($dtid[$depas]))$mid = (int)$dtid[$depas][$esfi];
			}
		}
		if($mid == 0 && is_array($allars))$mid = (int)$allars[$esfi];
		if($mid==0)$mid=$momid;
		return $mid;
	}
	
	public function kqanayall($month, $where='')
	{
		if(isempt($month))return;
		$month	= substr($month, 0, 7);
		$start	= ''.$month.'-01';
		$max 	= c('date')->getmaxdt($month);
		$enddt	= ''.$month.'-'.$max.'';
		$s 		= "and (`quitdt` is null or `quitdt`>='$start') and (`workdate` is null or `workdate`<='$enddt') $where";
		$urows 	= $this->admindb->getall('1=1 '.$s.'', '`id`,`workdate`,`quitdt`');
		foreach($urows as $k=>$urs){
			$this->kqanaymonth($urs['id'], $month, $urs, $max);
		}
	}
	
	public function kqanayalldt($dt)
	{
		$s 		= "and (`quitdt` is null or `quitdt`>='$dt') and (`workdate` is null or `workdate`<='$dt')";
		$urows 	= $this->admindb->getall('1=1 '.$s.'', '`id`,`workdate`,`quitdt`');
		foreach($urows as $k=>$urs){
			$this->kqanay($urs['id'], $dt);
		}
	}
	
	public function kqanaymonth($uid, $month, $urs=false, $max=0)
	{
		$month	= substr($month, 0, 7);
		if(!$urs)$urs 	= $this->admindb->getone($uid, '`workdate`,`quitdt`,`id`');
		if($max==0)$max = c('date')->getmaxdt($month);
		for($i=1; $i<=$max; $i++){
			$oi = $i;if($oi<10)$oi='0'.$i.'';
			$dt = ''.$month.'-'.$oi.'';
			if(!isempt($urs['workdate']) && $urs['workdate']>$dt)continue;
			if(!isempt($urs['quitdt']) && $urs['quitdt']<$dt)continue;
			$this->kqanay($uid, $dt);
		}
		$this->delquitwork($urs, $month, $max);
	}
	
	private function delquitwork($urs, $month, $max)
	{
		$ds = array();
		$dt1= ''.$month.'-01'; $dt2= ''.$month.'-'.$max.'';
		$uid= $urs['id'];
		if(!isempt($urs['workdate'])){
			if($urs['workdate']>$dt1)$ds[] = "dt<'".$urs['workdate']."'";
		}
		if(!isempt($urs['quitdt'])){
			if($urs['quitdt']<$dt2)$ds[] = "dt>'".$urs['quitdt']."'";
		}
		$str = join(' or ', $ds);
		if(!isempt($str))$this->db->delete('[Q]kqanay',"`uid`=$uid and ($str)");
	}
	
	public function kqanay($uid, $dt)
	{
		if($dt > $this->rock->date)return;
		$dkarr 	= $this->db->getrows('[Q]kqdkjl',"`uid`='$uid' and `dkdt` like '$dt%'",'`dkdt`','`dkdt` asc');
		$this->_dkarr = $dkarr;
		$iswork	= $this->isworkdt($uid, $dt);
		$sjarr	= $this->getkqsj($uid, $dt);
		$db 	= m('kqanay');
		$ids 	= '0';
		foreach($sjarr as $k=>$rs){
			$ztname = $rs['name'];
			$arrs 	= $this->kqanaysss($uid, $dt, $rs, $this->_dkarr);
			$state	= $arrs['state'];
			$states	= $arrs['states'];
			
			//判断是否有请假和外出。。
			if($iswork==1 && $state !='正常'){
				$zcarr	= array();
				foreach($rs['children'] as $k2=>$cog2){
					if($cog2['name']=='正常')$zcarr = $cog2;
				}
				$states = $this->getstates($zcarr, $dt, $uid);	
			}
			
			$emiao	= $arrs['emiao'];
			$time	= $arrs['time'];
			$arr	= array(
				'ztname' 	=> $ztname,
				'state' 	=> $state,
				'states' 	=> $states,
				'remark' 	=> $arrs['remark'],
				'time' 		=> $time,
				'uid' 		=> $uid,
				'dt' 		=> $dt,
				'sort' 		=> $k,
				'iswork' 	=> $iswork,
				'optdt' 	=> $this->rock->now,
				'emiao' 	=> $emiao
			);
			$where 	= "`uid`='$uid' and `dt`='$dt' and `ztname`='$ztname'";
			$id 	= (int)$db->getmou('id', $where);
			if($id==0)$where='';
			$db->record($arr, $where);
			if($id==0)$id = $this->db->insert_id();
			$ids.=','.$id.'';
		}
		$db->delete("id not in ($ids) and `uid`='$uid' and `dt`='$dt'");
	}
	private function kqanaysss($uid, $dt, $kqrs, $dkarr)
	{
		$kqarr	= $kqrs['children'];
		$state	= '未打卡';$states = $remark = ''; $emiao	= 0; $tpk=-1; $time	= ''; $pdtime	= 0;
		if($dkarr && $kqarr)foreach($kqarr as $k=>$rs){
			$stime 	= strtotime(''.$dt.' '.$rs['stime'].'');
			$etime 	= strtotime(''.$dt.' '.$rs['etime'].'');
			$qtype	= $rs['qtype'];
			foreach($dkarr as $k1=>$rs1){
				$dkdt = strtotime($rs1['dkdt']);
				if($stime>$dkdt || $etime<$dkdt)continue;
				$time	= $dkdt;
				$state	= $rs['name'];
				$tpk	= $k1;
				if($qtype==0)break;
			}
			$pdtime	= $stime;
			if($qtype==1)$pdtime = $etime;
			if($time!='')break;
		}
		if($time!=''){
			if($state!='正常'){
				$emiao = $pdtime-$time;
			}
			unset($this->_dkarr[$tpk]);//一次打卡记录只能使用一次
		}
		$barr['state'] 		= $state;
		$barr['emiao'] 		= abs($emiao);
		if($time!='')$time	= date('Y-m-d H:i:s', $time);
		$barr['time'] 		= $time;
		$barr['states'] 	= $states;
		$barr['remark'] 	= $remark;
		if($pdtime!=0)$barr['pdtime'] = date('Y-m-d H:i:s', $pdtime);
		return $barr;
	}
	
	

	
	/**
	*	上班: (当前qtype==0)请假开始时间小于等于 设置正常的截止时间（取最小值）
	*	下班: (当前qtype==1)请假截止时间大于等于 设置正常的开始时间（取最大值）
	*/
	private function getstates($ztarr, $dts, $uid)
	{
		$st1	= strtotime($dts.' '.$ztarr['stime']);
		$et1	= strtotime($dts.' '.$ztarr['etime']);
		$s 		= '';
		$rows 	= $this->db->getall("select `stime`,`etime`,`qjkind` from `[Q]kqinfo` where `uid`='$uid' and `status`=1 and `isturn`=1 and `kind`='请假' and `stime`<='$dts 23:59:59' and `etime`>='$dts 00:00:00'");
		foreach($rows as $k=>$rs){
			$qst = strtotime($rs['stime']);
			$qet = strtotime($rs['etime']);
			if($ztarr['qtype']==1){
				if($qet >= $st1){
					$s = $rs['qjkind'];
				}
			}else{
				if($qst <= $et1){
					$s = $rs['qjkind'];
				}
			}
		}
		if($s==''){
			$rows 	= $this->db->getall("select `outtime`,`intime`,`atype` from `[Q]kqout` where `uid`='$uid' and `status`=1 and `isturn`=1 and `outtime`<='$dts 23:59:59' and `intime`>='$dts 00:00:00'");
			foreach($rows as $k=>$rs){
				$qst = strtotime($rs['outtime']);
				$qet = strtotime($rs['intime']);
				if($ztarr['qtype']==1){
					if($qet >= $st1){
						$s = $rs['atype'];
					}
				}else{
					if($qst <= $et1){
						$s = $rs['atype'];
					}
				}
			}
		}
		return $s;
	}
	
	
	public function getanay($uid, $month)
	{
		$month	= substr($month, 0, 7);
		$dtobj  = c('date');
		$max 	= $dtobj->getmaxdt($month);
		$startdt= ''.$month.'-01';
		$enddt  = ''.$month.'-'.$max.'';
		$rows  	= $this->db->getrows('[Q]kqanay',"`uid`='$uid' and `dt` between '$startdt' and '$enddt'",'*','dt,sort');
		$barr 	= array();
		foreach($rows as $k=>$rs){
			if(!isset($barr[$rs['dt']]))$barr[$rs['dt']]=array();
			$miaocn	= '';
			if($rs['emiao']>0){
				$stssa = explode(':', $dtobj->sjdate($rs['emiao'],'H:i:s'));
				if($stssa[0]>0)$miaocn=''.$stssa[0].'时';
				$miaocn.=''.$stssa[1].'分'.$stssa[2].'秒';
			}
			$rs['miaocn']		= $miaocn;
			$barr[$rs['dt']][] 	= $rs;
		}
		return $barr;
	}
	
	
	/**
	*	计算剩余假期时间,如果审核未通过，申请人不删除照样也会扣除时间
	*/
	public function getqjsytime($uid, $type, $dt='', $id=0)
	{
		$types 	= '增加'.$type.'';
		if($type=='调休')$types='加班';
		if($dt=='')$dt = $this->rock->now;
		$to1	= $this->db->getmou('[Q]kqinfo',"sum(totals)", "`kind`='请假' and `qjkind`='$type' and `uid`='$uid' and `status` not in(5) and `id`<>$id ");
		$zto	= $this->db->getmou('[Q]kqinfo',"sum(totals)", "`kind`='$types' and `uid`='$uid'  and `status`=1 and `stime`<='$dt'");
		if(is_null($to1))$to1=0;
		if(is_null($zto))$zto=0;
		return intval($zto) - intval($to1);
	}
	
	/**
	*	判断这段时间是否可以申请请假
	*/
	public function leavepan($uid, $qjkind, $start, $end, $totals, $id=0)
	{
		$msg 	= '';
		$sdf 	= $this->db->rows('[Q]kqinfo',"`uid`='$uid' and `status`<>5 and ((`stime`<='$start' and `etime`>='$start') or (`stime`<='$end' and `etime`>='$end') or (`stime`>='$start' and `etime`<='$end')) and `kind`='请假' and `id`<>'$id' ");
		if($sdf > 0){
			$msg = '该时间段已申请过了';
		}
		$tsjia	= '事假,病假';
		if($msg == '' && !$this->contain($tsjia, $qjkind)){
			$sy1	= $this->getqjsytime($uid, $qjkind, $start, $id);
			if($sy1<0)$sy1=0;
			$totals	= floatval($totals);
			if($totals>$sy1)$msg = '剩余['.$qjkind.']'.$sy1.'小时,不能申请,'.c('xinhu')->helpstr('kaoqin').'';
		}
		return $msg;
	}
	
	/**
	*	计算当月请假时间合计
	*/
	public function getqjtotal($uid, $month)
	{
		$to 	= $this->getkqtotalsss($uid, $month,'请假');
		return  $to;
	}
	
	/**
	*	计算当月加班时间合计
	*/
	public function getjbtotal($uid, $month)
	{
		$to 	= $this->getkqtotalsss($uid, $month,'加班');
		return  $to;
	}
	private function getkqtotalsss($uid, $month, $kind)
	{
		$ors  	= $this->db->getmou('[Q]kqinfo','sum(totals)as totals',"`status`=1 and `uid`=$uid and `stime` like '$month%' and `kind`='$kind'");
		$to 	= 0;
		if($ors)$to = $ors;
		return  $to;
	}
	
	/**
	*	统计当前我考勤异常的状态
	*/
	public function getkqtotal($uid, $month)
	{
		$sql 	= "SELECT state,count(1)stotal FROM `[Q]kqanay` where `dt` like '$month%' and `uid`=$uid and `iswork`=1 and `states` is null GROUP BY `state`";
		$rows 	= $this->db->getall($sql);
		$chidao	= $zaotui = $weidk = 0;
		foreach($rows as $k=>$rs){
			if($rs['state']=='迟到')$chidao = $rs['stotal'];
			if($rs['state']=='早退')$zaotui = $rs['stotal'];
			if($rs['state']=='未打卡')$weidk= $rs['stotal'];
		}
		return array(
			'cidao' 	=> $chidao,
			'zaotui' 	=> $zaotui,
			'weidk' 	=> $weidk,
			'jiaban' 	=> $this->getjbtotal($uid, $month),
			'leave' 	=> $this->getqjtotal($uid, $month),
		);
	}
	
	/**
	*	获取默认某天应该上班时间段
	*	返回array(array('每天上班时间断'))
	*/
	public function getsbarr($uid, $dt)
	{
		$arr 	= $this->getkqsj($uid, $dt, 1);
		$barr	= array();
		foreach($arr as $k=>$rs){
			if(!isempt($rs['stime']) && !isempt($rs['etime']))$barr[] = $rs;
		}
		return $barr;
	}
	
	/**
	*	根据时间间隔获取上班时间小时
	*/
	public function getsbtime($uid,$sdt, $edt)
	{
		$tot	= 0;
		$sdt1	= strtotime($sdt);
		$edt1	= strtotime($edt);
		$dtsa	= explode(' ', $sdt);
		$dts	= $dtsa[0];
		$dtobj	= c('date', true);
		$jg		= $dtobj->datediff('d', $sdt, $edt);
		
		for($i=0; $i<$jg+1; $i++){
			if($i>0)$dts = $dtobj->adddate($dts, 'd', 1);
			if($this->isworkdt($uid, $dts)==0)continue;
			$arr 	= $this->getsbarr($uid, $dts);
			foreach($arr as $k=>$rs){
				$_sts = strtotime($dts.' '.$rs['stime']);
				$_ets = strtotime($dts.' '.$rs['etime']);
				if($_sts<$sdt1)$_sts=$sdt1;
				if($_ets>$edt1)$_ets=$edt1;
				$_tisg = $_ets - $_sts;
				if($_tisg>0)$tot+=$_tisg;
			}
		}
		return $tot / 3600;
	}
	
	/**
	*	统计月份应上班天数
	*	return array(应上班天数, 有上班天数);
	*/
	public function getsbdt($uid, $month)
	{
		$month	= substr($month, 0, 7);
		$dtobj  = c('date');
		$max 	= $dtobj->getmaxdt($month);
		$startdt= ''.$month.'-01';
		$enddt  = ''.$month.'-'.$max.'';
		$tian	= 0;
		$ysb	= 0;
		$anayarr= $this->db->getrows('[Q]kqanay', "`uid`=$uid and `dt` like '$month%'");
		for($i=0; $i<$max; $i++){
			$oi = ($i<10) ? '0'.$i.'' : $i;
			$dt = ''.$month.'-'.$oi.'';
			if($this->isworkdt($uid, $dt)==1){
				$tian++;
				foreach($anayarr as $k=>$rs){
					if($rs['dt']==$dt && $rs['state']!='未打卡'){
						$ysb++;
						break;
					}
				}
			}
		}
		return array($tian, $ysb, $tian-$ysb);
	}
	
	
	/**
	*	all统计
	*/
	public function alltotal($month, $uids)
	{
		$barr = $this->alltotalrows($month, $uids);
		$data = $barr['rows'][0];
		$farrs 				= $barr['columns'];
		$farrs['未打卡'] 	= 'weidk';
		$farrs['请假'] 		= 'qingjia';
		$farrs['加班'] 		= 'jiaban';
		$farrs['外出出差'] 	= 'outci';
		$farrs['打卡异常'] 	= 'errci';
		$farrs['应上班'] 	= 'sbday';
		$farrs['已上班'] 	= 'ysbday';
		return array('data'=>$data, 'fields'=>$farrs, 'unita'=> array(
			'qingjia' => '小时',
			'jiaban'  => '小时',
			'sbday'  	=> '天',
			'ysbday'  	=> '天',
		));
	}
	
	public function alltotalrows($month, $uids)
	{
		$rows	= array();
		if(is_array($uids)){
			$rows = $uids;
		}else{
			$uidsa = explode(',', $uids);
			foreach($uidsa as $_uids)$rows[] = array('id'=>$_uids);
		}
		$uids 	= '0';
		foreach($rows as $k=>$rs)$uids.=','.$rs['id'].'';
		$farrs	= $columns = array();
		//获取考勤状态数组{'正常':'state0'}
		if($rows){
			$fuid 	= $rows[0]['id'];
			$farrs 	= $this->getkqztarr($fuid, $month.'-01');
			$columns= $farrs;
		}
		
		$darr	= $this->db->getall("SELECT uid,state,states FROM `[Q]kqanay` where iswork=1 and dt like '$month%' and `uid` in($uids)");
		$sarr 	= array();
		foreach($darr as $k=>$rs){
			$state 	= $rs['state'];
			$uid 	= $rs['uid'];
			if(!isempt($rs['states']))$state='正常';
			if(!isset($sarr[$uid]))$sarr[$uid]=array();
			if(!isset($sarr[$uid][$state]))$sarr[$uid][$state]=0;
			$sarr[$uid][$state]++;
		}
		
		$farrs['未打卡'] 	= 'weidk';
		$farrs['请假'] 		= 'qingjia';
		$farrs['事假'] 		= 'shijia';
		$farrs['加班'] 		= 'jiaban';
		$lxarr 				= m('option')->getmnum('kqqjkind');
		foreach($lxarr as $k=>$rs){
			$tev 				= 'qingjia'.$rs['id'].'';
			$farrs[$rs['name']] = $tev;
			//$columns[$rs['name']]		= $tev;
		}
		
		$kqarr	= $this->db->getall("select sum(totals)as totals,kind,qjkind,uid from `[Q]kqinfo` where `status`=1 and `uid` in($uids) and `stime` like '$month%' and `kind` in('请假','加班') group by `uid`,`qjkind`");
		foreach($kqarr as $k=>$rs){
			$uid 	= $rs['uid'];
			$kind	= $rs['qjkind'];
			if(isempt($kind))$kind = '加班';
			if(!isset($sarr[$uid]))$sarr[$uid]=array();
			$sarr[$uid][$kind] = $rs['totals'];
			$kinds = $kind.'(时)';
			if($rs['kind']=='请假' && !isset($columns[$kinds])){
				$columns[$kinds] = $farrs[$kind];
			}
		}
		
		foreach($rows as $k=>$rs){
			$uid 	= $rs['id'];
			if(isset($sarr[$uid])){
				foreach($sarr[$uid] as $zt=>$v){
					if(isset($farrs[$zt])){
						$rows[$k][$farrs[$zt]] = $v;
					}
				}
			}
			$outci	= $this->db->rows('[Q]kqout',"`status`=1 and `uid`=$uid and `outtime` like '$month%'");
			if($outci==0)$outci='';
			$rows[$k]['outci'] = $outci;
			
			$errci	= $this->db->rows('[Q]kqerr',"`status`=1 and `uid`=$uid and `dt` like '$month%'");
			if($errci==0)$errci='';
			$rows[$k]['errci'] = $errci;
			
			$sbdt 			   	= $this->getsbdt($uid, $month);
			$rows[$k]['sbday'] 	= $sbdt[0];
			$rows[$k]['ysbday'] = $sbdt[1]>0 ?  $sbdt[1] : '';
		}
		
		return array('rows'=>$rows,'columns'=>$columns);
	}
}