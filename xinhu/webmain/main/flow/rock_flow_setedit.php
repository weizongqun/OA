<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	{params};
	var id = params.id,arrlist;
	if(!id)id = 0;
	var h = $.bootsform({
		window:false,rand:'{rand}',tablename:'flow_set',
		url:publicsave('{mode}','{dir}'),url:publicsave('{mode}','{dir}'),
		params:{otherfields:'optdt={now}'},aftersaveaction:'flowsetsaveafter',beforesaveaction:'flowsetsavebefore',
		submitfields:'name,tables,type,num,table,sort,isscl,status,where,summary,summarx,pctx,mctx,wxtx,emtx,isflow,sericnum,receid,recename,names,statusstr,isgbjl,isgbcy',
		requiredfields:'name,type,num,table',
		success:function(){
			closenowtabs();
			try{guanflowsetlist.reload();}catch(e){}
		}
	});
	h.forminit();
	h.load(js.getajaxurl('loaddata','{mode}','{dir}',{id:id}));
	var c = {
		getdist:function(o1, lx){
			var cans = {
				nameobj:h.form.recename,
				idobj:h.form.receid,
				type:'deptusercheck',
				title:'选择针对人员'
			};
			js.getuser(cans);
		},
		allqt:function(){
			h.form.recename.value='全体人员';
			h.form.receid.value='all';
		},
		setstatus:function(){
			var val = h.form.statusstr.value;
			var sha = [],vala;
			if(val)sha = val.split(',');
			var str = '<table width="100%"><tr><td align="center"  height="30" nowrap>状态值</td><td>状态名</td><td>状态颜色</td></tr>';
			for(var i=0;i<=9;i++){
				var na='',col='',naa;
				if(sha[i]){
					naa = sha[i].split('|');
					na  = naa[0];if(naa[1])col=naa[1];
				}
				str+='<tr><td width="20%" align="center">'+i+'</td><td width="40%"><input maxlength="10" value="'+na+'" id="abc_xtname'+i+'" style="color:'+col+'" class="form-control"></td><td width="40%"><input class="form-control" maxlength="7" style="color:'+col+'" value="'+col+'"  id="abc_xtcol'+i+'"></td></tr>';
			}
			str+='</table>';
			
			js.tanbody('sttts','设置状态值',400,300,{
				html:'<div style="height:400px;overflow:auto;padding:5px">'+str+'</div>',
				btn:[{text:'确定'}]
			});
			$('#sttts_btn0').click(function(){
				c.setstatusok();
			});
		},
		setstatusok:function(){
			var str = '';
			for(var i=0;i<=9;i++){
				var na=get('abc_xtname'+i+'').value,col=get('abc_xtcol'+i+'').value;
				if(na&&i==5)na='已作废';
				if(na&&i==1&&(na!='已完成'||na!='已通过'||na!='已审核'))na='已完成';
				if(!na)break;
				str+=','+na+'';
				if(col)str+='|'+col+'';
			}
			if(str!='')str=str.substr(1);
			h.form.statusstr.value=str;
			js.tanclose('sttts');
		}
	};
	js.initbtn(c);
});

</script>

<div align="center">
<div  style="padding:10px;width:700px">
	
	
	<form name="form_{rand}">
	
		<input name="id" value="0" type="hidden" />
		
		<table cellspacing="0" border="0" width="100%" align="center" cellpadding="0">
		<tr>
			<td  align="right" ><font color=red>*</font> 模块名称：</td>
			<td class="tdinput"><input name="name" class="form-control"></td>
			<td  align="right"  ><font color=red>*</font> 类型：</td>
			<td class="tdinput"><input name="type" class="form-control"></td>
		</tr>
		
		<tr>
			<td  align="right" width="15%" nowrap ><font color=red>*</font> 编号：</td>
			<td width="35%"  class="tdinput"><input name="num" maxlength="20" class="form-control"></td>
			
			<td  width="15%" align="right" nowrap><font color=red>*</font> 对应表：</td>
			<td width="35%"  class="tdinput"><input name="table" maxlength="50" class="form-control"></td>
		</tr>
		
		<tr>
			<td align="right">单号规则：</td>
			<td class="tdinput"><input placeholder="如XA-Ymd-" name="sericnum" class="form-control"></td>
			<td align="right">多行子表：</td>
			<td class="tdinput"><input name="tables" placeholder="多个,分开" class="form-control"></td>
		</tr>
		<tr>
			<td align="right">排序号：</td>
			<td class="tdinput"><input name="sort" value="0" maxlength="3" type="number"  onfocus="js.focusval=this.value" onblur="js.number(this)" class="form-control"></td>
			<td align="right">多行子表名称：</td>
			<td class="tdinput"><input name="names"  placeholder="跟多行子表个数一样" class="form-control"></td>
		</tr>
		<tr>
			<td  align="right" >针对人员：</td>
			<td class="tdinput" colspan="3">
				<div class="input-group" style="width:100%">
					<input readonly class="form-control"  name="recename" >
					<input type="hidden" name="receid" >
					<span class="input-group-btn">
						<button class="btn btn-default" click="allqt" type="button">全体人员</button>
						<button class="btn btn-default" click="getdist,1" type="button"><i class="icon-search"></i></button>
					</span>
				</div>
			</td>
			
		</tr>
		
	
		
		<tr>
			<td  align="right" >相应条件：</td>
			<td class="tdinput" colspan="3"><textarea  name="where" style="height:60px" class="form-control"></textarea></td>
		</tr>
		
		<tr>
			<td  align="right" >摘要规则：</td>
			<td class="tdinput" colspan="3"><textarea  name="summary" style="height:60px" class="form-control"></textarea></td>
		</tr>
		
		<tr>
			<td  align="right" >应用上摘要显示：</td>
			<td class="tdinput" colspan="3"><textarea  name="summarx"
placeholder="title:{title}
optdt:{optdt}
cont:
" 
			style="height:100px" class="form-control"></textarea>
			<font color=#888888>title:标题，optdt:显示的时间，cont:内容信息</font>
			</td>
		</tr>
		
		<tr>
			<td  align="right" ></td>
			<td class="tdinput" colspan="3">
				<label><input name="isflow" value="1" type="checkbox"> 有流程?</label>&nbsp; &nbsp; 
				<label><input name="pctx" value="1" type="checkbox"> PC端提醒</label>&nbsp; &nbsp; 
				<label><input name="emtx" value="1" type="checkbox"> 邮件提醒</label>&nbsp; &nbsp; 
				<label><input name="mctx" value="1" type="checkbox"> APP提醒</label>&nbsp; &nbsp; 
				<label><input name="wxtx" value="1" type="checkbox"> 微信提醒</label>&nbsp; &nbsp; 
				<label><input name="status" value="1" checked type="checkbox"> 启用</label><br>
				<font color=#888888>微信提醒需要有微信企业号或企业微信，否则将崩毁。</font>
			</td>
		</tr>
		
		
		<tr>
			<td  colspan="4"><div class="inputtitle">更多扩展选项</div></td>
		</tr>
		<tr>
			<td  align="right" >status字段状态值设置：</td>
			<td class="tdinput" colspan="3"><input name="statusstr" class="form-control"><a href="javascript:;" click="setstatus">[设置]</a><font color=#888888>默认状态值是：【待处理|blue,已审核|green,未通过|red】对应值从0开始，其中0,1,2,5固定的5是作废,1必须是已完成,已审核状态</font></td>
		</tr>
		
		<tr>
			<td  align="right" >单据详情上：</td>
			<td class="tdinput" colspan="3">
				<label><input name="isgbjl" value="1" type="checkbox"> 不显示操作记录</label>&nbsp; &nbsp; 
				<label><input name="isgbcy" value="1" type="checkbox"> 不显示查阅记录</label>&nbsp; 
				<label><input name="isscl" value="1" type="checkbox"> 标识已生成列表页</label>
			</td>
		</tr>
		

		
		<tr>
			<td  align="right"></td>
			<td style="padding:15px 0px" colspan="3" align="left"><button disabled class="btn btn-success" id="save_{rand}" type="button"><i class="icon-save"></i>&nbsp;保存</button>&nbsp; <span id="msgview_{rand}"></span>&nbsp;<a href="http://xh829.com/view_flowset.html" target="_blank">[看帮助]</a>查看各个字段说明
		</td>
		</tr>
		
		</table>
		</form>
	
</div>
</div>
