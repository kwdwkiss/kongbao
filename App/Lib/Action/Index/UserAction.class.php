<?php
//dezend by 辰梦大人 QQ:30881917
class UserAction extends CommonAction
{
	public function _initialize()
	{
		parent::_initialize();
	}

	public function index()
	{
		$this->home();
	}

	public function home()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$config = M('config')->where('id=1')->find();
		$tuiguang_url = 'http://' . $_SERVER['HTTP_HOST'] . __APP__ . '?ruid=' . session('userid');

		if ($config['tuiguang_shorturl'] == 1) {
			if (!class_exists('Short_Url')) {
				require 'Public/Short_Url.php';
			}

			$short_url = new Short_Url();
			$tuiguang_url = $short_url->url_short($tuiguang_url);
		}

		$image_url = 'http://' . $_SERVER['HTTP_HOST'] . $config['tg_images'];
		$tg_content1 = '';
		$tg_content2 = '';
		$tg_content3 = '';

		if ($config['tg_content1'] != '') {
			$tg_content1 = str_replace('{refer_url}', $tuiguang_url, $config['tg_content1']);
		}

		if ($config['tg_content2'] != '') {
			$tg_content2 = str_replace('{refer_url}', $tuiguang_url, $config['tg_content2']);
		}

		if ($config['tg_content3'] != '') {
			$tg_content3 = str_replace('{refer_url}', $tuiguang_url, $config['tg_content3']);
		}

		$this->assign('image_url', $image_url);
		$this->assign('tuiguang_url', $tuiguang_url);
		$this->assign('tg_content', array($tg_content1, $tg_content2, $tg_content3));
		$refer_nums = D('user')->where('refer_id=' . session('userid'))->count();
		$this->assign('refer_nums', $refer_nums);
		$reg_setting = M('user')->where('id=' . session('userid'))->find();
		$wrefer_id = $reg_setting['refer_id'];
		$vip_setting = json_decode($reg_setting['vip_set'], true);
		$wrefer = M('user')->where('id=' . $wrefer_id)->find();
		$wusername = $wrefer['username'];
		$this->assign('wusername', $wusername);
		$this->assign('vip_setting', $vip_setting);
		$config = M('config');
		$setting = $config->where('id=1')->find();
		$mibao_setting = json_decode($setting['mibao_setting'], true);
		$this->assign('mibao_setting', $mibao_setting);
		session('userid');
		$row = M('user')->where('id=' . session('userid'))->find();

		if (trim($row['mibao_setting']) == '') {
			$mibao_display = 'none';
		}

		$this->assign('mibao_display', $mibao_display);
		$cwsz_config = json_decode($this->user['cwsz_config'], true);
		$this->assign('cwsz_config', $cwsz_config);
		$cwsz_config = json_decode($user['cwsz_config'], true);
		$this->assign('userInfo', $user);
		$this->assign('cwsz_config', $cwsz_config);
		$config = M('config')->where('id=1')->find();
		$tixian_setting = json_decode($config['tixian_setting'], true);
		$this->assign('tixian_setting', $tixian_setting);
		$model = new UserOrderViewModel();
		$where = array();
		$where['type'] = 1;
		$where['status'] = 1;
		$recharge_list = $model->where($where)->order('addtime desc')->limit(100)->select();
		$this->assign('recharge_list', $recharge_list);
		$where = array();
		$where['type'] = 0;
		$where['status'] = 1;
		$pay_order_list = $model->where($where)->order('addtime desc')->limit(100)->select();
		$this->assign('order_list', $pay_order_list);
		$gonggao_list = M('gonggao')->order('time desc')->limit(5)->select();
		$this->assign('gonggao_list', $gonggao_list);
		$help_list = M('help')->order('time desc')->limit(5)->select();
		$this->assign('help_list', $help_list);
		$sys_config = M('config')->where('id=1')->find();
		$current_moban = $sys_config['site_template'];

		if ($current_moban == 'kb580') {
			$this->getReferInfo();
		}

		$this->display();
	}

	public function reg()
	{
		if (session('IS_LOGIN') == 1) {
			$this->message2('您已经登录系统！', __APP__ . '/Index');
		}

		$refer_id = cookie('vgs-referid');

		if (!$refer_id) {
			$refer_id = 0;
		}

		if (0 < $refer_id) {
			$refer_user = M('user')->where('id=' . $refer_id)->find();

			if (!empty($refer_user)) {
				$refer_user_type = $refer_user['user_type'];
				$user_level = M('user_level')->where('id=' . $refer_user_type)->find();
				$level_config = json_decode($user_level['config'], true);

				if ($level_config['refer'] != 1) {
					$refer_id = 0;
				}
			}
			else {
				$refer_id = 0;
			}
		}

		$this->assign('refer_id', $refer_id);
		$config = M('config')->where('id=1')->find();
		$reg_setting = json_decode($config['reg_setting'], true);
		$mibao_setting = json_decode($config['mibao_setting'], true);
		$this->assign('mibao_setting', $mibao_setting);
		$this->assign('reg_setting', $reg_setting);
		$this->display();
	}

	public function verify()
	{
		$type = I('type', 'gif');
		import('ORG.Util.Image');
		ob_end_clean();
		Image::buildImageVerify(4, 1, $type, '', '20px');
	}

	public function Regsave()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$config = M('config')->where('id=1')->find();
		$reg_setting = json_decode($config['reg_setting'], true);
		$user_model = M('user');
		$data = array();

		if ($reg_setting[verycode] == 1) {
			if (session('verify') != md5(strtoupper(I('captcha', '')))) {
				$this->message2('验证码不正确!', 'reg');
			}
		}

		$is_valid = 1;

		if ($reg_setting['shenhe'] == 1) {
			$data['isvalid'] = 0;
			$is_valid = 0;
		}

		$zs_money = 0;
		if (($reg_setting['zs_valid'] == 1) && (0 < $reg_setting['zs_money'])) {
			$zs_money = $reg_setting['zs_money'];
		}

		$zs_user_type = 1;
		if (($reg_setting['zs_levl'] == 1) && (0 < $reg_setting['zs_user_type'])) {
			$zs_user_type = $reg_setting['zs_user_type'];
		}

		$create_ip = get_client_ip();

		if ($reg_setting['reg_ip'] == 1) {
			$ip_counts = $reg_setting['ip_counts'];
			$reg_counts = M('user')->where('create_ip="' . $create_ip . '"')->count();

			if ($ip_counts <= $reg_counts) {
				$this->message2('注册失败：您的IP已被限制注册!', 'reg');
			}
		}

		$username = I('user_name', NULL);
		$data['username'] = $username;
		$data['password'] = MD5(I('pwd', NULL));
		$data['email'] = I('email', NULL);
		$data['user_qq'] = I('user_qq', NULL);

		if (0 < $zs_money) {
			$data['money'] = $zs_money;
		}

		if (0 < $zs_user_type) {
			$data['user_type'] = $zs_user_type;
		}

		$data['create_time'] = $this->getDate();
		$data['last_login_time'] = $this->getDate();
		$data['refer_id'] = I('refer_id', 0);
		$data['last_login_ip'] = get_client_ip();
		$data['login_counts'] = 1;
		$data['create_ip'] = $create_ip;

		if ($reg_setting['reg_mibao'] == 1) {
			$mibao_name = I('mibao_name', '');
			$mibao_daan = I('mibao_daan', '');
			$data['mibao_setting'] = '{name:"' . $mibao_name . '",daan:"' . $mibao_daan . '"}';

			if ($mibao_name == '') {
				$this->message2('请选择保密问题，便于日后找回密码!', __URL__ . '/reg');
			}

			if ($mibao_daan == '') {
				$this->message2('请输入保密答案!', __URL__ . '/reg');
			}
		}

		$userid = $user_model->data($data)->add();

		if (false !== $userid) {
			if (0 < $zs_money) {
				if (0 < $zs_user_type) {
					$log_array = array();
					$log_array['user_id'] = $userid;
					$log_array['stage'] = 'admin';
					$log_array['money'] = $zs_money;
					$log_array['remain_money'] = $zs_money;
					$log_array['user_type'] = $zs_user_type;
					$log_array['comm'] = '会员注册赠送体验金';
					$log_array['addtime'] = $this->getDate();
					M('account_log')->data($log_array)->add();
				}
			}

			session('userid', $userid);
			session('username', $username);
			session('IS_LOGIN', 1);
			session('isvalid', $is_valid);
			$this->message2('注册成功!', __APP__ . '/User/Home');
		}
		else {
			$this->message2('注册失败：' . $user_model->getError(), 'reg');
		}
	}

	public function checkUser()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$username = I('username', '');
		$count = D('user')->where('username=\'' . $username . '\'')->count();

		if (0 < $count) {
			echo 'false';
			exit();
		}
		else {
			echo 'true';
			exit();
		}
	}

	public function checkEmail()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$email = I('email', '');
		$count = D('user')->where('email=\'' . $email . '\'')->count();

		if (0 < $count) {
			echo 'false';
			exit();
		}
		else {
			echo 'true';
			exit();
		}
	}

	public function checkUserqq()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$user_qq = I('user_qq', '');
		$count = D('user')->where('user_qq=\'' . $user_qq . '\'')->count();

		if (0 < $count) {
			echo 'false';
			exit();
		}
		else {
			echo 'true';
			exit();
		}
	}

	public function checkVerifyCode()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		if (session('verify') != md5(strtoupper(I('captcha', '')))) {
			echo 'false';
			exit();
		}
		else {
			echo 'true';
			exit();
		}
	}

	public function uslogin()
	{
		$config = M('config');
		$setting = $config->where('id=1')->find();
		$mibao_setting = json_decode($setting['mibao_setting'], true);
		$this->assign('mibao_setting', $mibao_setting);
		$this->display();
	}

	public function login()
	{
		$username = I('username', NULL);

		if (empty($username)) {
			$this->message2('请输入登陆名', __APP__ . '/Index');
		}

		$password = I('password', NULL);

		if (empty($password)) {
			$this->message2('请输入登陆密码', __APP__ . '/Index');
		}

		$where['username'] = $username;
		$where['password'] = MD5($password);
		$user = M('user')->where($where)->find();

		if (empty($user)) {
			$this->message2('用户名或密码错误', __APP__ . '/Index');
		}
		else {
			$userdata = array();
			$userdata['last_login_time'] = $this->getDate();
			$userdata['last_login_ip'] = get_client_ip();
			$userdata['login_counts'] = $user['login_counts'] + 1;
			$user_model = M('user');

			if (false !== $user_model->where('id=' . $user['id'])->data($userdata)->save()) {
				session('userid', $user['id']);
				session('username', $username);
				session('IS_LOGIN', 1);
				session('isvalid', $user['isvalid']);
				$logdata = array();
				$logdata['user_id'] = $user['id'];
				$logdata['opt_type'] = '用户登陆';
				$logdata['opt_ip'] = get_client_ip();
				$logdata['addtime'] = $this->getDate();
				M('user_log')->data($logdata)->add();
				$sys_config = M('config')->where('id=1')->find();
				$current_moban = $sys_config['site_template'];

				if ($current_moban == 'longxiang') {
					$this->message2('登陆成功!', 'home');
					exit();
				}

				$this->message2('登陆成功!', __APP__ . '/Index');
			}
			else {
				$this->message2('登陆失败：' . $user_model->getError(), __APP__ . '/Index/');
			}
		}
	}

	public function logout()
	{
		unset($_SESSION['username']);
		unset($_SESSION['userid']);
		unset($_SESSION['IS_LOGIN']);
		unset($_SESSION['isvalid']);
		$this->message2('安全退出!', __APP__ . '/');
	}

	public function reinfo()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$this->display();
	}

	public function repass()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$this->display();
	}

	public function mibao()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$config = M('config');
		$setting = $config->where('id=1')->find();
		$mibao_setting = json_decode($setting['mibao_setting'], true);
		$this->assign('mibao_setting', $mibao_setting);
		session('userid');
		$row = M('user')->where('id=' . session('userid'))->find();

		if (trim($row['mibao_setting']) == '') {
			$mibao_display = 'none';
		}

		$this->assign('mibao_display', $mibao_display);
		$this->display();
	}

	public function mibao_save()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __URL__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$mibao_name_old = I('mibao_name_old', '');
		$mibao_daan_old = I('mibao_daan_old', '');
		$mibao_name = I('mibao_name', '');
		$mibao_daan = I('mibao_daan', '');
		$row = M('user')->where('id=' . session('userid'))->find();
		$mibao_setting_old = '{name:"' . $mibao_name_old . '",daan:"' . $mibao_daan_old . '"}';

		if (trim($row['mibao_setting']) != '') {
			if ($row['mibao_setting'] != $mibao_setting_old) {
				$this->message2('旧保密提问错误', __URL__ . '/Home');
			}

			if ($mibao_name == '') {
				$this->message2('请选择提问!', __URL__ . '/Home');
			}
		}

		$model = M('user');
		$map['id'] = session('userid');
		$data['mibao_setting'] = '{name:"' . $mibao_name . '",daan:"' . $mibao_daan . '"}';

		if (false !== $model->where($map)->save($data)) {
			$this->message2('设置成功', __URL__ . '/Home');
		}
		else {
			$this->message2('设置失败：' . $model->getError(), __URL__ . '/Home');
		}

		exit();
	}

	public function back()
	{
		$config = M('config');
		$setting = $config->where('id=1')->find();
		$mibao_setting = json_decode($setting['mibao_setting'], true);
		$this->assign('mibao_setting', $mibao_setting);
		$this->display();
	}

	public function back_send()
	{
		$mibao_name = I('mibao_name', '');
		$mibao_daan = I('mibao_daan', '');
		$mibao_email = I('mibao_email', '');

		if ($mibao_name == '') {
			$this->message2('请选择提问!', __APP__ . '/Index');
		}

		if ($mibao_name == '') {
			$this->message2('请选择提问!', __APP__ . '/Index');
		}

		$model = M('user');
		$map['email'] = $mibao_email;
		$map['mibao_setting'] = '{name:"' . $mibao_name . '",daan:"' . $mibao_daan . '"}';
		$row = $model->where($map)->find();

		if ($row) {
			$newpwd = time();
			$map1['id'] = $row['id'];
			$data1['password'] = md5($newpwd);
			$model->where($map1)->save($data1);
			$this->assign('username', $row['username']);
			$this->assign('userpwd', $newpwd);
			$this->display('xinmima');
		}
		else {
			$this->message2('找回失败：' . $model->getError(), __APP__ . '/Index');
		}
	}

	public function cwsz()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$cwsz_config = json_decode($this->user['cwsz_config'], true);
		$this->assign('cwsz_config', $cwsz_config);
		$this->display();
	}

	public function address()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$model = M('user_address');
		$address_list = $model->where('user_id=' . $userid)->select();
		$address = array();

		foreach ($address_list as $k => $v) {
			$v['address'] = $v['address_province'] . '~' . $v['address_city'] . '~' . $v['address_district'] . '~' . $v['address'] . ',' . $v['zipcode'];
			$address[] = $v;
		}

		$this->assign('address_list', $address);
		$this->display();
	}

	public function address_add()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$this->display();
	}

	public function address_save()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'address_add');
		}

		$address_province = I('address_province', '省份');

		if ($address_province == '省份') {
			$this->message2('请正确填写省份信息!', 'address_add');
		}

		$address_province = I('address_city', '地级市');

		if ($address_province == '地级市') {
			$this->message2('请正确填写地级市信息!', 'address_add');
		}

		$address_province = I('address_district', '市、县级市');

		if ($address_province == '市、县级市') {
			$this->message2('请正确填写区县信息!', 'address_add');
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
			$this->message2('地址新增成功', 'address');
		}
		else {
			$this->message2('地址新增失败', 'address');
		}
	}

	public function address_edit()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未选择编辑项!', 'address');
		}

		$address = M('user_address')->where('id=' . $id)->find();
		$this->assign('address', $address);
		$this->display();
	}

	public function address_update()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'address');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未选择编辑项!', 'address');
		}

		$address_province = I('address_province', '省份');

		if ($address_province == '省份') {
			$this->message2('请正确填写省份信息!', 'address');
		}

		$address_province = I('address_city', '地级市');

		if ($address_province == '地级市') {
			$this->message2('请正确填写地级市信息!', 'address');
		}

		$address_province = I('address_district', '市、县级市');

		if ($address_province == '市、县级市') {
			$this->message2('请正确填写区县信息!', 'address');
		}

		$address = I('address', '填写街道地址(可为空)');

		if ($address == '填写街道地址(可为空)') {
			$address = '';
		}

		$_POST['address'] = $address;
		$address_model = M('user_address');
		$data = $address_model->create();

		if (false !== $address_model->where('id=' . $id)->save()) {
			$this->message2('地址编辑成功', 'address');
		}
		else {
			$this->message2('地址编辑失败', 'address');
		}
	}

	public function address_del()
	{
		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未选择删除项!', U('address'));
		}

		$model = M('user_address');

		if (false !== $model->where('id=' . $id)->delete()) {
			$this->message2('删除成功!', U('address'));
		}
		else {
			$this->message2('删除失败：' . $model->getError(), U('address'));
		}
	}

	public function address_default()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求', 'address');
		}

		$id = I('id', NULL);

		if (empty($id)) {
			$this->message2('未选择编辑项!', 'address');
		}

		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		$address_data['is_default'] = 0;
		$where['user_id'] = session('userid');
		D('user_address')->where($where)->save($address_data);
		$address_data['is_default'] = 1;
		$where['id'] = $id;
		$model = D('user_address');
		$return = $model->where($where)->save($address_data);

		if (false !== $return) {
			$this->ajaxReturn('', '默认设置成功', 1);
		}
		else {
			$this->ajaxReturn('', '默认设置失败：' . $model->getError(), 0);
		}
	}

	public function savepass()
	{
		if (!IS_POST) {
			$this->message2('无效请求', 'repass');
		}

		$id = I('id', NULL);
		$user = M('user');
		$data = $user->create();

		if (!empty($id)) {
			$data['password'] = md5($data['password']);

			if (false !== $user->where('id=\'' . $id . '\'')->data($data)->save()) {
				session('userid', NULL);
				session('IS_LOGIN', NULL);
				$this->message2('操作成功,请重新登陆系统', __APP__ . '/index');
			}
			else {
				$this->message2('操作失败：' . $user->getDbError());
			}
		}
		else {
			$this->message2('请选择编辑用户', __URL__ . '/index');
		}
	}

	public function savecwsz()
	{
		if (!IS_POST) {
			$this->message2('无效请求', __APP__ . '/Index');
		}

		$id = session('userid');

		if (!empty($id)) {
			$user = new UserModel();
			$data = $user->create();
			$cwsz_config = I('post.cwsz_config', '', 'strip_tags');

			if ($cwsz_config['account_no'] == '') {
				$this->message2('提现账号不能为空!', __APP__ . '/User/Home');
			}

			if ($cwsz_config['account_name'] == '') {
				$this->message2('提现账户名不能为空!', __APP__ . '/User/Home');
			}

			if ($cwsz_config['type'] == '3') {
				if ($cwsz_config['account_bank'] == '') {
					$this->message2('开户银行不能为空!', __APP__ . '/User/Home');
				}

				if ($cwsz_config['account_address'] == '') {
					$this->message2('开户银行所在地不能为空!', __APP__ . '/User/Home');
				}
			}

			$data['cwsz_config'] = json_encode($cwsz_config);

			if (false !== $user->save($data)) {
				$this->message2('编辑成功', __APP__ . '/User/Home');
			}
			else {
				$this->message2('编辑失败' . $user->getError(), __APP__ . '/User/Home');
			}
		}
		else {
			$this->message2('请选择编辑对象', __APP__ . '/User/Home');
		}
	}

	public function saveuserinfo()
	{
		$id = session('userid');

		if (!empty($id)) {
			$user = new UserModel();
			$data = $user->create();

			if (false !== $user->save()) {
				$this->message2('编辑成功', 'reinfo');
			}
			else {
				$this->message2('编辑失败' . $user->getError(), 'reinfo');
			}
		}
		else {
			$this->message2('请选择编辑对象', __URL__ . '/index');
		}
	}

	public function complaint()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		import('ORG.Util.Page');
		$model = M('guest_book');
		$where['user_id'] = session('userid');
		$stage = I('stage', '');

		if ($stage != '') {
			$where['stage'] = $stage;
		}

		$id = I('id', NULL);

		if (!empty($id)) {
			$where['status'] = 1;
		}
		else {
			$where['status'] = 0;
		}

		$count = $model->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$log_list = $model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$status = $where['status'];
		$this->assign('status', $status);
		$this->assign('show', $show);
		$this->assign('count', $count);
		$this->assign('log_list', $log_list);
		$this->display();
	}

	public function liuyan_add()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/Index');
		}

		if (!IS_POST) {
			$this->message2('无效请求', 'liuyan');
		}

		$logdata = array();
		$logdata['user_id'] = session('userid');
		$logdata['username'] = session('username');
		$logdata['opt_type'] = $_POST['type'];
		$logdata['title'] = I('post.title', '', 'strip_tags');
		$logdata['content'] = I('post.content', '', 'strip_tags');
		$logdata['addtime'] = $this->getDate();
		M('guest_book')->data($logdata)->add();
		$this->message2('问题提交成功', 'complaint');
	}

	public function liuyaninfo()
	{
		$id = I('id', NULL);

		if (!empty($id)) {
			$xiaohao = M('guest_book')->where('id=' . $id)->find();
			$this->assign('xiaohao', $xiaohao);
		}
		else {
			$this->message2('请选择要编辑的项目', 'index');
		}

		$this->display();
	}

	private function getReferInfo()
	{
		$userid = session('userid');
		$user = M('user')->where('id=' . $userid)->find();
		$user_level = M('user_level')->where('id=' . $user['user_type'])->find();
		$level_config = json_decode($user_level['config'], true);
		$this->assign('allow_refer', $level_config['refer']);

		if ($level_config['refer'] == 1) {
			$config = M('config')->where('id=1')->find();
			$tuiguang_url = 'http://' . $_SERVER['HTTP_HOST'] . __APP__ . '?ruid=' . session('userid');

			if ($config['tuiguang_shorturl'] == 1) {
				if (!class_exists('Short_Url')) {
					require 'Public/Short_Url.php';
				}

				$short_url = new Short_Url();
				$tuiguang_url = $short_url->url_short($tuiguang_url);
			}

			$image_url = 'http://' . $_SERVER['HTTP_HOST'] . $config['tg_images'];
			$tg_content1 = '';
			$tg_content2 = '';
			$tg_content3 = '';

			if ($config['tg_content1'] != '') {
				$tg_content1 = str_replace('{refer_url}', $tuiguang_url, $config['tg_content1']);
			}

			if ($config['tg_content2'] != '') {
				$tg_content2 = str_replace('{refer_url}', $tuiguang_url, $config['tg_content2']);
			}

			if ($config['tg_content3'] != '') {
				$tg_content3 = str_replace('{refer_url}', $tuiguang_url, $config['tg_content3']);
			}

			$this->assign('image_url', $image_url);
			$this->assign('tuiguang_url', $tuiguang_url);
			$this->assign('tg_content', array($tg_content1, $tg_content2, $tg_content3));
		}
	}
}


?>
