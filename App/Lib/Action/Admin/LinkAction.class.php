<?php
//dezend by 辰梦大人 QQ:30881917
class LinkAction extends CommonAction
{
	public function index()
	{
		$model = new LinkModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$count = $model->where($where)->count();
		$this->assign('count', $count);
		$link = $model->where($where)->select();
		$this->assign('link', $link);
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

		$model = new LinkModel();

		if ($data = $model->create()) {
			if (false !== $model->add()) {
				$this->message('链接添加成功', __URL__ . '/index');
			}
			else {
				$this->message('链接添加失败：' . $model->getError(), __URL__ . '/add');
			}
		}
		else {
			$this->message('操作有误：' . $model->getError(), __URL__ . '/add');
		}
	}

	public function edit()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new LinkModel();
			$data = $model->getById($id);
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
			$model = new LinkModel();
			$link = $model->where('id=' . $id)->find();

			if ($link['is_sys'] == 1) {
				$this->message('系统内置，删除失败!', __URL__ . '/index');
			}

			if ($data = $model->create()) {
				if (false !== $model->where('id=' . $id)->save()) {
					$this->message('编辑成功', __URL__ . '/index');
				}
				else {
					$this->message('编辑失败：' . $model->getError(), __URL__ . '/add');
				}
			}
			else {
				$this->message('编辑失败：' . $model->getError(), __URL__ . '/add');
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
			$model = new LinkModel();

			if (is_array($id)) {
				$where = 'id in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
				$link = $model->where('id=' . $id)->find();

				if ($link['is_sys'] == 1) {
					$this->message('系统内置，删除失败!', __URL__ . '/index');
				}
			}

			if (false !== $model->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/index');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}
}

?>
