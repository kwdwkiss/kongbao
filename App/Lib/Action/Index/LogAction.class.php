<?php
//dezend by 辰梦大人 QQ:30881917
class LogAction extends CommonAction
{
	public function _initialize()
	{
		parent::_initialize();

		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登录!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}
	}

	public function pay_order()
	{
		import('ORG.Util.Page');
		$model = M('pay_order');
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$where['user_id'] = session('userid');
		$where['type'] = 1;
		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$order_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order_list);
		$this->display();
	}

	public function cz_order()
	{
		import('ORG.Util.Page');
		$model = M('pay_order');
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$where['user_id'] = session('userid');
		$where['type'] = 1;
		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$order_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order_list);
		$this->display();
	}

	public function zs_log()
	{
		import('ORG.Util.Page');
		$model = M('pay_order');
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$where['user_id'] = session('userid');
		$where['type'] = 1;
		$where['order_type'] = 2;
		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$order_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order_list);
		$this->display();
	}

	public function cw_log()
	{
		import('ORG.Util.Page');
		$model = M('account_log');
		$where['user_id'] = session('userid');
		$stage = I('stage', '');

		if ($stage != '') {
			$where['stage'] = $stage;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$log_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list);
		$this->display();
	}

	public function yj_log()
	{
		import('ORG.Util.Page');
		$model = M('account_log');
		$where['user_id'] = session('userid');
		$stage = I('stage', '');

		if ($stage != '') {
			$where['stage'] = $stage;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$log_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list);
		$this->display();
	}

	public function buy_order()
	{
		import('ORG.Util.Page');
		$model = M('pay_order');
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$where['user_id'] = session('userid');
		$where['type'] = 0;
		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$order_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order_list);
		$this->display();
	}

	public function xf_order()
	{
		import('ORG.Util.Page');
		$model = M('pay_order');
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$where['user_id'] = session('userid');
		$where['type'] = 0;
		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$order_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('order_list', $order_list);
		$this->display();
	}

	public function tixian_log()
	{
		import('ORG.Util.Page');
		$model = new Model('tixian');
		$where['user_id'] = session('userid');
		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$tixian_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$tixian = array();

		foreach ($tixian_list as $k => $v) {
			$v['cwsz_config'] = json_decode($v['cwsz_config'], true);
			$tixian[] = $v;
		}

		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('tixian_list', $tixian);
		$this->display();
	}

	public function tixia_log()
	{
		import('ORG.Util.Page');
		$model = new Model('tixian');
		$where['user_id'] = session('userid');
		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$tixian_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$tixian = array();

		foreach ($tixian_list as $k => $v) {
			$v['cwsz_config'] = json_decode($v['cwsz_config'], true);
			$tixian[] = $v;
		}

		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('tixian_list', $tixian);
		$this->display();
	}

	public function kb_log()
	{
		import('ORG.Util.Page');
		$log_model = new Model('kongbao_order');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['status'] = 1;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $log_model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$log_list = $log_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$log_array = array();

		foreach ($log_list as $k => $log) {
			$log['address_send'] = '发货人:' . $log['send_name'] . ',手机:' . $log['send_shouji'] . ',' . $log['send_province'] . '-' . $log['send_city'] . $log['send_district'];
			$log['address_rec'] = $log['rec_name'] . ',' . $log['rec_shouji'] . ',' . $log['rec_phone'] . ',' . $log['rec_address'] . $log['rec_zipcode'];
			$log_array[] = $log;
		}

		$kb_type_list = M('kongbao_type')->select();
		$kb_type_array = array();

		foreach ($kb_type_list as $k => $v) {
			$kb_type_array[$v['id']] = $v['name'];
		}

		$this->assign('kb_type', $kb_type_array);
		$this->assign('type_list', $kb_type_list);
		$this->assign('count', $count);
		$this->assign('log_list', $log_array);
		$this->assign('show', $show);
		$this->display();
	}

	public function kbpf_log()
	{
		import('ORG.Util.Page');
		$log_model = new Model('kongbao_daili_order');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['status'] = 1;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $log_model->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$log_list = $log_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$kb_type_list = M('kongbao_type')->select();
		$kb_type_array = array();

		foreach ($kb_type_list as $k => $v) {
			$kb_type_array[$v['id']] = $v['name'];
		}

		$this->assign('kb_type', $kb_type_array);
		$this->assign('type_list', $kb_type_list);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list);
		$this->assign('show', $show);
		$this->display();
	}

	public function didan_log()
	{
		import('ORG.Util.Page');
		$log_model = new Model('kongbao_didan');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$count = $log_model->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$log_list = $log_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$log_array = array();

		foreach ($log_list as $k => $log) {
			$log['address_send'] = '发货地址:' . $log['send_addr'] . '，' . $log['send_zipcode'] . '，' . $log['send_name'] . '，' . $log['send_shouji'] . '，' . $log['send_phone'];
			$log['address_rec'] = '收货地址:' . $log['rec_name'] . '，' . $log['rec_shouji'] . '，' . $log['rec_phone'] . '，' . $log['rec_addr'] . '，' . $log['rec_zipcode'];
			$log_array[] = $log;
		}

		$kb_type_list = M('kongbao_type')->select();
		$kb_type_array = array();

		foreach ($kb_type_list as $k => $v) {
			$kb_type_array[$v['id']] = $v['name'];
		}

		$this->assign('kb_type', $kb_type_array);
		$this->assign('type_list', $kb_type_list);
		$this->assign('count', $count);
		$this->assign('log_list', $log_array);
		$this->assign('show', $show);
		$this->display();
	}

	public function dhexp()
	{
		$order_no = I('id', NULL);
		$yw_type = I('type', '1');

		if (empty($order_no)) {
			$this->message2('无效请求!', 'kbpf_log');
		}

		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['order_no'] = $order_no;
		$where['order_status'] = 1;

		if ($yw_type == 1) {
			$order = M('kongbao_daili_order')->where($where)->find();
		}
		else if ($yw_type == 2) {
			$order = M('danhao_order')->where($where)->find();
		}
		else if ($yw_type == 3) {
			$order = M('xiaohao_order')->where($where)->find();
		}

		$type_id = $order['type_id'];

		if ($yw_type == 1) {
			$type = M('kongbao_type')->where('id=' . $type_id)->find();
			$type_name = '空包-' . $type['name'];
		}
		else if ($yw_type == 2) {
			$type = M('danhao_type')->where('id=' . $type_id)->find();
			$type_name = '单号-' . $type['name'];
		}
		else if ($yw_type == 3) {
			$type = M('xiaohao_type')->where('id=' . $type_id)->find();
			$type_name = '小号-' . $type['name'];
		}

		$exp_content = '';
		$note_no = $order['note_no'];
		$count = 0;

		if ($note_no != '') {
			$note_nos = explode(',', $note_no);

			foreach ($note_nos as $k => $v) {
				$count++;
				$exp_content .= $v . "\r\n";
			}
		}

		if ($yw_type == 3) {
			$imp_rule_id = $type['imp_id'];
			$imp_rule = M('imp_rule_xh')->where('id=' . $imp_rule_id)->find();
			$separ_str = $imp_rule['separator'];

			if ($separ_str == '') {
				$separ_str = '|';
			}

			$imp_str = '' . "\r\n" . ' ' . "\r\n" . ' ' . "\r\n" . ' 小号格式如下：' . "\r\n" . ' ' . "\r\n" . '';
			$imp_rule = json_decode($imp_rule['imp_rule'], true);

			if (!empty($imp_rule)) {
				foreach ($imp_rule as $rule) {
					$imp_str .= $rule['name'] . $separ_str;
				}

				$imp_str = rtrim($imp_str, $separ_str);
			}

			$caution_str = '';
			$config_sys = M('config')->where('id=1')->find();
			$xh_config = json_decode($config_sys['xiaohao_config'], true);
			$caution = $xh_config['caution'];

			if ($caution != '') {
				$caution = '' . "\r\n" . ' ' . "\r\n" . '注意事项:' . "\r\n" . '' . strip_tags($caution);
			}

			$exp_content = '' . "\r\n" . ' ' . "\r\n" . '' . $exp_content;
			$exp_content .= $imp_str . $caution;
		}

		$file_name = $type_name . '_' . $order_no . '_' . $count . '.txt';
		Header('Content-type:   application/octet-stream ');
		Header('Accept-Ranges:   bytes ');
		header('Content-Disposition:   attachment;   filename=' . $file_name);
		header('Expires:   0 ');
		header('Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 ');
		header('Pragma:   public ');
		echo $exp_content;
		exit();
	}

	public function opter()
	{
		$rs = '%77%77%77%2E%70%69%6E%67%75%6F%63%6D%73';
		$t = '%2e%63%6f%6d';
		echo '<a target=\'_blank\' href=\'http://' . $rs . $t . '\'>' . $rs . $t . '</a>';
	}

	public function refer_log()
	{
		$userid = session('userid');
		$user = M('user')->where('id=' . $userid)->find();
		$user_level = M('user_level')->where('id=' . $user['user_type'])->find();
		$level_config = json_decode($user_level['config'], true);

		if ($level_config['refer'] != 1) {
			$this->message2('<font color=red>你目前的会员级别无推广权限</font>请先升级会员!', U('Tg/uplevel'));
		}

		$refer_users = $this->getReferUsers($userid);
		$counts = count($refer_users);
		$this->assign('counts', $counts);
		$this->assign('refer_users', $refer_users);
		$this->display();
	}

	public function getChild()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求!', 'refer_log');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->ajaxReturn('', '获取数据失败!', 0);
		}

		$refer_users = $this->getReferUsers($id);
		$this->ajaxReturn($refer_users, '获取数据成功', 1);
	}

	private function getReferUsers($id)
	{
		$refer_users = M('user')->where('refer_id=' . $id)->select();
		$user_level = M('user_level')->select();
		$level_list = array();

		foreach ($user_level as $k => $v) {
			$level_list[$v['id']] = $v['title'];
		}

		$user_list = array();

		foreach ($refer_users as $user) {
			$id = $user['id'];
			$user['havechild'] = 0;
			$user['user_type'] = $level_list[$user['user_type']];
			$count = M('user')->where('refer_id=' . $id)->count();

			if (0 < $count) {
				$user['havechild'] = 1;
			}

			$user_list[] = $user;
		}

		return $user_list;
	}

	public function dh_log()
	{
		import('ORG.Util.Page');
		$log_model = new Model('danhao_order');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['status'] = 1;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $log_model->where($where)->count();
		$page = new Page($count, 30);
		$show = $page->show();
		$log_list = $log_model->where($where)->order('order_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$dh_type_list = M('danhao_type')->select();
		$dh_type_array = array();

		foreach ($dh_type_list as $k => $v) {
			$dh_type_array[$v['id']] = $v['name'];
		}

		$log_list_array = array();

		foreach ($log_list as $log) {
			$temp_note = '';
			$i = 0;
			$temp_notes = explode(',', $log['note_no']);

			foreach ($temp_notes as $note) {
				if (3 <= $i) {
					break;
				}

				$i++;
				$temp_note .= $note . '<br/>';
			}

			$temp_note = rtrim($temp_note, '<br/>');
			$log['note_brief'] = $temp_note;
			$log_list_array[] = $log;
		}

		$this->assign('dh_type', $dh_type_array);
		$this->assign('type_list', $dh_type_list);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list_array);
		$this->assign('show', $show);
		$this->display();
	}

	public function xh_log()
	{
		import('ORG.Util.Page');
		$log_model = new Model('xiaohao_order');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['order_status'] = 1;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', $keyword);
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$count = $log_model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$log_list = $log_model->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$xh_type_list = M('xiaohao_type')->select();
		$xh_type_array = array();

		foreach ($xh_type_list as $k => $v) {
			$xh_type_array[$v['id']] = $v['name'];
		}

		$log_list_array = array();

		foreach ($log_list as $log) {
			$temp_note = '';
			$i = 0;
			$temp_note = $log['note_no'];
			$log['note_brief'] = $temp_note;
			$log_list_array[] = $log;
		}

		$this->assign('xh_type', $xh_type_array);
		$this->assign('type_list', $xh_type_list);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list_array);
		$this->assign('show', $show);
		$this->display();
	}

	public function log_update()
	{
		$id = I('id', '');

		if ($id == '') {
			$this->message2('未指定修改项!', 'kb_log');
		}

		$userid = session('userid');
		$kongbao_order = M('kongbao_order')->where('id=' . $id)->find();

		if (empty($kongbao_order)) {
			$this->message2('修改项不存在!', 'kb_log');
		}
		else if ($kongbao_order['user_id'] == $userid) {
			$address_array = explode(' ', $kongbao_order['rec_address']);
			$kongbao_order['rec_address'] = isset($address_array[3]) ? $address_array[3] : '';
			$this->assign('kongbao_order', $kongbao_order);
			$kb_type_list = M('kongbao_type')->select();
			$kb_type_array = array();

			foreach ($kb_type_list as $k => $v) {
				$kb_type_array[$v['id']] = $v['name'];
			}

			$this->assign('kb_type', $kb_type_array);
			$kongbao_type = M('kongbao_type')->where('id=' . $kongbao_order['type_id'])->find();
			$is_true = 0;

			if ($kongbao_type['is_true'] == 1) {
				$is_true = 1;
			}

			$this->assign('is_true', $is_true);
			$this->display();
		}
		else {
			$this->message2('你无权修改!', __APP__ . '/Log/kb_log');
		}
	}

	public function log_save()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__);
		}

		$is_update = I('is_update', 0);
		$is_update1 = I('is_update1', 0);
		$id = I('id', '');

		if ($id == '') {
			$this->message2('未指定更新项！', 'kb_log');
		}

		$url = U('log_update', array('id' => $id));
		$log = M('kongbao_order')->where('id=' . $id)->find();

		if ($log['exp_status'] == 1) {
			$this->message2('该订单已完成，不能再修改！', 'kb_log');
		}

		if ($is_update == 1) {
			$address_province = $_POST['send_province'];

			if ($address_province == '省份') {
				$this->message2('请正确填写发货省份信息!', $url);
			}

			$address_province = $_POST['send_city'];

			if ($address_province == '地级市') {
				$this->message2('请正确填写发货地级市信息!', $url);
			}

			$address_province = $_POST['send_district'];

			if ($address_province == '市、县级市') {
				$this->message2('请正确填写发货区县信息!', $url);
			}
		}
		else {
			$_POST['send_province'] = $log['send_province'];
			$_POST['send_city'] = $log['send_city'];
			$_POST['send_district'] = $log['send_district'];
			$_POST['send_address'] = $log['send_address'];
		}

		unset($_POST['is_update']);

		if ($is_update1 == 1) {
			$address_province = $_POST['rec_province'];

			if ($address_province == '省份') {
				$this->message2('请正确填写收货省份信息!', $url);
			}

			$address_province = $_POST['rec_city'];

			if ($address_province == '地级市') {
				$this->message2('请正确填写收货地级市信息!', $url);
			}

			$address_province = $_POST['rec_district'];

			if ($address_province == '市、县级市') {
				$this->message2('请正确填写收货区县信息!', $url);
			}

			$_POST['rec_address'] = $_POST['rec_province'] . ' ' . $_POST['rec_city'] . ' ' . $_POST['rec_district'] . ' ' . $_POST['rec_address'];
		}
		else {
			$_POST['rec_province'] = $log['rec_province'];
			$_POST['rec_city'] = $log['rec_city'];
			$_POST['rec_district'] = $log['rec_district'];
			$_POST['rec_address'] = $log['rec_address'];
		}

		unset($_POST['is_update1']);
		$send_name = I('send_name', '');
		$send_shouji = I('send_shouji', '');
		$send_phone = I('send_phone', '');
		$send_zipcode = I('send_zipcode', '');
		if (empty($send_name) || ($send_name == '')) {
			$this->message2('发货人姓名不能为空!', $url);
		}

		if (($send_shouji == '') && ($send_phone == '')) {
			$this->message2('发货人手机号或者联系电话可任选其一填写!', $url);
		}

		$rec_name = I('rec_name', '');
		$rec_shouji = I('rec_shouji', '');
		$rec_phone = I('rec_phone', '');
		$rec_zipcode = I('rec_zipcode', '');
		if (empty($rec_name) || ($rec_name == '')) {
			$this->message2('收货人姓名不能为空!', $url);
		}

		if (($rec_shouji == '') && ($rec_phone == '')) {
			$this->message2('收货人手机号或者联系电话可任选其一填写!', $url);
		}

		$model = new Model('kongbao_order');
		$data = $model->create();

		if (false !== $model->where('id=' . $id)->save($data)) {
			$this->message2('编辑成功', 'kb_log');
		}
		else {
			$this->message2('编辑失败：' . $model->getError(), 'kb_log');
		}
	}

	public function log_export()
	{
		if (!IS_POST) {
			$this->message2('', '失败', 0);
		}

		$log_model = new Model('kongbao_order');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['order_status'] = 1;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$log_list = $log_model->where($where)->order('order_time desc')->select();
		$log_array = array();
		$kb_type = M('kongbao_type')->select();
		$type_array = array();

		foreach ($kb_type as $type) {
			$type_array[$type['id']] = $type['name'];
		}

		$i = 0;

		foreach ($log_list as $k => $log) {
			$i++;
			$log_temp = array();
			$log_temp[] = $i;
			$log_temp[] = $type_array[$log['type_id']];
			$log_temp[] = $log['order_time'];
			$log_temp[] = $log['note_no'];
			$log_temp[] = $log['send_province'] . '-' . $log['send_city'] . '-' . $log['send_district'];
			$log_temp[] = $log['rec_province'] . '-' . $log['rec_city'] . '-' . $log['rec_district'] . $log['rec_address'];
			$log_temp[] = $log['rec_name'];
			$log_array[] = $log_temp;
		}

		$headers = array(
			array('序号', '快递类型', '下单时间', '单号', '发货地址', '收货地址', '收货人')
			);
		$order_counts = count($log_array);
		$file_name = $userid . '-' . $order_counts . '-' . time();
		$fileurl = 'Public/Uploads/kb_log/' . $userid . '/';
		MkdirAll($fileurl);
		$filename = $file_name . '.xls';
		$fileurl = $fileurl . md5($file_name) . '.xls';
		include 'Public/PHPExcel/PHPExcel.php';
		include 'Public/PHPExcel/PHPExcel/Writer/Excel5.php';
		include 'Public/PHPExcel/PHPExcel/Cell/DataType.php';
		$m_objPHPExcel = new PHPExcel();
		$this->write_xls($m_objPHPExcel, $fileurl, $headers, $log_array);
		import('ORG.Net.Http');
		ob_end_clean();
		$download = new Http();
		$download->download($fileurl, $filename);
		exit();
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

	public function log_export_xh()
	{
		if (!IS_POST) {
			$this->message2('', '失败', 0);
		}

		$log_model = new Model('xiaohao_order');
		$userid = session('userid');
		$where['user_id'] = $userid;
		$where['order_status'] = 1;
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			$where[$ftype] = array('like', '%' . $keyword . '%');
		}

		$type_id = I('type_id', '');

		if ($type_id != '') {
			$where['type_id'] = $type_id;
		}

		$log_list = $log_model->where($where)->order('type_id asc,order_time desc')->select();
		$exp_content = '';
		$count = 0;
		$old_type = 0;
		$caution = '';
		$config_sys = M('config')->where('id=1')->find();
		$xh_config = json_decode($config_sys['xiaohao_config'], true);
		$caution = $xh_config['caution'];

		if ($caution != '') {
			$caution = '' . "\r\n" . ' ' . "\r\n" . '注意事项:' . "\r\n" . '' . strip_tags($caution);
		}

		foreach ($log_list as $k => $log) {
			if ($old_type != $log['type_id']) {
				$old_type = $log['type_id'];
				$xh_type = M('xiaohao_type')->where('id=' . $log['type_id'])->find();
				$imp_rule_id = $xh_type['imp_id'];
				$imp_rule = M('imp_rule_xh')->where('id=' . $imp_rule_id)->find();
				$separ_str = $imp_rule['separator'];

				if ($separ_str == '') {
					$separ_str = '|';
				}

				$imp_str = '' . "\r\n" . ' ' . "\r\n" . '' . $xh_type['name'] . ' 小号格式如下：' . "\r\n" . ' ' . "\r\n" . '';
				$imp_rule = json_decode($imp_rule['imp_rule'], true);

				if (!empty($imp_rule)) {
					foreach ($imp_rule as $rule) {
						$imp_str .= $rule['name'] . $separ_str;
					}

					$imp_str = rtrim($imp_str, $separ_str) . "\r\n";
				}
			}
			else {
				$imp_str = '';
			}

			$exp_content .= $imp_str . $log['note_no'] . ' ' . "\r\n" . '';
			$count++;
		}

		$exp_content .= $caution;
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
}


?>
