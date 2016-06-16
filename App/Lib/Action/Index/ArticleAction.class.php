<?php
//dezend by 辰梦大人 QQ:30881917
class ArticleAction extends CommonAction
{
	public function _initialize()
	{
		parent::_initialize();
		$model = new Model('gonggao');
		$gonggao = $model->order('time desc')->limit(8)->select();
		$this->assign('gonggao_list', $gonggao);
		$model = new Model('help');
		$help = $model->order('time desc')->limit(8)->select();
		$this->assign('help_list', $help);
		$model = new Model('article');
		$article = $model->order('article_time desc')->limit(8)->select();
		$this->assign('article_list', $article);
		$model = new Model('about');
		$about = $model->order('time desc')->limit(8)->select();
		$this->assign('about_list', $about);
	}

	public function index()
	{
		import('ORG.Util.Page');
		$model = new ArticleViewModel();
		$count = $model->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$article_list = $model->order('article_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('article_list', $article_list);
		$this->display();
	}

	public function article()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定文章!', 'index');
		}

		$model = new Model('article');
		$article = $model->where('id=' . $id)->find();

		if ($article['article_keyword'] != '') {
			$this->assign('seo_keyword', $article['article_keyword']);
		}

		if ($article['article_desc'] != '') {
			$this->assign('seo_desc', $article['article_desc']);
		}

		$config = M('config')->where('id=1')->find();
		$this->assign('seo_title', $article['article_name'] . '-' . $config['sitename']);
		$this->assign('article', $article);
		$this->display();
	}
}

?>
