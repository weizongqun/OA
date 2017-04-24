<?php
/**
*	模块：project.项目，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：http://xxxxxxxx.com/
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'project',modename='项目',isflow=0,modeid='22',atype = params.atype,pnum=params.pnum;
	if(!atype)atype='';if(!pnum)pnum='';
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
			return js.getajaxurl(act,'mode_project|input','flow',{'modeid':modeid});
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
		}
	};	
	
	//表格参数设定
	var bootparams = {
		fanye:true,modenum:modenum,modename:modename,
		url:c.storeurl(),storeafteraction:'storeaftershow',storebeforeaction:'storebeforeshow',
		params:{atype:atype},
		columns:[{text:"项目类型",dataIndex:"type"},{text:"编号",dataIndex:"num"},{text:"名称",dataIndex:"title"},{text:"开始时间",dataIndex:"startdt"},{text:"预计结束时间",dataIndex:"enddt"},{text:"负责人",dataIndex:"fuze"},{text:"执行人",dataIndex:"runuser"},{text:"进度(%)",dataIndex:"progress"},{text:"状态",dataIndex:"state"},{
			text:'',dataIndex:'caozuo',callback:'opegs{rand}'
		}],
		itemdblclick:function(){
			c.view();
		},
		itemclick:function(){
			get('xiang_{rand}').disabled=false;
		},
		beforeload:function(){
			get('xiang_{rand}').disabled=true;
		},
		load:function(d){
			c.loaddata(d);
		}
	};
	
	opegs{rand}=function(){
		c.reload();
	}
	
//[自定义区域start]

c.setcolumns('progress',{
	renderer:function(v){
		return '<div class="progress" style="margin:0;width:120px;"><div class="progress-bar progress-bar-success" style="width:'+v+'%;color:#000000;">'+v+'%</div></div>';
	},
	text:'进度'
});
c.setcolumns('workshu',{
	renderer:function(v,d,i){
		return ''+v+'&nbsp;<a href="javascript:;" onclick="viespere{rand}('+i+')">查看</a>';
	}
});
viespere{rand}=function(id){
	var d 	= a.getData(id);
	var bo 	= addtabs({name:'项目['+d.title+']的任务',url:'flow,page,work,pnum=allall,atype=all,projcetid='+d.id+'',num:'projcetidwork'+d.id+''});
}

//[自定义区域end]

	js.initbtn(c);//初始化绑定按钮方法
	var a = $('#viewproject_{rand}').bootstable(bootparams);//加载表格
	c.init();
	$('#downbtn_{rand}').rockmenu({
		width:110,top:35,donghua:false,
		data:[{
			name:'高级搜索',lx:0
		},{
			name:'打印',lx:1
		}],
		itemsclick:function(d, i){
			if(d.lx==0)c.searchhigh();
			if(d.lx==1)c.printlist();
		}
	});
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
			<div style="width:81px" class="btn-group">
			<button class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" nowrap>
			<button class="btn btn-default" id="xiang_{rand}" click="view" disabled type="button">详情</button> &nbsp; 
			<button class="btn btn-default" click="daochu,1" type="button">导出</button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewproject_{rand}"></div>
<!--HTMLend-->