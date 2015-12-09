<?php

class ArticleTypeModel extends Model{
    //表单验证
    protected $tableName="article_type";
    protected $_validate=array(
      array('name','require','请输入类型！',1,'regex',3),
      array('name','','类型已经存在',1,'unique',1),
      array('brief','require','请填写类型描述！',1,'regex',1),
      array('bieming','require','请填写类型别名！',1,'regex',1),
    );
    protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('addtime','getDate',1,'callback'),
       );
    function getDate(){
		return date('Y-m-d');
	  } 
}
?>