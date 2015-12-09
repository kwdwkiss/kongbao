<?php
/**
 * 用户提现视图模型
 * 李强 20140310
 */
 Class UserTixianViewModel extends  ViewModel
 {
 	 protected $viewFields = array(
 	   'tixian'=>array('id','user_id','money'=>'tx_money','cwsz_config','addtime','ip','status','comm','error_msg','backtime'),
 	   'user'=>array('username','nickname','_on'=>'tixian.user_id=user.id')
	 );
 }
?>