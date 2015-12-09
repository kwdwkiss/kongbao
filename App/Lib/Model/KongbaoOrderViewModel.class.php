<?php
/**
 * 空包订单视图
 * 李强 20140312
 */
 Class KongbaoOrderViewModel extends  ViewModel
 {
 	 protected $viewFields=array(
 	    'user'=>array('username','nickname'),
 	    'kongbao_order'=>array('id','user_id','note_no','order_no','order_time','order_status',
		'send_province','send_city','send_district','send_address','send_name','send_phone',
		'send_shouji','rec_province','rec_city','rec_district','rec_shouji',
		'rec_address','rec_name','rec_phone' ,'_on'=>'kongbao_order.user_id=user.id'),
		'kongbao_type'=>array('name','_on'=>'kongbao_order.type_id=kongbao_type.id')
	 );
 }
?>