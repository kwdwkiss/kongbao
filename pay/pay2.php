<?php
//header("Content-type:text/html;charset=gb2312");
require_once 'config.php';
$user_id=$_SESSION['userid'];
$pay_money=htmlspecialchars($_POST['payAmount']);
$pay=$_POST['optEmail'];
$order_no=date('YmdHis').rand(10,99);
$subject=$order_no;
$type=1;
$addtime=date('Y-m-d H:i:s',time());
$comm="tenpay";
$insert="INSERT INTO pay_order (user_id,order_no,pay_money,type,addtime,comm) VALUES ('$user_id','$order_no','$pay_money','$type','$addtime','$comm')";
$xr=mysql_query($insert);


if($xr){
$md5=md5($pay."&".$pay_money."&".$subject);
	echo '<form name="myform" action="https://www.tenpay.com/v2/account/pay/paymore_cft.shtml?data='.$pay.'%26'.$pay_money.'%26'.$subject.'&validate='.$md5.'" method="post">';
	echo '<script type="text/javascript">document.myform.submit();</script>';
}else{
	echo "<script type='text/javascript'>alert('ERROR!');window.location.href='/';</script>";
}
?>