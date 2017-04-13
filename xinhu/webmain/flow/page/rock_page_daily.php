<?php
/**
*	模块：daily.工作日报，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：http://xh829.com/
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'daily',modename='工作日报',modeid='3',atype = params.atype;
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
			return js.getajaxurl(act,'mode_daily|input','flow');
		},
		//查看切换
		changatype:function(o1,lx){
			$("button[id^='changatype{rand}']").removeClass('active');
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
		columns:[{text:"部门",dataIndex:"deptname"},{text:"人员",dataIndex:"optname"},{text:"日报类型",dataIndex:"type",sortable:true},{text:"日期",dataIndex:"dt",sortable:true},{text:"内容",dataIndex:"content",align:"left"},{text:"新增时间",dataIndex:"adddt"},{text:"操作时间",dataIndex:"optdt",sortable:true},{
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



//[自定义区域end]

	js.initbtn(c);//初始化绑定按钮方法
	var a = $('#viewdaily_{rand}').bootstable(bootparams);//加载表格
	c.init();
});
</script>
<!--SCRIPTend-->
<!--HTMLstart-->
<div>
	<table width="100%">
	<tr>
		<td style="padding-right:10px;"><button class="btn btn-primary" click="clickwin,0" type="button"><i class="icon-plus"></i> 新增</button></td>
		<td>
			<input class="form-control" style="width:160px" id="key_{rand}" placeholder="搜索关键词">
		</td>
		<td style="padding-left:10px">
			<button class="btn btn-default" click="searchbtn" type="button">搜索</button> 
		</td>
		<td style="padding-left:10px">
			<button class="btn btn-default" click="searchhigh" type="button">高级搜索</button> 
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"><button class="btn btn-default" id="changatype{rand}_my" click="changatype,my" type="button">我的日报</button><button class="btn btn-default" id="changatype{rand}_undall" click="changatype,undall" type="button">下属全部日报</button><button class="btn btn-default" id="changatype{rand}_undwd" click="changatype,undwd" type="button">全部下属未读</button></div></td>
	
		<td align="right" nowrap>
			<button class="btn btn-default" id="xiang_{rand}" click="view" disabled type="button">详情</button> &nbsp; 
			<button class="btn btn-default" click="daochu,1" type="button">导出</button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewdaily_{rand}"></div>
<!--HTMLend-->