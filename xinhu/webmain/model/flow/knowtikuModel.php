<?php
class flow_knowtikuClassModel extends flowModel
{
	protected function flowchangedata(){
		$this->rs['content'] = c('html')->replace($this->rs['content']);
	}
	
	
	
	public function flowrsreplace($rs,$isv=0)
	{
		if(isset($rs['typeid']))$rs['typeid'] 	= $this->db->getmou('[Q]option','name',"`id`='".$rs['typeid']."'");
		$rs['type'] = ($rs['type']==1)?'多选':'单选';
		if($isv==1){
			$ss = '<font color=#888888>停用</font>';
			if($rs['status']==1)$ss = '<font color=green>启用</font>';
			$rs['status'] = $ss;
		}
		return $rs;
	}
	
	
	protected function flowbillwhere($uid, $lx)
	{
		$where  = '';
		$typeid = $this->rock->post('typeid','0');
		$key 	= $this->rock->post('key');
		if($lx=='xuexi')$where='and a.`status`=1';
		if($typeid!='0'){
			$alltpeid = m('option')->getalldownid($typeid);
			$where .= ' and a.`typeid` in('.$alltpeid.')';
		}
		if($key != ''){
			$where.=" and (a.`title` like '%$key%' or b.`name` like '%$key%')";
		}
		return array(
			'where' => $where,
			'order' => 'a.sort,a.`optdt` desc',
			'table'	=> '`[Q]'.$this->mtable.'` a left join `[Q]option` b on a.`typeid`=b.`id`',
			'fields'=> 'a.*,b.name as typename'
		);
	}
}