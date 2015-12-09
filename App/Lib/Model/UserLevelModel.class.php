<?php
/**
 * 会员级别模型
 */
 Class UserLevelModel extends  Model
 {
 	 protected $tableName = "user_level";
	//表单数据的验证
   protected $_validate=array(
       array('title','require','请填写会员级别名称',1,'regex',1),  
   );
 }
?>