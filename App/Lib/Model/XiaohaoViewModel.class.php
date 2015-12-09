<?php
/**
 * 小号视图
 * 李强 20140312
 */
 Class XiaohaoViewModel extends ViewModel
 {
 	 protected $viewFields = array(
 	    'xiaohao'=>array('id','note_no','type_id','type','isused','order_no','addtime','account',
 	                               'password','email','email_pass','pay_pass','shenfenzheng','truename',
								   'bank_account','bank_yue','pay_account'),
 	    'xiaohao_type'=>array('name','_on'=>'xiaohao.type_id=xiaohao_type.id'),
	 );
 }
?>