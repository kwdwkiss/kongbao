<?php

class AboutModel extends Model{
    //表单数据的验证
   protected $_validate=array(
       array('title','require','请填写标题',1,'regex',1),
       array('content','require','请填写内容',1,'regex',1), 
   );
   //自动填充
    protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('time','getDate',1,'callback'),
       );

    function getDate(){
		return date('Y-m-d H:i:s');
	  }
	
}

?>