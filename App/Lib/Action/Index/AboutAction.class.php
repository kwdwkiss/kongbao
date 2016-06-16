<?php
//dezend by 辰梦大人 QQ:30881917
class AboutAction extends CommonAction
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
		$model = new AboutModel();
		$count = $model->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$about_list = $model->order('time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('about_list', $about_list);
		$this->display();
	}

	public function about()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定常见问题!', 'index');
		}

		$model = new AboutModel();
		$about = $model->where('id=' . $id)->find();

		if ($about['keyword'] != '') {
			$this->assign('seo_keyword', $about['keyword']);
		}

		if ($about['desc'] != '') {
			$this->assign('seo_desc', $about['desc']);
		}

		$config = M('config')->where('id=1')->find();
		$this->assign('seo_title', $about['title'] . '-' . $config['sitename']);
		$this->assign('about', $about);
		$this->display();
	}
}

?>
