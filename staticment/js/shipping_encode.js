$(function(){
    
    $("input[type='submit']").click(function(){
        var nation      = $("select[name='nation_f']").val();
        var code        = $("input[name='code']").val();
        if (nation < 0){
            alert("请选择国家");
            return false;
        }
        
        if (code == ''){
            alert("请填写编码");
            return false;
        }
       
        var patrn = /^[a-zA-Z]+/; 
        if (!patrn.exec(code)){
            alert("代码错误");
            return false;
        }   
    })
})