<?php
/**
*	考勤统计
*/
class agent_kqtotalClassModel extends agentModel
{
	protected function agentdata($uid, $lx)
	{
		$key 	= $this->rock->post('key');
		
		$rows[] = array(
			'title' => '我['.date('Y-m').']考勤统计',
			'cont'	=> '应上班：2天，已上班：2天',
			'uid'	=> $uid
		);
		
		$arr['rows'] 	= $rows;
		return $arr;
	}
}