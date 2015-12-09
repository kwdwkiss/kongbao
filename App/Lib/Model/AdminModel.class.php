<?php

class AdminModel extends Model{ 
    protected $_validate=array(
       //array 验证的字段，验证的规则，错误提示，验证条件，附加规则，验证时间
          array('repassword','password','两次密码不一致！',0,'confirm'),
       );
     
}

?>