<?php
/***
 * 单号订单视图模型
 * 李强 20140312
 */
 Class DHOrderViewModel extends ViewModel
 {
 	  protected $viewFields=array(
 	    'user'=>array('username','nickname'),
 	    'danhao_order'=>array('id','user_id','type_id','order_no','order_time',
 	                                   'order_status','note_no','_on'=>'user.id=danhao_order.user_id'),
 	    'danhao_type'=>array('name','_on'=>'danhao_order.type_id=danhao_type.id')
	   );
 }
?>