<?php
require_once 'config.php';
$ddh=trim(htmlspecialchars($_POST['ddh'])); //支付宝交易号
$money=trim(htmlspecialchars($_POST['money'])); //付款金额
$name=trim(htmlspecialchars($_POST['name']));   //付款说明,参数一
$key=trim(htmlspecialchars($_POST['key'])); //密钥
$addtime=date('Y-m-d H:i:s',time());
$stage="recharge";

if($key == 'cly20150929'){
        $rs=mysql_query("SELECT * FROM pay_order WHERE order_no='$name'");
        $row=mysql_fetch_array($rs);
        if($row ){
            if($row['status']==0){

                $pay=mysql_query("Update pay_order set outer_order_no='$ddh',pay_money='$money',status=1  WHERE order_no='$name'");
                $user_id=$row['user_id'];
                mysql_query("UPDATE user set money=money+".$money." WHERE id='$user_id'");
				//写日志
                $rss=mysql_query("SELECT * FROM user WHERE id='$user_id'");
                $rows=mysql_fetch_array($rss);
                $remain_money=$rows['money'];
$i="INSERT INTO account_log (user_id,stage,money,remain_money,comm,addtime) VALUES ('$user_id','$stage','$money','$remain_money','$name','$addtime')";
mysql_query($i);


                
                echo "okokokokok";
            }else{
                 echo "0";
            }
        }else{
           echo 'no';
        }
}else{
	echo  'no';	
}
?>
