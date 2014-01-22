$(function() {	
	$("input[name=supplier]").autocomplete('index.php?action=product_list&detail=getsupplier',{
        max:50,
        scrollHeight:150,
        dataType: "json",
        parse: function(data) {
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: row.id,     //返回的formatted数据
                    result: row.name   //设置返回Input框给用户看到的数据
                }
            });
        },
        formatItem:function(row){return row.name}	//设置显示效果(JSON下也是匹配的效果)
	}).result(function(event, data, formatted){
		$('input[name=supplier_id]').val(data.id);
	});
	
	$("input[name=supplier]").keydown(function(event){		
		if(event.keyCode != '9' && event.keyCode != '13') {
			$('input[name=supplier_id]').val("");
		}
	});
	
	/*图片预览*/
	var x = 10;
	var y = 20;
	$("a.tooltip").mouseover(function(e){
		this.myTitle = this.title;
		this.title = "";	
		var tooltip = "<div id='tooltip'><img src='"+ this.href +"' alt='产品预览图' /><\/div>"; //创建 div 元素
		$("body").append(tooltip);	//把它追加到文档中						 
		$("#tooltip")
			.css({
				"top": (e.pageY+y) + "px",
				"left":  (e.pageX+x)  + "px"
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
	
	
    //模板导出
    $("input[name=btn_template]").click(function(){
        var pid = $(this).prev().attr("id");//产品编号
        var tid = $(this).prev().prev().attr("value");//模板编号
        window.location = 'index.php?action=product_list&detail=product_template&pid='+ pid +'&tid=' + tid;
    });
	
	//查询库存*
	$("a[name=btn_stock]").click(function(){
        var btn_check = $(this);
		var checksku = $(this).prev().attr("id");
		btn_check.hide();
        $(this).parent().html('<span id="'+ checksku +'">查询中，请稍等...</span>');
        CommomAjax('POST','index.php?action=product_list',{'detail':'check_stock','checksku':checksku},function(msg){
            $('#'+ checksku).html(msg);
		});
	});
    
    //保存标签
    $("input[name=btn_label]").click(function(){
        var pid = $(this).prev().attr("id");//产品编号
        var lid = $(this).prev().prev().attr("value");//标签编号
        if(lid == ''){
            alert("请选择标签后再保存！");
            return false;
        } else {
            CommomAjax('POST','index.php?action=product_list',{'detail':'save_label','pid':pid,'lid':lid},function(msg){
                alert(msg);
		    });
        }
    });
	
	/*失去焦点移除下拉*/	
	$('#bdiv').live('mouseover',function(){ clearTimeout(closetime);});	
	$('#bdiv').live('mouseleave',function()	{ closetime = setTimeout("disappear('#bdiv')",500);});
	$('#reset_tag').mouseout(function(){ closetime = setTimeout("disappear('#bdiv')",500);});
});

/*标签设置*/
	function reset_tag(){
		
		var butobj 	= $('#reset_tag');
		btop 		= butobj.offset().top;
		bleft		= butobj.offset().left;
		bwidth		= butobj.width();
		

		var pid = $("input:checked[name^=check]").eq(0).attr("title");
		$.getJSON('index.php?action=product_list&detail=user_change_label',{'pid':pid},function(msg){
			var divval = '';
			for(var i=0;i<msg.length;i++){
				divval += '<div onclick = check_checkbox('+msg[i].id+') class="check_box list_b" style="cursor:pointer;" >'+msg[i].label_name+'</div>';
			}
			
			var tdiv = '<div id=bdiv>'+divval+'</div>';
			//alert(tdiv);
			//把它追加到文档中
			$("body").append(tdiv);
			
			set_css($(".list_b"));
			$("#bdiv").css({
				"top"				: (btop+20) + "px",
				"left"				: (bleft)  + "px",
				"width"				: (bwidth+40) + "px",
				//"height"			: "150px",
				"position"			:'absolute',
				'background-color'	:'#ffffff',
				'font-size'			:'12px',
				'color'				:'black',
				'border'			:'#cdcdcd 1px solid',
				'line-height'		:'18px',
				'padding'			:'0px',
				'margin'			:'0px',
				'box-shadow'		:'1px 1px 2px #CCC',
				'-webkit-box-shadow':'1px 1px 2px #CCC',
				'-moz-box-shadow'	:'1px 1px 2px #CCC'
			}).show("fast");//设置x坐标和y坐标，并且显示	
		})

	}
	
	/*设置弹出下拉CSS*/
	function set_css(obj){
		obj.css({'margin':'0','padding-left':'10px','line-height':'25px','cursor':'pointer'})
			.mouseover(function(){$(this).css({'background':'#CAddfe'})})
			.mouseout(function(){$(this).css('background','#fff')});
	}
	
	/*下拉消失*/
	function disappear(obj){
		$(obj).remove();
	}

	/*取选中的checkbox*/
	function check_checkbox(label_id){
		var pid = '';
		$("input[name=checkmod[]]:checked").each(function(){
			pid += $(this).attr('value')+',';
		})
		if(pid == '')  {alert('请选择需要添标签的产品');return false;}
		$.post('index.php?action=product_list',{'detail':'save_user_change_label','pid':pid,'label_id':label_id},function(msg) {
			if(msg == 1){
                alert('保存成功');
                window.location.reload();
            }else{
                alert('保存失败');
            }
		});	
	}