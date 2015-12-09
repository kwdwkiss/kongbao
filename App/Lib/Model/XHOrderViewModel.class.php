<?php
/***
 * 小号订单视图模型
 * 李强 20140312
 */
 Class XHOrderViewModel extends ViewModel
 {
 	  protected $viewFields=array(
 	    'user'=>array('username','nickname'),
 	    'xiaohao_order'=>array('id','user_id','type_id','order_no','order_time',
 	                                   'order_status','note_no','_on'=>'user.id=xiaohao_order.user_id'),
 	    'xiaohao_type'=>array('name','_on'=>'xiaohao_order.type_id=xiaohao_type.id')
	   );
 }
?>