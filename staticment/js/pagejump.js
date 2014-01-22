// JavaScript Document每页显示条数跳转JS
/*下拉跳转页面*/
function jumppage(val,statu,action,extra){
	window.location='index.php?action='+action+'&detail=list&statu='+statu+'&selfval_set='+val+extra;
}