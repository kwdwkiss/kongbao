<?php
/**
 * 小号信息模型
 *  李强 20140312
 */
 Class XiaohaoModel extends  Model
 {      //表单的数据验证
       protected $_validate=array(
       //array 验证的字段，验证的规则，错误提示，验证条件，附加规则，验证时间
          array('note_no','require','请输入单号！',1,'regex',1),
          array('note_no','','单号已经存在！',1,'unique',1),
          array('type_id','require','请选择单号类型！',1,'regex',1),  
       );
	       //表单的自动填充
       protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('addtime','getDate',1,'callback'), 
       ); 
       function getDate(){
		return date('Y-m-d H:i:s');
	  }
 }
?>