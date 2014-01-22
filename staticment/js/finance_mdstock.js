/********财务模块JS*********/

/*合并付款*/
function combine_morder(){
	var confmsg,url,strID='';
	confmsg = '确定合并?';	
	if(confmsg){
		var fg=window.confirm(confmsg);
		if(!fg){
			return;
		}
	}
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	CommomAjax('post','index.php?action=finance_mdstock',{'detail':'combine_needed','order_id':strID},function(msg){
		if(msg == '1'){alert('操作成功!');window.location.reload();}else{alert(msg);}
	});
}


/*取得订单ID，过滤重复*/
function get_orderid(){
	
	var obj=document.getElementsByName('checkmod[]');
	var strarray = new Array();
	var countstr = 0,strID='';
	

	/*取得需要操作的采购订单号,过滤重复的订单号*/
	for(var i=0;i<obj.length;i++){
		
		if(obj[i].checked){			
			strarray[countstr] = obj[i].value;
			countstr++;
		}	

	}
	if(!strarray.length){
		return;
	}

	strID +="'"+strarray[0]+"',";
	for(var j=1;j<strarray.length;j++){
		if(strarray[j] != strarray[j-1]){strID +="'"+strarray[j]+"',";}
	}	
	/*END*/
	
	strID = strID.substr(0,strID.length-1);
	return strID;
}

var x,y
function canMove()
{
	x=document.body.scrollLeft+event.clientX
	y=document.body.scrollTop+event.clientY
}
function move()
{
	tips.style.posLeft=x+10;
	tips.style.posTop=y+10;
	setTimeout("move()",10);
}
function showTips(content,obj)
{
	obj.innerText=content+"\t\t\t\t\t\t";
	obj.filters.alpha.opacity=100;
}
function hideTips(content,obj)
{
	obj.innerText=content;
	obj.filters.alpha.opacity=0;
}
document.write("<div id=tips style='filter:alpha(opacity=0);position:absolute;background-color:#f3f3f3;font-size:9pt;color:#6c6c6c;border:#d9d9c6 1px solid;'></div>");

$(document).ready(function(){
	
	/*图片预览*/
	var x = 10;
	var y = 20;
	$("a.movetips").mouseover(function(e){
		this.myTitle = this.title;
		this.title = "";
		var tooltip = "<div id='tooltip'>&nbsp;"+this.myTitle+"&nbsp;<\/div>"; //创建 div 元素
		$("body").append(tooltip);	//把它追加到文档中						 
		$("#tooltip").css({
				'top': (e.pageY+y) + "px",
				'left':  (e.pageX+x)  + "px",
				'background-color':'#f3f3f3',
				'font-size':'9pt',
				'color':'#6c6c6c',
				'border':'#d9d9c6 1px solid',
				'position':'absolute',
				'line-height':'20px'
			}).show("fast");	  //设置x坐标和y坐标，并且显示
    }).mouseout(function(){
		this.title = this.myTitle;	
		$("#tooltip").remove();	 //移除 
    }).mousemove(function(e){
		$("#tooltip")
			.css({
				"top": (e.pageY+y) + "px",
				"left":  (e.pageX+x)  + "px"
			});
	});
	/*End*/
})


/*下拉跳转页面*/
function jumppage(val,statu,action){
	window.location='index.php?action='+action+'&detail=list&moment='+statu+'&selfval_set='+val;
}