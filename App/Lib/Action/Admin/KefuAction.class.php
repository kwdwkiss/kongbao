<?php
//dezend by 辰梦大人 QQ:30881917
class KefuAction extends CommonAction
{
	public function index()
	{
		$model = new Model('kefu');
		$count = $model->count();
		$this->assign('count', $count);
		$kefu_list = $model->order('kf_type desc,sort_order asc ')->select();
		$this->assign('kefu_list', $kefu_list);
		$type_list_array = M('kefu_type')->order('sort_order asc')->select();
		$type_list = array();

		foreach ($type_list_array as $type) {
			$type_list[$type['id']] = $type['title'];
		}

		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function add()
	{
		$type_list_array = M('kefu_type')->order('sort_order asc')->select();
		$type_list = array();

		foreach ($type_list_array as $type) {
			$type_list[$type['id']] = $type['title'];
		}

		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$model = new Model('kefu');

		if ($_POST['kf_name'] == '') {
			$this->message('客服名称不能为空！', 'add');
		}

		if ($_POST['kf_qq'] == '') {
			$this->message('客服QQ不能为空！', 'add');
		}

		if ($_POST['kf_type'] == '') {
			$this->message('请选择客服组！', 'add');
		}

		if ($data = $model->create()) {
			if (false !== $model->add()) {
				$this->message('客服添加成功', __URL__ . '/index');
			}
			else {
				$this->message('客服添加失败：' . $model->getError(), __URL__ . '/add');
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
			$model = new Model('kefu');
			$data = $model->getById($id);
			$this->assign('udata', $data);
		}

		$type_list_array = M('kefu_type')->order('sort_order asc')->select();
		$type_list = array();

		foreach ($type_list_array as $type) {
			$type_list[$type['id']] = $type['title'];
		}

		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new Model('kefu');

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
			$model = new Model('kefu');

			if (is_array($id)) {
				$where = 'id in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
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

	public function type_index()
	{
		$model = new Model('kefu_type');
		$count = $model->count();
		$this->assign('count', $count);
		$type_list = $model->order('sort_order asc ')->select();
		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function type_add()
	{
		$this->display();
	}

	public function type_insert()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$model = new Model('kefu_type');

		if ($_POST['title'] == '') {
			$this->message('客服组名称不能为空！', 'type_add');
		}

		if ($data = $model->create()) {
			if (false !== $model->add()) {
				$this->message('客服组添加成功', __URL__ . '/type_index');
			}
			else {
				$this->message('客服组添加失败：' . $model->getError(), __URL__ . '/type_add');
			}
		}
		else {
			$this->message('操作有误：' . $model->getError(), __URL__ . '/type_add');
		}
	}

	public function type_edit()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new Model('kefu_type');
			$data = $model->getById($id);
			$this->assign('udata', $data);
		}

		$this->display();
	}

	public function type_update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new Model('kefu_type');

			if ($data = $model->create()) {
				if (false !== $model->where('id=' . $id)->save()) {
					$this->message('编辑成功', __URL__ . '/type_index');
				}
				else {
					$this->message('编辑失败：' . $model->getError(), __URL__ . '/type_add');
				}
			}
			else {
				$this->message('编辑失败：' . $model->getError(), __URL__ . '/type_add');
			}
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/type_index');
		}
	}

	public function type_del()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new Model('kefu_type');

			if (is_array($id)) {
				$where = 'id in (' . implode(',', $id) . ')';
				$where_a = 'kf_type in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
				$where_a = 'kf_type =' . $id;
			}

			$count = M('kefu')->where($where_a)->count();

			if (0 < $count) {
				$this->message('该客服组下已经存在客服，不能删除！', __URL__ . '/type_index');
			}

			if (false !== $model->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/type_index');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/type_index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}
}

?>
