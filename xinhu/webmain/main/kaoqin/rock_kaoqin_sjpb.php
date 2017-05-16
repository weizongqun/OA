<?php defined('HOST') or die ('not access');?>
<script>
$(document).ready(function(){
	{params}
	var atype=params.atype,columna=[],chagnedtarr={},lbob=false;
	var column = [{
		text:'部门',dataIndex:'deptname',align:'left',sortable:true
	},{
		text:'姓名',dataIndex:'name',sortable:true
	}];
	for(var i=1;i<=28;i++){
		columna.push({
			text:''+i+'',
			dataIndex:'day'+i+'',
		});
	}
	var a = $('#view_{rand}').bootstable({
		tablename:'admin',fanye:true,url:publicstore('{mode}','{dir}'),storeafteraction:'pbkqdistafter',storebeforeaction:'pbkqdistbefore',
		columns:[].concat(column,columna),
		itemclick:function(d,oi, e){
			var e1 = e.target;
			if(e1.nodeName.toLowerCase()=='td'){
				var o1 = $(e1),row = o1.attr('row'),cell = parseFloat(o1.attr('cell'));
				if(cell>=2){
					var ke = 'a_'+row+'_'+cell+'';
					if(chagnedtarr[ke]){
						o1.css('background','');
						chagnedtarr[ke] = false;
					}else{
						o1.css('background','#94DDFC');
						chagnedtarr[ke] = o1;
					}
				}
			}
		},
		loadbefore:function(d){
			var cs = [],i;
			for(i in column)cs.push(column[i]);
			var warr=['日','一','二','三','四','五','六'],w=parseFloat(d.week),tsa;
			for(i=1;i<=d.maxjg;i++){
				if(i>1)w++;
				if(w>6)w=0;
				tsa = ''+i+'<br>('+warr[w]+')';
				if(w==0||w==6)tsa='<font color="#ff6600">'+tsa+'</font>';
				cs.push({
					text:tsa,
					dataIndex:'day'+i+''
				});
			}
			a.setColumns(cs);
		},
		load:function(d){
			var str='提示：其中空白为休息日',gzrows=d.gzrows;
			
			var rda = [{
				name:'设置为休息日',lx:0
			},{
				name:'取消休息日',lx:1
			},{
				name:'设置为工作日',lx:2
			},{
				name:'取消工作日',lx:3
			}];
			for(var i=0;i<gzrows.length;i++){
				str+='，'+gzrows[i].id+'：'+gzrows[i].name+'';
				rda.push({name:'设置规则为：'+gzrows[i].name+'',lx:4,id:gzrows[i].id});
			}
			rda.push({name:'取消规则',id:5});
			$('#guistr_{rand}').html(str);
			
			if(!lbob){
				$('#downbtn_{rand}').rockmenu({
					width:230,top:35,donghua:false,
					data:rda,
					itemsclick:function(d, i){
						c.setdownss(d);
					}
				});
			}
		}
	});
	var c = {
		search:function(){
			var s=get('key_{rand}').value;
			a.setparams({key:s,dt1:get('dt1_{rand}').value},true);
		},
		clickdt:function(o1, lx){
			$(o1).rockdatepicker({initshow:true,view:'month',inputid:'dt'+lx+'_{rand}'});
		},
		daochu:function(){
			a.exceldown('考勤时间排班('+get('dt1_{rand}').value+')');
		},
		xuanzeq:function(){
			for(var i in chagnedtarr){
				if(chagnedtarr[i])chagnedtarr[i].css('background','');
			}
			chagnedtarr={};
		},
		setdownss:function(){
			js.msg('msg','待开发');
		}
	};
	
	$('#dt1_{rand}').val(js.now('Y-m'));
	js.initbtn(c);
});
</script>
<div>
<table width="100%"><tr>
	<td nowrap>月份&nbsp;</td>
	<td nowrap>
		<div style="width:120px"  class="input-group">
			<input placeholder="月份" readonly class="form-control" id="dt1_{rand}" >
			<span class="input-group-btn">
				<button class="btn btn-default" click="clickdt,1" type="button"><i class="icon-calendar"></i></button>
			</span>
		</div>
	</td>
	<td  style="padding-left:10px">
		<select class="form-control" style="width:150px" id="plx_{rand}">
		<option value="0">根据人员来排</option>
		</select>
	</td>
	<td  style="padding-left:10px">
		<input class="form-control" style="width:150px" id="key_{rand}"   placeholder="姓名/部门">
	</td>
	<td  style="padding-left:10px">
		<button class="btn btn-default" click="search" type="button">搜索</button>
	</td>
	<td  style="padding-left:10px">
		
	</td>
	<td  style="padding-left:5px">
		
	</td>
	<td width="80%"></td>
	<td align="right" nowrap>
		<button class="btn btn-default" id="downbtn_{rand}"  type="button">选中标识为 <i class="icon-angle-down"></i></button>&nbsp;&nbsp;
		<button class="btn btn-default" click="xuanzeq" type="button">取消选择</button>&nbsp;&nbsp;
		<button class="btn btn-default" click="daochu" type="button">导出</button>
	</td>
</tr></table>
</div>
<div class="blank10"></div>
<div id="view_{rand}" style="cursor:default"></div>
<div class="tishi" id="guistr_{rand}">提示：</div>