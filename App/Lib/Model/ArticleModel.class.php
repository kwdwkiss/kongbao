<?php

class ArticleModel extends Model {
	//表单数据的验证
   protected $_validate=array(
       array('article_name','require','请填写文章标题',1,'regex',1),
       array('type_id','require','请选择文章类型',1,'regex',1),
       array('article_content','require','请填写文章内容',1,'regex',1), 
   );
   //自动填充
    protected $_auto=array(
       //array 填充的字段，填充的内容，填充条件，附加规则
       	array('article_time','getDate',1,'callback'),
       );

    function getDate(){
		return date('Y-m-d H:i:s');
	  }

}
?>