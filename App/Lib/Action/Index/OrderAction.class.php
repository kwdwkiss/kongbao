<?php
//dezend by 辰梦大人 QQ:30881917
class OrderAction extends CommonAction
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

	public function kb()
	{
		$userid = session('userid');
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$model = new Model('kongbao_type');
		$list = $model->where('is_true=0')->order('sort_order asc')->select();
		$type_list = array();

		foreach ($list as $k => $v) {
			if (($kongbao_config['stop_view'] == 0) && ($v['state'] == 1)) {
				continue;
			}

			if ($v['state'] == 0) {
				$v['name'] = $v['name'] . '(正常出售)';
			}

			if ($v['state'] == 1) {
				$v['name'] = $v['name'] . '(暂停出售)';
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		$user_default_kuaidi = $user['default_kuaidi'];
		$this->assign('user_default_kuaidi', $user_default_kuaidi);
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('level_config', $level_config);
		$address_list = M('user_address')->where('user_id=' . $userid)->select();
		$address_array = array();

		foreach ($address_list as $address) {
			$address['title'] = $address['name'] . ',' . $address['shouji'] . ',' . $address['address_province'] . '-' . $address['address_city'] . '-' . $address['address_district'] . '-' . $address['address'];
			$address_array[] = $address;
		}

		$this->assign('address_list', $address_array);
		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $k => $v) {
			$kongbao_page_temp[$k] = stripslashes(htmlspecialchars_decode($v));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $k => $v) {
			$seo_seoo_temp[$k] = stripslashes(htmlspecialchars_decode($v));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		$this->display();
	}

	public function true_kb()
	{
		$userid = session('userid');
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$model = new Model('kongbao_type');
		$list = $model->where('is_true=1')->order('sort_order asc')->select();
		$type_list = array();

		foreach ($list as $k => $v) {
			if (($kongbao_config['stop_view'] == 0) && ($v['state'] == 1)) {
				continue;
			}

			if ($v['state'] == 0) {
				$v['name'] = $v['name'] . '(正常出售)';
			}

			if ($v['state'] == 1) {
				$v['name'] = $v['name'] . '(暂停出售)';
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('level_config', $level_config);
		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $k => $v) {
			$kongbao_page_temp[$k] = stripslashes(htmlspecialchars_decode($v));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $k => $v) {
			$seo_seoo_temp[$k] = stripslashes(htmlspecialchars_decode($v));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		$this->display();
	}

	public function setDefaultKuaidi()
	{
		$userid = session('userid');

		if ($userid == '') {
			$this->message2('请先登录!', __APP__ . '/Index');
		}

		$map['id'] = $userid;
		$data['default_kuaidi'] = $_POST['default_kuaidi'];
		$mod = M('user');

		if (false !== $mod->where($map)->save($data)) {
			$str = '{"status":"设置成功"}';
		}
		else {
			$str = '{"status":"无效请求"}';
		}

		echo $str;
	}

	public function getDetail()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$type_id = I('id', NULL);

		if (empty($type_id)) {
			$this->ajaxReturn('', '未指定任何类型!', 0);
		}

		$kucunliang = 0;
		$yw_type = I('type', 'kb');
		$where = array();
		$where['type_id'] = $type_id;
		$where['isused'] = 0;
		$sys_config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($sys_config['kongbao_config'], true);
		$danhao_config = json_decode($sys_config['danhao_config'], true);
		$xiaohao_config = json_decode($sys_config['xiaohao_config'], true);
		$view_kucun = 0;
		$view_comm = '';

		if ($yw_type == 'kb') {
			$kongbao_type = M('kongbao_type')->where('id=' . $type_id)->find();

			if ($kongbao_config['view_kucun'] == 1) {
				$kucunliang = M('kongbao')->where($where)->count();
				$view_kucun = 1;
			}

			if ($kongbao_config['view_comm'] == 1) {
				$view_comm = $kongbao_type['comm'];
			}
		}
		else if ($yw_type == 'dh') {
			$kongbao_type = M('danhao_type')->where('id=' . $type_id)->find();

			if ($danhao_config['view_kucun'] == 1) {
				$kucunliang = M('danhao')->where($where)->count();
				$view_kucun = 1;
			}

			if ($danhao_config['view_comm'] == 1) {
				$view_comm = $kongbao_type['comm'];
			}
		}
		else if ($yw_type == 'xh') {
			$kongbao_type = M('xiaohao_type')->where('id=' . $type_id)->find();

			if ($xiaohao_config['view_kucun'] == 1) {
				$kucunliang = M('xiaohao')->where($where)->count();
				$view_kucun = 1;
			}

			if ($xiaohao_config['view_comm'] == 1) {
				$view_comm = $kongbao_type['comm'];
			}
		}

		$config = json_decode($kongbao_type['config'], true);
		$user_type_list = M('user_level')->order('sort_order asc')->select();
		$user_type_list_array = array();
		$html_string = '';

		foreach ($user_type_list as $level) {
			$level['config'] = json_decode($level['config'], true);
			if (isset($level['config']['level_view']) && ($level['config']['level_view'] == 1)) {
				continue;
			}

			$key = 'a' . $level['id'];
			$html_string .= $level['title'] . ':' . $config['price'][$key] . '元/个 |';
		}

		if ($view_kucun == 1) {
			$html_string .= '当前库存量:' . $kucunliang . '个';
		}

		if ($view_comm != '') {
			$html_string .= '|' . $view_comm;
		}

		$keyid = 'a' . $this->user['user_type'];
		$current_text = $config['price'][$keyid];
		$return_array['detail'] = $html_string;
		$return_array['dd_price'] = $config['didan'];
		$return_array['price'] = $current_text;
		$return_array['current'] = $current_text . '元/个';

		if ($kongbao_type['is_true'] == 1) {
			$return_array['address'] = $config['address']['province'] . ' > ' . $config['address']['city'] . ' > ' . $config['address']['district'];
		}

		$return_array['file_url'] = U('downfile', array('id' => $type_id));
		$return_array['image_url'] = $kongbao_type['image_url'];
		$this->ajaxReturn($return_array, '获取成功', 1);
	}

	public function kbsave()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$order_money = I('order_money', 0);

		if ($order_money <= 0) {
			$this->message2('订单金额不能小于0', 'kb');
		}

		$order_nums = I('order_nums', 0);

		if ($order_nums <= 0) {
			$this->message2('请填写收货地址!', 'kb');
		}

		$type_id = I('type_id', '');
		if (empty($type_id) || ($type_id == '')) {
			$this->message2('请选择快递类型!', 'kb');
		}

		$curr_type = M('kongbao_type')->where('id=' . $type_id)->find();

		if ($curr_type['state'] == 1) {
			$this->message2('该快递目前暂停出售，暂不能下单!', 'kb');
		}

		$type_config = json_decode($curr_type['config'], true);
		$address_id = I('address_id', '');
		if (empty($address_id) || ($address_id == '')) {
			$this->message2('请选择发货地址!', 'kb');
		}

		$where_k['type_id'] = $type_id;
		$where_k['isused'] = 0;
		$count = M('kongbao')->where($where_k)->count();

		if ($count < $order_nums) {
			$this->message2('系统内数量不足，请联系网站客服!', 'kb');
		}

		$kongbao_list_temp = M('kongbao')->where($where_k)->limit($order_nums)->select();
		$kongbao = array();

		foreach ($kongbao_list_temp as $kb) {
			$temp = array();
			$temp['id'] = $kb['id'];
			$temp['note_no'] = $kb['note_no'];
			$kongbao[] = $temp;
		}

		$address_model = new Model('user_address');
		$where_a['id'] = $address_id;
		$where_a['user_id'] = $userid;
		$address = $address_model->where($where_a)->find();

		if (empty($address)) {
			$this->message2('发货地址不存在，请联系网站客服!', 'kb');
		}

		$addinputarr = $_POST['addinputarr'];
		$data_count = count($addinputarr);

		if ($data_count != $order_nums) {
			$this->message2('提交地址数目出错，请联系网站客服!', 'kb');
		}

		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$utype = 'a' . $user['user_type'];
		$uprice = (isset($type_config['price'][$utype]) ? $type_config['price'][$utype] : 0);
		$inprice = (isset($type_config['in_price']) ? $type_config['in_price'] : 0);
		$money = $user['money'];
		$ysmoney = round($uprice * $order_nums, 2);

		if ($ysmoney != round($order_money, 2)) {
			$this->message2('下单金额有误，请重新下单!', U('kb'));
		}

		if ($money < $order_money) {
			$this->message2('你的账户可用金额不足，请先充值!', U('Cash/recharge'));
		}

		$user_model->startTrans();
		$userdata['id'] = $userid;
		$userdata['money'] = $user['money'] - $order_money;
		$userdata['used_money'] = $user['used_money'] + $order_money;

		if (false !== $user_model->data($userdata)->save()) {
			$order_no = $this->create_orderno('SBuy');
			$reason = '(购买空包) 订单号 ' . $order_no;
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'buy';
			$account_log['money'] = 0 - $order_money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $user['refer_money'];
			$account_log['order_no'] = $order_no;
			$account_log_model = new Model('account_log');
			$return_1 = D('account_log')->data($account_log)->add();
			$paydata = array();
			$paydata['user_id'] = $userid;
			$paydata['user_type'] = $user['user_type'];
			$paydata['pay_money'] = $order_money;
			$paydata['order_no'] = $order_no;
			$paydata['type'] = 0;
			$paydata['comm'] = '空包订单';
			$paydata['status'] = 1;
			$paydata['addtime'] = $this->getDate();
			$return_2 = M('pay_order')->data($paydata)->add();
			if ($return_1 && $return_2) {
				$i = 0;
				$order_time = $this->getDate();
				$result = true;

				foreach ($addinputarr as $input) {
					$kongbao_id = $kongbao[$i]['id'];
					$kb_order = array();
					$kb_order['user_id'] = $userid;
					$kb_order['type_id'] = $type_id;
					$kb_order['note_no'] = $kongbao[$i]['note_no'];
					$kb_order['order_no'] = $order_no;
					$kb_order['order_time'] = $order_time;
					$kb_order['order_status'] = 1;
					$kb_order['user_name'] = $user['username'];
					$kb_order['user_qq'] = $user['user_qq'];
					$kb_order['order_money'] = $uprice;
					$kb_order['in_price'] = $inprice;
					$kb_order['user_type'] = $user['user_type'];
					$kb_order['send_province'] = $address['address_province'];
					$kb_order['send_city'] = $address['address_city'];
					$kb_order['send_district'] = $address['address_district'];
					$kb_order['send_address'] = $address['address'];
					$kb_order['send_name'] = $address['name'];
					$kb_order['send_shouji'] = $address['shouji'];
					$kb_order['send_phone'] = $address['phone'];
					$kb_order['send_zipcode'] = $address['zipcode'];
					$input_array = explode('，', $input);
					$input_length = count($input_array);
					$kb_order['rec_name'] = trim($input_array[0]);
					$kb_order['rec_shouji'] = trim($input_array[1]);

					if ($input_length == 4) {
						$kb_order['rec_phone'] = '';
						$kb_order['rec_address'] = $input_array[2];
						$kb_order['rec_zipcode'] = $input_array[3];
					}
					else if ($input_length == 5) {
						$kb_order['rec_phone'] = trim($input_array[2]);
						$kb_order['rec_address'] = $input_array[3];
						$kb_order['rec_zipcode'] = $input_array[4];
					}

					$rec_address = $kb_order['rec_address'];
					$address_array = explode(' ', ltrim($rec_address));
					$kb_order['rec_province'] = $address_array[0];
					$kb_order['rec_city'] = $address_array[1];
					$kb_order['rec_district'] = $address_array[2];

					if (false !== D('kongbao_order')->data($kb_order)->add()) {
						$kongbao_data = array();
						$kongbao_data['id'] = $kongbao[$i]['id'];
						$kongbao_data['isused'] = 1;
						$kongbao_data['order_type'] = 0;
						$kongbao_data['order_no'] = $order_no;
						$return_3 = D('kongbao')->data($kongbao_data)->save();

						if (!$return_3) {
							$result = false;
						}
					}
					else {
						$result = false;
					}

					$i++;
				}

				if (!$result) {
					$user_model->rollback();
					$this->message2('下单失败1：' . $user_model->getError(), U('kb'));
				}

				$refer_users = calc_commission($userid, 1, 1, $type_id);
				$add_result = true;

				if (!empty($refer_users)) {
					$add_result = addrefer_money($refer_users, 1, $order_nums, 1, $order_no);
				}

				if (!$add_result) {
					$user_model->rollback();
					$this->message2('下单失败4：' . $user_model->getError(), U('kb'));
				}

				$user_model->commit();
				$this->message2('订单提交成功!', U('Log/kb_log', array('ftype' => 'order_no', 'keyword' => $order_no)));
			}
			else {
				$user_model->rollback();
				$this->message2('下单失败2：' . $user_model->getError(), U('kb'));
			}
		}
		else {
			$user_model->rollback();
			$this->message2('下单失败3：' . $user_model->getError(), U('kb'));
		}
	}

	public function opter()
	{
		$rs = '%77%77%77%2E%70%69%6E%67%75%6F%63%6D%73';
		$t = '%2e%63%6f%6d';
		echo '<a target=\'_blank\' href=\'http://' . $rs . $t . '\'>' . $rs . $t . '</a>';
	}

	public function truekbsave()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$order_money = I('order_money', 0);

		if ($order_money <= 0) {
			$this->message2('订单金额不能小于0', 'true_kb');
		}

		$order_nums = I('order_nums', 0);

		if ($order_nums <= 0) {
			$this->message2('请填写收货地址!', 'true_kb');
		}

		$type_id = I('type_id', '');
		if (empty($type_id) || ($type_id == '')) {
			$this->message2('请选择快递类型!', 'true_kb');
		}

		$curr_type = M('kongbao_type')->where('id=' . $type_id)->find();

		if ($curr_type['state'] == 1) {
			$this->message2('该快递目前暂停出售，暂不能下单!', 'true_kb');
		}

		$type_config = json_decode($curr_type['config'], true);
		$send_address = I('send_address', '');
		$send_name = I('send_name', '');
		$send_shouji = I('send_shouji', '');
		$send_phone = I('send_phone', '');
		$send_zipcode = I('send_zipcode', '');
		if (empty($send_name) || ($send_name == '')) {
			$this->message2('发货人姓名不能为空!', 'true_kb');
		}

		if (($send_shouji == '') && ($send_phone == '')) {
			$this->message2('发货人手机号或者联系电话可任选其一填写!', 'true_kb');
		}

		$where_k['type_id'] = $type_id;
		$where_k['isused'] = 0;
		$count = M('kongbao')->where($where_k)->count();

		if ($count < $order_nums) {
			$this->message2('系统内数量不足，请联系网站客服!', 'true_kb');
		}

		$kongbao_list_temp = M('kongbao')->where($where_k)->limit($order_nums)->select();
		$kongbao = array();

		foreach ($kongbao_list_temp as $kb) {
			$temp = array();
			$temp['id'] = $kb['id'];
			$temp['note_no'] = $kb['note_no'];
			$kongbao[] = $temp;
		}

		$address_model = new Model('kongbao_type');
		$where_a['id'] = $type_id;
		$type_kb = $address_model->where($where_a)->find();

		if (empty($type_kb)) {
			$this->message2('发货省市获取错误，请联系网站客服!', 'true_kb');
		}

		$address_info = json_decode($type_kb['config'], true);
		$send_province = $address_info['address']['province'];
		$send_city = $address_info['address']['city'];
		$send_district = $address_info['address']['district'];
		$addinputarr = $_POST['addinputarr'];
		$data_count = count($addinputarr);

		if ($data_count != $order_nums) {
			$this->message2('提交地址数目出错，请联系网站客服!', 'true_kb');
		}

		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$utype = 'a' . $user['user_type'];
		$uprice = (isset($type_config['price'][$utype]) ? $type_config['price'][$utype] : 0);
		$inprice = (isset($type_config['in_price']) ? $type_config['in_price'] : 0);
		$money = $user['money'];
		$ysmoney = round($uprice * $order_nums, 2);

		if ($ysmoney != round($order_money, 2)) {
			$this->message2('下单金额有误，请重新下单!', U('true_kb'));
		}

		if ($money < $order_money) {
			$this->message2('你的账户可用金额不足，请先充值!', U('Cash/recharge'));
		}

		$user_model->startTrans();
		$userdata['id'] = $userid;
		$userdata['money'] = $user['money'] - $order_money;
		$userdata['used_money'] = $user['used_money'] + $order_money;

		if (false !== $user_model->data($userdata)->save()) {
			$order_no = $this->create_orderno('SBuy');
			$reason = '(购买真实空包) 订单号 ' . $order_no;
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'buy';
			$account_log['money'] = 0 - $order_money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $user['refer_money'];
			$account_log['order_no'] = $order_no;
			$account_log_model = new Model('account_log');
			$return_1 = D('account_log')->data($account_log)->add();
			$paydata = array();
			$paydata['user_id'] = $userid;
			$paydata['user_type'] = $user['user_type'];
			$paydata['pay_money'] = $order_money;
			$paydata['order_no'] = $order_no;
			$paydata['type'] = 0;
			$paydata['comm'] = '真实空包订单';
			$paydata['status'] = 1;
			$paydata['addtime'] = $this->getDate();
			$return_2 = M('pay_order')->data($paydata)->add();
			if ($return_1 && $return_2) {
				$i = 0;
				$order_time = $this->getDate();
				$result = true;

				foreach ($addinputarr as $input) {
					$kongbao_id = $kongbao[$i]['id'];
					$kb_order = array();
					$kb_order['user_id'] = $userid;
					$kb_order['type_id'] = $type_id;
					$kb_order['note_no'] = $kongbao[$i]['note_no'];
					$kb_order['order_no'] = $order_no;
					$kb_order['order_time'] = $order_time;
					$kb_order['order_status'] = 1;
					$kb_order['user_name'] = $user['username'];
					$kb_order['user_qq'] = $user['user_qq'];
					$kb_order['order_money'] = $uprice;
					$kb_order['in_price'] = $inprice;
					$kb_order['user_type'] = $user['user_type'];
					$kb_order['send_province'] = $send_province;
					$kb_order['send_city'] = $send_city;
					$kb_order['send_district'] = $send_district;
					$kb_order['send_address'] = $send_address;
					$kb_order['send_name'] = $send_name;
					$kb_order['send_shouji'] = $send_shouji;
					$kb_order['send_phone'] = $send_phone;
					$kb_order['send_zipcode'] = $send_zipcode;
					$input_array = explode('，', $input);
					$input_length = count($input_array);
					$kb_order['rec_name'] = trim($input_array[0]);
					$kb_order['rec_shouji'] = trim($input_array[1]);

					if ($input_length == 4) {
						$kb_order['rec_phone'] = '';
						$kb_order['rec_address'] = $input_array[2];
						$kb_order['rec_zipcode'] = $input_array[3];
					}
					else if ($input_length == 5) {
						$kb_order['rec_phone'] = trim($input_array[2]);
						$kb_order['rec_address'] = $input_array[3];
						$kb_order['rec_zipcode'] = $input_array[4];
					}

					$rec_address = $kb_order['rec_address'];
					$address_array = explode(' ', ltrim($rec_address));
					$kb_order['rec_province'] = $address_array[0];
					$kb_order['rec_city'] = $address_array[1];
					$kb_order['rec_district'] = $address_array[2];

					if (false !== D('kongbao_order')->data($kb_order)->add()) {
						$kongbao_data = array();
						$kongbao_data['id'] = $kongbao[$i]['id'];
						$kongbao_data['isused'] = 1;
						$kongbao_data['order_type'] = 0;
						$kongbao_data['order_no'] = $order_no;
						$return_3 = D('kongbao')->data($kongbao_data)->save();

						if (!$return_3) {
							$result = false;
						}
					}
					else {
						$result = false;
					}

					$i++;
				}

				if (!$result) {
					$user_model->rollback();
					$this->message2('下单失败1：' . $user_model->getError(), U('kb'));
				}

				$refer_users = calc_commission($userid, 1, 1, $type_id);
				$add_result = true;

				if (!empty($refer_users)) {
					$add_result = addrefer_money($refer_users, 1, $order_nums, 1, $order_no);
				}

				if (!$add_result) {
					$user_model->rollback();
					$this->message2('下单失败4：' . $user_model->getError(), U('true_kb'));
				}

				$user_model->commit();
				$this->message2('订单提交成功!', U('Log/kb_log', array('ftype' => 'order_no', 'keyword' => $order_no)));
			}
			else {
				$user_model->rollback();
				$this->message2('下单失败2：' . $user_model->getError(), U('true_kb'));
			}
		}
		else {
			$user_model->rollback();
			$this->message2('下单失败3：' . $user_model->getError(), U('true_kb'));
		}
	}

	public function kb_pf()
	{
		$userid = session('userid');
		$model = new Model('kongbao_type');
		$list = $model->order('sort_order asc')->select();
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$type_list = array();

		foreach ($list as $k => $v) {
			if (($kongbao_config['stop_view'] == 0) && ($v['state'] == 1)) {
				continue;
			}

			if ($v['state'] == 0) {
				$v['name'] = $v['name'] . '(正常出售)';
			}

			if ($v['state'] == 1) {
				$v['name'] = $v['name'] . '(暂停出售)';
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('level_config', $level_config);

		if ($level_config['pifa'] != 1) {
			$this->message2('<font color=red>你目前无批发权限，如想批发，请联系网站客服！</font>', __APP__);
		}

		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $key1 => $v1) {
			$kongbao_page_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $k => $v) {
			$seo_seoo_temp[$k] = stripslashes(htmlspecialchars_decode($v));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		$this->display();
	}

	public function downfile()
	{
		$type_id = I('id', NULL);
		$type = M('kongbao_type')->where('id=' . $type_id)->find();
		if (!empty($type) && ($type['file_url'] != '')) {
			$file_path = $type['file_url'];
			$file_name = $type['name'] . '_代理.xls';
		}
		else {
			$file_path = 'Public/Uploads/kb/common_pf.xls';
			$file_name = '通用_代理.xls';
		}

		$file_path = iconv('utf-8', 'gb2312', $file_path);
		import('ORG.Net.Http');
		ob_end_clean();
		$download = new Http();
		$file_name = urlencode($file_name);
		$download->download($file_path, $file_name);
	}

	public function kb_pfsave()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$order_money = I('total_price', 0);

		if ($order_money <= 0) {
			$this->message2('订单金额不能小于0', 'kb_pf');
		}

		$order_nums = I('qty_item_1', 0);

		if ($order_nums <= 0) {
			$this->message2('请填写批发空包数目!', 'kb_pf');
		}

		$config = M('config')->where('id=1')->find();
		$kb_config = json_decode($config['kongbao_config'], true);

		if (0 < $kb_config['pf_minnums']) {
			if ($order_nums < $kb_config['pf_minnums']) {
				$this->message2('批发空包数目不能低于最低批发个数(' . $kb_config['pf_minnums'] . ')!', 'kb_pf');
			}
		}

		$type_id = I('type_id', '');
		if (empty($type_id) || ($type_id == '')) {
			$this->message2('请选择快递类型!', 'kb_pf');
		}

		$curr_type = M('kongbao_type')->where('id=' . $type_id)->find();

		if ($curr_type['state'] == 1) {
			$this->message2('该快递目前暂停出售，暂不能下单!', 'kb_pf');
		}

		$type_config = json_decode($curr_type['config'], true);
		$where_k['type_id'] = $type_id;
		$where_k['isused'] = 0;
		$count = M('kongbao')->where($where_k)->count();

		if ($count < $order_nums) {
			$this->message2('系统内数量不足，请联系网站客服!', 'kb_pf');
		}

		$kongbao_list_temp = M('kongbao')->where($where_k)->limit($order_nums)->select();
		$kongbao = array();

		foreach ($kongbao_list_temp as $kb) {
			$temp = array();
			$temp['id'] = $kb['id'];
			$temp['note_no'] = $kb['note_no'];
			$kongbao[] = $temp;
		}

		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$utype = 'a' . $user['user_type'];
		$uprice = (isset($type_config['price'][$utype]) ? $type_config['price'][$utype] : 0);
		$inprice = (isset($type_config['in_price']) ? $type_config['in_price'] : 0);
		$money = $user['money'];

		if ($money < $order_money) {
			$this->message2('你的账户可用金额不足，请先充值!', U('Cash/recharge'));
		}

		$user_model->startTrans();
		$userdata['id'] = $userid;
		$userdata['money'] = $user['money'] - $order_money;
		$userdata['used_money'] = $user['used_money'] + $order_money;

		if (false !== $user_model->data($userdata)->save()) {
			$order_no = $this->create_orderno('PBuy');
			$reason = '(批发空包) 订单号 ' . $order_no;
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'buy';
			$account_log['money'] = 0 - $order_money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $user['refer_money'];
			$account_log['order_no'] = $order_no;
			$account_log_model = new Model('account_log');
			$return_1 = D('account_log')->data($account_log)->add();
			$paydata = array();
			$paydata['user_id'] = $userid;
			$paydata['user_type'] = $user['user_type'];
			$paydata['pay_money'] = $order_money;
			$paydata['order_no'] = $order_no;
			$paydata['type'] = 0;
			$paydata['comm'] = '空包批发订单';
			$paydata['status'] = 1;
			$paydata['addtime'] = $this->getDate();
			$return_2 = M('pay_order')->data($paydata)->add();
			if ($return_1 && $return_2) {
				$i = 0;
				$order_time = $this->getDate();
				$result = true;
				$order_no_string = '';

				foreach ($kongbao as $kb) {
					$order_no_string .= $kb['note_no'] . ',';
					$kongbao_data = array();
					$kongbao_data['id'] = $kb['id'];
					$kongbao_data['isused'] = 1;
					$kongbao_data['order_type'] = 0;
					$kongbao_data['order_no'] = $order_no;
					$return_3 = D('kongbao')->data($kongbao_data)->save();

					if (!$return_3) {
						$result = false;
					}
				}

				if (!$result) {
					$user_model->rollback();
					$this->message2('下单失败1：' . $user_model->getError(), U('kb_pf'));
				}

				$daili_order_data = array();
				$daili_order_data['user_id'] = $userid;
				$daili_order_data['type_id'] = $type_id;
				$daili_order_data['order_no'] = $order_no;
				$daili_order_data['order_time'] = $order_time;
				$daili_order_data['order_status'] = 1;
				$daili_order_data['order_money'] = $uprice;
				$daili_order_data['order_nums'] = $order_nums;
				$daili_order_data['user_type'] = $user['user_type'];
				$daili_order_data['in_price'] = $inprice;
				$daili_order_data['note_no'] = rtrim($order_no_string, ',');
				$result_4 = D('kongbao_daili_order')->data($daili_order_data)->add();

				if (!$result_4) {
					$user_model->rollback();
					$this->message2('下单失败2：' . $user_model->getError(), U('kb_pf'));
				}

				$refer_users = calc_commission($userid, 1, 1, $type_id);
				$add_result = true;

				if (!empty($refer_users)) {
					$add_result = addrefer_money($refer_users, 1, $order_nums, 1, $order_no);
				}

				if (!$add_result) {
					$user_model->rollback();
					$this->message2('下单失败3：' . $user_model->getError(), U('kb_pf'));
				}

				$user_model->commit();
				$this->message2('订单提交成功!', U('Log/kbpf_log', array('ftype' => 'order_no', 'keyword' => $order_no)));
			}
			else {
				$user_model->rollback();
				$this->message2('下单失败4：' . $user_model->getError(), U('kb_pf'));
			}
		}
		else {
			$user_model->rollback();
			$this->message2('下单失败5：' . $user_model->getError(), U('kb_pf'));
		}
	}

	public function daoban()
	{
		$rs = '%77%77%77%2E%70%69%6E%67%75%6F%63%6D%73';
		$t = '%2e%63%6f%6d';
		echo '<a target=\'_blank\' href=\'http://' . $rs . $t . '\'>' . $rs . $t . '</a>';
	}

	public function dd_apply()
	{
		$userid = session('userid');
		$model = new Model('kongbao_type');
		$config = M('config')->where('id=1')->find();
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$list = $model->order('sort_order asc')->select();
		$type_list = array();

		foreach ($list as $k => $v) {
			if (($kongbao_config['stop_view'] == 0) && ($v['state'] == 1)) {
				continue;
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('level_config', $level_config);
		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $key1 => $v1) {
			$kongbao_page_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $k => $v) {
			$seo_seoo_temp[$k] = stripslashes(htmlspecialchars_decode($v));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		import('ORG.Util.Page');
		$where = array();
		$where['user_id'] = $userid;
		$count = M('didan_uploadfiles')->where($where)->count();
		$page = new Page($count, 5);
		$didan_list = M('didan_uploadfiles')->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$show = $page->show();
		$this->assign('didan_list', $didan_list);
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->display();
	}

	public function ddsave()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$type_id = I('type_id', NULL);
		$userid = session('userid');
		$kongbao_type = M('kongbao_type')->where('id=' . $type_id)->find();

		if (empty($kongbao_type)) {
			$this->message2('快递类型不存在！请核实!', 'dd_apply');
		}

		$kb_config = json_decode($kongbao_type['config'], true);
		$didan = $kb_config['didan'];
		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$didan_model = M('kongbao_didan');
		$where['note_no'] = I('note_no', '');
		$where['type_id'] = $type_id;
		$counts = $didan_model->where($where)->count();

		if (0 < $counts) {
			$this->message2('该订单号已经申请过，如需再次提交请联系客服!', 'dd_apply');
		}

		$didan_data = array();
		$didan_data['user_id'] = $userid;
		$didan_data['type_id'] = $type_id;
		$didan_data['type_name'] = $kongbao_type['name'];
		$didan_data['note_no'] = I('note_no', '');
		$didan_data['dd_price'] = 0;

		if (0 < $didan) {
			if ($user['money'] < $didan) {
				$this->message2('账户余额不足，请先充值!', 'dd_apply');
			}

			$didan_data['dd_price'] = $didan;
		}

		$jietu = I('jietu', '');

		if ($jietu == '') {
			$this->message2('降权截图不能为空，请上传!', 'dd_apply');
		}

		$didan_data['jietu'] = $jietu;
		$fhaddr = I('fhaddr', '');

		if ($fhaddr == '') {
			$this->message2('发货地址不能为空!', 'dd_apply');
		}

		$shaddr = I('shaddr', '');

		if ($shaddr == '') {
			$this->message2('收货地址不能为空!', 'dd_apply');
		}

		$fhaddr_array = explode('，', $fhaddr);
		$didan_data['send_name'] = $fhaddr_array[2];
		$didan_data['send_shouji'] = $fhaddr_array[3];
		$didan_data['send_phone'] = $fhaddr_array[4];
		$didan_data['send_addr'] = $fhaddr_array[0];
		$didan_data['send_zipcode'] = $fhaddr_array[1];
		$shaddr_array = explode('，', $shaddr);
		$didan_data['rec_name'] = $shaddr_array[0];
		$didan_data['rec_shouji'] = $shaddr_array[1];
		$didan_data['rec_phone'] = $shaddr_array[2];
		$didan_data['rec_addr'] = $shaddr_array[3];
		$didan_data['rec_zipcode'] = $shaddr_array[4];
		$didan_data['weight'] = I('weight', '');
		$didan_data['yunfei'] = I('yunfei', 0);
		$didan_data['order_time'] = I('order_time', '');
		$didan_data['goods_name'] = I('goods_name', '');
		$didan_data['addtime'] = $this->getDate();
		$didan_data['status'] = '1';

		if (0 < $didan_data['dd_price']) {
			$user_model->startTrans();
			$userdata = array();
			$userdata['id'] = $userid;
			$userdata['money'] = $user['money'] - $didan;
			$result = $user_model->data($userdata)->save();

			if ($result) {
				$order_no = $this->create_orderno('DApl');
				$didan_data['order_no'] = $order_no;
				$reason = '(底单申请) 订单号 ' . $order_no;
				$account_log = array();
				$account_log['user_id'] = $userid;
				$account_log['stage'] = 'buy';
				$account_log['money'] = 0 - $didan;
				$account_log['comm'] = $reason;
				$account_log['addtime'] = $this->getDate();
				$account_log['remain_money'] = $userdata['money'];
				$account_log['remain_refer_money'] = $user['refer_money'];
				$account_log['order_no'] = $order_no;
				$account_log_model = new Model('account_log');
				$return_1 = D('account_log')->data($account_log)->add();
				$paydata = array();
				$paydata['user_id'] = $userid;
				$paydata['user_type'] = $user['user_type'];
				$paydata['pay_money'] = $didan;
				$paydata['order_no'] = $order_no;
				$paydata['type'] = 0;
				$paydata['comm'] = '底单申请';
				$paydata['status'] = 1;
				$paydata['addtime'] = $this->getDate();
				$return_2 = M('pay_order')->data($paydata)->add();
				$return_3 = $didan_model->data($didan_data)->add();
				if ($return_1 && $return_2 && $return_3) {
					$user_model->commit();
					$this->message2('底单申请成功，等待处理!', __APP__ . '/Index');
				}
				else {
					$user_model->rollback();
					$this->message2('底单申请失败：' . $didan_model->getError(), 'dd_apply');
				}
			}
			else {
				$user_model->rollback();
				$this->message2('底单申请失败2：' . $didan_model->getError(), 'dd_apply');
			}
		}
		else {
			$result = $didan_model->data($didan_data)->add();

			if ($result) {
				$this->message2('底单申请成功，等待处理!', __APP__ . '/Index');
			}
			else {
				$this->message2('底单申请失败：' . $didan_model->getError(), 'dd_apply');
			}
		}
	}

	public function ddsave_file()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$userid = session('userid');
		$username = session('username');
		$content = I('file_content', '');

		if ($content == '') {
			$this->message2('内容不能为空！', 'dd_apply');
		}

		$data = array();
		$data['user_id'] = $userid;
		$data['username'] = $username;
		$data['input_content'] = $content;
		$data['addtime'] = date('Y-m-d H:i:s', time());
		$model = new Model('didan_uploadfiles');

		if (false !== $model->data($data)->add()) {
			$this->message2('底单申请提交成功，耐心等待处理！', 'dd_apply');
		}
		else {
			$this->message2('底单申请提交失败：' . $model->getError(), 'dd_apply');
		}
	}

	public function daili_upload()
	{
		$userid = session('userid');
		$model = new Model('kongbao_type');
		$list = $model->order('sort_order asc')->select();
		$type_list = array();

		foreach ($list as $k => $v) {
			$v['config'] = json_decode($v['config'], true);
			$type_list[$v['id']] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);

		if ($level_config['pifa'] != 1) {
			$this->message2('<font color=red>你目前的会员级别无批发权限，无需上传表格</font>', __APP__ . '/Index');
		}

		$this->assign('level_config', $level_config);
		$config = M('config')->where('id=1')->find();
		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $key1 => $v1) {
			$kongbao_page_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $key1 => $v1) {
			$seo_seoo_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		import('ORG.Util.Page');
		$type_id = I('type_id', NULL);

		if (!empty($type_id)) {
			$where['type_id'] = $type_id;
		}

		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$where['user_id'] = $userid;
		$count = M('uploadfiles')->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$file_list = M('uploadfiles')->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('file_list', $file_list);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->display();
	}

	public function upload()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', 'daili_upload');
		}

		import('ORG.Net.UploadFile');
		$userid = session('userid');
		$type_id = I('type_id', NULL);

		if (empty($type_id)) {
			$this->message2('请选择快递类型!', 'daili_upload');
		}

		$filename = I('filename', NULL);

		if (empty($filename)) {
			$this->message2('请上传表格文件!', 'daili_upload');
		}

		$type = M('kongbao_type')->where('id=' . $type_id)->find();
		$filename_array = explode('-', $filename);

		if (count($filename_array) != 3) {
			$this->message2('上传文件名不符合规范!', 'daili_upload');
		}

		$type_name = $filename_array[0];

		if ($type['name'] != $type_name) {
			$this->message2('上传文件名中类型与选择类型不符!', 'daili_upload');
		}

		$uploadfiles_model = new Model('uploadfiles');
		$where['user_id'] = $userid;
		$where['filename'] = $filename;
		$count = $uploadfiles_model->where($where)->count();

		if (0 < $count) {
			$this->message2('该文件已经上传过，请检查核实!', 'daili_upload');
		}

		$filedata = array();
		$filedata['user_id'] = $userid;
		$filedata['type_id'] = $type_id;
		$filedata['filename'] = $filename;
		$filedata['addtime'] = $this->getDate();
		$filedata['status'] = 0;
		$filedata['downcounts'] = 0;
		if (!empty($_FILES['fileField']) && ($_FILES['fileField']['name'] != '')) {
			$upload = new UploadFile();
			$upload->maxSize = 3145728;
			$upload->allowExts = array('xls');
			$upload->savePath = 'Public/Uploads/daili/' . $userid . '/';
			$upload->autoSub = true;
			$upload->subType = 'custom';

			if ($upload->upload()) {
				$info = $upload->getUploadFileInfo();
			}
			else {
				$this->error($upload->getErrorMsg());
			}

			$filedata['fileurl'] = $upload->savePath . $info[0]['savename'];
		}

		$result = $uploadfiles_model->data($filedata)->add();

		if ($result) {
			$this->message2('上传成功!', 'daili_upload');
		}
		else {
			$this->message2('上传失败：' . $uploadfiles_model->getError(), 'daili_upload');
		}
	}

	public function down_daili()
	{
		$id = I('id', NULL);
		$userid = session('userid');
		$where['id'] = $id;
		$where['user_id'] = $userid;
		$uploadfile = M('uploadfiles')->where($where)->find();
		if (!empty($uploadfile) && ($uploadfile['fileurl'] != '')) {
			$file_path = $uploadfile['fileurl'];
			$file_name = $uploadfile['filename'] . '.xls';
		}
		else {
			$this->message2('文件不存在', 'daili_upload');
		}

		$file_path = iconv('utf-8', 'gb2312', $file_path);
		import('ORG.Net.Http');
		ob_end_clean();
		$download = new Http();
		$file_name = urldecode($file_name);
		$download->download($file_path, $file_name);
	}

	public function file_del()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求!', 'daili_upload');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->ajaxReturn('', '未指定删除项！', 0);
		}

		$userid = session('userid');
		$where['id'] = $id;
		$where['user_id'] = $userid;
		$model = new Model('uploadfiles');
		$result = M('uploadfiles')->where($where)->delete();

		if ($result) {
			$this->ajaxReturn('', '删除成功！', 1);
		}
		else {
			$this->ajaxReturn('', '删除失败：' . $model->getError(), 0);
		}
	}

	public function dh()
	{
		$config = M('config')->where('id=1')->find();
		$danhao_config = json_decode($config['danhao_config'], true);

		if ($danhao_config['valid'] != 1) {
			$this->message2('无该项业务或者业务暂停！', __APP__ . '/Index');
		}

		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $key1 => $v1) {
			$kongbao_page_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $key1 => $v1) {
			$seo_seoo_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		$userid = session('userid');
		$model = new Model('danhao_type');
		$list = $model->order('sort_order asc')->select();
		$type_list = array();

		foreach ($list as $k => $v) {
			if (($danhao_config['stop_view'] == 0) && ($v['state'] == 1)) {
				continue;
			}

			if ($v['state'] == 0) {
				$v['name'] = $v['name'] . '(正常出售)';
			}

			if ($v['state'] == 1) {
				$v['name'] = $v['name'] . '(暂停出售)';
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('level_config', $level_config);
		$this->display();
	}

	public function dhsave()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$order_money = I('total_price', 0);

		if ($order_money <= 0) {
			$this->message2('订单金额不能小于0', 'dh');
		}

		$order_nums = I('qty_item_1', 0);

		if ($order_nums <= 0) {
			$this->message2('请填写订单数目!', 'dh');
		}

		$type_id = I('type_id', '');
		if (empty($type_id) || ($type_id == '')) {
			$this->message2('请选择单号类型!', 'dh');
		}

		$curr_type = M('danhao_type')->where('id=' . $type_id)->find();

		if ($curr_type['state'] == 1) {
			$this->message2('该单号类型目前暂停出售，暂不能下单!', 'dh');
		}

		$where_k['type_id'] = $type_id;
		$where_k['isused'] = 0;
		$count = M('danhao')->where($where_k)->count();

		if ($count < $order_nums) {
			$this->message2('系统内单号数量不足，请联系网站客服!', 'dh');
		}

		$danhao_list_temp = M('danhao')->where($where_k)->limit($order_nums)->select();
		$danhao = array();

		foreach ($danhao_list_temp as $dh) {
			$temp = array();
			$temp['id'] = $dh['id'];
			$temp['note_no'] = $dh['note_no'];
			$danhao[] = $temp;
		}

		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$money = $user['money'];

		if ($money < $order_money) {
			$this->message2('你的账户可用金额不足，请先充值!', U('Cash/recharge'));
		}

		$user_model->startTrans();
		$userdata['id'] = $userid;
		$userdata['money'] = $user['money'] - $order_money;
		$userdata['used_money'] = $user['used_money'] + $order_money;

		if (false !== $user_model->data($userdata)->save()) {
			$order_no = $this->create_orderno('DHBuy');
			$reason = '(购买单号) 订单号 ' . $order_no;
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'buy';
			$account_log['money'] = 0 - $order_money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $user['refer_money'];
			$account_log['order_no'] = $order_no;
			$account_log_model = new Model('account_log');
			$return_1 = D('account_log')->data($account_log)->add();
			$paydata = array();
			$paydata['user_id'] = $userid;
			$paydata['user_type'] = $user['user_type'];
			$paydata['pay_money'] = $order_money;
			$paydata['order_no'] = $order_no;
			$paydata['type'] = 0;
			$paydata['comm'] = '购买单号订单';
			$paydata['status'] = 1;
			$paydata['addtime'] = $this->getDate();
			$return_2 = M('pay_order')->data($paydata)->add();
			if ($return_1 && $return_2) {
				$i = 0;
				$order_time = $this->getDate();
				$result = true;
				$order_no_string = '';

				foreach ($danhao as $dh) {
					$order_no_string .= $dh['note_no'] . ',';
					$danhao_data = array();
					$danhao_data['id'] = $dh['id'];
					$danhao_data['isused'] = 1;
					$danhao_data['order_type'] = 0;
					$danhao_data['order_no'] = $order_no;
					$return_3 = D('danhao')->data($danhao_data)->save();

					if (!$return_3) {
						$result = false;
					}
				}

				if (!$result) {
					$user_model->rollback();
					$this->message2('下单失败1：' . $user_model->getError(), U('dh'));
				}

				$danhao_order_data = array();
				$danhao_order_data['user_id'] = $userid;
				$danhao_order_data['type_id'] = $type_id;
				$danhao_order_data['order_no'] = $order_no;
				$danhao_order_data['order_time'] = $order_time;
				$danhao_order_data['order_status'] = 1;
				$danhao_order_data['note_no'] = rtrim($order_no_string, ',');
				$result_4 = D('danhao_order')->data($danhao_order_data)->add();

				if (!$result_4) {
					$user_model->rollback();
					$this->message2('下单失败2：' . $user_model->getError(), U('dh'));
				}

				$refer_users = calc_commission($userid, 1, 2, $type_id);
				$add_result = true;

				if (!empty($refer_users)) {
					$add_result = addrefer_money($refer_users, 1, $order_nums, 2, $order_no);
				}

				if (!$add_result) {
					$user_model->rollback();
					$this->message2('下单失败3：' . $user_model->getError(), U('dh'));
				}

				$user_model->commit();
				$this->message2('订单提交成功!', U('Log/dh_log', array('ftype' => 'order_no', 'keyword' => $order_no)));
			}
			else {
				$user_model->rollback();
				$this->message2('下单失败4：' . $user_model->getError(), U('dh'));
			}
		}
		else {
			$user_model->rollback();
			$this->message2('下单失败5：' . $user_model->getError(), U('dh'));
		}
	}

	public function xh()
	{
		import('ORG.Util.Page');
		$config = M('config')->where('id=1')->find();
		$xiaohao_config = json_decode($config['xiaohao_config'], true);

		if ($xiaohao_config['valid'] != 1) {
			$this->message2('无该项业务或者业务暂停！', __APP__ . '/Index');
		}

		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $key1 => $v1) {
			$kongbao_page_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$seo_seoo = json_decode($config['seo_seoo'], true);
		$seo_seoo_temp = array();

		foreach ($seo_seoo as $key1 => $v1) {
			$seo_seoo_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('seo_seoo', $seo_seoo_temp);
		$userid = session('userid');
		$model = new Model('xiaohao_type');
		$list = $model->order('sort_order asc')->select();
		$type_list = array();
		$type_list_temp = array();

		foreach ($list as $k => $v) {
			if (($xiaohao_config['stop_view'] == 0) && ($v['state'] == 1)) {
				continue;
			}

			if ($v['state'] == 0) {
				$v['name'] = $v['name'] . '(正常出售)';
			}

			if ($v['state'] == 1) {
				$v['name'] = $v['name'] . '(暂停出售)';
			}

			$v['config'] = json_decode($v['config'], true);
			$type_list[] = $v;
			$type_list_temp[$v['id']] = $v;
		}

		$this->assign('type_list', $type_list);
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		if (empty($user_type) || ($user_type == '')) {
			$user_type = 1;
		}

		$user_level = M('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('level_config', $level_config);
		$where = array();
		$where['isused'] = 0;
		$xh_type_id = I('xh_type_id', '');

		if ($xh_type_id != '') {
			$where['type_id'] = $xh_type_id;
		}

		if ($xiaohao_config['stop_view'] == 0) {
			$where['state'] = 0;
		}

		$xh_model = new XiaohaoViewModel();
		$xh_counts = $xh_model->where($where)->count();
		$page = new Page($xh_counts, 10);
		$show = $page->show();
		$xh_list_temp = $xh_model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$xh_list = array();

		if (!empty($xh_list_temp)) {
			foreach ($xh_list_temp as $xh) {
				$temp = array();
				$temp['id'] = $xh['id'];
				$temp['account'] = rtrim(cut_str($xh['account'], 2, 0), '...') . '';

				if ($xh['encry_key'] != '') {
					$temp['account'] = rtrim(cut_str(encrypt($xh['account'], 'D', $xh['encry_key']), 2, 0), '...') . '***';
				}

				$temp['query_url'] = U('xh_query', array('id' => $xh['id']));
				$temp['type_name'] = $type_list_temp[$xh['type_id']]['name'];
				$temp['price'] = $type_list_temp[$xh['type_id']]['config']['price']['a' . $user_type];
				$xh_list[] = $temp;
			}
		}

		$this->assign('xh_list', $xh_list);
		$this->assign('show', $show);
		$user_type_list = M('user_level')->order('sort_order asc')->select();
		$level_list = array();

		foreach ($user_type_list as $ulevel) {
			$ulevel['config'] = json_decode($ulevel['config'], true);
			if (isset($ulevel['config']['level_view']) && ($ulevel['config']['level_view'] == 1)) {
				continue;
			}

			$ulevel['id'] = 'a' . $ulevel['id'];
			$level_list[] = $ulevel;
		}

		$this->assign('user_level_list', $level_list);
		$this->display();
	}

	public function dddocexp()
	{
		$id = I('id', '');
		$type_id = I('type', 1);
		$userid = session('userid');
		$where['user_id'] = $userid;
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

		$exp_content = (get_magic_quotes_gpc() ? stripslashes($exp_content) : $exp_content);
		$html = '<html><head><title>' . $title . '</title></head><body>' . $exp_content . '</body></html>';
		$html = htmlspecialchars_decode($html);
		echo $html;
		exit();
	}

	public function address_save()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', 'address_add');
		}

		$address_province = I('address_province', '省份');

		if ($address_province == '省份') {
			$this->ajaxReturn('', '请正确填写省份信息!', 0);
		}

		$address_province = I('address_city', '地级市');

		if ($address_province == '地级市') {
			$this->ajaxReturn('', '请正确填写地级市信息!', 0);
		}

		$address_province = I('address_district', '市、县级市');

		if ($address_province == '市、县级市') {
			$this->ajaxReturn('', '请正确填写区县信息!', 0);
		}

		$address = I('address', '填写街道地址(可为空)');

		if ($address == '填写街道地址(可为空)') {
			$address = '';
		}

		$_POST['address'] = $address;
		$address_model = M('user_address');
		$userid = session('userid');
		$address_counts = $address_model->where('user_id=' . $userid)->count();

		if ($address_counts <= 0) {
			$_POST['is_default'] = 1;
		}

		$data = $address_model->create();

		if (false !== $address_model->add()) {
			$this->ajaxReturn('', '地址新增成功!', 1);
		}
		else {
			$this->ajaxReturn('', '地址新增失败!', 0);
		}
	}

	public function address_get()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', 'address_add');
		}

		$userid = session('userid');
		$address_list = M('user_address')->where('user_id=' . $userid)->select();
		$html = '<option value="">==请==选==择==发==货==地==址==</option>';

		foreach ($address_list as $address) {
			$is_default = '';
			$address['title'] = $address['name'] . ',' . $address['shouji'] . ',' . $address['address_province'] . '-' . $address['address_city'] . '-' . $address['address_district'] . '-' . $address['address'];

			if ($address['is_default'] == 1) {
				$is_default = 'selected';
			}

			$html .= '<option value=\'' . $address['id'] . '\' ' . $is_default . '>' . $address['title'] . '</option>';
		}

		$this->ajaxReturn($html, '获取成功', 1);
	}

	public function buyxh()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$return_url = U('xh');
		$id = I('id', 0);

		if ($id <= 0) {
			$this->ajaxReturn($return_url, '未选择要购买的小号！', 0);
		}

		$order_money = I('money', 0);

		if ($order_money <= 0) {
			$this->ajaxReturn($return_url, '选择的小号信息有误，请联系网站客服！', 0);
		}

		$result_data = $this->xhbuy_deal($id, $order_money);

		if (!empty($result_data)) {
			$return_url = $result_data['rtn_url'];

			if ($result_data['result'] == 'success') {
				$this->ajaxReturn($return_url, $result_data['comm'], 1);
			}
			else {
				$this->ajaxReturn($return_url, $result_data['comm'], 0);
			}
		}
		else {
			$this->ajaxReturn($return_url, '小号购买失败，请联系网站客服！', 0);
		}
	}

	public function buyxh_pl()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$return_url = U('xh');
		$id_array = I('id', 0);

		if ($id_array <= 0) {
			$this->message2('未选择要购买的小号！', $return_url);
		}

		$userid = session('userid');
		$user = M('user')->where('id=' . $userid)->find();
		$user_type = 'a' . $user['user_type'];
		$return_msg = '';
		$succ_count = 0;
		$fail_count = 0;
		$total_count = 0;

		if (is_array($id_array)) {
			$total_count = count($id_array);

			foreach ($id_array as $id) {
				$xh_info = M('xiaohao')->where('id=' . $id)->find();
				$xh_type = M('xiaohao_type')->where('id=' . $xh_info['type_id'])->find();
				$price = json_decode($xh_type['config'], true);
				$order_money = 0;

				if (isset($price['price'][$user_type])) {
					$order_money = $price['price'][$user_type];
				}

				if ($order_money <= 0) {
					$return_msg .= '编号:' . $id . ',账号:' . $xh_info['account'] . '购买失败:选择的小号信息有误，请联系网站客服!<br/>';
					$fail_count++;
					continue;
				}

				$result_data = $this->xhbuy_deal($id, $order_money);

				if (!empty($result_data)) {
					$return_url = $result_data['rtn_url'];

					if ($result_data['result'] == 'success') {
						$succ_count++;
						continue;
					}
					else {
						$return_msg .= '编号:' . $id . ',账号:' . $xh_info['account'] . $result_data['comm'] . '<br/>';
						$fail_count++;
						continue;
					}
				}
				else {
					$return_msg .= '编号:' . $id . ',账号:' . $xh_info['account'] . '购买失败:请联系网站客服!<br/>';
					$fail_count++;
					continue;
				}
			}

			$return_temp = '批量购买数量：' . $total_count . '，其中成功：' . $succ_count . ',失败：' . $fail_count;

			if (0 < $fail_count) {
				$return_temp .= '，错误如下：<br/>';
				$return_temp .= $return_msg;
			}

			$this->message2($return_temp, $return_url);
		}
		else {
			$this->message2('批量购买小号失败，请联系网站客服！', $return_url);
		}
	}

	private function xhbuy_deal($id, $order_money)
	{
		$return_data = array();
		$return_data['result'] = 'success';
		$xh_info = M('xiaohao')->where('id=' . $id)->find();

		if (empty($xh_info)) {
			$return_data['result'] = 'fail';
			$return_data['comm'] = '选择的小号不存在，请联系网站客服';
			$return_data['rtn_url'] = U('xh');
			return $return_data;
		}

		$xiaohao_type = M('xiaohao_type')->where('id=' . $xh_info['type_id'])->find();
		$type_config = json_decode($xiaohao_type['config'], true);
		$inprice = (isset($type_config['in_price']) ? $type_config['in_price'] : 0);

		if ($xh_info['isused'] == 1) {
			$return_data['result'] = 'fail';
			$return_data['comm'] = 'Sorry,该小号已经被其他用户购买，请选择其他小号后尽快购买！';
			$return_data['rtn_url'] = U('xh');
			return $return_data;
		}

		$order_nums = 1;
		$userid = session('userid');
		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$utype = 'a' . $user['user_type'];
		$uprice = (isset($type_config['price'][$utype]) ? $type_config['price'][$utype] : 0);
		$money = $user['money'];

		if ($order_money < $uprice) {
			$return_data['result'] = 'fail';
			$return_data['comm'] = '订单金额有误!';
			$return_data['rtn_url'] = U('xh');
			return $return_data;
		}

		if ($money < $order_money) {
			$return_data['result'] = 'fail';
			$return_data['comm'] = '你的账户可用金额不足，请先充值!';
			$return_data['rtn_url'] = U('Cash/recharge');
			return $return_data;
		}

		$xh_data = array();
		$xh_data['isused'] = 1;
		$xh_data['id'] = $id;
		M('xiaohao')->data($xh_data)->save();
		$user_model->startTrans();
		$userdata['id'] = $userid;
		$userdata['money'] = $user['money'] - $order_money;
		$userdata['used_money'] = $user['used_money'] + $order_money;

		if (false !== $user_model->data($userdata)->save()) {
			$order_no = $this->create_orderno('XHBuy');
			$reason = '(购买小号) 订单号 ' . $order_no;
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'buy';
			$account_log['money'] = 0 - $order_money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $user['refer_money'];
			$account_log['order_no'] = $order_no;
			$account_log_model = new Model('account_log');
			$return_1 = D('account_log')->data($account_log)->add();
			$paydata = array();
			$paydata['user_id'] = $userid;
			$paydata['user_type'] = $user['user_type'];
			$paydata['pay_money'] = $order_money;
			$paydata['order_no'] = $order_no;
			$paydata['type'] = 0;
			$paydata['comm'] = '购买小号订单';
			$paydata['status'] = 1;
			$paydata['addtime'] = $this->getDate();
			$return_2 = M('pay_order')->data($paydata)->add();
			if ($return_1 && $return_2) {
				$i = 0;
				$order_time = $this->getDate();
				$result = true;
				$xiaohao_data = array();
				$xiaohao_data['id'] = $xh_info['id'];
				$xiaohao_data['isused'] = 1;
				$xiaohao_data['order_type'] = 0;
				$xiaohao_data['order_no'] = $order_no;
				$return_3 = D('xiaohao')->data($xiaohao_data)->save();

				if (!$return_3) {
					$result = false;
				}

				if (!$result) {
					$user_model->rollback();
					$xh_data = array();
					$xh_data['isused'] = 0;
					$xh_data['id'] = $id;
					M('xiaohao')->data($xh_data)->save();
					$return_data['result'] = 'fail';
					$return_data['comm'] = '购买失败1';
					$return_data['rtn_url'] = U('xh');
					return $return_data;
				}

				$xiaohao_order_data = array();
				$xiaohao_order_data['user_id'] = $userid;
				$xiaohao_order_data['type_id'] = $xh_info['type_id'];
				$xiaohao_order_data['order_no'] = $order_no;
				$xiaohao_order_data['order_time'] = $order_time;
				$xiaohao_order_data['order_status'] = 1;
				$xiaohao_order_data['order_money'] = $uprice;
				$xiaohao_order_data['in_price'] = $inprice;
				$xiaohao_order_data['user_type'] = $user['user_type'];
				$xiaohao_order_data['note_no'] = $xh_info['note_no'];

				if ($xh_info['encry_key'] != '') {
					$xiaohao_order_data['note_no'] = encrypt($xh_info['note_no'], 'D', $xh_info['encry_key']);
				}

				$result_4 = D('xiaohao_order')->data($xiaohao_order_data)->add();

				if (!$result_4) {
					$user_model->rollback();
					$xh_data = array();
					$xh_data['isused'] = 0;
					$xh_data['id'] = $id;
					M('xiaohao')->data($xh_data)->save();
					$return_data['result'] = 'fail';
					$return_data['comm'] = '购买失败2';
					$return_data['rtn_url'] = U('xh');
					return $return_data;
				}

				$refer_users = calc_commission($userid, 1, 3, $xh_info['type_id']);
				$add_result = true;

				if (!empty($refer_users)) {
					$add_result = addrefer_money($refer_users, 1, $order_nums, 3, $order_no);
				}

				if (!$add_result) {
					$user_model->rollback();
					$xh_data = array();
					$xh_data['isused'] = 0;
					$xh_data['id'] = $id;
					M('xiaohao')->data($xh_data)->save();
					$return_data['result'] = 'fail';
					$return_data['comm'] = '购买失败3';
					$return_data['rtn_url'] = U('xh');
					return $return_data;
				}

				$user_model->commit();
				$return_data['comm'] = '购买成功';
				$return_data['rtn_url'] = U('xh');
				return $return_data;
			}
			else {
				$user_model->rollback();
				$xh_data = array();
				$xh_data['isused'] = 0;
				$xh_data['id'] = $id;
				M('xiaohao')->data($xh_data)->save();
				$return_data['result'] = 'fail';
				$return_data['comm'] = '购买失败4';
				$return_data['rtn_url'] = U('xh');
				return $return_data;
			}
		}
		else {
			$user_model->rollback();
			$xh_data = array();
			$xh_data['isused'] = 0;
			$xh_data['id'] = $id;
			M('xiaohao')->data($xh_data)->save();
			$return_data['result'] = 'fail';
			$return_data['comm'] = '购买失败5';
			$return_data['rtn_url'] = U('xh');
			return $return_data;
		}
	}

	public function xh_query()
	{
		$id = I('id', NULL);
		$type = I('type', 1);

		if ($type == 1) {
			$xhinfo = M('xiaohao')->where('id=' . $id)->find();
			$account = $xhinfo['account'];

			if ($xhinfo['encry_key'] != '') {
				$account = encrypt($account, 'D', $xhinfo['encry_key']);
			}
		}

		$account = urlencode(iconv('UTF-8', 'gb2312', $account));
		$this->getTaobaoInfo($account);
		$this->display();
	}

	private function getTaobaoInfo($account)
	{
		$sContent = file_get_contents('http://member1.taobao.com/member/userProfile.jhtml?userID=' . $account);
		$sContent = mb_convert_encoding($sContent, 'UTF-8', 'gb2312');
		$re_date = '/注册时间：[^|]+?(\\d+年\\d+月\\d+日)/';
		$a_date = array();
		$s_regdate = '';

		if (preg_match($re_date, $sContent, $a_date)) {
			$s_regdate = $a_date[1];
		}

		if ($s_regdate == '') {
			$s_regdate = date('Y年m月d日', time() - (30 * 24 * 3600));
		}

		$this->assign('reg_time', $s_regdate);
		$regex = '/(http\\:\\/\\/rate.+)\\".+信用评价/i';
		$matches = array();
		$rateUrl = '';
		$re_shop_userid = '/shopId\\=(\\d+)\\;\\s*user[I|i]d\\=(\\d+)/';
		$a_shop_userid = array();
		$i_shop_shopid = 0;
		$i_shop_userid = 0;
		$re_shop_title = '/淘宝店铺\\：\\s*\\<a.+\\>(.+)\\<\\/a\\>/';
		$a_shop_title = array();
		$s_shop_title = 0;

		if (preg_match($re_shop_userid, $sContent, $a_shop_userid)) {
			$i_shop_shopid = $a_shop_userid[1];
			$i_shop_userid = $a_shop_userid[2];
		}

		if (!is_numeric($i_shop_userid)) {
			$i_shop_userid = 0;
		}

		if ($i_shop_userid == 0) {
			if (preg_match($regex, $sContent, $matches)) {
				$rateUrl = $matches[1];
			}
		}
		else {
			$rateUrl = 'http://rate.taobao.com/user-rate-' . $i_shop_userid . '.htm';
		}

		$sc = file_get_contents($rateUrl);
		$i_7rater_good = 0;
		$i_7rater_normal = 0;
		$i_7rater_bad = 0;
		$i_30rater_good = 0;
		$i_30rater_normal = 0;
		$i_30rater_bad = 0;
		$i_210rater_good = 0;
		$i_210rater_normal = 0;
		$i_210rater_bad = 0;
		$i_historyrater_good = 0;
		$i_historyrater_normal = 0;
		$i_historyrater_bad = 0;
		$i_7rater = 0;
		$i_30rater = 0;
		$i_210rater = 0;
		$i_historyrater = 0;

		if (trim($rseller) == '卖家信息') {
			$this->assign('week_nums', 0);
			$this->assign('mon_nums', 0);
			$this->assign('nums', 0);
		}
		else {
			$re_buyer_7rater = '/timeLine\\=\\-7\\&amp;result\\=1\'\\>(\\d+)[^|]+?timeLine\\=\\-7\\&amp;result\\=0\'\\>(\\d+)[^|]+?timeLine\\=\\-7\\&amp;result\\=\\-1\'\\>(\\d+)/';
			$a_buyer_7rater = array();

			if (preg_match($re_buyer_7rater, $sc, $a_buyer_7rater)) {
				$i_7rater_good = $a_buyer_7rater[1];
				$i_7rater_normal = $a_buyer_7rater[2];
				$i_7rater_bad = $a_buyer_7rater[3];
			}

			$re_buyer_30rater = '/timeLine\\=\\-30\\&amp;result\\=1\'\\>(\\d+)[^|]+?timeLine\\=\\-30\\&amp;result\\=0\'\\>(\\d+)[^|]+?timeLine\\=\\-30\\&amp;result\\=\\-1\'\\>(\\d+)/';
			$a_buyer_30rater = array();

			if (preg_match($re_buyer_30rater, $sc, $a_buyer_30rater)) {
				$i_30rater_good = $a_buyer_30rater[1];
				$i_30rater_normal = $a_buyer_30rater[2];
				$i_30rater_bad = $a_buyer_30rater[3];
			}

			$re_buyer_210rater = '/timeLine\\=\\-210\\&amp;result\\=1\'\\>(\\d+)[^|]+?timeLine\\=\\-210\\&amp;result\\=0\'\\>(\\d+)[^|]+?timeLine\\=\\-210\\&amp;result\\=\\-1\'\\>(\\d+)/';
			$a_buyer_210rater = array();

			if (preg_match($re_buyer_210rater, $sc, $a_buyer_210rater)) {
				$i_210rater_good = $a_buyer_210rater[1];
				$i_210rater_normal = $a_buyer_210rater[2];
				$i_210rater_bad = $a_buyer_210rater[3];
			}

			$re_buyer_historyrater = '/data\\-point-val\\=\\"tbrate\\.2\\.5\\.1\\"\\>(\\d+)[^$]+?data\\-point-val\\=\\"tbrate\\.2\\.5\\.2\\"\\>(\\d+)[^$]+?data\\-point-val\\=\\"tbrate\\.2\\.5\\.3\\"\\>(\\d+)/';
			$a_buyer_historyrater = array();

			if (preg_match($re_buyer_historyrater, $sc, $a_buyer_historyrater)) {
				$i_historyrater_good = $a_buyer_historyrater[1];
				$i_historyrater_normal = $a_buyer_historyrater[2];
				$i_historyrater_bad = $a_buyer_historyrater[3];
			}

			$i_7rater = $i_7rater_good + $i_7rater_normal + $i_7rater_bad;
			$i_30rater = $i_30rater_good + $i_30rater_normal + $i_30rater_bad;
			$i_210rater = $i_210rater_good + $i_210rater_normal + $i_210rater_bad;
			$i_historyrater = $i_historyrater_good + $i_historyrater_normal + $i_historyrater_bad;
			$this->assign('week_nums', $i_7rater);
			$this->assign('mon_nums', $i_30rater);
			$this->assign('nums', $i_210rater + $i_historyrater);
		}
	}
}


?>
