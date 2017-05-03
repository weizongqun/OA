<?php
/**
*	模块：work.任务，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：http://xh829.com/
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'work',modename='任务',isflow=1,modeid='4',atype = params.atype,pnum=params.pnum;
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"fields":"title","name":"\u6807\u9898","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"type","name":"\u7c7b\u578b","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"grade","name":"\u7b49\u7ea7","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"dist","name":"\u5206\u914d\u7ed9","fieldstype":"changeuser","ispx":"0","isalign":"0","islb":"1"},{"fields":"projectid","name":"\u6240\u5c5e\u9879\u76ee","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"startdt","name":"\u5f00\u59cb\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"ddname","name":"\u7763\u5bfc\u4eba","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"score","name":"\u4efb\u52a1\u5206\u503c","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u521b\u5efa\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"optdt","name":"\u521b\u5efa\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [];
	
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
			openinput(modename,modenum,id,'opegs{rand}');
		},
		//打开详情
		view:function(){
			var d=a.changedata;
			openxiangs(modename,modenum,d.id,'opegs{rand}');
		},
		searchbtn:function(){
			this.search({});
		},
		//搜索
		search:function(cans){
			var s=get('key_{rand}').value,zt='';
			if(get('selstatus_{rand}'))zt=get('selstatus_{rand}').value;
			var canss = js.apply({key:s,keystatus:zt}, cans);
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
			return js.getajaxurl(act,'mode_work|input','flow',{'modeid':modeid});
		},
		//查看切换
		changatype:function(o1,lx){
			$("button[id^='changatype{rand}']").removeClass('active');
			$('#changatype{rand}_'+lx+'').addClass('active');
			a.setparams({atype:lx},true);
			nowtabssettext($(o1).html());
		},
		init:function(){
			$('#key_{rand}').keyup(function(e){
				if(e.keyCode==13)c.searchbtn();
			});
			this.initpage();
		},
		initpage:function(){
			
		},
		loaddata:function(d){
			if(!d.atypearr)return;
			var d1 = d.atypearr,len=d1.length,i,str='';
			for(i=0;i<len;i++){
				str+='<button class="btn btn-default" click="changatype,'+d1[i].num+'" id="changatype{rand}_'+d1[i].num+'" type="button">'+d1[i].name+'</button>';
			}
			$('#changatype{rand}').html(str);
			$('#changatype{rand}_'+atype+'').addClass('active');
			js.initbtn(c);
		},
		setcolumns:function(fid, cnas){
			var d = false,i,ad=bootparams.columns,len=ad.length,oi=-1;
			for(i=0;i<len;i++){
				if(ad[i].dataIndex==fid){
					d = ad[i];
					oi= i;
					break;
				}
			}
			if(d){
				d = js.apply(d, cnas);
				bootparams.columns[oi]=d;
			}
		},
		initcolumns:function(bots){
			var num = 'columns_'+modenum+'_'+pnum+'',d=[],d1,d2={},i,len=fieldsarr.length,bok;
			var nstr= fieldsselarr[num];if(!nstr)nstr='';
			if(nstr)nstr=','+nstr+',';
			for(i=0;i<len;i++){
				d1 = fieldsarr[i];
				bok= false;
				if(nstr==''){
					if(d1['islb']=='1')bok=true;
				}else{
					if(nstr.indexOf(','+d1.fields+',')>=0)bok=true;
				}
				if(bok){
					d2={text:d1.name,dataIndex:d1.fields};
					if(d1.ispx=='1')d2.sortable=true;
					if(d1.isalign=='1')d2.align='left';
					if(d1.isalign=='2')d2.align='right';
					d.push(d2);
				}
			}
			if(nstr=='' || nstr.indexOf(',caozuo,')>=0)d.push({text:'',dataIndex:'caozuo',callback:'opegs{rand}'});
			if(!bots){
				bootparams.columns=d;
			}else{
				a.setColumns(d);
			}
		},
		setparams:function(cs){
			var ds = js.apply({},cs);
			a.setparams(ds);
		},
		storeurl:function(){
			var url = this.getacturl('publicstore')+'&pnum='+pnum+'';
			return url;
		},
		printlist:function(){
			js.msg('success','可使用导出，然后打开在打印');
		},
		getbtnstr:function(txt, click, ys, ots){
			if(!ys)ys='default';
			if(!ots)ots='';
			return '<button class="btn btn-'+ys+'" id="btn'+click+'_{rand}" click="'+click+'" '+ots+' type="button">'+txt+'</button>';
		},
		setfieldslist:function(){
			new highsearchclass({
				modenum:modenum,
				modeid:modeid,
				type:1,
				pnum:pnum,atype:atype,
				fieldsarr:fieldsarr,
				fieldsselarr:fieldsselarr,
				oncallback:function(str){
					fieldsselarr[this.columnsnum]=str;
					c.initcolumns(true);
					c.reload();
				}
			});
		}
	};	
	
	//表格参数设定
	var bootparams = {
		fanye:true,modenum:modenum,modename:modename,
		url:c.storeurl(),storeafteraction:'storeaftershow',storebeforeaction:'storebeforeshow',
		params:{atype:atype},
		columns:[{text:"标题",dataIndex:"title",align:"left"},{text:"类型",dataIndex:"type"},{text:"等级",dataIndex:"grade",sortable:true},{text:"分配给",dataIndex:"dist"},{text:"开始时间",dataIndex:"startdt"},{text:"截止时间",dataIndex:"enddt"},{text:"督导人",dataIndex:"ddname"},{text:"任务分值",dataIndex:"score"},{text:"创建人",dataIndex:"optname"},{text:"创建时间",dataIndex:"optdt"},{text:"状态",dataIndex:"status",sortable:true},{text:"状态",dataIndex:"statustext"},{
			text:'',dataIndex:'caozuo',callback:'opegs{rand}'
		}],
		itemdblclick:function(){
			c.view();
		},
		load:function(d){
			c.loaddata(d);
		}
	};
	c.initcolumns(false);
	opegs{rand}=function(){
		c.reload();
	}
	
//[自定义区域start]

var plid = params.projcetid;
if(plid)bootparams.url+='&projcetid='+plid+'';
c.setcolumns('title',{
	renderer:function(v,d){
		var s = v;
		if(d.projectid!='')s+='<br><span style="color:#888888;font-size:12px">'+d.projectid+'</span>';
		return s;
	}
});

//[自定义区域end]

	js.initbtn(c);
	var a = $('#viewwork_{rand}').bootstable(bootparams);
	c.init();
	var ddata = [{name:'高级搜索',lx:0}];
	if(admintype==1)ddata.push({name:'自定义列显示',lx:2});
	ddata.push({name:'打印',lx:1});
	$('#downbtn_{rand}').rockmenu({
		width:120,top:35,donghua:false,
		data:ddata,
		itemsclick:function(d, i){
			if(d.lx==0)c.searchhigh();
			if(d.lx==1)c.printlist();
			if(d.lx==2)c.setfieldslist();
		}
	});
});
</script>
<!--SCRIPTend-->
<!--HTMLstart-->
<div>
	<table width="100%">
	<tr>
		<td style="padding-right:10px;" id="tdleft_{rand}" nowrap><button class="btn btn-primary" click="clickwin,0" type="button"><i class="icon-plus"></i> 新增</button></td>
		<td>
			<input class="form-control" style="width:160px" id="key_{rand}" placeholder="搜索关键词">
		</td>
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">待分配</option><option style="color:green" value="1">已完成</option><option style="color:red" value="2">未通过</option><option style="color:#ff6600" value="3">待执行</option><option style="color:#526D08" value="4">执行中</option><option style="color:#888888" value="5">已作废</option><option style="color:" value="6">待验证</option><option style="color:#17B2B7" value="23">退回</option></select></td>
		<td style="padding-left:10px">
			<div style="width:81px" class="btn-group">
			<button class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" id="tdright_{rand}" nowrap>
			<button class="btn btn-default" click="daochu,1" type="button">导出</button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewwork_{rand}"></div>
<!--HTMLend-->