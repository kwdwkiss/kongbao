<?php
/**
 * 空包底单单视图
 * 李强 20140312
 */
 Class KongbaoDidanViewModel extends  ViewModel
 {
 	 protected $viewFields=array(
 	    'user'=>array('username','nickname'),
 	    'kongbao_didan'=>array('id','user_id','note_no','order_no','order_time','status','goods_name','didan_image',
		'addtime','yunfei','weight','send_addr','send_name','send_phone','send_shouji','send_zipcode','jietu','dd_price',
		'rec_addr','rec_name','rec_phone' ,'rec_shouji','rec_zipcode','_on'=>'kongbao_didan.user_id=user.id'),
		'kongbao_type'=>array('name','_on'=>'kongbao_didan.type_id=kongbao_type.id')
	 );
 }
?>