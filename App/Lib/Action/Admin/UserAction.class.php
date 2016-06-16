<?php
//dezend by 辰梦大人 QQ:30881917
class UserAction extends CommonAction
{
	public function setting()
	{
		$config = M('config');
		$setting = $config->where('id=1')->find();
		$tixian_setting = json_decode($setting['tixian_setting'], true);
		$refer_mode = $setting['refer_mode'];
		$daili_refer_mode = $setting['daili_refer_mode'];
		$reg_setting = json_decode($setting['reg_setting'], true);
		$pay_rule = json_decode($setting['payrule_setting'], true);
		$mibao_setting = json_decode($setting['mibao_setting'], true);
		$this->assign('reg_setting', $reg_setting);
		$this->assign('tixian_setting', $tixian_setting);
		$this->assign('mibao_setting', $mibao_setting);
		$this->assign('refer_mode', $refer_mode);
		$this->assign('daili_refer_mode', $daili_refer_mode);
		$this->assign('pay_rule', $pay_rule);
		$user_level = M('user_level');
		$level = $user_level->select();
		$this->assign('user_level', $level);
		$this->display();
	}

	public function settingsave()
	{
		if (!IS_POST) {
			$this->message2('非法操作', 'setting');
		}

		$config = M('config');
		$reg_setting = I('reg_setting', '');
		$tixian_setting = I('tixian_setting', '');
		$data = $config->create();
		$data['reg_setting'] = json_encode($reg_setting);
		$data['tixian_setting'] = json_encode($tixian_setting);
		$data['refer_mode'] = I('refer_mode', 0);
		$data['daili_refer_mode'] = I('daili_refer_mode', 0);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message2('配置修改成功', 'setting');
		}
		else {
			$this->message2('配置修改失败：' . $config->getError(), 'setting');
		}
	}

	public function payrule_save()
	{
		if (!IS_POST) {
			$this->message2('非法操作', 'setting');
		}

		$config = M('config');
		$data = $config->create();
		$data['payrule_setting'] = json_encode($data['payrule_setting']);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message2('充值赠送配置修改成功', 'setting');
		}
		else {
			$this->message2('充值赠送配置修改失败：' . $config->getError(), 'setting');
		}
	}

	public function mibao_save()
	{
		if (!IS_POST) {
			$this->message2('非法操作', 'setting');
		}

		$config = M('config');
		$data = $config->create();
		$data['mibao_setting'] = json_encode($data['mibao_setting']);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message2('安全问题配置修改成功', 'setting');
		}
		else {
			$this->message2('安全问题配置修改失败：' . $config->getError(), 'setting');
		}
	}

	public function level()
	{
		$user_level = M('user_level');
		$level = $user_level->select();
		$level_list = array();

		if (!empty($level)) {
			foreach ($level as $k => $v) {
				$v['config'] = json_decode($v['config'], true);
				$level_list[] = $v;
			}
		}

		$this->assign('level_list', $level_list);
		$this->display();
	}

	public function level_edit()
	{
		$config = M('config')->where('id=1')->find();
		$daili_refer_mode = $config['daili_refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $daili_refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$this->assign('daili_refer_mode', $mode);
		$user_level = new UserLevelModel();
		$id = I('id', '');

		if ($id != '') {
			$data = $user_level->where('id=' . $id)->find();

			if (empty($data)) {
				$this->message2('你选择的会员级别不存在!', 'level');
			}

			$data['config'] = json_decode($data['config'], true);
		}
		else {
			$this->message2('请选择编辑项目!', 'level');
		}

		$this->assign('data', $data);
		$level = D('user_level')->where('id=' . $id)->find();
		$this->assign('config', $data['config']);
		$this->assign('level', $level);
		$this->display();
	}

	public function level_add()
	{
		$config = M('config')->where('id=1')->find();
		$daili_refer_mode = $config['daili_refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $daili_refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$this->assign('daili_refer_mode', $mode);
		$this->display();
	}

	public function level_insert()
	{
		if (!IS_POST) {
			$this->message2('非法操作', 'setting');
		}

		$user_level = new UserLevelModel();
		$_POST['config'] = json_encode($_POST['config']);

		if ($data = $user_level->create()) {
			$data['title'] = I('title', '');
			$data['comm'] = I('comm', '');
			$data['is_sys'] = 0;
			$data['sort_order'] = I('sort_order', 255);
			$data['config'] = $_POST['config'];
			if (!empty($_FILES['image_url']) && ($_FILES['image_url']['name'] != '')) {
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 3145728;
				$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
				$upload->savePath = 'Public/Uploads/level/';

				if ($upload->upload()) {
					$info = $upload->getUploadFileInfo();
				}
				else {
					$this->error($upload->getErrorMsg());
				}

				$data['image_url'] = $upload->savePath . $info[0]['savename'];
			}

			if (false !== $user_level->data($data)->add()) {
				$this->message2('新增成功', 'level');
			}
			else {
				$this->message2('新增失败：' . $user_level->getError(), 'level_add');
			}
		}
		else {
			$this->message2('新增失败：' . $user_level->getError(), 'level_add');
		}
	}

	public function level_update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$id = $_POST[id];

		if (!empty($id)) {
			$_POST['config'] = json_encode($_POST['config']);
			$model = new UserLevelModel();
			$data = $model->create();
			$data['config'] = $_POST['config'];
			if (!empty($_FILES['image_url']) && ($_FILES['image_url']['name'] != '')) {
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 3145728;
				$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
				$upload->savePath = 'Public/Uploads/level/';

				if ($upload->upload()) {
					$info = $upload->getUploadFileInfo();
				}
				else {
					$this->error($upload->getErrorMsg());
				}

				$data['image_url'] = $upload->savePath . $info[0]['savename'];
			}

			if (false !== $model->where('id=' . $id)->save($data)) {
				$this->message('编辑成功', __URL__ . '/level');
			}
			else {
				$this->message('编辑失败：' . $model->getError(), __URL__ . '/level');
			}
		}
		else {
			$this->message('请选择编辑对象', __URL__ . '/level');
		}
	}

	public function level_del()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$count = M('user')->where('user_type=' . $id)->count();

			if (0 < $count) {
				$this->message('已经存在该级别的会员，系统将不能删除!', __URL__ . '/level');
			}

			$model = new UserLevelModel();

			if (false !== $model->delete($id)) {
				$this->message('删除成功', __URL__ . '/level');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/level');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/level');
		}
	}

	public function add()
	{
		$level_model = M('user_level');
		$user_level = $level_model->order('sort_order asc')->select();
		$this->assign('level_list', $user_level);
		$this->display();
	}

	public function insert()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$User = new UserModel();
		$money = I('money', 0);

		if ($data = $User->create()) {
			$newuserid = $User->add();

			if (false !== $newuserid) {
				if (0 < $money) {
					$account_log['user_id'] = $newuserid;
					$account_log['stage'] = 'admin';
					$account_log['money'] = $money;
					$account_log['comm'] = '新增用户注入金额';
					$account_log['addtime'] = $this->getDate();
					$account_log['remain_money'] = $money;

					if (false !== D('account_log')->data($account_log)->add()) {
						$this->message('添加用户操作成功', __URL__ . '/index');
					}
					else {
						$this->message('添加用户操作失败', __URL__ . '/index');
					}
				}

				$this->message('添加用户操作成功', __URL__ . '/index');
			}
		}
		else {
			$this->message('添加用户失败：' . $User->getError(), __URL__ . '/add');
		}
	}

	public function index()
	{
		import('ORG.Util.Page');
		$type_model = new UserLevelModel();
		$type_list = $type_model->select();
		$user_model = new UserViewModel();
		$keyword = I('keyword');
		$ftype = I('ftype');
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			if (($ftype == 'id') || ($ftype == 'refer_id')) {
				$where[$ftype] = $keyword;
			}
			else {
				$where[$ftype] = array('like', '%' . $keyword . '%');
			}
		}

		$user_type = I('user_type', '');

		if ($user_type != '') {
			$where['user_type'] = $user_type;
		}

		$count = $user_model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$user = $user_model->where($where)->order('create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$user_list = array();

		if (!empty($user)) {
			foreach ($user as $k => $v) {
				$v['cwsz_config'] = json_decode($v['cwsz_config'], true);
				$user_list[] = $v;
			}
		}

		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('type_list', $type_list);
		$this->assign('user_list', $user_list);
		$this->display();
	}

	public function edit()
	{
		$userid = I('id', 0);
		$type_model = new UserLevelModel();
		$type_list = $type_model->order('sort_order asc')->select();
		$this->assign('type_list', $type_list);

		if (0 < $userid) {
			$user = new UserViewModel();
			$where['id'] = $userid;
			$data = $user->where($where)->find();
			$cwsz_config = array();

			if ($data['cwsz_config'] != '') {
				$cwsz_config = json_decode($data['cwsz_config'], true);
			}

			$this->assign('userdata', $data);
			$this->assign('cwsz_config', $cwsz_config);
			$this->display();
		}
		else {
			$this->message('未指定任何用户信息', __URL__ . '/index');
		}
	}

	public function repass()
	{
		$userid = I('id', 0);

		if (0 < $userid) {
			$user = new UserModel();
			$data = $user->getById($userid);
			$this->assign('data', $data);
			$this->display();
		}
		else {
			$this->message('未指定任何用户信息', __URL__ . '/index');
		}
	}

	public function passsave()
	{
		$user = new UserModel();
		$id = I('id', 0);

		if ($data = $user->create()) {
			if (0 < $id) {
				$data['password'] = md5($data['password']);

				if (false !== $user->where('id=\'' . $id . '\'')->data($data)->save()) {
					$this->message('操作成功', __URL__ . '/index');
				}
				else {
					$this->message('操作失败：' . $user->getDbError(), __URL__ . '/index');
				}
			}
			else {
				$this->message('请选择编辑用户', __URL__ . '/index');
			}
		}
		else {
			$this->message('操作失败：数据验证( ' . $user->getError() . ' )', __URL__ . '/index');
		}
	}

	public function addmoney()
	{
		$userid = I('id', NULL);
		$user = new UserModel();
		$data = $user->getById($userid);

		if (!empty($userid)) {
			$this->assign('data', $data);
			$this->display();
		}
		else {
			$this->message2('非法操作', __APP__ . '/Admin');
		}
	}

	public function moneysave()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$user_model = new UserModel();
		$user_account = new Model('account_log');
		$id = I('id', 0);

		if ($id <= 0) {
			$this->message('未指定会员信息!', __URL__ . '/index');
		}

		$user = $user_model->where('id=' . $id)->find();

		if (empty($user)) {
			$this->message('未找到指定会员信息!', __URL__ . '/index');
		}

		$account_type = I('account_type', 1);
		$bg_type = I('bg_type', 1);
		$addmoney = I('addmoney', 0);
		$reason = I('reason', '');

		if ($addmoney <= 0) {
			$this->message('请填写变动金额!', __URL__ . '/addmoney/id/' . $id);
		}

		if ($reason == '') {
			$this->message('请填写变更原因!', __URL__ . '/addmoney/id/' . $id);
		}

		if ($account_type == 1) {
			if ($bg_type == 1) {
				$money = $user['money'] + $addmoney;
				$content = '增加账户余额' . $addmoney;
			}
			else {
				$money = $user['money'] - $addmoney;
				$content = '减少账户余额' . $addmoney;
				$addmoney = 0 - $addmoney;
			}

			if ($money < 0) {
				$this->message('该人账户可用余额已不足以冲减!', __URL__ . '/addmoney/id/' . $id);
			}

			$data['money'] = $money;
		}
		else if ($account_type == 2) {
			if ($bg_type == 1) {
				$money = $user['refer_money'] + $addmoney;
				$content = '增加可用佣金' . $addmoney;
			}
			else {
				$money = $user['refer_money'] - $addmoney;
				$content = '减少可用佣金' . $addmoney;
				$addmoney = 0 - $addmoney;
			}

			$data['refer_money'] = $money;

			if ($money < 0) {
				$this->message('该人账户可用佣金已不足以冲减!', __URL__ . '/addmoney/id/' . $id);
			}
		}

		$user_model->startTrans();

		if (false !== $user_model->where('id=\'' . $id . '\'')->data($data)->save()) {
			$account_log['user_id'] = $id;
			$account_log['stage'] = 'admin';
			$account_log['money'] = $addmoney;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();

			if ($account_type == 1) {
				$account_log['remain_money'] = $money;
				$account_log['remain_refer_money'] = $user['refer_money'];
			}
			else if ($account_type == 2) {
				$account_log['remain_money'] = $user['money'];
				$account_log['remain_refer_money'] = $money;
			}

			if (false !== $user_account->data($account_log)->add()) {
				$user_model->commit();
				$content = '对会员' . $user['username'] . $content . ',若为本人操作，请忽略该条提醒！';
				send_fetion($content);
				$this->message('操作成功', __URL__ . '/index');
			}
			else {
				$user_model->rollback();
				$this->message('操作失败1：' . $user_account->getError(), __URL__ . '/addmoney/id/' . $id);
			}
		}
		else {
			$user_model->rollback();
			$this->message('操作失败2：' . $user_account->getDbError());
		}
	}

	public function account_log()
	{
		import('ORG.Util.Page');
		$account_log = new UserAccountViewModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			if ($ftype == 'user_id') {
				$where[$ftype] = $keyword;
			}
			else {
				$where[$ftype] = array('like', '%' . $keyword . '%');
			}
		}

		$stage = I('stage', '');

		if ($stage != '') {
			$where['stage'] = $stage;
		}

		$order_no = I('order_no', '');

		if ($order_no != '') {
			$where['order_no'] = $order_no;
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

		$where['addtime'] = array(
	'between',
	array($starttime, $endtime)
	);

		if ($_REQUEST['action'] == 'del') {
			$mod = new Model('account_log');

			if (false !== $mod->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/account_log');
			}
			else {
				$this->message('删除失败：' . $mod->getError(), __URL__ . '/account_log');
			}
		}

		$count = $account_log->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$log_list = $account_log->order('id desc')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('log_list', $log_list);
		$this->display();
	}

	public function tixian_index()
	{
		import('ORG.Util.Page');
		$tixian = new UserTixianViewModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
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

		$count = $tixian->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$tixian_list = $tixian->order('addtime desc')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$tixian_return = array();

		if (!empty($tixian_list)) {
			foreach ($tixian_list as $k => $v) {
				$cwsz_config = json_decode($v['cwsz_config'], true);
				$v['cwsz_config'] = $cwsz_config;
				$tixian_return[] = $v;
			}
		}

		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->assign('tixian_list', $tixian_return);
		$this->display();
	}

	public function tixian_edit()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$tixian = new UserTixianViewModel();
			$data = $tixian->where('tixian.id=' . $id)->find();
			$cwsz_config = json_decode($data['cwsz_config'], true);
			$this->assign('cwsz_config', $cwsz_config);
			$this->assign('tixian', $data);
			$this->display();
		}
		else {
			$this->message('非法操作', __APP__ . '/Admin');
		}
	}

	public function tixian_update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$tixian_model = new TixianModel();
		$deal_type = I('deal_type', 0);
		$tixian_data = array();
		$memberid = I('user_id', 0);

		if ($memberid == 0) {
			$this->message2('没有找到提现会员信息!', __URL__ . '/tixian_index');
		}

		$user = D('user')->where('id=' . $memberid)->find();
		$id = I('id', 0);

		if ($id == 0) {
			$this->message2('没有找到提现信息!', __URL__ . '/tixian_index');
		}

		$tixian = D('tixian')->where('id=' . $id)->find();

		if ($deal_type == 0) {
			$tixian_model->startTrans();
			$reason = '(提现失败冲回)：' . I('error_msg', '');
			$tixian_data['id'] = $id;
			$tixian_data['error_msg'] = I('error_msg', '');
			$tixian_data['status'] = 3;

			if (false !== $tixian_model->save($tixian_data)) {
				$userdata['id'] = $memberid;
				$userdata['refer_money'] = $user['refer_money'] + $tixian['money'];
				$userdata['invalid_money'] = $user['invalid_money'] - $tixian['money'];

				if (false !== D('user')->save($userdata)) {
					$account_log = array();
					$account_log['user_id'] = $memberid;
					$account_log['stage'] = 'return';
					$account_log['money'] = $tixian['money'];
					$account_log['comm'] = $reason;
					$account_log['addtime'] = $this->getDate();
					$account_log['remain_money'] = $user['money'];
					$account_log['remain_refer_money'] = $userdata['refer_money'];
					D('account_log')->data($account_log)->add();
					$tixian_model->commit();
					$this->message('操作成功！', __URL__ . '/tixian_index');
				}
				else {
					$tixian_model->rollback();
					$this->message('冲回账户失败！', __URL__ . '/tixian_index');
				}
			}
			else {
				$tixian_model->rollback();
				$this->message('操作失败：' . $tixian_model->getDbError(), __URL__ . '/tixian_index');
			}
		}

		if ($deal_type == 1) {
			$tixian_data['id'] = $id;
			$tixian_data['status'] = 2;

			if (false !== $tixian_model->save($tixian_data)) {
				$userdata['id'] = $memberid;
				$userdata['invalid_money'] = $user['invalid_money'] - $tixian['money'];

				if (false !== D('user')->save($userdata)) {
					$this->message('操作成功！', __URL__ . '/tixian_index');
				}
				else {
					$this->message('调整账户失败！', __URL__ . '/tixian_index');
				}
			}
			else {
				$this->message('操作失败：' . $tixian_model->getError(), __URL__ . '/tixian_index');
			}
		}
	}

	public function user_log()
	{
		import('ORG.Util.Page');
		$log_model = new UserLogViewModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		$where = array();
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$count = $log_model->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$log_list = $log_model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('log_list', $log_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->display();
	}

	public function user_log_del()
	{
		$model = new Model('user_log');

		if (IS_POST) {
			$ids = I('del_id', '');

			if ($ids == '') {
				$this->message2('请选择要删除的信息!', __URL__ . '/user_log');
			}

			$where = 'id in (' . implode(',', $ids) . ')';
		}
		else {
			$id = I('id', '');

			if ($id == '') {
				$this->message2('请选择要删除的信息!', __URL__ . '/user_log');
			}
			else {
				$where = 'id =' . $id;
			}
		}

		if (false !== $model->where($where)->delete()) {
			$this->message('日志删除成功！', __URL__ . '/user_log');
		}
		else {
			$this->message('日志删除失败：' . $model->getError(), __URL__ . '/user_log');
		}
	}

	public function del()
	{
		$user_id = I('id', 0);

		if (0 < $user_id) {
			$user = new UserModel();

			if (false !== $user->delete($user_id)) {
				$this->message('操作成功', __URL__ . '/index');
			}
			else {
				$this->message('操作失败：' . $user->getDbError());
			}
		}
		else {
			$this->message('请选择删除用户', __URL__ . '/index');
		}
	}

	public function getDate()
	{
		return date('Y-m-d H:i:s');
	}

	public function update()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Admin');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('无效参数!', __APP__ . '/Admin');
		}

		$userdata = array();
		$userdata['id'] = $id;
		$userdata['isvalid'] = I('isvalid', 1);
		$userdata['user_qq'] = I('user_qq', '');
		$userdata['user_type'] = I('user_type', 1);
		$refer_id = I('refer_id', 0);
		$refer_user = M('user')->where('id=' . $refer_id)->find();

		if (empty($refer_user)) {
			$refer_id = 0;
		}

		$userdata['refer_id'] = $refer_id;
		$user_model = new Model('user');
		$result = $user_model->data($userdata)->save();

		if (false !== $result) {
			$this->message('操作成功', 'index');
		}
		else {
			$this->message('操作失败：' . $user_model->getError(), 'index');
		}
	}

	public function user_del()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求!', __APP__ . '/Admin');
		}

		$days = I('days', 0);
		$model = new Model();
		$user_model = new Model('user');
		$current_time = time();
		$before7time = $current_time - ($days * 24 * 3600);
		$before7date = date('Y-m-d H:i:s', $before7time);
		$where = array();
		$where['create_time'] = array('lt', $before7date);
		$user_list = $user_model->where($where)->order('id asc')->select();
		$user_del = '';
		$del_count = 0;

		foreach ($user_list as $k => $user) {
			$where = array();
			$where['user_id'] = $user['id'];
			$where['type'] = 1;
			$counts = M('pay_order')->where($where)->count();
			$count1 = M('account_log')->where('user_id=' . $user['id'])->count();
			$refer_count = $user_model->where('refer_id=' . $user['id'])->count();
			if ((0 < $refer_count) || (0 < $count1) || (0 < $user['money']) || (0 < $user['used_money']) || (0 < $user['refer_money']) || (0 < $user['invalid_money'])) {
				continue;
			}

			if ($counts <= 0) {
				$sql = 'insert into user_del select * from user where id=' . $user['id'];
				M()->query($sql);
				$user_model->where('id=' . $user['id'])->delete();
				$user_del .= '[id:' . $user['id'] . 'name:' . $user['username'] . ']';
				$del_count++;
			}
		}

		if (0 < $del_count) {
			$logdata = array();
			$logdata['user_id'] = session('adminid');
			$logdata['opt_type'] = '删除' . $days . '天前注册未曾有过账户变动记录的会员';
			$logdata['comm'] = $user_del;
			$logdata['opt_ip'] = get_client_ip();
			$logdata['addtime'] = date('Y-m-d H:i:s', time());
			M('admin_log')->data($logdata)->add();
		}

		$url = U('index');
		$this->ajaxReturn($url, '清理成功', 1);
	}
}


?>
