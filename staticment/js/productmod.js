// JavaScript Document
/*取得键盘ctrl键生成X符号*/
function getkey(obj){
	
	/*改为按星号键*/
	if(window.event.keyCode==106){
		var keyval = $('input[name='+obj+']').val();
		keyval = keyval.replace('*','');
		$('input[name='+obj+']').attr('value',keyval+'x');
	}

}
	
/*COST2是COST1的1.05*/
function docost(){
		var cost1 = $('input[name=cost1]').val();
		var cost2 = cost1*1.05;
		$('input[name=cost2]').attr('value',cost2);
}


/*检查长宽高格式*/
function check_desc(t){
	
	var tmsg = '长宽高格式不对，正确格如：15x10x5.6';//正小数

	var patrn=  /^[\d]+(\.[\d]+)?x[\d]+(\.[\d]+)?x[\d]+(\.[\d]+)?$/;//
	if (!patrn.exec(t)) {alert(tmsg);return 'wrong';}
}

/*加载编辑器之类与显示提交*/
$(function(){
	$('#savenew').data('sku', '0');
	$('#savenew').data('product_name', '0');
	$('#savenew').click(function(){				
		if(formedit.sku.value == ''){
			alert('sku不能为空!');
			formedit.sku.focus();
			return false;
		}
		
		if (formedit.product_name.value == '') {		
			alert('产品名称不能为空!');
			formedit.product_name.focus();
			return false;
		}
		
		/*倘若有供应商权限，需要录入供应商*/
		/*if($('input[name=supplier]').length>0){
		
			if(!$('input[name^=supplier_id]').first().val()){
				alert('请至少关联一个供应商!');			
				$('input[name=supplier]').focus();
				return false;			
			}
		}*/
		
		if (formedit.upc_or_ean.value == '') {		
			alert('MOQ不能为空!');
			formedit.upc_or_ean.focus();
			return false;
		}
		
		
		if (formedit.cost1.value == '') {		
			alert('原始成本不能为空!');
			formedit.cost1.focus();
			return false;
		}
		/*
		if (formedit.cost2.value == '') {		
			alert('销售成本不能为空!');
			formedit.cost2.focus();
			return false;
		}
		
		*/
        if (formedit.cost3.value == ''){
            alert('市场指导价不可以为空！');
            formedit.cost3.focus();
            return false;
        }
        
		if (formedit.cost3.value <= 0) {		
			alert('市场指导价必须为数字!');
			formedit.cost3.focus();
			return false;
		}
		
		if ($('#show').text() == '') {
			alert('产品图片不能为空!');
			return false;
		}			
		
		if (editor1.text() == '') {		
			alert('官方描述不能为空!');
			editor1.focus();
			return false;
		}	
         
		
		if (formedit.product_dimensions.value == '') {		
			alert('长×宽×高不能为空!');
			formedit.product_dimensions.focus();
			return false;
		}	
		
		if (formedit.shipping_weight.value == '') {		
			alert('发货重量不能为空!');
			formedit.shipping_weight.focus();
			return false;
		}
		
		if (formedit.color.value == '') {		
			alert('颜色不能为空!');
			formedit.color.focus();
			return false;
		}
					
		
		/*检测长宽高格式*/
		if(formedit.product_dimensions.value){
			if(check_desc(formedit.product_dimensions.value) == 'wrong') {
				formedit.product_dimensions.focus();
				return false;
			}
		}
		
		/*检测一箱的长宽高格式*/
		if(formedit.box_product_dimensions.value){
			if(check_desc(formedit.box_product_dimensions.value) == 'wrong') {
				formedit.box_product_dimensions.focus();
				return false;
			}
		}
		
		/*检测重量*/
		if(formedit.shipping_weight.value){
			if(isNaN(formedit.shipping_weight.value)) {	alert('产品重量填写错误，请填写数字！'); formedit.shipping_weight.focus(); return false; }
		}
		
		/*检测重量(一箱)*/
		if(formedit.box_shipping_weight.value){
			if(isNaN(formedit.box_shipping_weight.value)) {	alert('产品重量(箱)填写错误，请填写数字！'); formedit.box_shipping_weight.focus(); return false; }
		}
		
		if ($('#savenew').data('product_name') == '0') {
			alert('产品名称已经存在！'); 
			formedit.product_name.focus(); 
			return false;
		}
		else if ($('#savenew').data('product_name') == '0') {
			alert('产品名称检测中！ 请稍等....');  
			return false;
		}
		
		if ($('#savenew').data('sku') == '2') {
			alert('产品sku已经存在！'); 
			formedit.sku.focus(); 
			return false;
		}	
		else if ($('#savenew').data('sku') == '0') {
			alert('产品sku检测中！ 请稍等....');  
			return false;
		}	
	});
	
	$('.checkproduct_name').focus(function() {
		$('#product_nameTip').html('');
		$('#savenew').data('product_name', '0');
	}).blur(function() {
		if (formedit.product_name.value == '') {
			$('#product_nameTip').html('<font color="red">产品名称不能为空！</font>');
		}
		else {
			$.post('index.php?action=product_new&detail=checkname',{'product_name':$(this).val()},function(msg){
				if(msg == '1') { 
					$('#product_nameTip').html('<font color="red">产品名称已经存在！</font>');
					$('#savenew').data('product_name', '2');
				}
				else {
					$('#product_nameTip').html('<font color="green">产品名称可用！</font>');
					$('#savenew').data('product_name', '1');
				}
			});
		}
	});
	
	$('.checksku').focus(function() {
		$('#skuTip').html('');
		$('#savenew').data('sku', '0');
	}).blur(function() {
		if (formedit.sku.value == '') {
			$('#skuTip').html('<font color="red">产品sku不能为空！</font>');
		}
		else {
			$.post('index.php?action=product_new&detail=checksku',{'sku':$(this).val()},function(msg){
				if(msg == '1') { 
					$('#skuTip').html('<font color="red">sku已经存在！</font>');
					$('#savenew').data('sku', '2');
				}
				else {
					$('#skuTip').html('<font color="green">sku可用</font>！');
					$('#savenew').data('sku', '1');
				}
			});
		}
	});
	
	/*供应商选择*/
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
		$("#supplierselected").append('<li style="list-style: circle"><input type="hidden" name="supplier_id[]" value="'+data.id+'">'+data.name+'<a style="cursor:pointer;" onclick="removesupplier(this)" title="移除">&times;</a></li>');
		$('input[name=supplier]').attr('value','');		
	});	
})

KindEditor.ready(function(K) {
			editor1 = K.create('textarea[name="product_desc"]', {
				cssPath : 'editor/plugins/code/prettify.css',
				uploadJson : 'editor/php/upload_json.php',
				fileManagerJson : 'editor/php/file_manager_json.php',
				allowFileManager : true,
				afterCreate : function() {
					var self = this;
					K.ctrl(document, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
					K.ctrl(self.edit.doc, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
				}
			});
            editor2 = K.create('textarea[name="product_desc2"]', {
				cssPath : 'editor/plugins/code/prettify.css',
				uploadJson : 'editor/php/upload_json.php',
				fileManagerJson : 'editor/php/file_manager_json.php',
				allowFileManager : true,
				afterCreate : function() {
					var self = this;
					K.ctrl(document, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
					K.ctrl(self.edit.doc, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
				}
			});
            editor3 = K.create('textarea[name="eng_product_desc3"]', {
				cssPath : 'editor/plugins/code/prettify.css',
				uploadJson : 'editor/php/upload_json.php',
				fileManagerJson : 'editor/php/file_manager_json.php',
				allowFileManager : true,
				afterCreate : function() {
					var self = this;
					K.ctrl(document, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
					K.ctrl(self.edit.doc, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					}); 
				}
                
			});
			prettyPrint();
});
  
function checkform(){

	if(formedit.sku.value == '' && formedit.product_name.value == ''){
		alert('sku与产品名称不能同时为空!');
		formedit.sku.focus();
		return false;
	}
	
		/*倘若有供应商权限，需要录入供应商*/
	/*if($('input[name=supplier]').length>0){	
	
		if(!$('input[name^=supplier_id]').first().val()){
			alert('请至少关联一个供应商!');			
			$('input[name=supplier]').focus();
			return false;			
		}
	}	*/
	
	if (formedit.upc_or_ean.value == '') {		
		alert('MOQ不能为空!');
		formedit.upc_or_ean.focus();
		return false;
	}
	
    if (formedit.cost3.value == ''){
        alert('市场指导价不可以为空！');
        formedit.cost3.focus();
        return false;
    }else if (formedit.cost3.value <= 0) {		
		alert('市场指导价必须为数字!');
		formedit.cost3.focus();
		return false;
	}	
	
	//海关编码
	var codereg = /^\d{10}$/;
	var error = 0;
	$("input[name^='key_product_features']").each(function(i){
		var keycode = $(this).val();
		if (keycode && !codereg.test(keycode)){
			error++;
		}
	})
	if (error>0){
			alert('海关编码必须为10位数字!')
			return false;
	}
	
	
	if (editor1.text() == '') {		
		alert('官方描述不能为空!');
		editor1.focus();
		return false;
	}	

	
	if (formedit.product_dimensions.value == '') {		
		alert('长×宽×高不能为空!');
		formedit.product_dimensions.focus();
		return false;
	}	
	
	if (formedit.shipping_weight.value == '') {		
		alert('发货重量不能为空!');
		formedit.shipping_weight.focus();
		return false;
	}
	
	if (formedit.color.value == '') {		
		alert('颜色不能为空!');
		formedit.color.focus();
		return false;
	}	
	
	
	
	/*检测长宽高格式*/
	if(formedit.product_dimensions.value){
		if(check_desc(formedit.product_dimensions.value) == 'wrong') {
			formedit.product_dimensions.focus();
			return false;
		}
	}
				
	/*检测一箱的长宽高格式*/
	if(formedit.box_product_dimensions.value){
		if(check_desc(formedit.box_product_dimensions.value) == 'wrong') {
			formedit.box_product_dimensions.focus();
			return false;
		}
	}
				
	/*检测重量*/
	if(formedit.shipping_weight.value){
		if(isNaN(formedit.shipping_weight.value)) {	alert('产品重量填写错误，请填写数字！'); formedit.shipping_weight.focus(); return false; }
	}
				
	/*检测重量(一箱)*/
	if(formedit.box_shipping_weight.value){
		if(isNaN(formedit.box_shipping_weight.value)) {	alert('产品重量(箱)填写错误，请填写数字！'); formedit.box_shipping_weight.focus(); return false; }
	}				
}

/*更改价格*/
function changeprice(tobe){
	var source 	= $('input[id=coin_code]');
	var costp 	= $('input[name=cost1]');
	var cost2 	= $('input[name=cost2]');
	var cost3 	= $('input[name=cost3]');
	var costpre = $('input[name=costpre]');
	
	$.getJSON('index.php?action=product_new',{'detail':'changeprice','costp':costp.val(),'cost2':cost2.val(),'cost3':cost3.val(),'costpre':costpre.val(),'source':source.val(),'tobe':tobe},function(msg){

		costp.attr('value',cuttwo(msg.costp));
		cost2.attr('value',cuttwo(msg.cost2));
		cost3.attr('value',cuttwo(msg.cost3));
		costpre.attr('value',cuttwo(msg.costpre));
		source.attr('value',tobe);
		})
}


/*保留两位小数*/
function cuttwo(obj){
		return Math.round((Math.floor(obj*1000)/10))/100;
}

/*单位转换*/
function changeinch(obj){
	var newstr = '';
	for(i = 0; i < obj.length; i++){
		newstr += cuttwo(obj[i]/2.54);
		if(i < obj.length-1) newstr+='x';
	}
	return newstr;
}

/*根据选择的下拉显示质检图片*/
function showcheckpic(value){
	var imgsrc = value?'<a title="点击查看原图" target="_blank" href="./data/images/qualitycheck/'+value+'"><img src="./data/images/qualitycheck/'+value+'"   style="border:1px solid #ececec" width="117"/></a>':'';
	$('#qualitycheck').html(imgsrc);
}


$(function(){
    
    //类似qq相册查看图片
	$("a.tooltip_d").click(function(){
		var str = '';
		$("input[name='checkall']:checked").each(function(){
			str+=$(this).val()+',';
		})
		if(str == ''){alert('你没选择任何内容！');return '';}
		var sku = $("#sku").val();
		var pic = $(this).children().attr('src');
		if(pic == ''){alert('当前图片不存在！');return '';}
		str = str.substring(0,str.length-1);
		var url= 'index.php?action=product_list&detail=showpiclist&str='+str+'&sku='+sku+'&pic='+pic;
		self.parent.addMenutab(124440043,"查看图片"+sku,encodeURI(url));
	}); 
	
	$('#ch_inch,#ch_oz,#ch_lbs').mouseenter(function(){
		var thisid= $(this).attr('id'); 		
		var btop = $(this).offset().top;
		var bleft= $(this).offset().left;
		var value;
		
		/*有填写长宽高，则进行转换英寸*/
		if(thisid == 'ch_inch'){
		 	value = formedit.product_dimensions.value;
			if(value){
				var str = new Array(),inch;
				str 	= value.split('x');
				inch	= changeinch(str);
				$('#tipcont').html('英寸：' + inch);
				$('#tipsdiv').css({'top':btop-30+'px','left':bleft-35+'px'}).show();
			}		
		}

		/*转换重量*/
		else if(thisid == 'ch_oz' || thisid == 'ch_lbs'){
			value = formedit.shipping_weight.value;
			if(value){
				if(thisid == 'ch_oz'){$('#tipcont').html(cuttwo(value*35.27396) + ' 盎司');}else if(thisid == 'ch_lbs'){$('#tipcont').html(cuttwo(value*2.2046) + ' 磅');}
				$('#tipsdiv').css({'top':btop-30+'px','left':bleft-35+'px'}).show();
			}
		}
		
		
	}).mouseout(function(){
		$('#tipsdiv').hide();
	});
});
/*listing  图片预览*/
function listingimgshow(type,i){
		var str = '';
		$("input[name='"+type+"checkall']:checked").each(function(){
			str+=$(this).val()+',';
		})
		if(str == ''){alert('你没选择任何内容！');return '';}
		var sku = $("#sku").val();
		var pic = $("img.img"+type+i).attr('src');
		if(pic == ''){alert('当前图片不存在！');return '';}
		str = str.substring(0,str.length-1);
		var url= 'index.php?action=product_list&detail=showpiclist&str='+str+'&sku='+sku+'&pic='+pic;
		self.parent.addMenutab(124440043,"查看图片"+sku,encodeURI(url));
}