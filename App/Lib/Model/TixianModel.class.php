<?php

class TixianModel extends Model 
{
	   //自动填充
    protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('addtime','getDate',1,'callback'),
       	array('ip','get_client_ip',1,'callback'),
       );

    function getDate(){
		return date('Y-m-d H:i:s');
	  }	   	
	 function get_client_ip() 
	 {
			$ip = $_SERVER['REMOTE_ADDR'];
			if (isset($_SERVER['HTTP_X_REAL_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_REAL_FORWARDED_FOR'];
			}  
		    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}  
			elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} 
			return $ip;
	 } 
}
?>