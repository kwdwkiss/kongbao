<?php
//dezend by 辰梦大人 QQ:30881917
class PublicAction extends Action
{
	public function _initialize()
	{
		header('Content-Type:text/html; charset=utf-8');
		$this->web_config = M('config')->where('id=1')->find();
	}

	public function lefttopframe()
	{
		$this->display();
	}

	public function leftframe()
	{
		$this->display();
	}

	public function switchframe()
	{
		$this->display();
	}

	public function mainframe()
	{
		$this->display();
	}

	public function manframe()
	{
		if (!session('?adminid')) {
			redirect(U(C('USER_AUTH_GATEWAY')));
		}

		$config = M('config')->where('id=1')->find();
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$this->assign('xiaohao_config', $xiaohao_config);
		$user_array = array();
		$counts = M('user')->count();
		$user_array['user_counts'] = $counts;
		$model = new UserViewModel();
		$user_array['user_detail'] = $model->field('user_type,type_name,count(1) counts')->group('user_type')->order('user_type asc')->select();
		$this->assign('user_array', $user_array);
		$recharge = array();
		$where = array();
		$where['type'] = 1;
		$recharge_list = M('pay_order')->where($where)->field('status,count(1) counts,sum(pay_money) money')->group('status')->select();

		foreach ($recharge_list as $rk => $rv) {
			if ($rv['status'] == 0) {
				$recharge['n'] = $rv;
			}
			else {
				$recharge['y'] = $rv;
			}
		}

		$recharge['a']['counts'] = $recharge['n']['counts'] + $recharge['y']['counts'];
		$recharge['a']['money'] = $recharge['n']['money'] + $recharge['y']['money'];
		$this->assign('recharge', $recharge);
		$where['type'] = 0;
		$buy_order['y'] = M('pay_order')->where($where)->field('count(1) counts,sum(pay_money) money')->find();
		$this->assign('buy_order', $buy_order);
		$tixian = array();
		$where = array();
		$tixian_list = M('tixian')->where($where)->field('status,count(1) counts,sum(money) money')->group('status')->select();

		foreach ($tixian_list as $tk => $tv) {
			if ($tv['status'] == 1) {
				$tixian['n'] = $tv;
			}
			else if ($tv['status'] == 2) {
				$tixian['y'] = $tv;
			}
			else if ($tv['status'] == 3) {
				$tixian['f'] = $tv;
			}
		}

		$tixian['a']['counts'] = $tixian['n']['counts'] + $tixian['y']['counts'] + $tixian['f']['counts'];
		$tixian['a']['money'] = $tixian['n']['money'] + $tixian['y']['money'] + $tixian['f']['money'];
		$this->assign('tixian', $tixian);
		$kongbao = array();
		$model = new KongbaoViewModel();
		$kongbao['n'] = M('kongbao')->where('isused=0')->count();
		$kongbao['detail'] = $model->field('type_id,name,count(1) counts')->where('isused=0')->group('type_id')->order('type_id asc')->select();
		$this->assign('kongbao', $kongbao);
		$tixing_kb = $kongbao_config['tixing'];
		$tixing_text_kb = '';

		if ($kongbao['n'] <= $tixing_kb) {
			$close_url = '     <a href=\'javascript:void(0)\' onclick=\'btn_close("kongbao")\'>【关闭提示】</a>';
			$tixing_text_kb = '你当前的未使用空包数量已不足，请及时补货！' . $close_url;
		}

		$this->assign('tixing_text_kb', $tixing_text_kb);

		if ($xiaohao_config['valid'] == 1) {
			$xiaohao = array();
			$model = new XiaohaoViewModel();
			$xiaohao['n'] = M('xiaohao')->where('isused=0')->count();
			$xiaohao['detail'] = $model->field('type_id,name,count(1) counts')->where('isused=0')->group('type_id')->order('type_id asc')->select();
			$this->assign('xiaohao', $xiaohao);
			$tixing_xh = $xiaohao_config['tixing'];
			$tixing_text_xh = '';

			if ($xiaohao['n'] <= $tixing_xh) {
				$close_url = '     <a href=\'javascript:void(0)\' onclick=\'btn_close("xiaohao")\'>【关闭提示】</a>';
				$tixing_text_xh = '你当前的未使用小号数量已不足，请及时补货！' . $close_url;
			}

			$this->assign('tixing_text_xh', $tixing_text_xh);
		}

		$auth_code = '';

		if ($config['auth_code'] != '') {
			$auth_code = $config['auth_code'];
			$key_code = $config['auth_keycode'];
			$CommonAction = new CommonAction();
			$auth_code = $CommonAction->passport_decrypt($auth_code, $key_code);
			$auth_code = explode('&', $auth_code);
		}

		$this->assign('auth_code', $auth_code);
		$array['osinfo'] = PHP_OS;
		$array['osinfo'] .= (@ini_get('safe_mode') ? ' Safe Mode' : NULL);
		$array['serverinfo'] = $_SERVER['SERVER_SOFTWARE'];
		$sqlinfo = M()->query('SELECT VERSION()');
		$array['sqlinfo'] = $sqlinfo[0]['VERSION()'];
		$array['serverip'] = $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]';
		$array['servertime'] = date('Y年n月j日 H:i:s');

		if (@ini_get('file_uploads')) {
			$upload = ini_get('upload_max_filesize');
		}
		else {
			$upload = '<font color="red">不支持上传</font>';
		}

		$array['upload'] = $upload;
		$this->assign('config', $config);
		$this->assign($array);
		$this->display();
	}

	public function login()
	{
		if (session('adminid')) {
			if ($this->web_config['back_url'] == '') {
				$this->message2('你已经登录成功！', U('Admin/Index/index'));
			}

			$redirect_url = __APP__ . '/' . $this->web_config['back_url'];
			$this->message2('你已经登录成功！', $redirect_url);
		}

		$this->checkUrl();
		$config = M('config')->where('id=1')->find();

		if (trim($config['backbind_ip']) != '') {
			$error_msg = array();
			$bindips = preg_replace('/([\\s]{2,})/', "\n", trim($config['backbind_ip']));
			$ip_array = explode("\n", $bindips);
			$client_ip = get_client_ip();

			if ($client_ip != '') {
				if (!in_array($client_ip, $ip_array)) {
					redirect(__APP__);
				}
			}
		}

		$this->display();
	}

	private function vgsoft_getip()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		}
		else if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		else if (getenv('REMOTE_ADDR')) {
			$ip = getenv('REMOTE_ADDR');
		}
		else {
			$ip = '';
		}

		return $ip;
	}

	public function logins()
	{
		if ($this->web_config['back_url'] == '') {
			$redirect_url = U('login');
		}
		else {
			$redirect_url = U('login') . '?' . cookie('encry_code');
		}

		$username = I('username', '');
		$password = I('password', '');
		$shouquanma = I('shouquanma', '');

		if ($username == '') {
			$this->message2('请填写管理员账号！', $redirect_url);
		}

		if ($password == '') {
			$this->message2('请填写账号密码！', $redirect_url);
		}

		if ($shouquanma != C('SHOUQUANMA')) {
			$this->message2('授权码错误！', $redirect_url);
		}

		if ($_SESSION['verify'] != md5(strtoupper(I('verify', '')))) {
			$this->message2('验证码错误！', $redirect_url);
		}
		else {
			$user = new Model('admin');
			$condition['username'] = $username;
			$condition['password'] = md5($password);
			$checkUser = $user->where($condition)->find();

			if (!$checkUser) {
				$this->message2('用户名或密码错误！', $redirect_url);
			}
			else {
				session('adminid', $checkUser['id']);
				session('adminname', $condition['username']);
				$user->where('id = ' . $checkUser['id'])->setField('last_login_time', time());
				$ip = get_client_ip();
				$config = M('config')->where('id=1')->find();
				$fetion_setting = json_decode($config['fetion_setting'], true);

				if ($fetion_setting['login_tip'] == 1) {
					$content = '登陆提醒：' . date('Y-m-d H:i:s', time()) . '于' . $ip . '登陆网站后台管理,若非本人操作，请及时修改后台登陆密码！';
					send_fetion($content);
				}

				$logdata = array();
				$logdata['user_id'] = $checkUser['id'];
				$logdata['opt_type'] = '管理员登陆';
				$logdata['comm'] = $_SERVER['HTTP_HOST'];
				$logdata['opt_ip'] = get_client_ip();
				$logdata['addtime'] = date('Y-m-d H:i:s', time());
				M('admin_log')->data($logdata)->add();

				if ($this->web_config['back_url'] == '') {
					$index_url = U('Admin/Index/index');
				}
				else {
					$index_url = __APP__ . '/' . $this->web_config['back_url'];
				}

				$this->message2('登陆成功', $index_url);
			}
		}
	}

	public function verify()
	{
		$type = (isset($_GET['type']) ? $_GET['type'] : 'png');
		import('ORG.Util.Image');
		ob_end_clean();
		Image::buildImageVerify(4, 1, $type, '48', '20');
	}

	public function message($msg, $url)
	{
		$tip = C('SITENAME') . '提示信息:';
		echo '<script>
';
		echo 'var pgo=0;
';
		echo 'function JumpUrl(){
';
		echo 'if(pgo==0){ top.location.href=\'' . $url . '\'; pgo=1; }}' . "\n" . '';
		echo 'document.write("<br/><div style=\'width:400px;margin:150 0 0 300px;padding-top:4px;height:24px;line-height: 24px;font-size:10pt;border:1px solid #cad9ea;background-color:#59d6ff;\'>&nbsp;' . $tip . '</div>");' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;margin:0 0 0 300px;height:100;font-size:10pt;text-align: center;border:1px solid #cad9ea;background-color:#e4f1fa\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo 'document.write("<br/><br/><a href=\'javascript:JumpUrl()\'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>");
';
		echo 'setTimeout(\'JumpUrl()\',5000);</script>
';
		exit();
	}

	public function message2($msg, $url)
	{
		echo '<script>' . "\n" . '';
		echo 'var pgo=0;
';
		echo 'function JumpUrl(){
';
		echo 'if(pgo==0){ top.location.href=\'' . $url . '\'; pgo=1; }}' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;height:100px;margin-top: -50px;margin-left: -200px;position: absolute;top: 50%;left: 50%;font-size:10pt;text-align: center;background:url(/Public/images/htdl.png)\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo 'document.write("<br/><br/><a href=\'javascript:JumpUrl()\'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>");
';
		echo 'setTimeout(\'JumpUrl()\',5000);</script>
';
		exit();
	}

	public function logout()
	{
		if ($this->web_config['back_url'] == '') {
			$index_url = U('Admin/Index/index');
		}
		else {
			$index_url = __APP__ . '/' . $this->web_config['back_url'];
		}

		if (session('?adminid')) {
			session('adminid', NULL);
			session('adminname', NULL);
			cookie('encry_code', NULL);
			$this->message2('注销成功！', $index_url);
		}
		else {
			$this->checkUrl();
			$this->message2('已经注销！', $index_url);
		}

		$this->forward();
	}

	private function checkUrl()
	{
		$back_url = $this->web_config['back_url'];

		if ($back_url != '') {
			$path_array = explode('?', $_SERVER['REQUEST_URI']);
			$route_name = strtolower($path_array[1]);
			$encry_code = cookie('encry_code');
			if ((($back_url != '') && ($route_name != $encry_code)) || empty($encry_code)) {
				redirect(U('Index/Index/index'));
			}
		}
	}
}


?>
