<?php
require_once 'config.php';
$ddh=trim(htmlspecialchars($_POST['ddh'])); //֧�������׺�
$money=trim(htmlspecialchars($_POST['money'])); //������
$name=trim(htmlspecialchars($_POST['name']));   //����˵��,����һ
$key=trim(htmlspecialchars($_POST['key'])); //��Կ
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
				//д��־
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
