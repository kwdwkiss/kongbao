<?php
/**
 * 空包底单视图模型
 * 李强 20140313
 */
 Class KBDidanViewModel1 extends  ViewModel
 {
 	 protected $viewFields = array( 
 	   'kongbao_didan'=>array('id','user_id','type_id','note_no','send_addr','send_name','send_phone','rec_shouji','send_shouji',
	                                     'rec_addr','rec_name','rec_phone','weight','yunfei','order_time','goods_name',
										 'addtime','status','order_no','rec_zipcode','send_zipcode','type_name'),
	   'user'=>array('username','nickname','_on'=>'kongbao_didan.user_id=user.id')		 
	 );
 }
?>