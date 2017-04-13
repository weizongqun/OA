<?php
/**
*	模块：meet.会议，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：http://xh829.com/
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'meet',modename='会议',modeid='2',atype = params.atype;
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
			return js.getajaxurl(act,'mode_meet|input','flow');
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
		columns:[{text:"会议室",dataIndex:"hyname",sortable:true},{text:"主题",dataIndex:"title",align:"left"},{text:"开始时间",dataIndex:"startdt"},{text:"结束时间",dataIndex:"enddt"},{text:"参会人",dataIndex:"joinname"},{text:"说明",dataIndex:"explain"},{text:"发起人",dataIndex:"optname",sortable:true},{text:"状态",dataIndex:"state",sortable:true},{text:"会议纪要人",dataIndex:"jyname"},{
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

c.initpage=function(){
	$('#key_{rand}').parent().before('<td style="padding-right:10px;"><input onclick="js.datechange(this,\'date\')" style="width:110px" placeholder="日期" readonly class="form-control datesss" id="dt_{rand}" ></td>');
}
c.searchbtn=function(){
	var dt = get('dt_{rand}').value;
	this.search({dt:dt});
}

//[自定义区域end]

	js.initbtn(c);//初始化绑定按钮方法
	var a = $('#viewmeet_{rand}').bootstable(bootparams);//加载表格
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
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"><button class="btn btn-default" id="changatype{rand}_today" click="changatype,today" type="button">今日会议</button><button class="btn btn-default" id="changatype{rand}_bzhy" click="changatype,bzhy" type="button">本周会议</button><button class="btn btn-default" id="changatype{rand}_all" click="changatype,all" type="button">全部会议</button><button class="btn btn-default" id="changatype{rand}_myfq" click="changatype,myfq" type="button">我发起会议</button></div></td>
	
		<td align="right" nowrap>
			<button class="btn btn-default" id="xiang_{rand}" click="view" disabled type="button">详情</button> &nbsp; 
			<button class="btn btn-default" click="daochu,1" type="button">导出</button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewmeet_{rand}"></div>
<!--HTMLend-->