<!--凭证录入界面头部-->
<link rel="stylesheet" type="text/css" href="./staticment/css/main.css" />
<link rel="stylesheet" type="text/css" href="./staticment/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="./staticment/css/thickbox.css" />
<script type="text/javascript" src="./staticment/js/jquery.js"></script>
<script type="text/javascript" src="./staticment/js/new.js"></script>
<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>
<script language="javascript" type="text/javascript" src="./staticment/js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
	$('input[type=image]').live('click',function(){ isloading('body',0,'保存中...');});
});

var pro_ajax_url = 'index.php?action=finance_evd&detail=get_pro';
var esse_url = 'index.php?action=finance_evd&detail=get_esse_cat';
var check_pro_url = 'index.php?action=finance_evd&detail=check_pro';
var check_esse_url = 'index.php?action=finance_evd&detail=check_esse';
var check_pro_waring = '输入科目错误，请重新输入！';
var check_esse_waring = '输入核算项目错误，请重新输入！';

/*  判定值
 *  0为初始值
 *  1为科目输入框进入时赋值为1，选择查找下拉值后赋值为2，若为1，不能直接丢失焦点
 *  2为科目输入框选择了查询结果，可以丢失焦点（暂时不用，待扩展）
 *  3为核对项目输入框获取焦点时赋值为1，选择查找下拉值后赋值为2，若为1，不能丢失焦点
 *  4为核对项目输入框选择了查询结果，可以丢失焦点（暂时不用，待扩展）
*/
var wall_select_type = 0;

/* 是否点击的当前编辑框
 * 0为初始值,非当前编辑框点击
 * 1为是点击当前科目编辑框
 * 2为点击的非当前科目编辑框（暂时不用，待扩展）
 * 3为是点击当前核对项目编辑框
 * 4为点击的非当前核对项目编辑框（暂时不用，待扩展）
*/
var isfocus = 0;

/* 防止ajax执行两次的标识量*/
var ajax_once = 0;

var test = 0;
/*保留两位小数*/
function cuttwo(obj){
	return Math.round((Math.floor(obj*1000)/10))/100;
}

//检测输入框是否正确
function check_pro_coke(nowfocus) {
    var index;
    var check_ajax_url;
    var check_ajax_waring;
    if (wall_select_type == 1) {
        index = $('input[isfocus=1]').index('.wall_select');
        check_ajax_url = check_pro_url;
        check_ajax_waring = check_pro_waring;
    }
    else if (wall_select_type == 3) {
        index = $('input[isfocus=1]').parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]');
        check_ajax_url = check_esse_url;
        check_ajax_waring = check_esse_waring;
    }    
    $.getJSON(
        check_ajax_url,
        {'pro_code':$('input[isfocus=1]').val(),'esse_id':$('.esse_id').eq(index).val()},
        function(msg){
            if (msg.id != '') {               
                $('input[isfocus=1]').val(msg.show);
                if (wall_select_type ==1) {
                    result_action(window.event,msg,msg.id);  
                }
                else if (wall_select_type == 3) {
                    ajax_esse_action(msg, index);
                }
                if (nowfocus) {
                    nowfocus.focus();
                    nowfocus.select();
                    $('.ac_results').hide();
                }                
            }
            else {
                if (ajax_once) {
                    alert(check_ajax_waring);
                    $('input[isfocus=1]').focus();
                    $('input[isfocus=1]').select();   
                }
                ajax_once = 0;
                return false;
            }
        }
    );    
}

function ajax_esse_action(data, index) {
    wall_select_type = 0;    
    isfocus = 0;
    var nowfocus = $('input[isfocus=1]');
    nowfocus.attr('isfocus',''); 
    $('.esse_cat_id').eq(index).val(data.id);
    $('.esse_cat_name').eq(index).val(data.name);
    $('.esse_cat_code').eq(index).val(data.show);
    $('.wall_select').eq(index).val($('.wall_select').eq(index).val()+'/'+data.show+'-'+data.name);
}

function result_action(event,data,formatted){
    //选择查找值后，置判定值为能直接移出焦点, 移除焦点后设置当前编辑框判定值为初始值0
    wall_select_type = 0;    
    isfocus = 0;
    var nowfocus = $('input[isfocus=1]');            
    nowfocus.prevAll('.pro_id').attr('value',formatted);
    nowfocus.prevAll('.pro_code').attr('value',data.pro_code);
    nowfocus.prevAll('.pro_name').attr('value',data.pro_name);
    nowfocus.prevAll('.esse_id').attr('value',data.esse_cat_id);
    nowfocus.prevAll('.esse_cat_type').attr('value',data.esse_cat_type);
    nowfocus.prevAll('.esse_cat_id').attr('value','');
    nowfocus.prevAll('.esse_cat_code').attr('value','');
    nowfocus.prevAll('.esse_cat_name').attr('value','');    
    //取得当前输入框的索引
    index = nowfocus.index('.wall_select');        
    //隐藏所有核算项目填写列
    $('table[id^=wall_live_table]').hide();
    //当前页面对应的核算项目填写列
    wall_live_table = $('table[id^=wall_live_table]').eq(index);
    //将当前核算项目列置空
    wall_live_table.html("");
    //如果传过来科目不包含核算项目，则核算项目列为空，否则生成当前对应核算项目列元素
    if (data.esse_cat_id != "0") {
        esse_ajax_url = esse_url + '&esse_cat_id=' + data.esse_cat_id;           
        //核算项目列的输入框元素
        esse_cat = $('<input class="esse_cat" type="text"  style="border:1px #b2b2b2 solid; width:150px; height:20px;"/>').autocomplete(esse_ajax_url,{
            max:50,
            scrollHeight:150,
            dataType: "json",
            parse: function(data) {
                return $.map(data, function(row) {
                    return {
                        data: row,
                        value: row.id,     //返回的formatted数据
                        result: row.show   //设置返回Input框给用户看到的数据
                    }
                });
            },
            formatItem:function(row){return row.show}	//设置显示效果(JSON下也是匹配的效果)
        }).result(function(event,data,formatted){    
            wall_select_type = 0;    
            isfocus = 0;
            var nowfocus = $('input[isfocus=1]');
            nowfocus.attr('isfocus',''); 
            //$(this).parent().next().html(data.name);
            $('.esse_cat_id').eq(index).val(data.id);
            $('.esse_cat_name').eq(index).val(data.name);
            $('.esse_cat_code').eq(index).val(data.show);
            var show = $('.pro_code').eq(index).val() + '-' + $('.pro_name').eq(index).val();            
            if ($('.esse_cat_name').eq(index).val() != '') {
                show += '/' + $('.esse_cat_code').eq(index).val() + '-' + $('.esse_cat_name').eq(index).val();                
            }            
            $('.wall_select').eq(index).val(show);            
        });
        wall_live_table.append(
            $('<tr></tr>').append($('<td width="20%" height="28" align="right"></td>').html(data.esse_cat_name+'：'))
                .append($('<td width="26%" align="center"></td>').append(esse_cat))
                .append($('<td width="54%" align="left"></td>'))
        ).show();
    }
    nowfocus.attr('isfocus','');
}

$(function(){   
    //
    $('.wall_table').parents('#content').mouseup(function(){        
        if (wall_select_type != 0 && isfocus ==0) {
            ajax_once = 1;
            check_pro_coke();             
        }
        else {
            $('input[isfocus=1]').attr('isfocus','');
        }
        if (isfocus != 0) {            
            isfocus = 0;
        }          
    });
    $('input').live('mousedown', function(){           
        if(wall_select_type != 0 && $(this).attr('isfocus') != '1') {
            ajax_once = 1;
            check_pro_coke($(this));
            return false;
        }       
    });    
    $('select').live('focus', function(){                
        if(wall_select_type != 0 && $(this).attr('isfocus') != '1') {
            ajax_once = 1;
            check_pro_coke($(this));       
            return false;
        }        
        index = $(this).parent().parent().index('.wall_table tr') - 1;
        $('table[id^=wall_live_table]').hide();
        $('table[id^=wall_live_table]').eq(index).show();
    });
    $('input[isfocus=1]').live('mousedown',function() {
        //当点击当前编辑框时，不提示
        isfocus = 1;        
    });
    //单击当前行控件，显示相对应的和对项目
    $('.wall_table input').live('focus', function(){   
        index = $(this).parent().parent().index('.wall_table tr') - 1;
        $('table[id^=wall_live_table]').hide();
        $('table[id^=wall_live_table]').eq(index).show();
    });
    
    
	$(".wall_select").autocomplete(pro_ajax_url,{
									max:50,
									scrollHeight:150,
									dataType: "json",
									parse: function(data) {
										return $.map(data, function(row) {
											return {
												data: row,
												value: row.id,     //返回的formatted数据
												result: row.show   //设置返回Input框给用户看到的数据
											}
										});
									},
                                    formatItem:function(row){return row.show}	//设置显示效果(JSON下也是匹配的效果)
	}).result(result_action);
	
    $(".wall_select").live('click',function(){
        $(this).attr('isfocus','1'); 
        $(this).val($(this).prevAll('.pro_code').val());
        $(this).select();
    }).live('keydown',function(e){
        if (e.keyCode != 9) {
            wall_select_type = 1;
            $(this).attr('isfocus','1'); 
            //取得当前输入框的索引
            index = $(this).index('.wall_select');
            $(this).prevAll('.pro_id').val('');
            $(this).prevAll('.pro_code').val('');
            $(this).prevAll('.pro_name').val('');
            $(this).prevAll('.esse_id').val('');
            $(this).prevAll('.esse_cat_type').val('');
            $(this).prevAll('.esse_cat_id').val('');
            $(this).prevAll('.esse_cat_name').val('');
            $(this).prevAll('.esse_cat_code').val('');
            //当前页面对应的核算项目填写列
            wall_live_table = $('table[id^=wall_live_table]').eq(index);
            //将当前核算项目列置空
            wall_live_table.html("");
        }        
    }).live('keyup', function(e){
        //如果编辑框为空时，取消验证判断
        if ($(this).val() == '') {
            wall_select_type = 0;
            $(this).attr('isfocus','');             
        }
    }).live('blur', function() {
        index = $(this).index('.wall_select');
        var iskeydown = 0;
        $('.ac_results').each(function(){
            if($(this).css('display') == 'block') {
                iskeydown = 1;
            }
        });
        if ($(this).val() == '' || ($(this).val() == $('.pro_code').eq(index).val() && iskeydown == 0)) {            
            $(this).attr('isfocus',''); 
        }        
        if ($('.pro_id').eq(index).val() != '') {
            var show = $('.pro_code').eq(index).val() + '-' + $('.pro_name').eq(index).val();
            if ($('.esse_cat_name').eq(index).val() != '') {
                show += '/' + $('.esse_cat_code').eq(index).val() + '-' + $('.esse_cat_name').eq(index).val();
            }
            $('.wall_select').eq(index).val(show);
        }   
        iskeydown = 0;
    });    
    
    $(".esse_cat").live('click',function(){
        $(this).attr('isfocus','1'); 
        index = $(this).parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]');         
        nowselect = $('.wall_select').eq(index);                
        nowselect.val(nowselect.prevAll('.pro_code').val() + '-' + nowselect.prevAll('.pro_name').val());        
    }).live('keydown',function(e){          
        index = $(this).parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]');          
        if ($(this).keyCode != 9) {
            wall_select_type = 3;
            $(this).attr('isfocus','1'); 
            //取得当前输入框的索引
            index = $(this).parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]');            
            $('.esse_cat_id').eq(index).val('');
            $('.esse_cat_name').eq(index).val(''); 
            $('.esse_cat_code').eq(index).val('');             
        }
    }).live('keyup', function(e){        
        //如果编辑框为空时，取消验证判断        
        if ($(this).val() == '') {
            wall_select_type = 0;
            $(this).attr('isfocus','');                 
        }        
        if ($(this).val() != $('.esse_cat_code').eq(index).val()) {
            //取得当前输入框的索引
            index = $(this).parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]');            
            $('.esse_cat_id').eq(index).val('');
            $('.esse_cat_name').eq(index).val(''); 
            $('.esse_cat_code').eq(index).val('');
        }
    }).live('blur', function() {
        index = $(this).parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]');
        var iskeydown = 0;
        $('.ac_results').each(function(){
            if($(this).css('display') == 'block') {
                iskeydown = 1;
            }
        });
        if ($(this).val() == '' || ($(this).val() == $('.esse_cat_code').eq(index).val() && iskeydown == 0)) {
            $(this).attr('isfocus',''); 
        }
        if ($('.pro_id').eq(index).val() != '') {
            var show = $('.pro_code').eq(index).val() + '-' + $('.pro_name').eq(index).val();            
            if ($('.esse_cat_name').eq(index).val() != '') {
                show += '/' + $('.esse_cat_code').eq(index).val() + '-' + $('.esse_cat_name').eq(index).val();                
            }            
            $('.wall_select').eq(index).val(show);
        }           
    });
    
    $('.esse_cat').each(function(){
        $(this).autocomplete(esse_url + '&esse_cat_id=' + $('.esse_id').eq($(this).parents('table[id^=wall_live_table]').index('table[id^=wall_live_table]')).val(),{
            max:50,
            scrollHeight:150,
            dataType: "json",
            parse: function(data) {
                return $.map(data, function(row) {
                    return {
                        data: row,
                        value: row.id,     //返回的formatted数据
                        result: row.show   //设置返回Input框给用户看到的数据
                    }
                });
            },
            formatItem:function(row){return row.show}	//设置显示效果(JSON下也是匹配的效果)
        }).result(function(event,data,formatted){    
            wall_select_type = 0;    
            isfocus = 0;
            var nowfocus = $('input[isfocus=1]');
            nowfocus.attr('isfocus',''); 
            //$(this).parent().next().html(data.name);
            $('.esse_cat_id').eq(index).val(data.id);
            $('.esse_cat_name').eq(index).val(data.name);
            $('.esse_cat_code').eq(index).val(data.show);
            var show = $('.pro_code').eq(index).val() + '-' + $('.pro_name').eq(index).val();            
            if ($('.esse_cat_name').eq(index).val() != '') {
                show += '/' + $('.esse_cat_code').eq(index).val() + '-' + $('.esse_cat_name').eq(index).val();                
            }            
            $('.wall_select').eq(index).val(show);            
        });
    });
    
	/*屏蔽回车提交*/
	$("form").keydown(function(e) {  
		if (e.which == 13) {  
		 	return false;  
		}  
	});

	/*借方，贷方输入框丢失焦点时：①统计总额，②正则判断格式*/
	$('input[class*=price]').live('blur',function(){
		var pricetag = /^[0-9]+([.][0-9]{1,2})?$/;
		if ($(this).val() != '' && !pricetag.exec($(this).val())) {
			alert('价格输入错误！重新填写！');
			$(this).focus();
			return false;
		}		
		total_price();
	})
    /*同一行借贷只能填一边，并支持自动赋值*/
	.live('focus', function() {
        var classname = $(this).attr('class');
        var index = $(this).index('input[class='+classname+']');     
        var s_price = $('input[name^=s_price]').eq(index);
        var coin_rate = $('input[name^=coin_rate]').eq(index);
        if(coin_rate.val() != '' && s_price.val() == '') {
            alert('选择了汇率必须填写原币！');
            s_price.focus();
            s_price.select();
            return false;//选择了汇率必须填写原币
        }     
        var other_price = $('input[class*=price]').not('input[class='+classname+']').eq(index);        
        if (coin_rate.val() != '') {
            var in_price = s_price.val()/coin_rate.val()*100;
            $(this).val(cuttwo(in_price));
        }
        else {
            if (other_price.val() != '') {
                $(this).val(other_price.val());        
            }
        }
        other_price.val('');
        total_price();
    });	
	/*同一行借贷只能填一边，并支持自动赋值
	.live('focus', function() {
		classname = $(this).attr('class');
		if (classname == 'in_price') {				
			if($(this).parents('tr').children().children('.out_price').val() != ''){
				alert('借方，贷方只能填一方！');
				$(this).attr('value','');
				$(this).parents('tr').children().children('.out_price').focus();
			}else{
				if($(this).parent().prev().prev().children().val() == '') return;//选择了汇率才进行赋值
				var in_price = $(this).parent().prev().children().val()/$(this).parent().prev().prev().children().val()*100;
				$(this).attr('value',cuttwo(in_price));
			}
			
		}else if (classname == 'out_price') {
			if($(this).parents('tr').children().children('.in_price').val() != ''){
				alert('借方，贷方只能填一方！');
				$(this).attr('value','');
				$(this).parents('tr').children().children('.in_price').focus();					
			}else{
				if($(this).parent().prev().prev().prev().children().val() == '')return;//选择了汇率才进行赋值
				var out_price = $(this).parent().prev().prev().children().val()/$(this).parent().prev().prev().prev().children().val()*100;
				$(this).attr('value',cuttwo(out_price));
			}
		}
	});*/

	/*增加行数*/
	$('.wall_add').click(function() {
		var wall_select = $('<input class="wall_select" type="text" />').autocomplete(
            pro_ajax_url,
            {
                max:50,
                scrollHeight:150,
                dataType: "json",
                parse: function(data) {
                    return $.map(data, function(row) {
                        return {
                            data: row,
                            value: row.id,
                            result: row.show
                        }
                    });
                },
                formatItem:function(row){return row.show}
            }
        ).result(result_action);			  
		$('.wall_table').append(
			$('<tr></tr>').append($('<td height="30"><input class="f_desc" name="comment[]" type="text" /></td>'))
					.append($('<td></td>').append('<input type="hidden"  name="pro_id[]" class="pro_id"/><input type="hidden"  name="pro_code[]" class="pro_code"/><input type="hidden"  name="pro_name[]" class="pro_name"/><input type="hidden"  name="esse_id[]" class="esse_id" /><input type="hidden"  name="esse_cat_type[]" class="esse_cat_type"/><input type="hidden"  name="esse_cat_id[]" class="esse_cat_id" /><input type="hidden"  name="esse_cat_name[]" class="esse_cat_name" /><input type="hidden" class="esse_cat_code" />').append(wall_select))
					.append('<td><!--{echo $coin_codehtml}--></td>')
					.append('<td><input class="exshow"  type="text" disabled="disabled" /><input class="exshow"  type="hidden" name="coin_rate[]" /></td>')
					.append('<td><input class="exshow"  type="text" name="s_price[]" /></td>')						
					.append($('<td><input class="in_price" type="text" name="in_price[]" /></td>'))
					.append($('<td><input class="out_price" type="text" name="out_price[]" /></td>'))
		);
        $('.bhz').append(
            $('<table id="wall_live_table[]" width="40%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px"></table>')
        );
	});
		 
	/*提交时判断借贷相等*/
	$('input[type=image]').click(function() {
		if($('.total_in_price').html() != $('.total_out_price').html()) {
			alert('借、贷方总额不相等！');
			return false;
		}
        var count = $('.wall_select').size();
        for (i = 0; i < count; i++) {
            if ($('.esse_cat_type').eq(i).val() != '' && $('.esse_cat_id').eq(i).val() == '') {
                alert('有核算项目未填写！请检查！');
                return false;
            }
        }
	});
});
	
/*获取所选币别的汇率，更换汇率时清空已填有的值*/
function getrate(obj){
	CommomAjax('post','index.php?action=exchange_rate&detail=getrate',{'code':$(obj).val(),'type':'rate_cny'},function(msg){
		var donext = $(obj).parent().next();																									 
		donext.children().attr('value',msg);
		if($(obj).val()=='') {donext.next().children().attr('value','');}//如果取消币别，同时清空原币金额
		donext.next().next().children().attr('value','');//清空借方内容
		donext.next().next().next().children().attr('value','');//清空贷方内容
	});
}
/*统计借方以及贷方的总额*/
function total_price() {
    totalinprice = 0;
	$('.in_price').each(function() {
		if ($(this).val() != '') {
			totalinprice += cuttwo(parseFloat($(this).val()));
		}
	});
	$('.total_in_price').html(cuttwo(totalinprice));
    totaloutprice = 0;
	$('.out_price').each(function() {
		if ($(this).val() != '') {
			totaloutprice += cuttwo(parseFloat($(this).val()));
		}
	});
	$('.total_out_price').html(cuttwo(totaloutprice));
}
</script>