<?php
class modeClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_set');
	}
	public function getmodearr()
	{
		$arr = $this->getall('status=1','`id`,`num`,`name`,`table`,`type`,`isflow`','sort');
		foreach($arr as $k=>$rs){
			$arr[$k]['name'] = ''.$rs['id'].'.'.$rs['name'].'('.$rs['num'].')';
		}
		return $arr;
	}
	
	public function getmoderows($uid, $sww='')
	{
		$where	= m('admin')->getjoinstr('receid', $uid);
		$arr 	= $this->getall("`status`=1 and `type`<>'系统' $sww $where",'`id`,`num`,`name`,`table`,`type`,`isflow`','`sort`');
		return $arr;
	}
	
	public function getmodemyarr($uid=0)
	{
		$where = '';
		if($uid>0)$where = m('admin')->getjoinstr('receid', $uid);
		$arr = $this->getall('status=1 and isflow=1 '.$where.'','`id`,`name`','sort');
		return $arr;
	}
	
	//生成列表页面
	public function createlistpage($modeid)
	{
		if(is_array($modeid)){
			$mors	= $modeid;
		}else{
			$mors 	= m('flow_set')->getone($modeid,'`id`,`table`,`num`,`name`,`isflow`');
		}
		$num	= $mors['num'];
		$name	= $mors['name'];
		$modeid	= $mors['id'];
		$columnsstr = '';
		$path 	= ''.P.'/flow/page/rock_page_'.$num.'.php';
		
		$farr 	= m('flow_element')->getall("`mid`='$modeid' and `iszb`=0 and `islb`=1",'`fields`,`name`,`fieldstype`,`ispx`,`isalign`','`sort`');
		foreach($farr as $k=>$rs){
			$columnsstr.='{text:"'.$rs['name'].'",dataIndex:"'.$rs['fields'].'"';
			if($rs['ispx']==1)$columnsstr.=',sortable:true';
			if($rs['isalign']==1)$columnsstr.=',align:"left"';
			if($rs['isalign']==2)$columnsstr.=',align:"right"';
			$columnsstr.='},';
		}
		$jgpstr 	= '<!--SCRIPTend-->';
		$hstart 	= '<!--HTMLstart-->';
		$hendts 	= '<!--HTMLend-->';
		$oldcont 	= @file_get_contents($path);
		$autoquye	= $this->rock->getcai($oldcont,'//[自定义区域start]','//[自定义区域end]');
		
		//读取流程模块的条件
		$wrows 		= m('flow_where')->getall("`setid`='$modeid' and `num` is not null and `status`=1 and `islb`=1",'`id`,`num`,`name`','`sort`');
		$whtml 		= '';
		if($wrows){
			$whtml='<div id="changatype{rand}" class="btn-group">';
			foreach($wrows as $k=>$rs){
				$whtml.='<button class="btn btn-default" id="changatype{rand}_'.$rs['num'].'" click="changatype,'.$rs['num'].'" type="button">'.$rs['name'].'</button>';
			}
			$whtml.='</div>';
		}
		
$html= "".$hstart."
<div>
	<table width=\"100%\">
	<tr>
		<td style=\"padding-right:10px;\"><button class=\"btn btn-primary\" click=\"clickwin,0\" type=\"button\"><i class=\"icon-plus\"></i> 新增</button></td>
		<td>
			<input class=\"form-control\" style=\"width:160px\" id=\"key_{rand}\" placeholder=\"搜索关键词\">
		</td>
		<td style=\"padding-left:10px\">
			<button class=\"btn btn-default\" click=\"searchbtn\" type=\"button\">搜索</button> 
		</td>
		<td style=\"padding-left:10px\">
			<button class=\"btn btn-default\" click=\"searchhigh\" type=\"button\">高级搜索</button> 
		</td>
		<td  width=\"90%\" style=\"padding-left:10px\">$whtml</td>
	
		<td align=\"right\" nowrap>
			<button class=\"btn btn-default\" id=\"xiang_{rand}\" click=\"view\" disabled type=\"button\">详情</button> &nbsp; 
			<button class=\"btn btn-default\" click=\"daochu,1\" type=\"button\">导出</button> 
		</td>
	</tr>
	</table>
</div>
<div class=\"blank10\"></div>
<div id=\"view".$num."_{rand}\"></div>
".$hendts."";		
$str = "<?php
/**
*	模块：".$num.".".$name."，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：http://xh829.com/
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = '".$num."',modename='".$name."',modeid='".$modeid."',atype = params.atype;
	if(!atype)atype='';
	//常用操作c方法
	var c = {
		//刷新
		reload:function(){
			a.reload();
		},
		//新增编辑窗口
		clickwin:function(o1,lx){
			var id=0;
			if(lx==1)id=a.changeid;
			openinput(modename,modenum,id);
		},
		//打开详情
		view:function(){
			var d=a.changedata;
			openxiangs(modename,modenum,d.id);
		},
		searchbtn:function(){
			this.search({});
		},
		//搜索
		search:function(cans){
			var s=get('key_{rand}').value;
			var canss = js.apply({key:s}, cans);
			a.setparams(canss,true);
		},
		//高级搜索
		searchhigh:function(){
			new highsearchclass({
				modenum:modenum,
				oncallback:function(d){
					c.searchhighb(d);
				}
			});
		},
		searchhighb:function(d){
			d.key='';
			get('key_{rand}').value='';
			a.setparams(d,true);
		},
		//导出
		daochu:function(){
			a.exceldown();
		},
		//对应控制器返回rul
		getacturl:function(act){
			return js.getajaxurl(act,'mode_".$num."|input','flow');
		},
		//查看切换
		changatype:function(o1,lx){
			$(\"button[id^='changatype{rand}']\").removeClass('active');
			$('#changatype{rand}_'+lx+'').addClass('active');
			a.setparams({atype:lx},true);
			nowtabssettext($(o1).html());
		},
		init:function(){
			$('#changatype{rand}_'+atype+'').addClass('active');
			this.initpage();
		},
		initpage:function(){
			
		}
	};	
	
	//表格参数设定
	var bootparams = {
		fanye:true,modenum:modenum,modename:modename,
		url:c.getacturl('publicstore'),storeafteraction:'storeaftershow',
		params:{atype:atype},
		columns:[".$columnsstr."{
			text:'',dataIndex:'caozuo'
		}],
		itemdblclick:function(){
			c.view();
		},
		itemclick:function(){
			get('xiang_{rand}').disabled=false;
		},
		beforeload:function(){
			get('xiang_{rand}').disabled=true;
		}
	};
	
//[自定义区域start]

$autoquye

//[自定义区域end]

	js.initbtn(c);//初始化绑定按钮方法
	var a = $('#view".$num."_{rand}').bootstable(bootparams);//加载表格
	c.init();
});
</script>
".$jgpstr."";	
		$bstrs = '<!--HTML-->';
		if(!isempt($oldcont) && contain($oldcont, $jgpstr) && contain($oldcont, $bstrs)){
			$strarr = explode($jgpstr, $oldcont);
			$nstr 	= $strarr[1];
			$htmlqy = $this->rock->getcai($nstr, $hstart, $hendts);
$rstr 	= "".$hstart."
".$htmlqy."
".$hendts."";
			$nstr 	= str_replace($rstr, '', $nstr);
			$nstr 	= str_replace($bstrs, $html.$bstrs, $nstr);
			$str	.= "\n".$nstr;
		}else{
			$str.= "\n".$html;
		}
		$bo = $this->rock->createtxt($path, $str);
		if(!$bo)$path='';
		return $path;
	}
}