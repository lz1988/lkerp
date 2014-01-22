/*表格宽度可拖动JS--Start*/
var currentResizeTdObj=null;
function MouseDownToResize(event,obj){
obj=obj||this;
event=event||window.event;
currentResizeTdObj=obj;
obj.mouseDownX=event.clientX;
obj.mouseDownY=event.clientY;
obj.tdW=obj.offsetWidth;
obj.tdH=obj.offsetHeight;
if(obj.setCapture) obj.setCapture();
else event.preventDefault();
}
function MouseMoveToResize(event){
if(!currentResizeTdObj) return ;
var obj=currentResizeTdObj;
event=event||window.event;
    if(!obj.mouseDownX) return false;
    if(obj.parentNode.rowIndex==0) {
      var newWidth=obj.tdW*1+event.clientX*1-obj.mouseDownX;
      if(newWidth>0) obj.style.width = newWidth;
    else obj.style.width =1;
}
if(obj.cellIndex==0){
      var newHeight=obj.tdH*1+event.clientY*1-obj.mouseDownY;
      if(newHeight>0) obj.style.height = newHeight;
    else obj.style.height =1;
}
}
function MouseUpToResize(){
if(!currentResizeTdObj) return;
if (currentResizeTdObj.releaseCapture) currentResizeTdObj.releaseCapture();
currentResizeTdObj=null;
}
//改变表格行列宽函数
function ResizeTable_Init(table,needChangeWidth,needChangeHeight)
{
if(!needChangeWidth && !needChangeHeight)
   return;
var oTh=table.rows[0];
if(needChangeWidth){
    for(var i=0;i<oTh.cells.length;i++)   {
       var cell=oTh.cells[i];
       cell.style.cursor="e-resize";
       cell.style.width=cell.offsetWidth;
       cell.onmousedown =MouseDownToResize;
    }
}
if(needChangeHeight){
    for(var j=0;j<table.rows.length;j++)   {
       var cell=table.rows[j].cells[0];
       cell.style.cursor="s-resize";
       cell.onmousedown =MouseDownToResize;
    }
}
if(needChangeWidth && needChangeHeight)
   oTh.cells[0].style.cursor="se-resize";
table.style.width=null;
table.style.tableLayout="fixed";
}
//函数块定义结束
/*表格可拖动JS--End*/



$(function(){

	/*固定表头*/	   
	var navH = $(".clearfix").offset().top;	
	$(window).scroll(function(){	
		var scroH = $(this).scrollTop();
		if(scroH>=navH){		
			$(".clearfix").css('top',scroH).addClass('floatfix');;
		}else if(scroH<navH){ 
			$(".clearfix").removeClass('floatfix');
		}
	})
	
	$('#subre').click(function(){
		isloading('body',0,'正在加载...','#C1DAD7');
	});
	
	/*行变换*/
	$("#mytable tr:odd").addClass("oddtrbg");
	$("#mytable tr:even").addClass("eventrbg");
	$("#mytable tr").mouseover(function(){$(this).addClass('hover');})
	$("#mytable tr").mouseout(function(){$(this).removeClass('hover');})


	$('#mytable tr').click(function() {

			//判断当前是否选中
			var hasSelected=$(this).hasClass('selected');
			//如果选中，则移出selected类，否则就加上selected类
			$(this)[hasSelected?"removeClass":"addClass"]('selected')
				//查找内部的checkbox,设置对应的属性。
				.find('[name=checkmod[]]:checkbox').attr('checked',!hasSelected);
			});
	// 如果复选框默认情况下是选择的，则高色.
	$('#mytable tr:has(:checked)').addClass('selected');




	<!--{if ($showcheckbox==1)}-->
	//复选框反选，用于批量删除或审核
	 $("#CheckedAll").click(function(){
			//所有checkbox跟着全选的checkbox走。
			if(this.checked == false){
				$('[name=checkmod[]]:checkbox').attr("checked", this.checked ).parent().parent().removeClass('selected');
			}else{
				$('[name=checkmod[]]:checkbox').attr("checked", this.checked ).parent().parent().addClass('selected');
			}
	 });
	<!--{/if}-->
})

<!--{if ($delajax)}-->
/*AJAX删除 */
function delitem(url){
	var msg	= arguments[1] ? arguments[1] : '确定执行此操作?';
	var p 	= window.confirm(msg);
	if(!p)return;
	CommomAjax('POST',url,'',function(msg){
			if(msg == 1) {msg = '操作成功';}
			alert(msg);
			if(msg=='删除成功' || msg == '操作成功'){window.location.reload();}
	});
}
<!--{/if}-->
