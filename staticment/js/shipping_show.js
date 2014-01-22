
$(document).ready(function(){
    
    $("input[name='sku']").blur(function(){
        var url = 'index.php?action=shipping_show&detail=getskuinfo';
        var sku = $(this).val();
        $.post(url,{"sku":sku},function(data){
            $("input[name='volume']").val(data.product_dimensions);
            $("input[name='weight']").val(data.shipping_weight);
        },'json')
    })
    $("input[name='weight']").focus(function(){
        $(this).val('');
        $(this).attr('title','默认:kg');
        
    })
    
});