<?php
/**
*	模块：userinfo.人员档案，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：http://xh829.com/
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'userinfo',modename='人员档案',isflow=0,modeid='29',atype = params.atype,pnum=params.pnum;
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"fields":"name","name":"\u59d3\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"ranking","name":"\u804c\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"state","name":"\u4eba\u5458\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"zhaopian","name":"\u7167\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"idnum","name":"\u8eab\u4efd\u8bc1\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"tel","name":"\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"housedizhi","name":"\u5bb6\u5ead\u4f4f\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"nowdizhi","name":"\u73b0\u4f4f\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"hunyin","name":"\u5a5a\u59fb","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"mobile","name":"\u624b\u673a\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"email","name":"\u90ae\u7bb1","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"birtype","name":"\u751f\u65e5\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"birthday","name":"\u751f\u65e5","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"xueli","name":"\u5b66\u5386","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"minzu","name":"\u6c11\u65cf","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"jiguan","name":"\u7c4d\u8d2f","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"banknum","name":"\u5de5\u8d44\u5361\u5e10\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"bankname","name":"\u5f00\u6237\u884c","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"spareman","name":"\u5907\u7528\u8054\u7cfb\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"sparetel","name":"\u5907\u7528\u8054\u7cfb\u4eba\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"workdate","name":"\u5165\u804c\u65f6\u95f4","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"positivedt","name":"\u8f6c\u6b63\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [];
	
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
			return js.getajaxurl(act,'mode_userinfo|input','flow',{'modeid':modeid});
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
		columns:[{text:"姓名",dataIndex:"name"},{text:"部门",dataIndex:"deptname"},{text:"职位",dataIndex:"ranking"},{text:"人员状态",dataIndex:"state",sortable:true},{text:"照片",dataIndex:"zhaopian"},{text:"手机号",dataIndex:"mobile"},{text:"邮箱",dataIndex:"email"},{text:"学历",dataIndex:"xueli"},{text:"民族",dataIndex:"minzu"},{text:"籍贯",dataIndex:"jiguan"},{text:"入职时间",dataIndex:"workdate",sortable:true},{text:"转正日期",dataIndex:"positivedt",sortable:true},{
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

$('#tdleft_{rand}').remove();

//[自定义区域end]

	js.initbtn(c);
	var a = $('#viewuserinfo_{rand}').bootstable(bootparams);
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
<div id="viewuserinfo_{rand}"></div>
<!--HTMLend-->