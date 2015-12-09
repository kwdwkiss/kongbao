<?php
/**
 * 账户日志视图
 */
 Class UserAccountViewModel extends  ViewModel
 {
 	 protected $viewFields=array(
 	    'account_log'=>array('id','user_id','stage','money'=>'tzmoney','remain_money','comm','addtime','remain_refer_money','order_no'),
 	    'user'=>array('username','nickname','_on'=>'account_log.user_id=user.id'),
	   );
 }
?>