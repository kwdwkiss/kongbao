<?php
header("Content-type:text/html;charset=gb2312");
error_reporting(0);
require_once 'config.php';
$user_id = $_SESSION['userid'];
$pay_money = $_POST['payAmount'];
$pay = $_POST['optEmail'];
$pay_name = $_POST['optName'];
$order_no = date('ymdHis') . rand(10, 99);
$subject = $order_no;
$type = 1;
$addtime = date('Y-m-d H:i:s', time());
$comm = "alipay";
$insert = "INSERT INTO pay_order (user_id,order_no,pay_money,type,addtime,comm) VALUES ('$user_id','$order_no','$pay_money','$type','$addtime','$comm')";
$xr = mysql_query($insert);

$page = <<<EOT
<html xmlns="http://www.w3.org/1999/xhtml"><head><!--[if gte IE 9]>--><script charset="utf-8" id="allmobilizeE">!function(e,i){function t(){var e=/webkit|(firefox)[\/\s](\d+)|(opera)[\s\S]*version[\/\s](\d+)|(msie)[\s](\d+)/i.exec(navigator.userAgent);return e?e[1]&&+e[2]<4?!1:e[3]&&+e[4]<11?!1:e[5]&&+e[6]<10?!1:-1!=i.cookie.indexOf(l)?!1:!0:!1}function a(){var e={id:"allmobilize",charset:"utf-8","data-mode":"cdn",src:"http://b.yunshipei.com/8761644ea28a6dbc71d0c73141a46c3b/allmobilize.min.js"},t=i.createElement("script");for(var a in e)e.hasOwnProperty(a)&&t.setAttribute(a,e[a]);var o=i.getElementById("allmobilizeE");o.parentNode.insertBefore(t,o.nextSibling)}function o(){var e=navigator.userAgent||"";if(e.match(/MSIE\s([\d.]+)/)||e.match(/Trident\/[\d](?=[^\?]+).*rv:([0-9.].)/)||e.match(/Firefox\/([\d.]+)/)){var i='javascript:window.location="'+location.href+'"';location.href=i}else location.reload()}var l="_yspdisable=1";if(t()){e._allmobilizeReload=function(){var e=new Date;e.setTime(e.getTime()+864e5),i.cookie=l+";path=/;expire="+e.toGMTString(),o()};var n='<plaintext id="allmobilizeH" style="display:none">';i.write(n),a(),setTimeout(function(){e.AMPlatform||_allmobilizeReload()},6e3)}}(window,document);</script><meta http-equiv="Cache-Control" content="no-transform "><link rel="alternate" media="handheld" href="#"><!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312">
    <title>在线充值</title>
    <link href="public.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        #wforder{width:800px}
        #wforder form,.wforderl,.wftitle{border-color:#52CC52;}

body {
font-size: 13px;
font-family: "微软雅黑",Arial, Helvetica, sans-serif;
color: #454545;
}
        -->
    </style>


</head>
<body youdao="bind">

<div id="wforder">
<form action="pay.php" method="get">
        <div class="wftitle"><img src="http://pay.336hyw.com/ceshi/images/tita.gif"></div>
        <div class="bdbox">
            <ul>
                <li>
                    <span class="bdl"></span>
                    <span class="bdr">
在线全自动购买教程：
<br>1.点击转帐或者手动打开转帐页面:<a target="_blank" href="https://shenghuo.alipay.com/send/payment/fill.htm">https://shenghuo.alipay.com/send/payment/fill.htm</a>
<br>2.请按下图输入收款人，金额，付款说明。
			  <p style="color: #000000;"></p>
			  <p style="color: #F00;">收款人：<span style="color: #E30074;">$pay</span>($pay_name)</p>
			  <p style="color: #000000;">付款金额：<span style="color: #E30074;">$pay_money</span></p>
			  <p style="color: #F00; font-weight: bold;">付款说明：<span style="color: #E30074;">$order_no</span>（必写 不写不会到帐）</p>
			  <p style="color: #F00; font-weight: bold;">请将以上三个参数复制到下图所示位置，完成付款后实时到账。</p>
<br>
</span>
                </li>
                <li><img src="pay.png">
                </li>
            </ul>
        </div>
    </form>
</div>

</body>
</html>
EOT;

$nickName=$pay_name;
$payAmount=$pay_money;
$memo="请勿更改";
$url="https://shenghuo.alipay.com/send/payment/taobaoSellerFill.htm";
//$url=urlencode($url);
$postPage=<<<EOT
<form action="$url" method="post">
<input type="submit" value="提交">
</form>
<script type="text/javascript">

</script>
EOT;

$javascript = <<<EOT
<script type="text/javascript">
var nickName='久爱空';
var payAmount=$pay_money;
var title=$order_no;
var memo='亲，欢迎充值，以上付款说明请勿更改，否则实现不了自动到账。';
var memo='请勿更改';
var url = "https://auth.alipay.com/login/index.htm?goto=https://shenghuo.alipay.com/send/payment/taobaoSellerFill.htm?nickName="+ nickName +"%26payAmount="+ payAmount +"%26title=" + title +"%26memo=" + memo;
var url = "http://shenghuo.alipay.com/send/payment/taobaoSellerFill.htm?nickName=%BE%C3%B0%AE%BF%D5&payAmount=10&memo=%d2%d4%c9%cf%d0%c5%cf%a2%c7%eb%ce%f0%d0%de%b8%c4&title=15121011354594";
window.location=url;
</script>
EOT;

if ($xr) {
    echo $javascript;
    exit();
    echo '<form name="myform" action="https://shenghuo.alipay.com/send/payment/fill.htm" method="post">';
    echo '<input name="optEmail" id="optEmail"  value="' . $pay . '"  type="hidden">';
    echo '<input name="payAmount" id="payAmount" value="' . $pay_money . '" type="hidden">';
    echo '<input name="title" id="title" value="' . $subject . '" type="hidden" />';
    echo '<input type="hidden" name="memo" value="亲，欢迎充值，以上付款说明请勿更改，否则实现不了自动到账。" />';
    echo '<script type="text/javascript">document.myform.submit();</script>';
} else {
    echo "<script type='text/javascript'>alert('ERROR!');window.location.href='/';</script>";
}
?>