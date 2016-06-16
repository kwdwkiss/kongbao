<?php
//dezend by 辰梦大人 QQ:308819170
class ArticleAction extends CommonAction
{
	public function index()
	{
		import('ORG.Util.Page');
		$article = new ArticleViewModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', 0);

		if (0 < $type_id) {
			$where['type_id'] = $type_id;
		}

		$count = $article->where($where)->count();
		$page = new Page($count, 8);
		$show = $page->show();
		$this->assign('show', $show);
		$articlelist = $article->order('id desc')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$type = M('article_type')->select();
		$this->assign('type_list', $type);
		$this->assign('count', $count);
		$this->assign('articlelist', $articlelist);
		$this->display();
	}

	public function add()
	{
		$articletype = new ArticletypeModel();
		$data = $articletype->select();
		$this->assign('type', $data);
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$article = new ArticleModel();

		if ($data = $article->create()) {
			if (false !== $article->add()) {
				$this->message('添加成功', __URL__ . '/index');
			}
			else {
				$this->message('添加失败：' . $article->getError(), __URL__ . '/add');
			}
		}
		else {
			$this->message('添加失败：' . $article->getError(), __URL__ . '/add');
		}
	}

	public function edit()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$article = new ArticleModel();
			$articledata = $article->getById($id);
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/index');
		}

		$type = new ArticletypeModel();
		$data = $type->select();
		$this->assign('type', $data);
		$this->assign('articledata', $articledata);
		$this->display();
	}

	public function update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (!empty($id)) {
			$article = new ArticleModel();

			if ($data = $article->create()) {
				if (false !== $article->save()) {
					$this->message('编辑成功', __URL__ . '/index');
				}
				else {
					$this->message('编辑失败：' . $article->getError(), __URL__ . '/index');
				}
			}
			else {
				$this->message('编辑失败：' . $article->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/index');
		}
	}

	public function del()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			if (is_array($id)) {
				$where = 'id  in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
			}

			$article = new ArticleModel();

			if (false !== $article->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/index');
			}
			else {
				$this->message('删除失败：' . $article->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}
}

?>
