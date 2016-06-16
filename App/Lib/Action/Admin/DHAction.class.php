<?php
//dezend by 辰梦大人 QQ:308819170
class DHAction extends CommonAction
{
	public function type()
	{
		$model = M('danhao_type');
		$type_list = $model->order('sort_order asc')->select();
		$list = array();

		foreach ($type_list as $k => $v) {
			$v['config'] = json_decode($v['config'], true);
			$list[] = $v;
		}

		$this->assign('type_list', $list);
		$this->display();
	}

	public function type_add()
	{
		$model_level = D('user_level');
		$level = $model_level->select();
		$level_arr = array();

		foreach ($level as $k => $v) {
			$level_arr['a' . $v['id']] = $v;
		}

		$this->assign('level_list', $level_arr);
		$config = M('config')->where('id=1')->find();
		$refer_mode = $config['refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$this->assign('refer_mode', $mode);
		$this->display();
	}

	public function type_insert()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'type');
		}

		$type_model = new DHTypeModel();
		$_POST['config'] = json_encode($_POST['config']);
		$data = $type_model->create();

		if (false !== $data) {
			$data['title'] = I('name', '');
			$data['comm'] = I('comm', '');
			$data['state'] = I('state', 0);
			$data['sort_order'] = I('sort_order', 0);
			$data['config'] = $_POST['config'];

			if (false !== $type_model->data($data)->add()) {
				$this->message2('新增成功', 'type');
			}
			else {
				$this->message2('新增失败：' . $type_model->getError(), 'type_add');
			}
		}
		else {
			$this->message2('新增失败：' . $type_model->getError(), 'type_add');
		}
	}

	public function type_edit()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('无效请求!', 'type');
		}

		$model_level = D('user_level');
		$level = $model_level->select();
		$level_arr = array();

		foreach ($level as $k => $v) {
			$level_arr['a' . $v['id']] = $v;
		}

		$this->assign('level_list', $level_arr);
		$config = M('config')->where('id=1')->find();
		$refer_mode = $config['refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$this->assign('refer_mode', $mode);
		$type = D('danhao_type')->where('id=' . $id)->find();
		$config = json_decode($type['config'], true);
		$this->assign('config', $config);
		$this->assign('type', $type);
		$this->display();
	}

	public function type_update()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (!empty($id)) {
			$model = new DHTypeModel();
			$_POST['config'] = json_encode($_POST['config']);
			$data = $model->create();

			if (false !== $data) {
				$data['config'] = $_POST['config'];

				if (false !== $model->where('id=' . $id)->data($data)->save()) {
					$this->message('编辑成功', __URL__ . '/type');
				}
				else {
					$this->message('编辑失败：' . $model->getError(), __URL__ . '/type');
				}
			}
			else {
				$this->message('编辑失败：' . $model->getError(), __URL__ . '/type');
			}
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/type');
		}
	}

	public function type_del()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			if (is_array($id)) {
				$where_e = 'type_id in (' . implode(',', $id) . ')';
				$where = 'id in  (' . implode(',', $id) . ')';
			}
			else {
				$where_e = 'type_id = ' . $id;
				$where = ' id = ' . $id;
			}

			$count = M('danhao')->where($where_e)->count();

			if (0 < $count) {
				$this->message('已经存在该类型的单号，系统将不能删除!', __URL__ . '/type');
			}

			$model = new DHTypeModel();

			if (false !== $model->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/type');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/type');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/type');
		}
	}

	public function index()
	{
		$config = M('config')->where('id=1')->find();
		$danhao_config = json_decode($config['danhao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_danhao_type();
		$this->assign('type_list', $type_list);
		$dh_model = new DanhaoViewModel();
		$keyword = I('keyword');
		$ftype = I('ftype');
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$where['isused'] = 0;
		$count = $dh_model->where($where)->count();
		$numsOfpage = $danhao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$danhao = $dh_model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('danhao_list', $danhao);
		$this->display();
	}

	public function edit()
	{
		$type_list = $this->get_danhao_type();
		$this->assign('type_list', $type_list);
		$id = I('id', NULL);

		if (!empty($id)) {
			$danhao = M('danhao')->where('id=' . $id)->find();
			$this->assign('danhao', $danhao);
		}
		else {
			$this->message2('请选择要编辑的项目', 'index');
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
			$model = new DanhaoModel();
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

	public function add()
	{
		$type_list = $this->get_danhao_type();
		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'add');
		}

		$model = new DanhaoModel();

		if ($data = $model->create()) {
			if (false !== $model->data($data)->add()) {
				$this->message2('新增成功', 'index');
			}
			else {
				$this->message2('新增失败：' . $model->getError(), 'add');
			}
		}
		else {
			$this->message2('新增失败：' . $model->getError(), 'add');
		}
	}

	public function insert_multi()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$note_nos = I('note_nos', NULL);
		$type_id = I('type_id', NULL);

		if (empty($note_nos)) {
			$this->ajaxReturn('', '单号不能为空！', 0);
		}

		$note_nos = trim($note_nos);

		if ($note_nos == '') {
			$this->ajaxReturn('', '单据号不能为空！', 0);
		}

		$error_msg = array();
		$note_nos = preg_replace('/([\\s]{2,})/', "\n", $note_nos);
		$note_array = explode("\n", $note_nos);
		$error_count = 0;
		$succ_count = 0;
		$insert_data = array();
		$return_array = array();
		$counts = count($note_array);

		foreach ($note_array as $k => $v) {
			$where = array();
			$where['note_no'] = $v;
			$count = M('danhao')->where($where)->find();

			if (0 < $count) {
				$error_count++;
				$error_msg[] = $v . '已存在';
				continue;
			}

			$temp_data = array();
			$temp_data['note_no'] = $v;
			$temp_data['type_id'] = $type_id;
			$temp_data['type'] = 1;
			$temp_data['isused'] = 0;
			$temp_data['addtime'] = $this->getDate();
			$insert_data[] = $temp_data;
		}

		$danhao = new DanhaoModel();

		if (!empty($insert_data)) {
			foreach ($insert_data as $data) {
				$id = $danhao->data($data)->add();

				if (0 < $id) {
					$succ_count++;
				}
				else {
					$error_count++;
					$error_msg[] = $data['note_no'] . '已存在';
				}
			}
		}

		$return_array['error_count'] = $error_count;
		$return_array['error_msg'] = implode('|', $error_msg);
		$info = '处理完成！导入总数：' . $counts . '，成功数：' . $succ_count . ',失败数：' . $error_count;
		$this->ajaxReturn($return_array, $info, 1);
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

			$danhao = new DanhaoModel();

			if (false !== $danhao->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/index');
			}
			else {
				$this->message('删除失败：' . $danhao->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}

	public function used_index()
	{
		$config = M('config')->where('id=1')->find();
		$danhao_config = json_decode($config['danhao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_danhao_type();
		$this->assign('type_list', $type_list);
		$dh_model = new DanhaoViewModel();
		$keyword = I('keyword');
		$ftype = I('ftype');
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$where['isused'] = 1;
		$count = $dh_model->where($where)->count();
		$numsOfpage = $danhao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$danhao = $dh_model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('danhao_list', $danhao);
		$this->display();
	}

	public function order_index()
	{
		import('ORG.Util.Page');
		$model = new DHOrderViewModel();
		$keyword = I('keyword');
		$ftype = I('ftype');
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			if ($ftype == 'user_id') {
				$where[$ftype] = $keyword;
			}
			else {
				$where[$ftype] = array('like', '%' . $keyword . '%');
			}
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$order = $model->where($where)->order('order_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_danhao_type();
		$this->assign('type_list', $type_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order);
		$this->display();
	}

	private function get_danhao_type()
	{
		$type_model = new Model('danhao_type');
		$type = $type_model->select();
		$type_list = array();

		foreach ($type as $k => $v) {
			if ($v['state'] == 1) {
				$v['name'] = $v['name'] . '(业务暂停)';
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
		}

		return $type_list;
	}

	public function getDate()
	{
		return date('Y-m-d H:i:s');
	}
}

?>
