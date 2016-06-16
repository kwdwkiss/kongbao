<?php
//dezend by 辰梦大人 QQ:308819170
class AboutAction extends CommonAction
{
	public function index()
	{
		import('ORG.Util.Page');
		$article = new AboutModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$count = $article->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$this->assign('show', $show);
		$articlelist = $article->order('id desc')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('articlelist', $articlelist);
		$this->display();
	}

	public function add()
	{
		$this->display();
	}

	public function insert()
	{
		$article = new AboutModel();

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
			$article = new AboutModel();
			$articledata = $article->getById($id);
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/index');
		}

		$this->assign('articledata', $articledata);
		$this->display();
	}

	public function update()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$article = new AboutModel();

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
			$article = new AboutModel();

			if (is_array($id)) {
				$where = 'id in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
			}

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
