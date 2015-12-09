<?php

class UserModel extends Model{
       //表单的数据验证
       protected $_validate=array(
       //array 验证的字段，验证的规则，错误提示，验证条件，附加规则，验证时间
          array('username','require','请输入用户名！',1,'regex',1),
          array('username','','用户名已经存在！',1,'unique',1),
          array('password','require','请输入密码！',1,'regex',1), 
          array('re_user_pass','password','两次密码不一致！',0,'confirm'),
       );
       //表单的自动填充
       protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('create_time','getDate',1,'callback'),
       	array('password','md5',1,'function'),
       );

       function getDate(){
		return date('Y-m-d H:i:s');
	  }

 
}

?>