<?php
error_reporting(0);
require_once 'config.php';

session_start();
header("Content-type:text/html;charset=gb2312");

$memo="久爱空";  //请设置您支付宝所绑定的淘宝账号
$user=$_SESSION['userid'];

$pay_money = $_POST['payAmount'];
$order_no = date('ymdHis') . rand(10, 99);
$type = 1;
$addtime = date('Y-m-d H:i:s', time());
$comm = "alipay";
$insert = "INSERT INTO pay_order (user_id,order_no,pay_money,type,addtime,comm) VALUES ('$user','$order_no','$pay_money','$type','$addtime','$comm')";
$xr = mysql_query($insert);
if(!$xr)
	exit('错误');

$money=htmlspecialchars($_POST['payAmount']);
$pay=$_POST['optEmail'];
$url="http://pay.336hyw.com/api.php?optEmail=".$pay."&title=".$order_no."&memo=".$memo."&payAmount=".$money;
$urlurl="http://pay.336hyw.com/apiapi.php?title=".$user;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>在线支付</title>
	<style type="text/css">
<!--
* { margin:0; padding:0; font-size:14px; font-weight:normal; font-style: normal;}
.jj  { font-weight:bolder!important; }
.box { font-weight:bolder!important; }
.pr  { font-weight:bolder!important; }
*.box { border-top-color:#c00!important; }
*.pr { color:#060!important; }
#content{margin:0 auto;width:960px;padding: 5px;border: 5px solid;border-color: #66C2FF;margin-top:10px;} 
#tab01 {position:relative; width:900px; height:450px; padding-top:15px; margin:5px;margin-left:65px; overflow:hidden; }
#tab01 h3 {font-size:18px;position:relative; z-index:2; float:left; height:23px; padding:4px 7px 0 8px; margin-left:-1px; border-left:solid 1px #ccc; border-right:solid 1px #fff; text-align:center; background:#fff; cursor:pointer; }
#tab01 h3.up { height:31px; padding:7px 7px 0 7px; margin:-6px 0 0 0; border:solid #ccc; border-width:1px 1px 0; color:#c00; }
#tab01 i {font-family: "微软雅黑",Arial, Helvetica, sans-serif;display:none; position:absolute; left:0; top:47px; z-index:1; width:824px; height:454px; padding:5px; border:solid 1px #ccc; color:#666; }
#tab01 i.up { display:block; }
ul, li {list-style: none;display: block;}
  
-->
</style>
</head>
<body>




<div id="content">
<div id="tab01">



		
	<h3 class="box">支付宝扫码付款</h3>
		<i>
		
		<ul style="float:left;width:400px;margin-left:10px;font-size:14px;margin-top:20px;">
					<li><span class="bdr" style="color: #060;">扫码后请填写金额和备注，不输入将不能自动完成充值</span></li>
					<li><br></li>
					<li><span class="bdl">金额&nbsp;&nbsp;</span><span style="color: #E30074;"><em><?php echo $money;?></em></span></li>
					<li><span class="bdl">备注&nbsp;&nbsp;</span><span style="color: #E30074;"><em><?php echo $order_no;?></em></span><br><br></li>
					<li><span class="bdr"><img src="code.png"  width="150" height="150"/></span></li>
					<li><span class="bdr" style="color: #060;">扫码后请填写备注（<span style="color: #E30074;"><em><?php echo $order_no;?></em></span>），不输入将不能自动完成充值</span></li>
	    </ul>		
		<ul style="float:right;width:400px;margin-left:10px;font-size:14px;margin-top:20px;">			
			        <li>
					<div style="text-align:center; height:30px; line-height:30px; font-size:14px;color:#F00;">扫码付款示意图</div >
                    <div style="background:url(http://pay.336hyw.com/ceshi/images/alipay.png) no-repeat; width:400px; height:360px;">
                    <div style="color: #E30074;font-size:16px; margin-left:130px; padding-top:65px;"><?php echo $money;?></div>
                    <div style="color: #E30074;font-size:16px; margin-left:130px; padding-top:55px;"><?php echo $order_no;?></div></div>
                    </li>	
			
		</ul>	
		</i>
		
		
	<h3 class="box">支付宝网页在线付款</h3>
		<i><ul style="float:left;width:320px;margin-left:10px;font-size:14px;margin-top:20px;">
				    <li style="height:25px; line-height:25px; font-size:15px;color:#060 ">步骤1.点击下方按钮登录支付宝后返回本页面。(支付宝如果已经登录了的可跳过此步) </li> 
					<li style="height:25px; line-height:25px; font-size:15px;color:#f00 ">(支付宝如果已经登录了的可跳过此步) </li> 
					<li><a href="http://www.alipay.com" target="_blank"><img src="http://pay.336hyw.com/ceshi/images/sub1.png"  width="150" height="30"/></a></li>
					 <li><br></li>
				    <li style="height:25px; line-height:25px; font-size:15px;color:#060 ">步骤2.点击下方按钮后即可到达充值页面。 </li> 
					<li><a href="<?php echo $url;?>" target="_blank"><img src="http://pay.336hyw.com/ceshi/images/sub2.png"  width="150" height="30"/></a></li>
					 <li><br></li>
					<li style="height:25px; line-height:25px; font-size:15px;color:#F00 ">付款完成后将自动为您充值。</li> 
					 </ul>
					
					<ul style="float:right;width:480px;margin-left:10px;font-size:14px;margin-top:20px;">
                    <li>
					<div style="text-align:center; height:30px; line-height:30px; font-size:14px;color:#F00 ">支付宝付款示意图</div>
                    <div style="background:url(http://pay.336hyw.com/ceshi/images/alipayy.jpg) no-repeat; width:450px; height:300px;  ">
                    <div style=" font-size:16px; margin-left:180px; padding-top:120px;"><?php echo $pay;?></div>
                    <div style=" font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $money;?></div>
                    <div style=" font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $order_no;?></div></div>
					</li>
 
         
                </ul></i>
		
		
 	<h3 class="pr">支付宝手动转账付款</h3>
		<i><ul style="float:left;width:380px;margin-left:10px;font-size:14px;margin-top:20px;">
	                <li><p style="font-size:14px; color:#666; padding-left:10px; line-height:25px;">请手动复制收款人账号和付款说明，填至您的支付宝付款页面,付款完成即可自动到账!</p></li>
                    <li><br></li>
                    <li><span class="bdl">收款账号&nbsp;&nbsp;</span><span style="color: #E30074;"><input style="color: #E30074;width:250px;" type="text" value="<?php echo $pay;?>"></span></li>
                    <li><br></li>
					<li><span class="bdl">支付金额&nbsp;&nbsp;</span><span style="color: #E30074;"><input type="text" value="<?php echo $money;?>"></span></li>
                    <li><br></li>
					<li><span class="bdl">付款说明&nbsp;&nbsp;</span><span style="color: #E30074;"><input type="text" value="<?php echo $order_no;?>"></span></li>
					<li><br></li>
					<li><a href="<?php echo $urlurl;?>" target="_blank"><img src="http://pay.336hyw.com/ceshi/images/sub1.png"  width="150" height="30"/></a></li>
					 </ul>
					
					<ul style="float:right;width:420px;margin-left:10px;font-size:14px;margin-top:20px;">
                    <li>
					<div style="text-align:center; height:30px; line-height:30px; font-size:14px;color:#F00 ">支付宝付款示意图</div>
                    <div style="background:url(http://pay.336hyw.com/ceshi/images/alipay.jpg) no-repeat; width:420px; height:300px;  ">
                    <div style="color: #E30074;font-size:16px; margin-left:180px; padding-top:120px;"><?php echo $pay;?></div>
                    <div style="color: #E30074;font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $money;?></div>
                    <div style="color: #E30074;font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $order_no;?></div></div>
					</li>
 
         
                </ul></i>

 
 
 
 

</div>
</div>


<script type="text/javascript">
<!--
function Pid(id,tag){
	if(!tag){
		return document.getElementById(id);
		}
	else{
		return document.getElementById(id).getElementsByTagName(tag);
		}
}

function tab(id,hx,box,iClass,s,pr){
	var hxs=Pid(id,hx);
	var boxs=Pid(id,box);
	if(!iClass){ // 如果不指定class，则：
		boxsClass=boxs; // 直接使用box作为容器
	}
	else{ // 如果指定class，则：
		var boxsClass = [];
		for(i=0;i<boxs.length;i++){
			if(boxs[i].className.match(/\btab\b/)){// 判断容器的class匹配
				boxsClass.push(boxs[i]);
			}
		}
	}
	if(!pr){ // 如果不指定预展开容器，则：
		go_to(0); // 默认展开序列
		yy();
	}
	else {
		go_to(pr);
		yy();
	}
	function yy(){
		for(var i=0;i<hxs.length;i++){
			hxs[i].temp=i;
			if(!s){// 如果不指定事件，则：
				s="onmouseover"; // 使用默认事件
				hxs[i][s]=function(){
					go_to(this.temp);
				}
			}
			else{
				hxs[i][s]=function(){
					go_to(this.temp);
				}
			}
		}
	}
	function go_to(pr){
		for(var i=0;i<hxs.length;i++){
			if(!hxs[i].tmpClass){
				hxs[i].tmpClass=hxs[i].className+=" ";
				boxsClass[i].tmpClass=boxsClass[i].className+=" ";
			}
			if(pr==i){
				hxs[i].className+=" up"; // 展开状态：标题
				boxsClass[i].className+=" up"; // 展开状态：容器
			}
			else {
				hxs[i].className=hxs[i].tmpClass;
				boxsClass[i].className=boxsClass[i].tmpClass;
			}
		}
	}
}
tab("tab01","h3","i","","onclick",0); tab("tab02","h4","ol");tab("tab03","h3","i","tab"); 
//-->
</script>


</body>
</html>