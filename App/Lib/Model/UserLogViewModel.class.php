<?php
/***
 * 用户操作日志
 * 李强 20140310
 */
 Class UserLogViewModel extends  ViewModel
 {
 	protected $viewFields = array(
 	  'user_log'=>array('id','user_id','opt_type','comm','opt_ip','addtime'),
 	  'user'=>array('username','nickname','_on'=>'user_log.user_id=user.id'),
	);
 }
?>