<?php
if(file_exists('config.inc.php'))
{
	$db_config = require('config.inc.php');
}
else {
	$db_config = array();
}
$config =  array(
	//'配置项'=>'配置值'
	'APP_GROUP_LIST' =>'Index,Admin',
	'DEFAULT_GROUP' =>'Index',
	//配置数据库连接参数
	//'DB_HOST' =>'127.0.0.1',
	//'DB_USER'  =>'root',
	//'DB_PWD'  =>'huanrenqi123',
	'DB_HOST' =>'localhost',
	'DB_USER'  =>'root',
	'DB_PWD'  =>'huanrenqi123',	
	'DB_NAME' =>'xincx',
	'DB_PREFIX' =>'',  
	//配置网站相关参数
	'SITENAME' =>'淘号网后台管理系统',
	'COMPANY'=>'淘号网管理系统',
	'COMPANY_URL' =>'http://www.taohaowang.com.cn',
	'AUTH_TYPE' =>'kongbao',
	'AUTH_URL'  =>'http://www.taohaowang.com.cn',
	'SYS_REFER_URL'  =>'http://www.taohaowang.com.cn',
);
return array_merge($config,$db_config);
?>