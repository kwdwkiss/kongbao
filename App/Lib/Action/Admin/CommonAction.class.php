<?php
//dezend by 辰梦大人 QQ:308819170
class CommonAction extends Action
{
	public function _initialize()
	{
		header('Content-Type:text/html; charset=utf-8');
		$this->web_config = M('config')->where('id=1')->find();

		if (!session('adminid')) {
			$this->checkUrl();

			if ($this->web_config['back_url'] == '') {
				redirect(U(C('USER_AUTH_GATEWAY')));
			}

			$encry_code = substr(md5($this->web_config['back_url'] . time()), 5, 10);
			cookie('encry_code', $encry_code, 300);
			$url = U(C('USER_AUTH_GATEWAY')) . '?' . $encry_code;
			redirect($url);
		}
	}

	public function message($msg, $url)
	{
		$config = M('config')->where('id=1')->find();
		$tip = $config['sitename'] . '提示信息:';
		echo '<script>
';
		echo 'var pgo=0;
';
		echo 'function JumpUrl(){
';
		echo 'if(pgo==0){ location=\'' . $url . '\'; pgo=1; }}' . "\n" . '';
		echo 'document.write("<br/><div style=\'width:400px;margin:150 0 0 300px;padding-top:4px;height:24px;line-height: 24px;font-size:10pt;color:#fff;background:url(/Public/images/dl.png);\'> ' . $tip . '</div>");' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;margin:0 0 0 300px;height:100;font-size:10pt;text-align: center;border:1px color:#0b4c6f;background:url(/Public/images/dlz.png);\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo 'document.write("<br/><br/><a href=\'' . $url . '\'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>");' . "\n" . '';
		echo 'setTimeout(\'JumpUrl()\',5000);</script>
';
		exit();
	}

	public function message2($msg, $url)
	{
		$config = M('config')->where('id=1')->find();
		$tip = $config['sitename'] . '提示信息:';
		echo '<script>
';
		echo 'var pgo=0;
';
		echo 'function JumpUrl(){
';
		echo 'if(pgo==0){ location=\'' . $url . '\'; pgo=1; }}' . "\n" . '';
		echo 'document.write("<br/><div style=\'width:400px;margin:150 0 0 300px;padding-top:4px;height:24px;line-height: 24px;font-size:10pt;color:#fff;background:url(/Public/images/dl.png);\'> ' . $tip . '</div>");' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;margin:0 0 0 300px;height:100;font-size:10pt;text-align: center;border:1px color:#0b4c6f;background:url(/Public/images/dlz.png);\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo 'document.write("<br/><br/><a href=\'' . $url . '\'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>");' . "\n" . '';
		echo 'setTimeout(\'JumpUrl()\',5000);</script>
';
		exit();
	}

	public function passport_decrypt($txt, $key)
	{
		$txt = $this->passport_key(base64_decode($txt), $key);
		$tmp = '';

		for ($i = 0; $i < strlen($txt); $i++) {
			$md5 = $txt[$i];
			$tmp .= $txt[++$i] ^ $md5;
		}

		return $tmp;
	}

	public function passport_key($txt, $encrypt_key)
	{
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';

		for ($i = 0; $i < strlen($txt); $i++) {
			$ctr = ($ctr == strlen($encrypt_key) ? 0 : $ctr);
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}

		return $tmp;
	}

	public function judge_code_valid()
	{
		$config = M('config')->where('id=1')->find();
		$code = C('AUTH_TYPE');

		if ($config['auth_code'] != '') {
			$expire_data = false;
			$auth_code = $this->passport_decrypt($config['auth_code'], $config['auth_keycode']);
			$auth_code = explode('&', $auth_code);
			$endtime = $auth_code[2];

			if ($endtime < time()) {
				$expire_data = true;
			}

			$domain = $_SERVER['HTTP_HOST'];
			if ((md5($code . 'vgsoft' . $domain . 'vgsoft') != $config['auth_host']) || $expire_data) {
				$logdata = 'domain:' . $domain . ' code:' . $code . ' expire_data:' . $endtime . ' auth_host:' . $config['auth_host'];
				$this->write_log_file($logdata);
				$data['id'] = 1;
				$data['auth_code'] = '';
				$data['auth_keycode'] = '';
				$data['auth_host'] = '';
				M('config')->data($data)->save();
				session('adminid', NULL);
				session('adminname', NULL);
				$index_url = __APP__ . '/Index/Index/index';
				redirect($index_url);
			}
		}
	}

	private function write_log_file($data)
	{
		$file = 'Public/Uploads/log.txt';
		$filedata = fopen($file, 'a');
		fwrite($filedata, 'Time:' . date('Y-m-d H:i:s', time()) . '  ' . $data . "\r\n");
		fclose($filedata);
	}

	public function opt_auth($acode, $keycode, $host)
	{
		$code = C('AUTH_TYPE');
		$auth_host = md5($code . 'vgsoft' . $host . 'vgsoft');
		$data = array();
		$data['auth_code'] = $acode;
		$data['auth_keycode'] = $keycode;
		$data['auth_host'] = $auth_host;

		if (false !== M('config')->where('id=1')->data($data)->save()) {
			return true;
		}
		else {
			return false;
		}
	}

	private function checkUrl()
	{
		$_SERVER['HTTP_HOST']==C('ADMIN_DOMAIN')||exit();
		$back_url = $this->web_config['back_url'];
		$path_array = explode('/', $_SERVER['REQUEST_URI']);
		$path_count = count($path_array) - 1;
		$route_name = strtolower($path_array[$path_count]);
		if (($back_url != '') && ($route_name != strtolower($back_url))) {
			redirect(U('Index/Index/index'));
		}
	}
}

?>
