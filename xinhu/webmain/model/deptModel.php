<?php
class deptClassModel extends Model
{
	/**
	*	$uarr 相应人员才能查看对应部门数据
	*/
	public function getdata($uarr=array())
	{
		$darr = $dtotal =array();
		$did  = '0';
		foreach($uarr as $k=>$rs){
			$dpath = str_replace(array('[',']'), array('',''), $rs['deptpath']);
			if(!isempt($dpath)){
				$dpatha = explode(',', $dpath);
				foreach($dpatha as $dpatha1){
					$darr[$dpatha1]=$dpatha1;
				}
			}
		}
		foreach($darr as $k1=>$v1)$did.=','.$k1.'';
		$rows = $this->getall('id in('.$did.')','id,name,pid,sort','sort');
		$dbs  = m('admin');
		foreach($rows as $k=>$rs){
			$stotal = $dbs->rows("`status`=1 and instr(`deptpath`,'[".$rs['id']."]')>0");
			$rows[$k]['stotal'] = $stotal; //对应部门下有多少人
		}
		return $rows;
	}
	
	/**
	*	获取部门和人员数据
	*/
	public function getdeptuserdata()
	{
		$userarr 	= m('admin')->getuser(1);
		$deptarr 	= $this->getdata($userarr);
		
		return array(
			'uarr' => $userarr,
			'darr' => $deptarr,
		);
	}
	
}