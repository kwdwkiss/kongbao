<?php
/**
 * 单号视图
 * 李强 20140312
 */
 Class DanhaoViewModel extends ViewModel
 {
 	 protected $viewFields = array(
 	    'danhao'=>array('id','note_no','type_id','type','isused','order_no','addtime'),
 	    'danhao_type'=>array('name','_on'=>'danhao.type_id=danhao_type.id'),
	 );
 }
?>