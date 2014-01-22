/* JavaScript Document -- 个人销售历史JS */


var closetime = '';
var disaptime = 600;
var line	  = 0;
var detail_id = 0;
var order_id  = '';
var x = 10;
var y = 20;

function setline(row){
	line = row;
}

$(document).ready(function(){						   

	
	/*显示标记详情*/
	$("a.movetips").live('mouseover',function(e){
													  
		detail_id	= this.id;
		line  		= this.name;
		
		if($('#tooltip')) {disappear('#tooltip');}//星标来回移动时，更时切换内容		
		if(closetime) {clearTimeout(closetime);}//保证移开又移回去时提醒框不消失
		
		/*取得标记内容*/
		$.getJSON('index.php?action=process_mysold&detail=show',{'os_id':detail_id},function(msg){
		
			//创建 div 元素
			var tooltip = "<div id='tooltip'><h2>标记人 "+msg.os_cuser+'<span>标记时间 '+msg.os_cdate+"</span></h2><p id=d_p>"+msg.os_desc+"</p><p><button id=cancel>取消标记</button></p><\/div>";		
		

			//把它追加到文档中
			$("body").append(tooltip);	
			$("#tooltip")
				.css({
					"top"				: (e.pageY-150) + "px",
					"left"				: (e.pageX+x)  + "px",
					"width"				: "300px",
					"height"			: "150px",
					"position"			:'absolute',
					'background-color'	:'#ffffff',
					'font-size'			:'12px',
					'color'				:'black',
					'border'			:'#cdcdcd 1px solid',
					'line-height'		:'18px',
					'padding'			:'0px 10px 10px 10px',
					'margin'			:'0px',
					'box-shadow'		:'3px 3px 5px #CCC',
					'-webkit-box-shadow':'3px 3px 5px #CCC',
					'-moz-box-shadow'	:'3px 3px 5px #CCC'
				})
				.show("fast");	  //设置x坐标和y坐标，并且显示
		})	
    })
	
	$("a.movetips").live('mouseout',function(){
		closetime = setTimeout("disappear('#tooltip')",disaptime);
    })
	
	$("a.movetips").live('mousemove',function(e){
		$("#tooltip")
			.css({
				"top"	: (e.pageY-150) + "px",
				"left"	: (e.pageX+x) + "px"
			});
	});
	
	
	/*如果鼠标移到DIV内，不消失*/
	$('#tooltip').live('mouseover',function(){
		clearTimeout(closetime);
	}).live('mouseout',function(){
		closetime = setTimeout("disappear('#tooltip')",disaptime);
	});
	
	
	/*设置标记窗口*/
	$("a.settips").live('click',function(e){

		var settips = "<div id='settips'><h2 style='color:#bdbdbd'>填写备注 ("+this.name+")<span style='cursor:pointer' title='关闭' onclick='disappear(\"#settips\")'><img src='./staticment/images/iconClose.gif' border='0'></span></h2><p><textarea cols=39 rows=5 id=os_desc></textarea><input type=hidden id=detail_id value="+this.id+"></p><p><span style='float:right'><button id='conset'>确定</button></span></p><\/div>"; //创建 div 元素
		$("body").append(settips);	//把它追加到文档中
		$("#settips")
			.css({
				"top"				: (e.pageY-150) + "px",
				"left"				: (e.pageX+x)  + "px",
				"width"				: "300px",
				"height"			: "150px",
				"position"			:'absolute',
				'background-color'	:'#ffffff',
				'font-size'			:'12px',
				'color'				:'black',
				'border'			:'#cdcdcd 1px solid',
				'line-height'		:'18px',
				'padding'			:'0px 10px 10px 10px',
				'margin'			:'0px',
				'box-shadow'		:'3px 3px 5px #CCC',
				'-webkit-box-shadow':'3px 3px 5px #CCC',
				'-moz-box-shadow'	:'3px 3px 5px #CCC'
		}).show("fast");	  //设置x坐标和y坐标，并且显示
		
	})
	
	/*提交备注*/
	$('#conset').live('click',function(){

		var os_desc = $('#os_desc').val();
		var os_id	= $('#detail_id').val();

		$.getJSON('index.php?action=process_mysold&detail=setsign',{'os_desc':os_desc,'os_id':os_id},function(msg){
				
				/*不刷新改变为星标状态*/
				var starstr = '<a  href="javascript:void(0)" class="movetips" name="'+line+'" id="'+os_id+'" ><img src="./staticment/images/star_t.gif" border="0"></a>';
				$('#stars_'+line).html(starstr);
				
				/*自动关闭窗口*/
				$('#settips').html('已标记，窗口将在1秒后关闭！').css({
					'height'		: '95px',
					'padding-top'	: '55px',
					'text-align'	: 'center'
				});
				setTimeout("disappear('#settips')",1000);

		});
	});
	
	/*取消标记*/
	$('#cancel').live('click',function(){
		
		var p = confirm('确定取消星标？');
		if(!p) return;
		
		CommomAjax('POST','index.php?action=process_mysold&detail=clesign',{'os_id':detail_id},function(msg){
			
			if(msg == '0'){
			 	alert('取消失败，请重试！')	;
			}else{
				//改变图标和链接内容																							
				var starstr = '<a href="javascript:void(0);setline('+line+')" class="settips" title="标为星标" name="'+msg+'" id='+detail_id+'><img src="./staticment/images/star_o.gif" border="0"></a>';
				$('#stars_'+line).html(starstr);
			
				//窗口消失
				disappear('#tooltip');
			}																										

		})
	});
	

});


/*提示框消失*/
function disappear(obj){
	$(obj).remove();
}
