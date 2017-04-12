<?php
class flow_userClassModel extends flowModel
{
	
	/**
	*	用户显示展示
	*/
	protected function flowbillwhere($uid, $lx)
	{
		$key	= $this->rock->post('key');
		if($lx == 'gl'){
			$where  = '';
		}else{
			$where 	= 'and `status`=1 '.$this->viewmodel->viewwhere($this->moders, $uid, 'id');
			
			 //全部下属
			if($lx=='downall'){
				$where  .= ' and '.m('admin')->getdownwheres('id', $uid, 0);
			}
			if($lx=='down'){
				$where  .= ' and '.m('admin')->getdownwheres('id', $uid, 1);
			}
		}
		
		
		if(!isempt($key))$where.= m('admin')->getkeywhere($key);
	
		return array(
			'where' => $where,
			'fields'=> '`name`,`id`,`id` as uid,`face`,`sort`,`deptallname`,`ranking`,`tel`,`mobile`,`email`',
			'order' => 'sort'
		);
	}
	
	//替换
	public function flowrsreplace($rs, $lx=0)
	{
		if($lx==2){
			if(isset($rs['mobile']) && !isempt($rs['mobile']))$rs['mobile']='<a href="tel:'.$rs['mobile'].'">'.$rs['mobile'].'</a>';
			if(isset($rs['tel']) && !isempt($rs['tel']))$rs['tel']='<a href="tel:'.$rs['tel'].'">'.$rs['tel'].'</a>';
		}
		return $rs;
	}
}