<?php
//dezend by 辰梦大人 QQ:30881917
class IndexAction extends CommonAction
{
	public function index()
	{
		$exp_time = time();
		$exp_datetime = date('Y-m-d H:i:s', $exp_time);
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
		$refer_id = I('ruid', 0);

		if (0 < $refer_id) {
			$refer_user = M('user')->where('id=' . $refer_id)->find();

			if (!empty($refer_user)) {
				$refer_user_type = $refer_user['user_type'];
				$user_level = M('user_level')->where('id=' . $refer_user_type)->find();
				$level_config = json_decode($user_level['config'], true);

				if ($level_config['refer'] == 1) {
					$cruid = cookie('vgs-referid');

					if (!$cruid) {
						cookie('vgs-referid', $refer_id, 604800);
					}
				}
			}
		}

		$model = new Model('gonggao');
		$gonggao = $model->order('time desc')->limit(8)->select();
		$this->assign('gonggao_list', $gonggao);
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
		$config = M('config');
		$setting = $config->where('id=1')->find();
		$mibao_setting = json_decode($setting['mibao_setting'], true);
		$this->assign('mibao_setting', $mibao_setting);
		$cwsz_config = json_decode($this->user['cwsz_config'], true);
		$this->assign('cwsz_config', $cwsz_config);
		$model = new Model('help');
		$help = $model->order('time desc')->limit(8)->select();
		$this->assign('help_list', $help);
		$model = new Model('article');
		$article = $model->order('article_time desc')->limit(8)->select();
		$this->assign('article_list', $article);
		$model = new Model('about');
		$about = $model->order('time desc')->limit(8)->select();
		$this->assign('about_list', $about);
		$model = new UserOrderViewModel();
		$where = array();
		$where['type'] = 1;
		$where['status'] = 1;
		$recharge_list = $model->where($where)->order('addtime desc')->limit(100)->select();
		$this->assign('recharge_list', $recharge_list);
		$where['type'] = 0;
		$where['status'] = 1;
		$pay_order_list = $model->where($where)->order('addtime desc')->limit(100)->select();
		$this->assign('order_list', $pay_order_list);

		if (session('IS_LOGIN') == 1) {
			$userid = session('userid');
			$model = new UserViewModel();
			$user = $model->where('user.id=' . $userid)->find();
			$this->assign('user', $user);
		}
		else {
			$sys_config = M('config')->where('id=1')->find();
			$current_moban = $sys_config['site_template'];
			if (($current_moban == 'default') || ($current_moban == 'longxiang') || ($current_moban == 'php')) {
				$this->display('index_login');
				exit();
			}
		}

		$this->display();
	}

	public function led()
	{
		$config = M('config')->where('id=1')->find();
		$this->assign('info', $config['led_content']);
		$this->display();
	}

	public function verify()
	{
		$type = (isset($_GET['type']) ? $_GET['type'] : 'gif');
		import('ORG.Util.Image');
		ob_end_clean();
		Image::buildImageVerify(4, 1, $type, '', '20');
	}

	public function tuiguang()
	{
		$this->display();
	}
}


?>
