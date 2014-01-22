<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-05-29 09:40:10, compiled from template/adminweb/showpic.tpl */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="./staticment/css/jquery.ad-gallery.css">
  <script src="formValidator4.0.1/jquery-1.4.4.js" type="text/javascript"></script>
  <script type="text/javascript" src="./staticment/js/jquery.ad-gallery.js"></script>
  <script type="text/javascript">
  $(function() {
  var num = $("#num").val(); 
  var galleries = $('.ad-gallery').adGallery({
  	 start_at_index:num //逗号要去掉，否则会报错
  }); 
    $('#switch-effect').change(
      function() {
        galleries[0].settings.effect = $(this).val();
        return false;
      }
    );
    $('#toggle-slideshow').click(
      function() {
        galleries[0].slideshow.toggle();
        return false;
      }
    );
    $('#toggle-description').click(
      function() {
        if(!galleries[0].settings.description_wrapper) {
          galleries[0].settings.description_wrapper = $('#descriptions');
        } else {
          galleries[0].settings.description_wrapper = false;
        }
        return false;
      }
    );
  });
  </script>

   <style type="text/css">
* {
	font-family: Arial,Helvetica,sans-serif;
	color: #333;
	line-height: 140%;
}
select, input, textarea {
	font-size: 1em;
}
body {
	padding: 0;
	font-size: 70%;
	margin: 0 auto;
}
#container{
 margin: 0 auto;
 width:700px;
}
h2 {
	margin-top: 1.2em;
	margin-bottom: 0;
	padding: 0;
	border-bottom: 1px dotted #dedede;
}
h3 {
	margin-top: 1.2em;
	margin-bottom: 0;
	padding: 0;
}
.example {
	border: 1px solid #CCC;
	background: #f2f2f2;
	padding: 10px;
}
ul {
	list-style-image:url(./staticment/images/list-style.gif);
}
pre {
	font-family: Arial,Helvetica,sans-serif;
	border: 1px solid #CCC;
	background: #f2f2f2;
	padding: 10px;
}
code {
	font-family: Arial,Helvetica,sans-serif;
	margin: 0;
	padding: 0;
}

#gallery {
	padding: 30px;
	background: #e1eef5;
}
#descriptions {
	position: relative;
	height: 50px;
	background: #EEE;
	margin-top: 10px;
	width: 640px;
	padding: 10px;
	overflow: hidden;
}
#descriptions .ad-image-description {
	position: absolute;
}
#descriptions .ad-image-description .ad-description-title {
	display: block;
}
  </style>
<title>查看图片</title>
</head>
<body>
  <div id="container">
   
    <div id="gallery" class="ad-gallery">
      <div class="ad-image-wrapper">
      </div>
      <!--<div class="ad-controls">
      </div>-->
      <div class="ad-nav">
        <div class="ad-thumbs">
          <ul class="ad-thumb-list">
            
                <?php foreach ($arypic as $k=>$v) { ?>
                <li>
                 <a href="<?php echo $v ?>">
                <img src="<?php echo $v ?>" width="90px" height="60px" class="image<?php echo $k;?>">
                </a>
                </li>
                <?php } ?>
  
          </ul>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" id="num" value="<?php echo $num ?>" />
</body>
</html>