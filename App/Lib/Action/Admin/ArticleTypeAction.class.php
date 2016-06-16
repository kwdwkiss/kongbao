<?php
//dezend by 辰梦大人 QQ:308819170
class ArticleTypeAction extends CommonAction
{
	public function index()
	{
		$article = new ArticletypeModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			if ($ftype == 'id') {
				$where[$ftype] = $keyword;
			}
			else {
				$where[$ftype] = array('like', '%' . $keyword . '%');
			}
		}

		$count = $article->where($where)->count();
		$this->assign('count', $count);
		$articletype = $article->where($where)->select();
		$this->assign('articletype', $articletype);
		$this->display();
	}

	public function add()
	{
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$articletype = new ArticletypeModel();

		if ($date = $articletype->create()) {
			if (false !== $articletype->add()) {
				$this->message('栏目添加成功', __URL__ . '/index');
			}
			else {
				$this->message('栏目添加失败：' . $articletype->getError(), __URL__ . '/add');
			}
		}
		else {
			$this->message('操作有误：' . $articletype->getError(), __URL__ . '/add');
		}
	}

	public function edit()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$articletype = new ArticletypeModel();
			$data = $articletype->getById($id);
			$this->assign('udata', $data);
		}

		$this->display();
	}

	public function update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (!empty($id)) {
			$articletype = new ArticletypeModel();

			if ($data = $articletype->create()) {
				if (false !== $articletype->save($data)) {
					$this->message('编辑成功', __URL__ . '/index');
				}
				else {
					$this->message('编辑失败:' . $articletype->getError(), __URL__ . '/add');
				}
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
			$articletype = new ArticletypeModel();

			if (is_array($id)) {
				$where = 'id in (' . implode(',', $id) . ')';
				$where_a = 'type_id in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
				$where_a = 'type_id = ' . $id;
			}

			$count = M('article')->where($where_a)->count();

			if (0 < $count) {
				$this->message('删除失败：该类型下已经存在文章!', __URL__ . '/index');
			}

			if (false !== $articletype->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/index');
			}
			else {
				$this->message('删除失败：' . $articletype->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}
}

?>
