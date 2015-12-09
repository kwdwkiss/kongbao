<?php
session_start();
header("Content-type:text/html;charset=gb2312");
$conn=mysql_connect('127.0.0.1','root','caoleiyu');
if(mysql_select_db('kongbao',$conn)){
	echo "";
}else{
	echo "数据库连接失败！";
}
mysql_query("set names 'utf8'" );
?>
