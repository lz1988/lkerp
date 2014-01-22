// JavaScript Document
function getparse(){

	isloading('body',0,'');
    var sku = $('input[name=sku]').val();

	$.getJSON('index.php?action=shipping_count',{'detail':'getparse','sku':sku},
		function(msg){
			closeloading();
			$('input[name=product_dimensions]').attr('value',msg.product_dimensions);
			$('input[name=box_product_dimensions]').attr('value',msg.box_product_dimensions);
			$('input[name=shipping_weight]').attr('value',msg.shipping_weight);
			$('input[name=box_shipping_weight]').attr('value',msg.box_shipping_weight);
			$('input[name=unit_box]').attr('value',msg.unit_box);
		}
	);
}

function countmod(){
	var mydetail={
		'product_dimensions':$('input[name=product_dimensions]').val(),
		'box_product_dimensions':$('input[name=box_product_dimensions]').val(),
		'shipping_weight':$('input[name=shipping_weight]').val(),
		'box_shipping_weight':$('input[name=box_shipping_weight]').val(),
		'country':$('select[name=country]').val(),
		'quantity':$('input[name=quantity]').val(),
		'type':$('input[name=type]:checked').val(),
		'currency':$('select[name=currency]').val(),
		'stockware':$('select[name=stockware]').val()
		};

	CommomAjaxNew('POST','index.php?action=shipping_count',{'detail':'list','mydetail':mydetail},isloading('body',0,''),function(msg){closeloading();$("#downdiv").html(msg);})

	return false;

}

