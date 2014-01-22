// JavaScript Document生成供应商下拉JS
var handiv =0;

/*点击外面，下拉框消失,需要延迟否则无法选择*/
function out(){
	setTimeout('disappear()',200);
}


/*在结果中选择*/
/*function select(obj, id, showdiv){
	if(showdiv==1){//选择
		$("#supplierselected").append('<div name="supplier_id" ><input type="hidden" name="supplier_id[]" value="'+id+'">'+obj.innerHTML.replace(/<.+?>|&nbsp;/gim,'')+'<a style="cursor:pointer;" onclick="removesupplier(this)">&times;x</a><br></div>');
		//$("#supplierselected").append(obj.innerHTML.replace(/<.+?>|&nbsp;/gim,'')+'<a>&times;'+id+'</a><br>');
		$("#supplierselected").show();
		$('input[name=supplier]').attr('value','');		
	}else{
		$("#supplier_id").append('<input type="hidden" name="supplier_id" value="'+id+'">');
		$("#supplier_id").show();
		$('input[name=supplier]').attr('value',obj.innerHTML.replace(/<.+?>|&nbsp;/gim,''));
	}
	//$('input[name=supplierselected]').attr('value',obj.innerHTML.replace(/<.+?>|&nbsp;/gim,''));
	disappear();
}*/

/*删除供应商*/
function removesupplier(obj){
		$(obj).parent().remove();
}

/*按已输入的关键字查出供应商10条*/
function getsupplier(show_hidden_supplier_id, showdiv){	
	name = $('input[name=supplier]').val();	
	//第一次输入，需生成DIV
	if(handiv == 0){
		var obj = $('#supplier');
		var top,left,name;	
		left = obj.offset().left;
		top  = obj.offset().top+25;

		
		//读取数据
		CommomAjax('post','index.php?action=warehouse&detail=getsupplier',{'name':name, 'show_hidden_supplier_id':show_hidden_supplier_id, 'showdiv':showdiv},function(msg){		
			var cdiv = "<div id='suppliers'>"+msg+"<\/div>"; //创建 div 元素		
				$("body").append(cdiv);	//把它追加到文档中
				$("#suppliers")
					.css({
						"top" :top+"px",
						"left":left+"px",
						"width":"200px",
						"height":"210px",
						"position":"absolute",
						"background-color":"#fff",
						"border":"#7f7f7f solid 1px"
					}).show("fast");//设置x坐标和y坐标，并且显示
			handiv =1;
		});
	}
	
	//非第一次敲击键盘，不用生成DIV，改变DIV内容。
	else if(handiv == 1){
		CommomAjax('post','index.php?action=warehouse&detail=getsupplier',{'name':name, 'show_hidden_supplier_id':show_hidden_supplier_id, 'showdiv':showdiv},function(msg){
			$("#suppliers").html(msg);
		});
	}
}

/*去掉供应商搜索结果DIV*/
function disappear(){
	$('#suppliers').remove();
	handiv = 0;
}

/*产品修改--批量下载*/
$(document).ready(function(){
						   
	//默认加载图片、复选框全部
	$("#cheall").attr("checked", true);
	$("input[name^='checkall']").each(function(i){	
		$(".img"+i).css("border", "1px red solid");	
		$(this).attr("checked", true);
		 
		//点击产品复选框
		$(this).click(function(){
			if($(this).attr("checked") === false){
				$("#cheall").removeAttr("checked");
				$(this).parent().prev().children().children().css("border", "");
			}else{
				$(this).parent().prev().children().children().css("border", "1px red solid");
			}
		})
	})
	
	//全选反选
	$("#cheall").click(function(){
		$("input[name^='checkall']").each(function(i){
			if($("#cheall").attr("checked") == false)	{
				$(this).removeAttr("checked");
				$(".img"+i).css("border", "");	
			}else{
				$(".img"+i).css("border", "1px red solid");	
				$(this).attr("checked", true);
			}
		})
		
	})  
	/*$("img").each(function(i){
		$(".img"+i).toggle(
			function(){
				$(this).css("border", "2px red solid");
				var _src = $(this).attr("src");
				var _li  = "<li id=img"+i+"><input type=hidden name=_img[] value="+_src+"></li>";
				$("#aryimg ul").append(_li);     
			},
			function(){
				$("#img"+i).remove();
				$(this).css("border", "");
				
		}
		);
	});*/
	
	/*批量下载图片*/
	$("#dbutton").click(function(){
		var str = '';
		$("input[name='checkall']:checked").each(function(){
			str+=$(this).val()+',';
		})
		if(str==''){alert('你没选择任何内容！');return '';}
		var sku = $("#sku").val();
		str = str.substring(0,str.length-1);
		var url= 'index.php?action=product_list&detail=downloadpic&str='+str+'&sku='+sku+'';
		self.parent.addMenutab(12444003,"下载图片"+sku,encodeURI(url));
		
	})
    
    //全选反选
	$("input[name=listingcheall]").click(function(){
        var type = $(this).attr('iid');
        var listbox = $(this).attr("checked");
		$("input[name^='"+type+"checkall']").each(function(i){
			if( listbox== false)	{  
				$(this).removeAttr("checked");
				$(".img"+type+i).css("border", "1px #828482 solid");	
			}else{
				$(".img"+type+i).css("border", "1px red solid");	
				$(this).attr("checked", true);
			}
		}) 
	})
	
})
/*listing 图片下载*/
function dbuttoncheck(type){
    var str = '';
    $("input[name='"+type+"checkall']:checked").each(function(){
        str+=$(this).val()+',';
    })
    if(str==''){alert('你没选择任何内容！');return '';}
    var sku = $("#sku").val();
    str = str.substring(0,str.length-1);
    var url= 'index.php?action=product_list&detail=downloadpic&str='+str+'&sku='+sku+'&type='+type;
    self.parent.addMenutab(12444003,"下载图片"+sku,encodeURI(url)); 
}
/*listing 图片单选*/
function checkboxlisting(type,i,obj){
    //点击产品复选框
    if(obj.attr("checked") == false){
        //obj.removeAttr("checked");
        $("input[iid^='"+type+"']").removeAttr("checked");
        $(".img"+type+i).css("border", "1px #828482 solid");	
    }else{
        //obj.attr("checked", true);
        $(".img"+type+i).css("border", "1px red solid");	
    }
}
