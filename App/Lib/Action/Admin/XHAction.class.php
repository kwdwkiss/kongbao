<?php
//dezend by 辰梦大人 QQ:30881917
class XHAction extends CommonAction
{
	public function type()
	{
		$model = M('xiaohao_type');
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
		$moban_list = M('imp_rule_xh')->select();
		$this->assign('moban_list', $moban_list);
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

		$type_model = new XHTypeModel();
		$_POST['config'] = json_encode($_POST['config']);
		$imp_id = I('imp_id', '');

		if ($imp_id == '') {
			$this->message2('请选择小号导入规则!', 'type_add');
		}

		if ($data = $type_model->create()) {
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
		$moban_list = M('imp_rule_xh')->select();
		$this->assign('moban_list', $moban_list);
		$config = M('config')->where('id=1')->find();
		$refer_mode = $config['refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$this->assign('refer_mode', $mode);
		$type = D('xiaohao_type')->where('id=' . $id)->find();
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
			$model = new XHTypeModel();
			$_POST['config'] = json_encode($_POST['config']);
			$imp_id = I('imp_id', '');

			if ($imp_id == '') {
				$this->message2('请选择小号导入规则!', 'type');
			}

			if ($data = $model->create()) {
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

			$count = M('xiaohao')->where($where_e)->count();

			if (0 < $count) {
				$this->message('已经存在该类型的小号，系统将不能删除!', __URL__ . '/type');
			}

			$model = new XHTypeModel();

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
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_xiaohao_type();
		$this->assign('type_list', $type_list);
		$xh_model = new XiaohaoViewModel();
		$keyword = I('keyword');
		$ftype = I('ftype');
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$where['isused'] = 0;
		$count = $xh_model->where($where)->count();
		$numsOfpage = $xiaohao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$xiaohao_temp = $xh_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$xiaohao = array();

		foreach ($xiaohao_temp as $k => $v) {
			if ($v['encry_key'] != '') {
				$v['note_no'] = '***' . str_replace('...', '***', cut_str(encrypt($v['note_no'], 'D', $v['encry_key']), 30, 3));
				$v['account'] = '***' . str_replace('...', '***', cut_str(encrypt($v['account'], 'D', $v['encry_key']), 3, 2));
				$v['email'] = '***' . str_replace('...', '***', cut_str(encrypt($v['email'], 'D', $v['encry_key']), 10, 3));
			}
			else {
				$v['note_no'] = '***' . str_replace('...', '***', cut_str($v['note_no'], 30, 3));
				$v['account'] = '***' . str_replace('...', '***', cut_str($v['account'], 3, 2));
				$v['email'] = '***' . str_replace('...', '***', cut_str($v['email'], 10, 3));
			}

			$xiaohao[] = $v;
		}

		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('xiaohao_list', $xiaohao);
		$this->display();
	}

	public function edit()
	{
		$type_list = $this->get_xiaohao_type();
		$this->assign('type_list', $type_list);
		$id = I('id', NULL);

		if (!empty($id)) {
			$xiaohao = M('xiaohao')->where('id=' . $id)->find();

			if ($xiaohao['encry_key'] != '') {
				$xiaohao['note_no'] = encrypt($xiaohao['note_no'], 'D', $xiaohao['encry_key']);
				$xiaohao['account'] = encrypt($xiaohao['account'], 'D', $xiaohao['encry_key']);
				$xiaohao['password'] = encrypt($xiaohao['password'], 'D', $xiaohao['encry_key']);
				$xiaohao['email'] = encrypt($xiaohao['email'], 'D', $xiaohao['encry_key']);
				$xiaohao['email_pass'] = encrypt($xiaohao['email_pass'], 'D', $xiaohao['encry_key']);
				$xiaohao['pay_pass'] = encrypt($xiaohao['pay_pass'], 'D', $xiaohao['encry_key']);
				$xiaohao['shenfenzheng'] = encrypt($xiaohao['shenfenzheng'], 'D', $xiaohao['encry_key']);
				$xiaohao['truename'] = encrypt($xiaohao['truename'], 'D', $xiaohao['encry_key']);
				$xiaohao['bank_account'] = encrypt($xiaohao['bank_account'], 'D', $xiaohao['encry_key']);
				$xiaohao['bank_yue'] = encrypt($xiaohao['bank_yue'], 'D', $xiaohao['encry_key']);
				$xiaohao['pay_account'] = encrypt($xiaohao['pay_account'], 'D', $xiaohao['encry_key']);
				$xiaohao['mobile_phone'] = encrypt($xiaohao['mobile_phone'], 'D', $xiaohao['encry_key']);
			}

			$this->assign('xiaohao', $xiaohao);
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
			$model = new XiaohaoModel();

			if ($data = $model->create()) {
				if (false !== $model->save()) {
					$this->message('编辑成功', __URL__ . '/index');
				}
				else {
					$this->message('编辑失败：' . $model->getError(), __URL__ . '/index');
				}
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
		$type_list = $this->get_xiaohao_type();
		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'add');
		}

		$config = M('config')->where('id=1')->find();
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$encry_key = '';

		if ($xiaohao_config['encry_key'] != '') {
			$encry_key = $xiaohao_config['encry_key'];
		}

		$model = new XiaohaoModel();

		if ($data = $model->create()) {
			$type_id = I('type_id', '');
			if (($type_id == '') || ($type_id == 0)) {
				$this->message2('请选择小号类型', 'add');
			}

			$xh_type = M('xiaohao_type')->where('id=' . $type_id)->find();
			$note_no = I('note_no', '');

			if ($note_no == '') {
				$this->message2('小号不能为空！', 'add');
			}

			$imp_field_file = 'Public/xhimp_fields.php';
			$filed_list = include $imp_field_file;

			foreach ($filed_list as $key => $field) {
				$key_list[$key] = $field['field'];
			}

			$xh_data = $this->parseToData($note_no, $xh_type['imp_id'], $key_list);

			if ($xh_data['result'] == 'succ') {
				if (!empty($xh_data['data'])) {
					foreach ($xh_data['data'] as $k => $v) {
						if ($encry_key != '') {
							$data[$k] = encrypt($v, 'E', $encry_key);
						}
						else {
							$data[$k] = $v;
						}
					}

					$data['encry_key'] = $encry_key;
				}
			}
			else {
				$this->message2('数据错误:' . $xh_data['comm'], 'add');
			}

			if ($encry_key != '') {
				$data['note_no'] = encrypt($note_no, 'E', $encry_key);
			}

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

		$config = M('config')->where('id=1')->find();
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$imp_field_file = 'Public/xhimp_fields.php';
		$filed_list = include $imp_field_file;

		foreach ($filed_list as $key => $field) {
			$key_list[$key] = $field['field'];
		}

		$note_nos = I('note_nos', NULL);
		$type_id = I('type_id', NULL);

		if (empty($note_nos)) {
			$this->ajaxReturn('', '小号不能为空！', 0);
		}

		$note_nos = trim($note_nos);

		if ($note_nos == '') {
			$this->ajaxReturn('', '小号不能为空！', 0);
		}

		$error_msg = array();
		$note_nos = preg_replace('/([\\s]{2,})/', "\n", $note_nos);
		$note_array = explode("\n", $note_nos);
		$error_count = 0;
		$succ_count = 0;
		$insert_data = array();
		$return_array = array();
		$counts = count($note_array);
		$xh_type = M('xiaohao_type')->where('id=' . $type_id)->find();

		foreach ($note_array as $k => $v) {
			$encry_key = '';

			if ($xiaohao_config['encry_key'] != '') {
				$encry_key = $xiaohao_config['encry_key'];
			}

			$where = array();
			$where['note_no'] = $v;
			$count = M('xiaohao')->where($where)->find();

			if (0 < $count) {
				$error_count++;
				$error_msg[] = $v . '已存在';
				continue;
			}

			$temp_data = array();
			$temp_data['note_no'] = $v;

			if ($encry_key != '') {
				$temp_data['note_no'] = encrypt($v, 'E', $encry_key);
			}

			$temp_data['type_id'] = $type_id;
			$temp_data['type'] = 1;
			$temp_data['isused'] = 0;
			$temp_data['addtime'] = $this->getDate();
			$xh_data = $this->parseToData($v, $xh_type['imp_id'], $key_list);

			if ($xh_data['result'] == 'succ') {
				if (!empty($xh_data['data'])) {
					foreach ($xh_data['data'] as $k => $v) {
						if ($encry_key != '') {
							$temp_data[$k] = encrypt($v, 'E', $encry_key);
						}
						else {
							$temp_data[$k] = $v;
						}
					}

					$temp_data['encry_key'] = $encry_key;
				}
			}
			else {
				$error_msg[] = $v . '数据错误:' . $xh_data['comm'];
				$error_count++;
				continue;
			}

			$insert_data[] = $temp_data;
		}

		$xiaohao = new XiaohaoModel();

		if (!empty($insert_data)) {
			foreach ($insert_data as $data) {
				$id = $xiaohao->data($data)->add();

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

			$xiaohao = new XiaohaoModel();

			if (false !== $xiaohao->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/index');
			}
			else {
				$this->message('删除失败：' . $xiaohao->getError(), __URL__ . '/index');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/index');
		}
	}

	public function used_index()
	{
		$config = M('config')->where('id=1')->find();
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		import('ORG.Util.Page');
		$type_list = $this->get_xiaohao_type();
		$this->assign('type_list', $type_list);
		$xh_model = new XiaohaoViewModel();
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
		$count = $xh_model->where($where)->count();
		$numsOfpage = $xiaohao_config['numsofpage'];

		if ($numsOfpage == '') {
			$numsOfpage = 30;
		}

		$page = new Page($count, $numsOfpage);
		$show = $page->show();
		$xiaohao_temp = $xh_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$xiaohao = array();

		foreach ($xiaohao_temp as $k => $v) {
			if ($v['encry_key'] != '') {
				$v['note_no'] = '***' . str_replace('...', '***', cut_str(encrypt($v['note_no'], 'D', $v['encry_key']), 30, 3));
				$v['account'] = '***' . str_replace('...', '***', cut_str(encrypt($v['account'], 'D', $v['encry_key']), 3, 2));
				$v['email'] = '***' . str_replace('...', '***', cut_str(encrypt($v['email'], 'D', $v['encry_key']), 10, 3));
			}
			else {
				$v['note_no'] = '***' . str_replace('...', '***', cut_str($v['note_no'], 30, 3));
				$v['account'] = '***' . str_replace('...', '***', cut_str($v['account'], 3, 2));
				$v['email'] = '***' . str_replace('...', '***', cut_str($v['email'], 10, 3));
			}

			$xiaohao[] = $v;
		}

		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('xiaohao_list', $xiaohao);
		$this->display();
	}

	public function order_index()
	{
		import('ORG.Util.Page');
		$model = new XHOrderViewModel();
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
		$order = $model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_xiaohao_type();
		$this->assign('type_list', $type_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order);
		$this->display();
	}

	private function get_xiaohao_type()
	{
		$type_model = new Model('xiaohao_type');
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

	public function rule_index()
	{
		$rule_list = M('imp_rule_xh')->select();
		$this->assign('rule_list', $rule_list);
		$this->display();
	}

	public function rule_add()
	{
		$imp_field_file = 'Public/xhimp_fields.php';
		$filed_list = include $imp_field_file;
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
			$imp_field_file = 'Public/xhimp_fields.php';
			$filed_list = include $imp_field_file;

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
			$data['separator'] = $_POST['separator'];
			$data['imp_rule'] = json_encode($rule_array);
			$model = new Model('imp_rule_xh');

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

		$rule = M('imp_rule_xh')->where('id=' . $id)->find();

		if (empty($rule)) {
			$this->message2('未找到指定的编辑项', 'rule_index');
		}

		$imp_rule = json_decode($rule['imp_rule'], true);
		$cols_count = count($imp_rule);
		$this->assign('imp_rule', $imp_rule);
		$this->assign('cols_count', $cols_count);
		$this->assign('rule', $rule);
		$imp_field_file = 'Public/xhimp_fields.php';
		$filed_list = include $imp_field_file;
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
			$imp_field_file = 'Public/xhimp_fields.php';
			$filed_list = include $imp_field_file;

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
			$data['separator'] = $_POST['separator'];
			$data['imp_rule'] = json_encode($rule_array);
			$model = new Model('imp_rule_xh');

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
				$where_e = 'imp_id in (' . implode(',', $id) . ')';
				$where = 'id in  (' . implode(',', $id) . ')';
			}
			else {
				$where_e = 'imp_id = ' . $id;
				$where = ' id = ' . $id;
			}

			$count = M('xiaohao_type')->where($where_e)->count();

			if (0 < $count) {
				$this->message('该导入规则正在被使用，系统将不能删除!', __URL__ . '/rule_index');
			}

			$model = new Model('imp_rule_xh');

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

	private function parseToData($note_no, $imp_id, $key_list)
	{
		$data = array();
		$data['result'] = 'succ';
		$data['data'] = array();
		$imp_rule = M('imp_rule_xh')->where('id=' . $imp_id)->find();

		if (!empty($imp_rule)) {
			$separator = $imp_rule['separator'];

			if ($separator == '') {
				$separator = '|';
			}

			$note_array = array();
			$note_array = explode($separator, $note_no);

			if ($imp_rule['imp_rule'] != '') {
				$rule = json_decode($imp_rule['imp_rule'], true);

				if (count($rule) != count($note_array)) {
					$data['result'] = 'fail';
					$data['comm'] = '小号数据与格式中项目数不符!';
				}

				$rule_data = array();

				foreach ($rule as $k => $v) {
					$note_data_temp = $note_array[$k];

					if ($key_list[$v['content']] == 'email') {
						if (!ereg('^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\\.[a-zA-Z0-9_-])+', $note_data_temp)) {
							$data['result'] = 'fail';
							$data['comm'] = '电子邮箱格式错误!';
						}
					}

					if ($key_list[$v['content']] == 'bank_yue') {
						if (!is_numeric($note_data_temp)) {
							$data['result'] = 'fail';
							$data['comm'] = '银行账户余额不为数字!';
						}
					}

					$rule_data[$key_list[$v['content']]] = $note_array[$k];
				}

				$data['data'] = $rule_data;
			}
		}

		return $data;
	}

	public function xhExport()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Admin');
		}

		$model = new Model('xiaohao');
		$id = $_POST['id'];

		if (!empty($id)) {
			$where = 'id in (' . implode(',', $id) . ')';
			$xh_list = $model->where($where)->order('id asc')->select();
			$exp_content = '';
			$count = 0;

			foreach ($xh_list as $k => $xiaohao) {
				if ($xiaohao['encry_key'] != '') {
					$exp_content .= encrypt($xiaohao['note_no'], 'D', $xiaohao['encry_key']) . "\r\n";
				}
				else {
					$exp_content .= $xiaohao['note_no'] . "\r\n";
				}

				$count++;
			}

			$file_name = 'xiaohaoexport_' . $count . '.txt';
			Header('Content-type:   application/octet-stream ');
			Header('Accept-Ranges:   bytes ');
			header('Content-Disposition:   attachment;   filename=' . $file_name);
			header('Expires:   0 ');
			header('Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 ');
			header('Pragma:   public ');
			echo $exp_content;
			exit();
		}
		else {
			$this->message2('尚未选择要导出的小号信息', 'index');
		}
	}
}


?>
