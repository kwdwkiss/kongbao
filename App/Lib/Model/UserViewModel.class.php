<?php
Class UserViewModel extends ViewModel
{
	protected $viewFields = array(
	'user'=>array('id','username','user_type','nickname','email','telphone','user_qq','money','invalid_money','used_money','refer_id','login_counts','cwsz_config','refer_money','isvalid','create_time','last_login_time','last_login_ip'),
	'user_level'=>array('title'=>'type_name','_on'=>'user.user_type=user_level.id'));
}
?>