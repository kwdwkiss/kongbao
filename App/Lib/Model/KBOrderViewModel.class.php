<?php
/***
 * 空包代理批发视图模型
 * 李强 20140312
 */
 Class KBOrderViewModel extends ViewModel
 {
 	  protected $viewFields=array(
 	    'user'=>array('username','nickname'),
 	    'kongbao_daili_order'=>array('id','user_id','type_id','order_no','order_time',
 	                                   'order_status','note_no','_on'=>'user.id=kongbao_daili_order.user_id'),
 	    'kongbao_type'=>array('name','_on'=>'kongbao_daili_order.type_id=kongbao_type.id')
	   );
 }
?>