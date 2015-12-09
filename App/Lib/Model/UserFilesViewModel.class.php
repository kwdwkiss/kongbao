<?php
/**
 * 代理文件上传视图模型
 * 李强 20140313
 */
 Class UserFilesViewModel extends  ViewModel
 {
 	protected $viewFields=array(
 	 'uploadfiles' =>array('id','user_id','type_id','filename','addtime','downcounts','fileurl','status'),
 	 'user' =>array('username','nickname','user_qq','_on'=>'uploadfiles.user_id=user.id'),
 	 'kongbao_type'=>array('name','_on'=>'kongbao_type.id=uploadfiles.type_id'),
	);
 }
?>