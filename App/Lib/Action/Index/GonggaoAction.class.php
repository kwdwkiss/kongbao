<?php
//dezend by 辰梦大人 QQ:30881917
class GonggaoAction extends CommonAction
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
		$model = new GonggaoModel();
		$count = $model->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$gonggao_list = $model->order('time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('gonggao_list', $gonggao_list);
		$this->display();
	}

	public function gonggao()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定公告!', 'index');
		}

		$model = new GonggaoModel();
		$gonggao = $model->where('id=' . $id)->find();

		if ($gonggao['keyword'] != '') {
			$this->assign('seo_keyword', $gonggao['keyword']);
		}

		if ($gonggao['desc'] != '') {
			$this->assign('seo_desc', $gonggao['desc']);
		}

		$config = M('config')->where('id=1')->find();
		$this->assign('seo_title', $gonggao['title'] . '-' . $config['sitename']);
		$this->assign('gonggao', $gonggao);
		$this->display();
	}
}

?>
