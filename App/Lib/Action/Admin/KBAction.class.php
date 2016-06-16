<?php
//dezend by 辰梦大人 QQ:30881917
class KBAction extends CommonAction
{
	public function type()
	{
		$model = M('kongbao_type');
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
		$moban_list = M('exp_rule')->select();
		$this->assign('moban_list', $moban_list);
		$this->display();
	}

	public function type_insert()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'type');
		}

		import('ORG.Net.UploadFile');
		$type_model = new KBTypeModel();
		$is_true = I('is_true', 0);

		if ($is_true == 1) {
			$address_province = $_POST['config']['address']['province'];

			if ($address_province == '省份') {
				$this->message2('请正确填写省份信息!', 'type_add');
			}

			$address_province = $_POST['config']['address']['city'];

			if ($address_province == '地级市') {
				$this->message2('请正确填写地级市信息!', 'type_add');
			}

			$address_province = $_POST['config']['address']['district'];

			if ($address_province == '市、县级市') {
				$this->message2('请正确填写区县信息!', 'type_add');
			}
		}

		$_POST['config'] = json_encode($_POST['config']);
		$exp_id = I('exp_id', '');

		if ($exp_id == '') {
			$this->message2('请选择订单导出模板格式!', 'type_add');
		}

		$data = $type_model->create();

		if (!$data) {
			$this->message2($type_model->getError(), 'type_add');
		}

		$upload = new UploadFile();
		if (!empty($_FILES['file_url']) && ($_FILES['file_url']['name'] != '')) {
			$upload->maxSize = 3145728;
			$upload->allowExts = array('xls');
			$upload->savePath = 'Public/Uploads/kb/';
			$upload->saveRule = '';

			if ($upload->upload()) {
				$info = $upload->getUploadFileInfo();
			}
			else {
				$this->error($upload->getErrorMsg());
			}

			$data['file_url'] = $upload->savePath . $info[0]['savename'];
		}

		if (!empty($_FILES['image_url']) && ($_FILES['image_url']['name'] != '')) {
			$upload->maxSize = 3145728;
			$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
			$upload->savePath = 'Public/Uploads/kb/';

			if ($upload->upload()) {
				$info1 = $upload->getUploadFileInfo();
			}
			else {
				$this->error($upload->getErrorMsg());
			}

			$data['image_url'] = $upload->savePath . $info1[0]['savename'];
		}

		$data['title'] = I('name', '');
		$data['comm'] = I('comm', '');
		$data['state'] = I('state', 0);
		$data['sort_order'] = I('sort_order', 0);
		$data['config'] = $_POST['config'];
		$data['is_true'] = I('is_true', 0);

		if (false !== $type_model->data($data)->add()) {
			$this->message2('新增成功', 'type');
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
		$type = D('kongbao_type')->where('id=' . $id)->find();
		$config = json_decode($type['config'], true);
		$moban_list = M('exp_rule')->select();
		$this->assign('moban_list', $moban_list);
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
			import('ORG.Net.UploadFile');
			$model = new KBTypeModel();
			$exp_id = I('exp_id', '');

			if ($exp_id == '') {
				$this->message2('请选择订单导出模板格式!', 'type');
			}

			$is_update = I('is_update', 0);
			$is_true = I('is_true', 0);
			if (($is_update == 1) && ($is_true == 1)) {
				$address_province = $_POST['config']['address']['province'];

				if ($address_province == '省份') {
					$this->message2('请正确填写省份信息!', 'type');
				}

				$address_province = $_POST['config']['address']['city'];

				if ($address_province == '地级市') {
					$this->message2('请正确填写地级市信息!', 'type');
				}

				$address_province = $_POST['config']['address']['district'];

				if ($address_province == '市、县级市') {
					$this->message2('请正确填写区县信息!', 'type');
				}
			}
			else {
				$old_type = $model->where('id=' . $id)->find();
				$old_config = json_decode($old_type['config'], true);
				$_POST['config']['address']['province'] = $old_config['address']['province'];
				$_POST['config']['address']['city'] = $old_config['address']['city'];
				$_POST['config']['address']['district'] = $old_config['address']['district'];
			}

			$_POST['config'] = json_encode($_POST['config']);
			unset($_POST['is_update']);
			$data = $model->create();
			if (!empty($_FILES['file_url']) && ($_FILES['file_url']['name'] != '')) {
				$upload = new UploadFile();
				$upload->maxSize = 3145728;
				$upload->allowExts = array('xls');
				$upload->savePath = 'Public/Uploads/kb/';
				$upload->saveRule = '';

				if ($upload->upload()) {
					$info = $upload->getUploadFileInfo();
				}
				else {
					$this->error($upload->getErrorMsg());
				}

				$data['file_url'] = $upload->savePath . $info[0]['savename'];
			}

			if (!empty($_FILES['image_url']) && ($_FILES['image_url']['name'] != '')) {
				$upload1 = new UploadFile();
				$upload1->maxSize = 3145728;
				$upload1->allowExts = array('jpg', 'gif', 'png', 'jpeg');
				$upload1->savePath = 'Public/Uploads/kb/';

				if ($upload1->upload()) {
					$info1 = $upload1->getUploadFileInfo();
				}
				else {
					$this->error($upload1->getErrorMsg());
				}

				$data['image_url'] = $upload1->savePath . $info1[0]['savename'];
			}

			$data['config'] = $_POST['config'];
			$data['is_true'] = $is_true;

			if (false !== $model->where('id=' . $id)->save($data)) {
				$this->message('编辑成功', __URL__ . '/type');
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

			$count = M('kongbao')->where($where_e)->count();

			if (0 < $count) {
				$this->message('已经存在该类型的空包单号，系统将不能删除!', __URL__ . '/type');
			}

			$model = new KBTypeModel();

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
		$kongbao_config = json_decode($config['kongbao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$kb_model = new KongbaoViewModel();
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
		$starttime = I('starttime', '');
		$endtime = I('endtime', '');

		if ($starttime != '') {
			$_POST['starttime'] = $starttime;
		}

		if ($endtime != '') {
			$_POST['endtime'] = $endtime;
		}

		if ($starttime == '') {
			$starttime = '2014-01-01 00:00:00';
		}

		if ($endtime == '') {
			$endtime = '2099-12-30 23:59:59';
		}

		$where['addtime'] = array(
	'between',
	array($starttime, $endtime)
	);

		if ($_REQUEST['action'] == 'del') {
			$mod = new Model('kongbao');
			$delcount = $mod->where($where)->delete();
			$this->message2('共删除' . $delcount . '条！', __URL__ . '/index');
			exit();
		}

		$count = $kb_model->where($where)->count();
		$numsOfpage = $kongbao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$kongbao = $kb_model->where($where)->order('addtime desc, kongbao.id asc ')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('kongbao_list', $kongbao);
		$this->display();
	}

	public function edit()
	{
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$id = I('id', NULL);

		if (!empty($id)) {
			$kongbao = M('kongbao')->where('id=' . $id)->find();
			$this->assign('kongbao', $kongbao);
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
			$model = new KongbaoModel();
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
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'add');
		}

		$model = new KongbaoModel();

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
			$this->ajaxReturn('', '单据号不能为空！', 0);
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
			$v = trim($v);
			$where['note_no'] = $v;
			$count = M('kongbao')->where($where)->find();

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

		$kongbao = new KongbaoModel();

		if (!empty($insert_data)) {
			foreach ($insert_data as $data) {
				$id = $kongbao->data($data)->add();

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

	private function get_kongbao_type()
	{
		$type_model = new Model('kongbao_type');
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

			$kongbao = new KongbaoModel();

			if (false !== $kongbao->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/index');
			}
			else {
				$this->message('删除失败：' . $kongbao->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}

	public function used_index()
	{
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$kb_model = new KongbaoViewModel();
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

		$where['isused'] = 1;
		$starttime = I('starttime', '');
		$endtime = I('endtime', '');

		if ($starttime != '') {
			$_POST['starttime'] = $starttime;
		}

		if ($endtime != '') {
			$_POST['endtime'] = $endtime;
		}

		if ($starttime == '') {
			$starttime = '2014-01-01 00:00:00';
		}

		if ($endtime == '') {
			$endtime = '2099-12-30 23:59:59';
		}

		$where['addtime'] = array(
	'between',
	array($starttime, $endtime)
	);

		if ($_REQUEST['action'] == 'del') {
			$mod = new Model('kongbao');
			$delcount = $mod->where($where)->delete();
			$this->message2('共删除' . $delcount . '条！', __URL__ . '/used_index');
			exit();
		}

		$count = $kb_model->where($where)->count();
		$numsOfpage = $kongbao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$kongbao = $kb_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('kongbao_list', $kongbao);
		$this->display();
	}

	public function order_index()
	{
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		import('ORG.Util.Page');
		$model = new KongbaoOrderViewModel();
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

		$starttime = I('starttime', '');
		$endtime = I('endtime', '');

		if ($starttime != '') {
			$_POST['starttime'] = urldecode($starttime);
		}

		if ($endtime != '') {
			$_POST['endtime'] = urldecode($endtime);
		}

		if ($starttime == '') {
			$starttime = '2014-01-01 00:00:00';
		}

		if ($endtime == '') {
			$endtime = '2099-12-30 23:59:59';
		}

		$where['order_time'] = array(
	'between',
	array($starttime, $endtime)
	);
		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		if ($_REQUEST['action'] == '删除成功') {
			$mod = new Model('kongbao_order');
			$delcount = $mod->where($where)->delete();
			$this->message2('共删除' . $delcount . '条！', __URL__ . '/order_index');
			exit();
		}

		$count = $model->where($where)->count();
		$numsOfpage = $kongbao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$order = $model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$this->assign('show', urldecode($show));
		$this->assign('count', $count);
		$this->assign('order_list', $order);
		$this->display();
	}

	public function order_daili()
	{
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		import('ORG.Util.Page');
		$model = new KBOrderViewModel();
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

		$starttime = I('starttime', '');
		$endtime = I('endtime', '');

		if ($starttime != '') {
			$_POST['starttime'] = urldecode($starttime);
		}

		if ($endtime != '') {
			$_POST['endtime'] = urldecode($endtime);
		}

		if ($starttime == '') {
			$starttime = '2014-01-01 00:00:00';
		}

		if ($endtime == '') {
			$endtime = '2099-12-30 23:59:59';
		}

		$where['order_time'] = array(
	'between',
	array($starttime, $endtime)
	);
		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $model->where($where)->count();

		if ($_REQUEST['action'] == 'del') {
			$mod = new Model('kongbao_order');
			$delcount = $mod->where($where)->delete();
			$this->message2('共删除' . $delcount . '条！', __URL__ . '/order_daili');
			exit();
		}

		$numsOfpage = $kongbao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$order = $model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order);
		$this->display();
	}

	public function dd_index()
	{
		import('ORG.Util.Page');
		$model = new KBDidanViewModel();
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

		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$didan = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $didan);
		$this->display();
	}

	public function dd_edit()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未选择编辑项!', 'dd_index');
		}

		$didan_model = new KongbaoDidanViewModel();
		$where['id'] = $id;
		$didan = $didan_model->where($where)->find();
		$this->assign('didan', $didan);
		$this->display();
	}

	public function dd_update()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', 'dd_index');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定更新项!', 'dd_index');
		}

		$type = I('type', 2);
		$dd_image = I('didan_image', NULL);
		if (empty($dd_image) && ($type == 2)) {
			$this->message2('请填写底单截图下载地址!', U('dd_edit', array('id' => $id)));
		}

		$comm = I('comm', '');

		if ($type == 3) {
			if ($comm == '') {
				$this->message2('请填写失败原因!', U('dd_edit', array('id' => $id)));
			}
		}

		$update_array = array();
		$update_array['id'] = $id;
		$update_array['didan_image'] = $dd_image;
		$update_array['status'] = $type;
		$update_array['comm'] = $comm;
		$model = M('kongbao_didan');

		if (false !== $model->data($update_array)->save()) {
			$this->message2('底单处理成功!', 'dd_index');
		}
		else {
			$this->message2('底单处理失败：' . $model->getError(), 'dd_index');
		}
	}

	public function dd_uploads()
	{
		import('ORG.Util.Page');
		$model = new Model('didan_uploadfiles');
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

		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$didan = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $didan);
		$this->display();
	}

	public function dd_uploads_edit()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未选择编辑项!', 'dd_uploads');
		}

		$didan_model = new Model('didan_uploadfiles');
		$where['id'] = $id;
		$didan = $didan_model->where($where)->find();
		$this->assign('didan', $didan);
		$this->display();
	}

	public function dd_uploads_update()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', 'dd_uploads');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定更新项!', 'dd_uploads');
		}

		$type = I('type', 2);
		$outer_content = I('outer_content', '');
		$comm = I('comm', '');

		if ($type == 3) {
			if ($comm == '') {
				$this->message2('请填写失败原因!', 'dd_uploads');
			}
		}

		$update_array = array();
		$update_array['id'] = $id;
		$update_array['status'] = $type;
		$update_array['comm'] = $comm;
		$update_array['outer_content'] = $outer_content;
		$model = M('didan_uploadfiles');

		if (false !== $model->data($update_array)->save()) {
			$this->message2('表格底单处理成功!', 'dd_uploads');
		}
		else {
			$this->message2('表格底单处理失败：' . $model->getError(), 'dd_uploads');
		}
	}

	public function dd_uploads_del()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			if (is_array($id)) {
				$where = 'id  in (' . implode(',', $id) . ')';
				$where_a = 'id  in (' . implode(',', $id) . ') and status=2';
			}
			else {
				$where = 'id = ' . $id;
				$where_a = 'id = ' . $id . ' and status=2';
			}

			$model = new Model('didan_uploadfiles');
			$count = $model->where($where_a)->count();

			if (0 < $count) {
				$this->message('不能删除已经处理完成的底单', __URL__ . '/dd_uploads');
			}

			if (false !== $model->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/dd_uploads');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/dd_uploads');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/dd_uploads');
		}
	}

	public function dddocexp()
	{
		$id = I('id', '');
		$type_id = I('type', 1);
		$where['id'] = $id;
		$order = M('didan_uploadfiles')->where($where)->find();

		if ($type_id == 1) {
			$exp_content = $order['input_content'];
			$title = '申请内容';
		}
		else if ($type_id == 2) {
			$exp_content = $order['outer_content'];
			$title = '处理结果';
		}

		$html = '<html><head><title>' . $title . '</title></head><body>' . $exp_content . '</body></html>';
		$html = htmlspecialchars_decode($html);
		echo $html;
		exit();
	}

	public function uploadfiles()
	{
		import('ORG.Util.Page');
		$model = new UserFilesViewModel();
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

		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$didan = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $didan);
		$this->display();
	}

	public function down_daili()
	{
		$id = I('id', NULL);
		$where['id'] = $id;
		$uploadfile = M('uploadfiles')->where($where)->find();
		if (!empty($uploadfile) && ($uploadfile['fileurl'] != '')) {
			$file_name = $uploadfile['filename'] . '.xls';
			$user = M('user')->where('id=' . $uploadfile['user_id'])->find();

			if (!empty($user)) {
				$file_name = '[' . $user['username'] . ']' . $uploadfile['filename'] . '.xls';
			}

			$file_path = $uploadfile['fileurl'];
		}
		else {
			$this->message2('文件不存在', U('uploadfiles'));
		}

		$filedata = array();
		$filedata['id'] = $id;
		$filedata['downcounts'] = $uploadfile['downcounts'] + 1;
		$result = M('uploadfiles')->data($filedata)->save();
		$file_path = iconv('utf-8', 'gb2312', $file_path);
		import('ORG.Net.Http');
		$file_name = urlencode($file_name);
		$download = new Http();
		$download->download($file_path, $file_name);
	}

	public function file_edit()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定编辑项!', __APP__ . '/Admin');
		}

		$model = new UserFilesViewModel();
		$where['id'] = $id;
		$file = $model->where($where)->find();
		$this->assign('file', $file);
		$this->display();
	}

	public function file_update()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定编辑项!', __APP__ . '/Admin');
		}

		$deal_type = I('deal_type', 0);
		$comm = I('comm', '');

		if ($deal_type == 2) {
			if ($comm == '') {
				$this->message2('请填写失败原因!', U('file_edit', array('id' => $id)));
			}
		}

		$model = new Model('uploadfiles');
		$filedata = array();
		$filedata['id'] = $id;
		$filedata['status'] = $deal_type;
		$filedata['comm'] = $comm;
		$result = $model->data($filedata)->save();

		if ($result) {
			$this->message2('处理成功!', 'uploadfiles');
		}
		else {
			$this->message2('处理失败：' . $model->getError(), U('file_edit', array('id' => $id)));
		}
	}

	public function order_exp()
	{
		import('ORG.Util.Page');
		$model = new KongbaoOrderViewModel();
		$starttime = I('starttime', '');
		$endtime = I('endtime', '');

		if ($starttime != '') {
			$_POST['starttime'] = urldecode($starttime);
		}

		if ($endtime != '') {
			$_POST['endtime'] = urldecode($endtime);
		}

		$where['order_time'] = array(
	'between',
	array($starttime, $endtime)
	);
		$where['order_status'] = 1;
		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$submit = I('form_submit', '');
		$count = 0;
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$order = array();

		if ($submit == 'ok') {
			$kb_type = M('kongbao_type')->where('id=' . $type_id)->find();

			if ($kb_type['exp_id'] != '') {
				$count = $model->where($where)->count();
				$page = new Page($count, 15);
				$show = $page->show();
				$order = $model->where($where)->order('note_no asc,order_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				$this->assign('show', urldecode($show));
				$this->assign('count', $count);
				$this->assign('order_list', $order);
				$this->display();
				exit();
			}
			else {
				$this->message2('请先维护快递类型的导出模板格式!', U('type_edit', array('id' => $type_id)));
			}
		}

		$this->assign('show', '');
		$this->assign('count', 0);
		$this->assign('order_list', array());
		$this->display();
	}

	public function export()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$starttime = I('start', '');
		$endtime = I('end', '');
		$where['order_time'] = array(
	'between',
	array($starttime, $endtime)
	);
		$where['order_status'] = 1;
		$type_id = I('tid', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$model = new KongbaoOrderViewModel();
		$kb_type = M('kongbao_type')->where('id=' . $type_id)->find();

		if ($kb_type['exp_id'] != '') {
			$headers = $this->getHeaderFromFile_kb($kb_type['exp_id']);
			$fields = $this->getFieldFromFile_kb($kb_type['exp_id']);
			$exp_list_temp = M('kongbao_order')->field($fields)->where($where)->order('note_no asc,order_time desc')->select();
			$exp_list = array();
			$i = 0;

			foreach ($exp_list_temp as $k => $v) {
				$i++;

				if (isset($v['id'])) {
					$v['id'] = $i;
				}

				if (isset($v['type_id'])) {
					$v['type_id'] = $kb_type['name'];
				}

				$exp_list[] = $v;
			}

			$update_array = array();
			$update_array['exp_status'] = 1;
			M('kongbao_order')->where($where)->data($update_array)->save();
		}

		$order_counts = count($exp_list);
		$starttime_new = date('YmdHis', strtotime($starttime));
		$endtime_new = date('YmdHis', strtotime($endtime));
		$file_name = $kb_type['name'] . '-' . $order_counts . '-' . $starttime_new . '-' . $endtime_new;
		$fileurl = 'Public/Uploads/kb/kb_log/';
		MkdirAll($fileurl);
		$filename = $file_name . '.xls';
		$fileurl = $fileurl . md5($file_name) . '.xls';
		include 'Public/PHPExcel/PHPExcel.php';
		include 'Public/PHPExcel/PHPExcel/Writer/Excel5.php';
		include 'Public/PHPExcel/PHPExcel/Cell/DataType.php';
		$m_objPHPExcel = new PHPExcel();
		$this->write_xls($m_objPHPExcel, $fileurl, $headers, $exp_list);
		import('ORG.Net.Http');
		ob_end_clean();
		$download = new Http();
		$download->download($fileurl, $filename);
		exit();
	}

	public function dd_exp()
	{
		import('ORG.Util.Page');
		$model = new DidanViewModel();
		$starttime = I('starttime', '');
		$endtime = I('endtime', '');
		$where['status'] = 1;
		$where['addtime'] = array(
	'between',
	array($starttime, $endtime)
	);
		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$didan = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$submit = I('form_submit', '');
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $didan);
		$this->display();
	}

	public function dd_export()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$where['status'] = 1;
		$starttime = I('start', '');
		$endtime = I('end', '');
		$where['addtime'] = array(
	'between',
	array($starttime, $endtime)
	);
		$type_id = I('tid', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$model = new DidanViewModel();
		$kb_type = M('kongbao_type')->where('id=' . $type_id)->find();
		$geshi = $this->getHeaderFromFile();
		$headers = array($geshi['title']);
		$fields = implode(',', $geshi['name']);
		$exp_list = M('kongbao_didan')->field($fields)->where($where)->order('addtime desc')->select();
		$order_counts = count($exp_list);
		$starttime_new = date('YmdHis', strtotime($starttime));
		$endtime_new = date('YmdHis', strtotime($endtime));
		$file_name = $kb_type['name'] . '-' . $order_counts . '-' . $starttime_new . '-' . $endtime_new;
		$fileurl = 'Public/Uploads/kb/kbdd_log/';
		MkdirAll($fileurl);
		$filename = $file_name . '.xls';
		$fileurl = $fileurl . md5($file_name) . '.xls';
		include 'Public/PHPExcel/PHPExcel.php';
		include 'Public/PHPExcel/PHPExcel/Writer/Excel5.php';
		include 'Public/PHPExcel/PHPExcel/Cell/DataType.php';
		$m_objPHPExcel = new PHPExcel();
		$this->write_xls($m_objPHPExcel, $fileurl, $headers, $exp_list);
		import('ORG.Net.Http');
		ob_end_clean();
		$download = new Http();
		$download->download($fileurl, $filename);
		exit();
	}

	private function getHeaderFromFile()
	{
		$return_array = array();
		$fileds_array = require 'Public/ddexp_setting_config.php';

		foreach ($fileds_array as $k => $v) {
			$return_array['title'][] = $v['title'];
			$return_array['name'][] = $v['name'];
		}

		return $return_array;
	}

	public function rule_index()
	{
		$rule_list = M('exp_rule')->select();
		$this->assign('rule_list', $rule_list);
		$this->display();
	}

	public function rule_add()
	{
		$exp_field_file = 'Public/exp_fields.php';
		$filed_list = include $exp_field_file;
		$fields_comm = '';

		foreach ($filed_list as $k => $v) {
			if ((($k % 6) == 0) && (0 < $k)) {
				$fields_comm .= $k . '=' . $v['name'] . '<br/>';
			}
			else {
				$fields_comm .= $k . '=' . $v['name'] . ',';
			}
		}

		$fields_comm = rtrim($fields_comm, ',');
		$fields_comm = rtrim($fields_comm, '<br/>');
		$this->assign('fields_comm', $fields_comm);
		$this->display();
	}

	public function rule_insert()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$title = I('title', '');

		if ($title == '') {
			$this->message2('规则名称不能为空！', 'rule_add');
		}

		if (empty($_POST['rule_col'])) {
			$this->message2('请增加单元表格！', 'rule_add');
		}
		else {
			$exp_field_file = 'Public/exp_fields.php';
			$filed_list = include $exp_field_file;

			foreach ($filed_list as $key => $field) {
				$key_list[] = $key;
			}

			$rule_col = $_POST['rule_col'];

			foreach ($rule_col as $k => $v) {
				if (trim($v['name']) == '') {
					$this->message2('增加的单元格表头名不能为空！', 'rule_add');
					break;
				}

				$content = $v['content'];

				if (trim($content) == '') {
					$this->message2('增加的单元格显示项目不能为空！', 'rule_add');
					break;
				}

				$c_array = explode(',', $content);

				foreach ($c_array as $con) {
					if (!in_array($con, $key_list)) {
						$this->message2('显示项目中存在不合法的字段！', 'rule_add');
						break;
					}
				}
			}

			$rule_array = array();

			foreach ($rule_col as $rule) {
				$rule_array[] = $rule;
			}

			$data = array();
			$data['title'] = $title;
			$data['comm'] = $_POST['comm'];
			$data['exp_rule'] = json_encode($rule_array);
			$model = new Model('exp_rule');

			if (false !== $model->data($data)->add()) {
				$this->message2('规则新增成功！', 'rule_index');
			}
			else {
				$this->message2('规则新增失败：' . $model->getError(), 'rule_index');
			}
		}
	}

	public function rule_edit()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定编辑项', 'rule_index');
		}

		$rule = M('exp_rule')->where('id=' . $id)->find();

		if (empty($rule)) {
			$this->message2('未找到指定的编辑项', 'rule_index');
		}

		$exp_rule = json_decode($rule['exp_rule'], true);
		$cols_count = count($exp_rule);
		$this->assign('exp_rule', $exp_rule);
		$this->assign('cols_count', $cols_count);
		$this->assign('rule', $rule);
		$exp_field_file = 'Public/exp_fields.php';
		$filed_list = include $exp_field_file;
		$fields_comm = '';

		foreach ($filed_list as $k => $v) {
			if ((($k % 6) == 0) && (0 < $k)) {
				$fields_comm .= $k . '=' . $v['name'] . '<br/>';
			}
			else {
				$fields_comm .= $k . '=' . $v['name'] . ',';
			}
		}

		$fields_comm = rtrim($fields_comm, ',');
		$fields_comm = rtrim($fields_comm, '<br/>');
		$this->assign('fields_comm', $fields_comm);
		$this->display();
	}

	public function rule_update()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未指定更新项！', 'rule_index');
		}

		$url = U('rule_edit', array('id' => $id));
		$title = I('title', '');

		if ($title == '') {
			$this->message2('规则名称不能为空！', $url);
		}

		if (empty($_POST['rule_col'])) {
			$this->message2('请增加单元表格！', $url);
		}
		else {
			$exp_field_file = 'Public/exp_fields.php';
			$filed_list = include $exp_field_file;

			foreach ($filed_list as $key => $field) {
				$key_list[] = $key;
			}

			$rule_col = $_POST['rule_col'];

			foreach ($rule_col as $k => $v) {
				if (trim($v['name']) == '') {
					$this->message2('增加的单元格表头名不能为空！', $url);
					break;
				}

				$content = $v['content'];

				if (trim($content) == '') {
					$this->message2('增加的单元格显示项目不能为空！', $url);
					break;
				}

				$c_array = explode(',', $content);

				foreach ($c_array as $con) {
					if (!in_array($con, $key_list)) {
						$this->message2('显示项目中存在不合法的字段！', $url);
						break;
					}
				}
			}

			$rule_array = array();

			foreach ($rule_col as $rule) {
				$rule_array[] = $rule;
			}

			$data = array();
			$data['id'] = $id;
			$data['title'] = $title;
			$data['comm'] = $_POST['comm'];
			$data['exp_rule'] = json_encode($rule_array);
			$model = new Model('exp_rule');

			if (false !== $model->data($data)->save()) {
				$this->message2('规则修改成功！', 'rule_index');
			}
			else {
				$this->message2('规则修改失败：' . $model->getError(), $url);
			}
		}
	}

	public function rule_del()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			if (is_array($id)) {
				$where_e = 'exp_id in (' . implode(',', $id) . ')';
				$where = 'id in  (' . implode(',', $id) . ')';
			}
			else {
				$where_e = 'exp_id = ' . $id;
				$where = ' id = ' . $id;
			}

			$count = M('kongbao_type')->where($where_e)->count();

			if (0 < $count) {
				$this->message('该导出规则正在被使用，系统将不能删除!', __URL__ . '/rule_index');
			}

			$model = new Model('exp_rule');

			if (false !== $model->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/rule_index');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/rule_index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/rule_index');
		}
	}

	private function getHeaderFromFile_kb($rule_id)
	{
		$rule = M('exp_rule')->where('id=' . $rule_id)->find();
		$exp_rule = json_decode($rule['exp_rule'], true);
		$header = array();

		foreach ($exp_rule as $k => $v) {
			$header[] = $v['name'];
		}

		return array($header);
	}

	private function getFieldFromFile_kb($rule_id)
	{
		$rule = M('exp_rule')->where('id=' . $rule_id)->find();
		$exp_rule = json_decode($rule['exp_rule'], true);
		$exp_field_file = 'Public/exp_fields.php';
		$filed_list = include $exp_field_file;
		$filed_array = array();

		foreach ($filed_list as $key => $field) {
			$filed_array[$key] = $field['field'];
		}

		$fields = '';

		foreach ($exp_rule as $k => $v) {
			$content = explode(',', $v['content']);

			if (1 < count($content)) {
				$temp = 'concat(';
				$temp1 = '';

				if ($v['sign'] == '') {
					$v['sign'] = ' ';
				}

				foreach ($content as $con) {
					$temp1 .= $filed_array[$con] . ',\'' . $v['sign'] . '\',';
				}

				$temp1 = rtrim($temp1, '\'' . $v['sign'] . '\',');
				$temp .= $temp1 . ')';
				$fields .= ',' . $temp;
			}
			else {
				$fields .= ',' . $filed_array[$v['content']];
			}
		}

		$fields = ltrim($fields, ',');
		return $fields;
	}

	public function order_exp_new()
	{
		import('ORG.Util.Page');
		$model = new Model('exp_log');
		$last_time = M('kongbao_type')->max('last_down_time');

		if (empty($last_time)) {
			$last_time = '系统尚未进行过批量导出！';
		}
		else {
			$last_time = date('Y-m-d H:i:s', $last_time);
		}

		$count = $model->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$log_list = $model->order('exp_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list);
		$this->assign('last_time', $last_time);
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function order_exp_post()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$type_list = M('kongbao_type')->where('state=0')->order('is_true desc,id asc')->select();
		$exp_time = time();
		$exp_date = date('Ymd', $exp_time);
		$exp_datetime = date('Y-m-d H:i:s', $exp_time);
		$exp_date_new = date('YmdHis', $exp_time);
		$where = array();
		$where['exp_date'] = $exp_date;
		$sys_config = M('config')->where('id=1')->find();
		$sys_config = json_decode($sys_config['kongbao_config'], true);
		$count = M('exp_log')->where($where)->count();
		if ((0 < $count) && ($sys_config['exp_setting'] != 1)) {
			$this->ajaxReturn('', '今日已经做过批量导出！', 0);
		}

		include 'Public/PHPExcel/PHPExcel.php';
		include 'Public/PHPExcel/PHPExcel/Writer/Excel5.php';
		include 'Public/PHPExcel/PHPExcel/Cell/DataType.php';
		$output_path = 'Public/Uploads/kb_log/';
		MkdirAll($output_path);
		$post_type_id = I('type_id', '');

		foreach ($type_list as $k => $type) {
			if ($post_type_id != '') {
				if ($type['id'] != $post_type_id) {
					continue;
				}
			}

			$type_id = $type['id'];
			$type_name = $type['name'];
			$type['last_down_time'] = $type['last_down_time'] + 1;
			$last_datetime = date('Y-m-d H:i:s', $type['last_down_time']);
			$last_date = date('YmdHis', $type['last_down_time']);
			$where = array();
			$where['type_id'] = $type_id;
			$where['order_status'] = 1;
			$where['order_time'] = array(
	'between',
	array($last_datetime, $exp_datetime)
	);
			$headers = $this->getHeaderFromFile_kb($type['exp_id']);
			$fields = $this->getFieldFromFile_kb($type['exp_id']);
			$exp_list_temp = M('kongbao_order')->field($fields)->where($where)->order('note_no asc,order_time desc')->select();
			$exp_list = array();
			$i = 0;

			foreach ($exp_list_temp as $k => $v) {
				$i++;

				if (isset($v['id'])) {
					$v['id'] = $i;
				}

				if (isset($v['type_id'])) {
					$v['type_id'] = $type_name;
				}

				$exp_list[] = $v;
			}

			$order_counts = count($exp_list);
			$filename = $type_name . '-' . $order_counts . '-' . $last_date . '-' . $exp_date_new . '.xls';
			$fileurl = $output_path . md5($type_name . '-' . $order_counts . '-' . $last_date . '-' . $exp_date_new) . '.xls';
			$update_array = array();
			$update_array['exp_status'] = 1;
			M('kongbao_order')->where($where)->data($update_array)->save();
			$m_objPHPExcel = new PHPExcel();
			$this->write_xls($m_objPHPExcel, $fileurl, $headers, $exp_list);
			$logdata = array();
			$logdata['type_id'] = $type_id;
			$logdata['type_name'] = $type_name;
			$logdata['exp_counts'] = count($exp_list);
			$logdata['exp_filename'] = $filename;
			$logdata['exp_fileurl'] = $fileurl;
			$logdata['last_time'] = $last_datetime;
			$logdata['exp_time'] = $exp_datetime;
			$logdata['exp_date'] = $exp_date;
			M('exp_log')->data($logdata)->add();
			$updatedata = array();
			$updatedata['id'] = $type_id;
			$updatedata['last_down_time'] = $exp_time;
			M('kongbao_type')->data($updatedata)->save();
		}

		$this->ajaxReturn('', '执行完毕！', 1);
	}

	private function write_xls($m_objPHPExcel, $filename, $header, $data_list)
	{
		$m_objPHPExcel->setActiveSheetIndex(0);
		$col = 0;

		foreach ($header[0] as $header) {
			$m_objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $header);
			$col++;
		}

		$row = 2;

		if (!empty($data_list)) {
			foreach ($data_list as $data) {
				$col = 0;

				foreach ($data as $v) {
					if (is_numeric($v)) {
						$excel_col = $this->numToEn($col) . $row;
						$m_objPHPExcel->getActiveSheet()->setCellValueExplicit($excel_col, $v, PHPExcel_Cell_DataType::TYPE_STRING);
					}
					else {
						$m_objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $v);
					}

					$col++;
				}

				$row++;
			}
		}

		$objWriter = new PHPExcel_Writer_Excel5($m_objPHPExcel);
		$objWriter->save($filename);
	}

	public function downfile()
	{
		$id = I('id', '');

		if ($id == '') {
			$this->message2('未指定下载项!', U('order_exp_new'));
		}

		$file = M('exp_log')->where('id=' . $id)->find();

		if (empty($file)) {
			$this->message2('指定下载项不存在!', U('order_exp_new'));
		}

		$fileurl = $file['exp_fileurl'];
		$filename = $file['exp_filename'];

		if ($fileurl == '') {
			$this->message2('无效请求!', U('order_exp_new'));
		}

		$filepath = $fileurl;

		if (!file_exists($filepath)) {
			$this->message2('未找到对应的导出文件！', U('order_exp_new'));
		}

		$file_path = $filepath;
		import('ORG.Net.Http');
		ob_end_clean();
		$download = new Http();
		$download->download($file_path, $filename);
	}

	public function order_cancel()
	{
		$id = I('id', NULL);
		$result = true;

		if (!empty($id)) {
			if (is_array($id)) {
				foreach ($id as $order_id) {
					$rtn = order_cancel($order_id, '1');

					if (!$rtn) {
						$result = false;
					}
				}
			}
			else {
				$result = order_cancel($id, '1');
			}

			if ($result) {
				$this->message('订单取消成功！', __URL__ . '/order_index');
			}
			else {
				$this->message('部分或全部订单取消失败', __URL__ . '/order_index');
			}
		}
		else {
			$this->message('请选择要取消的订单', __URL__ . '/order_index');
		}
	}

	public function dlorder_cancel()
	{
		$id = I('id', NULL);
		$result = true;

		if (!empty($id)) {
			if (is_array($id)) {
				foreach ($id as $order_id) {
					$rtn = order_cancel($order_id, '2');

					if (!$rtn) {
						$result = false;
					}
				}
			}
			else {
				$result = order_cancel($id, '2');
			}

			if ($result) {
				$this->message('订单取消成功！', __URL__ . '/order_daili');
			}
			else {
				$this->message('部分或全部订单取消失败', __URL__ . '/order_daili');
			}
		}
		else {
			$this->message('请选择要取消的订单', __URL__ . '/order_daili');
		}
	}

	public function invalid_index()
	{
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_kongbao_type();
		$this->assign('type_list', $type_list);
		$kb_model = new KongbaoViewModel();
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

		$where['isused'] = 2;
		$starttime = I('starttime', '');
		$endtime = I('endtime', '');

		if ($starttime != '') {
			$_POST['starttime'] = urldecode($starttime);
		}

		if ($endtime != '') {
			$_POST['endtime'] = urldecode($endtime);
		}

		if ($starttime == '') {
			$starttime = '2014-01-01 00:00:00';
		}

		if ($endtime == '') {
			$endtime = '2099-12-30 23:59:59';
		}

		$where['addtime'] = array(
	'between',
	array($starttime, $endtime)
	);

		if ($_REQUEST['action'] == 'del') {
			$mod = new Model('kongbao');
			$delcount = $mod->where($where)->delete();
			$this->message2('共删除' . $delcount . '条！', __URL__ . '/invalid_index');
			exit();
		}

		$count = $kb_model->where($where)->count();
		$numsOfpage = $kongbao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$kongbao = $kb_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('kongbao_list', $kongbao);
		$this->display();
	}

	public function restore_use()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			if (is_array($id)) {
				$where = 'id  in (' . implode(',', $id) . ')';
			}
			else {
				$where = 'id = ' . $id;
			}

			$model = new Model('kongbao');
			$kongbao_data = array();
			$kongbao_data['isused'] = 0;
			$kongbao_data['order_type'] = 0;
			$kongbao_data['order_no'] = '';

			if (false !== $model->where($where)->data($kongbao_data)->save()) {
				$this->message('空包单号恢复使用成功', __URL__ . '/invalid_index');
			}
			else {
				$this->message('空包单号恢复使用失败：' . $model->getError(), __URL__ . '/invalid_index');
			}
		}
		else {
			$this->message('请选择要恢复使用的空包单号！', __URL__ . '/invalid_index');
		}
	}

	private function numToEn($num)
	{
		$asc = 0;
		$en = '';
		$num = (int) $num + 1;

		if ($num < 26) {
			if ((int) $num < 10) {
				$asc = ord($num);
				$en = chr($asc + 16);
			}
			else {
				$num_g = substr($num, 1, 1);
				$num_s = substr($num, 0, 1);
				$asc = ord($num_g);
				$en = chr($asc + 16 + (10 * $num_s));
			}
		}
		else {
			$num_complementation = floor($num / 26);
			$en_q = $this->numToEn($num_complementation);
			$en_h = (($num % 26) != 0 ? $this->numToEn($num - ($num_complementation * 26)) : 'A');
			$en = $en_q . $en_h;
		}

		return $en;
	}
}


?>
