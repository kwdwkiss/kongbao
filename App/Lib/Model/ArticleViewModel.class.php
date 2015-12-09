<?php
class ArticleViewModel extends ViewModel {
	public $viewFields=array(
		'Article'=>array('id','article_name','type_id','article_content','article_time','article_keyword','article_desc'),
		'Article_type'=>array('name'=>'type_name','_on'=>'Article_type.id=Article.type_id'),
	);
}
?>