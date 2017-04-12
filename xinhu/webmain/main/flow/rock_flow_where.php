<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	{params};
	var num = params.num,mid=0,optlx=0;
	var at = $('#optionview_{rand}').bootstable({
		tablename:'flow_set',defaultorder:'`sort`',where:'and status=1',
		modedir:'{mode}:{dir}',storeafteraction:'setwherelistafter',
		columns:[{
			text:'名称',dataIndex:'name'
		},{
			text:'编号',dataIndex:'num'
		},{
			text:'条件数',dataIndex:'shu'
		},{
			text:'ID',dataIndex:'id'
		}],
		itemdblclick:function(ad,oi,e){
			$('#downshow_{rand}').html('设置<b>['+ad.id+'.'+ad.name+']</b>的条件列表');
			mid=ad.id;
			get('add_{rand}').disabled=false;
			a.search("and `setid`="+ad.id+"");
		}
	});
	
	function btn(bo){
		get('edit_{rand}').disabled = bo;
		get('del_{rand}').disabled = bo;
	}
	
	var a = $('#view_{rand}').bootstable({
		tablename:'flow_where',celleditor:true,defaultorder:'setid,sort,id desc',
		columns:[{
			text:'名称',dataIndex:'name',editor:true
		},{
			text:'编号',dataIndex:'num',editor:true
		},{
			text:'人员',dataIndex:'recename'
		},{
			text:'人员除外',dataIndex:'nrecename'
		},{
			text:'说明',dataIndex:'explain',editor:true
		},{
			text:'排序号',dataIndex:'sort',editor:true
		},{
			text:'列表页显示',dataIndex:'islb',type:'checkbox',editor:true,sortable:true
		},{
			text:'状态',dataIndex:'status',type:'checkbox',editor:true,sortable:true
		},{
			text:'ID',dataIndex:'id',sortable:true
		},{
			text:'模块id',dataIndex:'setid',sortable:true
		},{
			text:'',dataIndex:'opt',renderer:function(v,d,oi){
				var s='&nbsp;';
				if(!isempt(d.num)){
					s='<a href="javascript:;" onclick="chakan{rand}('+oi+')">查看</a>';
				}
				return s;
			}
		}],
		itemclick:function(d){
			mid=d.setid;
			btn(false);
		},
		beforeload:function(){
			btn(true);
		}
	});
	var c = {
		reload:function(){
			a.reload();
		},
		del:function(){
			a.del();
		},
		clickwin:function(o1,lx){
			var icon='plus',name='新增条件',id=0;
			if(lx==1){
				id = a.changeid;
				icon='edit';
				name='编辑条件';
			};
			guanflowwherelist = a;
			addtabs({num:'flowwhere'+id+'',url:'main,flow,whereedit,id='+id+',setid='+mid+'',icons:icon,name:name});
		}
	};
	js.initbtn(c);
	$('#optionview_{rand}').css('height',''+(viewheight-62)+'px');
	
	chakan{rand}=function(oi){
		var num = at.changedata.num;
		if(!at.changedata || !num){
			js.msg('msg','请先双击左边模块');
			return;
		}
		var d = a.getData(oi);
		addtabs({num:'flowviewset'+d.id+'',url:'flow,page,'+num+',atype='+d.num+'',name:d.name});
	}
});
</script>


<table width="100%">
<tr valign="top">
<td width="30%">
	<div class="panel panel-info" style="margin:0px">
	  <div class="panel-heading">
		<h3 class="panel-title">流程模块(双击显示)</h3>
	  </div>
	  <div id="optionview_{rand}" style="height:400px;overflow:auto"></div>
	</div>
</td>
<td width="10"></td>
<td>
	<div>
	<ul class="floats">
		<li class="floats70">
			<button class="btn btn-primary" click="clickwin,0" disabled id="add_{rand}" type="button"><i class="icon-plus"></i> 新增条件</button>&nbsp;&nbsp;
			<span id="downshow_{rand}"></span>
		</li>
		<li class="floats30" style="text-align:right">
			<button class="btn btn-info" id="edit_{rand}" click="clickwin,1" disabled type="button"><i class="icon-edit"></i> 编辑 </button> &nbsp; 
			<button class="btn btn-danger" id="del_{rand}" disabled click="del" type="button"><i class="icon-trash"></i> 删除</button>
		</li>
	</ul>
	</div>
	<div class="blank10"></div>
	<div id="view_{rand}"></div>
	<div class="tishi">列表页显示：会在生成列表页面上显示的，需要设置编号</div>
</td>
</tr>
</table>
