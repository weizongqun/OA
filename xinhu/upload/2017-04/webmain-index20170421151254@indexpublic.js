﻿var objcont,tabs_title,tabsarr={},nowtabs,opentabs=[],menutabs,menuarr;
var viewwidth,viewheight,optmenudatas=[];
function publicstore(mo,dos,oans){
	if(!mo)mo='index';
	if(!dos)dos='';
	return js.getajaxurl('publicstore',mo,dos,oans);
}
function publicsave(mo, dos,oans){
	if(!mo)mo='index';
	if(!dos)dos='';
	return js.getajaxurl('publicsave',mo,dos,oans);
}

function editfacechang(xid,nems){
	js.upload('_editfacechangback',{maxup:'1',thumbnail:'150x150','title':'修改['+nems+']的头像',uptype:'image','params1':xid});	
}
function _editfacechangback(a,xid){
	var f = a[0];
	var nf= f.thumbpath+'?'+Math.random()+'';
	if(xid==adminid)get('myface').src=nf;
	if(get('faceviewabc_'+xid+''))get('faceviewabc_'+xid+'').src=nf;
	js.msg('wait','头像修改中...');
	js.ajax(js.getajaxurl('editface','admin','system'),{fid:f.id,'uid':xid},function(){
		js.msg('success','修改成功,如没显示最新头像，请清除浏览器缓存');
	});
}
function _addbodykey(){
	$('body').keydown(function(e){
		var code	= e.keyCode;
		if(code==27){
			if(get('xpbg_bodydds')){
				js.tanclose($('#xpbg_bodydds').attr('xpbody'));
			}else{
				closenowtabs();
			}
			return false;
		}
		//弹出帮助
		if(code==113){
			js.confirm('是否打开查看关于['+nowtabs.name+']的帮助信息？',function(jg){
				if(jg=='yes')window.open('http://xxxxxxxx.com/view_'+nowtabs.num+'.html?title='+jm.base64encode(nowtabs.name)+'');
			});
			return false;
		}
	});
}


function openinput(name,num, id,cbal){
	if(!id)id=0;
	if(!cbal)cbal='';
	if(id==0){name='[新增]'+name+'';}else{name='[编辑]'+name+'';}
	var url='?a=lu&m=input&d=flow&num='+num+'&mid='+id+'';
	openxiangs(name, url,'', cbal);
	return false;
}
function openxiangs(name,num,id,cbal){
	if(!id)id=0;
	if(!cbal)cbal='';
	var url = 'task.php?a=p&num='+num+'&mid='+id+'';
	if(num.indexOf('?')>-1){url=num+'&callback='+cbal+'';}else{url+='&callback='+cbal+'';}
	js.winiframe(name,url);
	return false;
}
function openxiang(num,id,cbal){
	var url = 'task.php?a=p&num='+num+'&mid='+id+'';
	if(cbal)url+='&callback='+cbal+'';
	js.open(url, 800,500);
}

function optmenuclass(o1,num,id,obj,mname,oi, cola){
	this.modenum = num;
	this.modename= mname;
	this.id 	 = id;
	this.mid 	 = id;
	this.tableobj=obj;
	this.oi 	= oi;
	this.obj 	= o1;
	this.columns= cola;
	var me 		= this;
	this._init=function(){
		if(typeof(optmenuobj)=='object')optmenuobj.remove();
		this.callbackstr = '';
		if(this.columns.callback)this.callbackstr=this.columns.callback;
		optmenuobj=$.rockmenu({
			data:[],
			itemsclick:function(d){me.showmenuclick(d);},
			width:150
		});
		var da = [{name:'详情',lx:998,nbo:false},{name:'详情(新窗口)',lx:998,nbo:true}];
		var off=$(this.obj).offset();
		var subdata = optmenudatas[''+this.modenum+'_'+this.id+''];
		if(!subdata){
			da.push({name:'<img src="images/loadings.gif" align="absmiddle"> 加载菜单中...',lx:999});
			this.loadoptnum();
		}else{
			for(i=0;i<subdata.length;i++)da.push(subdata[i]);
		}
		optmenuobj.setData(da);
		optmenuobj.showAt(off.left,off.top+20);
	};
	this.xiang=function(oi,nbo){
		var mnem=this.modename;
		if(!nbo){
			if(!mnem)mnem='详情';
			openxiangs(mnem,this.modenum,this.mid, this.callbackstr);
		}else{
			openxiang(this.modenum,this.mid, this.callbackstr);
		}
	};
	this.openedit=function(){
		openinput(this.modename,this.modenum,this.mid);
	};
	this.showmenuclick=function(d){
		d.num=this.modenum;d.mid=this.id;
		d.modenum = this.modenum;
		var lx = d.lx;if(!lx)lx=0;
		if(lx==999)return;
		if(lx==998){this.xiang(d.oi, d.nbo);return;}
		if(lx==997){this.printexcel(d.oi);return;}
		if(lx==996){this.xiang(d.oi, d.nbo);return;}
		if(lx==11){this.openedit();return;}
		this.changdatsss = d;
		if(lx==2 || lx==3){
			var clx='user';if(lx==3)clx='usercheck';
			js.getuser({type:clx,title:d.name,callback:function(na,nid){me.changeuser(na,nid);}});
			return;
		}
		var nwsh = 'showfielsv_'+js.getrand()+'';
		var uostr= '<div align="left" style="padding:10px"><div id="'+nwsh+'" style="height:60px;overflow:auto" class="input"></div><input style="width:180px" id="'+nwsh+'_input" type="file"></div>';
		var bts = (d.issm==1)?'必填':'选填';
		if(lx==1 || lx==9 || lx==10){
			if(lx==9)uostr='';
			js.prompt(d.name,'请输入['+d.name+']说明('+bts+')：',function(index, text){
				if(index=='yes'){
					if(!text && d.issm==1){
						js.msg('msg','没有输入['+d.name+']说明');
					}else{
						me.okchangevalue(d, text);
					}
					return true;
				}
			},'','', uostr);
			this._uosschange(nwsh);
			return;
		}
		if(lx==4){
			js.prompt(d.name, '说明('+bts+')：', function(index, text){
				if(index=='yes'){
					var ad=js.getformdata('myformsbc');
					for(var i in ad)d['fields_'+i+'']=ad[i];
					me.okchangevalue(d, text);
					return true;
				}
			},'','<div align="left" id="showmenusss" style="padding:10px">加载中...</div>', uostr);
			var url='index.php?a=lus&m=input&d=flow&num='+d.modenum+'&menuid='+d.optmenuid+'&mid='+d.mid+'';
			$.get(url, function(s){
				var s='<form name="myformsbc">'+s+'</form>';
				$('#showmenusss').html(s);
				js.tanoffset('confirm');
			});
			this._uosschange(nwsh);
			return;
		}
		this.showmenuclicks(d,'');
	};
	this._uosschange=function(nwsh){
		this.fupobj = $.rockupload({
			autoup:false,
			fileview:nwsh,
			allsuccess:function(a,sid){
				me.upsuccessla(sid);
			}
		});
		$('#'+nwsh+'_input').change(function(){
			me.fupobj.change(this);
		});
	};
	this.upsuccessla=function(sid){
		var d = this.changdatsss;
		d.logfileid = sid;
		this.showmenuclicks(d, this.inputexplain);
		js.tanclose('confirm');
	};
	this.okchangevalue=function(d,text){
		this.changdatsss	= d;
		this.inputexplain 	= text;
		this.fupobj.start();
	};
	this.changeuser=function(nas,sid){
		if(!sid)return;
		var d = this.changdatsss,sm='';
		d.changename 	= nas; 
		d.changenameid  = sid; 
		this.showmenuclicks(d,sm);
	};
	this.showmenuclicks=function(d,sm){
		if(!sm)sm='';
		d.sm = sm;
		for(var i in d)if(d[i]==null)d[i]='';
		js.msg('wait','处理中...');
		js.ajax(js.getajaxurl('yyoptmenu','flowopt','flow'),d,function(ret){
			if(ret.code==200){
				optmenudatas[''+d.modenum+'_'+d.mid+'']=false;
				me.tableobj.reload();
				js.msg('success','处理成功');
			}else{
				js.msg('msg',ret.msg);
			}
		},'post,json');	
	};
	this.loadoptnum=function(){
		js.ajax(js.getajaxurl('getoptnum','flowopt','flow'),{num:this.modenum,mid:this.id},function(ret){
			if(ret.code == 200){
				optmenudatas[''+me.modenum+'_'+me.id+''] = ret.data;
				me._init();
			}else{
				js.msg('msg',ret.msg);
			}
		},'get,json');
	};
	this._init();
}
js.getuser = function(cans){
	var can = js.apply({title:'读取人员',idobj:false,nameobj:false,value:'',type:'deptusercheck',callback:function(){}}, cans);
	can.onselect=can.callback;
	js.changeuser(false, can.type, can.title, can);
}

/**
*	高级搜索使用
*/
var highdata={};
function highsearchclass(options){
	var me 		= this;
	var cans 	= js.apply({'oncallback':function(){},'modenum':''}, options);
	for(var a in cans)this[a]=cans[a];
	this.init 	= function(){
		if(!this.modenum)return;
		js.tanbody('searchhigh','高级搜索', 450,300,{
			html:'<div id="searchhighhtml" style="height:200px;overflow:auto;"></div>',
			btn:[{text:'搜索'}],
			msg:'<a id="searchhigh_cz" href="javascript:;">[重置]</a> &nbsp; '
		});
		$('#searchhigh_btn0').click(function(){
			me.queding();
		});
		$('#searchhigh_cz').click(function(){
			me.chongzhi();
		});
		this.initfields();
	};
	this.initfields=function(){
		$('#searchhighhtml').html('<div align="center" style="padding:10px">'+js.getmsg('加载中...')+'</div>');
		var fieldsat = this.getinitdata('fields');
		if(!fieldsat){
			var url = js.getajaxurl('getcolumns','mode_'+this.modenum+'|input','flow');
			js.ajax(url,{modenum:this.modenum},function(ret){
				me.searchhighshow(ret);
			},'get,json');
		}else{
			this.searchhighshow(fieldsat);
		}
	},
	this.getinitdata=function(lx){
		var d = highdata[this.modenum];
		if(!d)return false;
		return d[lx];
	};
	this.setinitdata=function(lx, da){
		if(!highdata[this.modenum])highdata[this.modenum]={};
		highdata[this.modenum][lx]=da;
	};
	this.searchhighshow=function(d){
		this.setinitdata('fields',d);
		var s = '<form name="highform"><table width="100%">',i,len=d.length,b;
		for(i=0;i<len;i++){
			b = d[i];
			s+='<tr>';
			s+='<td width="80" align="right"><font color="#555555">'+b.name+'</font></td>';
			s+='<td style="padding:5px">'+this.searchhighshowinput(b)+'</td>';
			s+='</tr>';
		}
		s+='</table></form>';
		$('#searchhighhtml').html(s);
		var obj	= document['highform'],i,data=this.getinitdata('data'),na;
		if(!data)data={};
		for(i=0;i<obj.length;i++){
			$(obj[i]).blur(function(){
				me.saveformdata();
			}).keyup(function(e){
				if(e.keyCode==13)me.queding();
			});
			na = obj[i].name;
			if(data[na])obj[i].value=data[na];
		}
	};
	this.chongzhi=function(){
		document['highform'].reset();
		this.saveformdata();
	};
	this.searchhighshowinput=function(b){
		var type = b.fieldstype,name = 'soufields_'+b.fields+'';
		var s = '<input placeholder="关键词包含" type="text" class="inputs" name="'+name+'">';
		if(type=='date' || type=='datetime' || type=='month'){
			s='<input style="width:150px" onclick="js.datechange(this,\'date\')" class="inputs datesss" readonly  name="'+name+'_start"> 至 <input onclick="js.datechange(this,\'date\')" style="width:150px" class="inputs datesss" readonly name="'+name+'_end"> ';
		}
		if(type=='month'){
			s='<input style="width:150px" onclick="js.datechange(this,\''+type+'\')" class="inputs datesss" readonly name="'+name+'">';
		}
		if(type=='number'){
			s='<input style="width:150px" type="number" onfocus="js.focusval=this.value" maxlength="10" onblur="js.number(this)" class="inputs" name="'+name+'_start"> 至 <input style="width:150px" type="number" onfocus="js.focusval=this.value" maxlength="10" onblur="js.number(this)" class="inputs" name="'+name+'_end"> ';
		}
		if(type=='select' || type=='rockcombo'){
			var i = 0,len=b.store.length;
			s='<select name="'+name+'" class="inputs">';
			s+='<option value="">-选择-</option>';
			for(i=0;i<len;i++){
				s+='<option value="'+b.store[i].value+'">'+b.store[i].name+'</option>';
			}
			s+='</select>';
		}
		return s;
	};
	this.queding=function(){
		var d = this.saveformdata();
		this.oncallback(d);
		js.tanclose('searchhigh');
	};
	this.saveformdata=function(){
		var d = js.getformdata('highform');
		this.setinitdata('data',d);
		return d;
	};
	this.init();
}