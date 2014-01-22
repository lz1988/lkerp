<style type="text/css">
.normal
{
      list-style: none; float: left;
      padding: 5px 2px 2px 2px;
      width: 90px; text-align :center ;
      vertical-align :middle ;
      cursor :pointer ;

      border-bottom :solid 1px #9cd9f5;
      border-right :solid 1px #9cd9f5;

      background-color: #dee7f5;
      border-collapse :separate ;
}

.selected
{
     list-style: none;
     float: left;
     padding: 5px 2px 2px 2px;
     width: 90px;
     text-align :center ;
     vertical-align :middle ;
     cursor :pointer ;

     border-bottom :solid 0px #9cd9f5;
     border-right :solid 1px #9cd9f5;
     background-color:#f8f8f8 ;
}

#divMainTab
{
     border-left :solid 1px #9cd9f5;
     border-top :solid 1px #9cd9f5;
     float:left;
     margin: 0px; padding: 0px
}

.divContent
{
        clear: both;
        border-left: solid 1px #9cd9f5;

}

a
{
    text-decoration: none;
    color: #00ccff;
}

a:hover
{
    text-decoration: underline;
    color: #cc0000;
}
</style>

<script language="javascript" type="text/javascript">
    function changeTab(index)
    {
        for (var i=1;i<=3;i++)
        {
            document.getElementById ("li_"+i).className ="normal";
            document.getElementById ("li_"+index).className ="selected";

            document.getElementById ("div"+i).style.display  ="none";
        } 
        document.getElementById ("div"+index).style.display  ="block";
    }
</script>

<div>
    <div id="aaaaa"></div>
    <div id="divMainTab">
        <ul style="list-style: none; margin: 0px; padding: 0px; border-collapse: collapse;">
            <li id="li_1" class="normal" onmouseover="changeTab('1')"><a href="#">用户描述</a> </li>
            <li id="li_2" class="selected" onmouseover="changeTab('2')"><a href="#">官方描述</a> </li>
            <li id="li_3" class="normal" onmouseover="changeTab('3')"><a href="#">英文描述</a> </li>
        </ul>
    </div>
    <div id="div1" style ="display :none" class ="divContent">
        <textarea id="product_desc2" name="product_desc2"><!--{echo stripslashes($product['product_desc2']); }--></textarea>
    </div>
    <div id="div2" style ="display :block" class ="divContent">
        <textarea id="product_desc" name="product_desc" style="width:650px;height:400px;visibility:hidden;"><!--{echo stripslashes($product['product_desc']); }--></textarea>                    
    </div>
    <div id="div3" style ="display :none" class ="divContent">
        <textarea id="eng_product_desc3" name="eng_product_desc3"><!--{echo stripslashes($product['product_desc3']); }--></textarea>
    </div>
</div>