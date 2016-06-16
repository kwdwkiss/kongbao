<?php
//dezend by 辰梦大人 QQ:308819170
class AdvAction extends CommonAction
{
	public function index()
	{
		$adv = new AdvModel();
		$data = $adv->select();
		$this->assign('data', $data);
		$this->display();
	}

	public function add1()
	{
		$this->display();
	}

	public function insert1()
	{
		$adv = new AdvModel();
		$typeid = I('typeid', '');

		if ($typeid == '') {
			$this->message2('广告位类型不能为空', __URL__ . '/index');
		}

		if ($data = $adv->create()) {
			$data[pic_url] = $_POST[pic_url];
			$data[ad_brief] = $_POST[ad_brief];
			$data[typeid] = $typeid;
			$data[pic] = $_POST[pic];

			if (false !== $adv->add($data)) {
				$this->message2('添加成功', __URL__ . '/index');
			}
			else {
				$this->message2('添加失败:' . $adv->getDbError(), __URL__ . '/add1');
			}
		}
		else {
			$this->message2('添加失败:' . $adv->getError(), __URL__ . '/add1');
		}
	}

	public function edit()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new AdvModel();
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
			$model = new AdvModel();
			$adv = $model->where('id=' . $id)->find();
			$data = $model->create();

			if (false !== $model->save()) {
				$this->message('编辑成功', __URL__ . '/index');
			}
			else {
				$this->message('编辑失败：' . $model->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/index');
		}
	}

	public function del()
	{
		$id = $_GET[id];

		if (!empty($id)) {
			$type = new AdvModel();

			if (false !== $type->delete($id)) {
				$this->message2('删除成功', __URL__ . '/index');
			}
			else {
				$this->message2('删除失败', __URL__ . '/index');
			}
		}
		else {
			$this->message2('请选择删除对象:' . $adv->getDbError(), __URL__ . '/index');
		}
	}
}

?>
