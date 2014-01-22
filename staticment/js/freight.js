//地址与国家映射

var getarea=function(nid,inputname){
    var url = 'index.php?action=shipping_countnew&detail=muitilevel_province';
        if (nid > 0){
            getstring1(url,nid,inputname);
        }else{
            $("input[name='"+inputname+"']").val('');
            $("#province_"+inputname+" select").html('<option value="-2">请选择</option>');
            $("#city_"+inputname+" select").html('<option value="-2">请选择</option>');
        }
}

var getprovince = function(pid,inputname){
    var url = 'index.php?action=shipping_countnew&detail=muitilevel_city';
        if (pid > 0){
            getstring2(url,pid,inputname);
        }else{
            var nationid = $("select[name='nation_"+inputname+"']").val();
            $("input[name='"+inputname+"']").val(nationid);
            $("#city_"+inputname+" select").html('<option value="-2">请选择</option>');
        }
}

var getcity = function(cid,inputname){
        if (cid > 0){
            $("input[name='"+inputname+"']").val(cid);
        }else{
            $("input[name='"+inputname+"']").val('');
        }
}

var getstring1 = function(url,nid,inputname){
    $.post(url,{"id":nid,"inputname":inputname},function(data){
        $("#province_"+inputname+" select").empty();
        $("#city_"+inputname+" select").html('<option value="-2">请选择</option>');
        $("#province_"+inputname+"").html(data);
        $("input[name='"+inputname+"']").val(nid);
    })
}

var getstring2 = function(url,id,inputname){
    $.post(url,{"id":id,"inputname":inputname},function(data){
        $("#city_"+inputname+" select").empty();
        $("#city_"+inputname).html(data);
        $("input[name='"+inputname+"']").val(id);
        
    })
}
