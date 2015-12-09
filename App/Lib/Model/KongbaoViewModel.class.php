<?php
/**
 * 空包单视图
 * 李强 20140312
 */
 Class KongbaoViewModel extends ViewModel
 {
 	 protected $viewFields = array(
 	    'kongbao'=>array('id','note_no','type_id','type','isused','order_no','addtime'),
 	    'kongbao_type'=>array('name','_on'=>'kongbao.type_id=kongbao_type.id'),
	 );
 }
?>