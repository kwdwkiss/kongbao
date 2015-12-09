<?php
/*
 * 友情链接模型表
 */
class LinkModel extends Model{
    //表单数据的验证
   protected $_validate=array(
       array('sitename','require','请填写友情链接网站名称',1,'regex',1),
       array('siteurl','require','请填写友情链接网站URL',1,'regex',1), 
   );
   //自动填充
    protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('addtime','getDate',1,'callback'),
       ); 
    function getDate(){
		return date('Y-m-d');
	  } 
}

?>