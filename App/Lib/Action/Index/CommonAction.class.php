<?php
//dezend by 辰梦大人 QQ:30881917
echo ' ';
header('Content-Type:text/html; charset=utf-8');
class CommonAction extends Action
{
	public function _initialize()
	{
		$config = M('config')->where('id=1')->find();

		if ($config['site_status'] == 0) {
			$this->message3($config['close_reason']);
		}

		$current_moban = $config['site_template'];
		$_POST['t'] = $current_moban;
		$_GET['t'] = $current_moban;
		$this->assign('config', $config);
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$this->assign('kongbao_config', $kongbao_config);
		$kongbao_page = json_decode($config['kongbao_page'], true);
		$danhao_config = json_decode($config['danhao_config'], true);
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$this->assign('danhao_config', $danhao_config);
		$this->assign('xiaohao_config', $xiaohao_config);
		$reg_setting = json_decode($config['reg_setting'], true);
		$this->assign('reg_setting', $reg_setting);
		$qq_setting = json_decode($config['qq_setting'], true);
		$this->assign('qq_setting', $qq_setting);
		$sj_setting = json_decode($config['sj_setting'], true);
		$this->assign('sj_setting', $sj_setting);
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
		$this->assign('seo_keyword', $config['metakeys']);
		$this->assign('seo_desc', $config['metadesc']);
		$this->assign('seo_title', $config['metatitle']);
		$rand_num = rand(1, 5);
		$header_adv = M('adv')->where('typeid=2')->order($rand_num)->find();
		$adv_id = $header_adv['id'];
		$this->assign('header_adv', $header_adv);
		$rand_num = rand(1, 5);
		$header_adv1 = M('adv')->where('typeid=2 and id<>' . $adv_id)->order($rand_num)->find();
		$this->assign('header_adv1', $header_adv1);
		$slide_list = M('adv')->where('typeid=1')->select();
		$this->assign('slide_list', $slide_list);
		$footer_adv = M('adv')->where('typeid=3')->order($rand_num)->find();
		$this->assign('footer_adv', $footer_adv);
		$kbhdp_list = M('adv')->where('typeid=4')->select();
		$this->assign('kbhdp_list', $kbhdp_list);
		$loging_adv = M('adv')->where('typeid=5')->order($rand_num)->find();
		$this->assign('loging_adv', $loging_adv);
		$loginj_adv = M('adv')->where('typeid=6')->order($rand_num)->find();
		$this->assign('loginj_adv', $loginj_adv);
		$model = new Model('link');
		$flink = $model->order('addtime asc')->select();
		$this->assign('flink_list', $flink);
		$kefu_type_list = M('kefu_type')->order('sort_order asc')->select();
		$kefu_list = array();

		foreach ($kefu_type_list as $ktype => $kvalue) {
			$kefu_list[$kvalue['id']]['title'] = $kvalue['title'];
			$kf_list = M('kefu')->where('kf_type=' . $kvalue['id'])->order('sort_order asc')->select();
			$kefu_list[$kvalue['id']]['data'] = $kf_list;
		}

		$this->assign('kefu_list', $kefu_list);

		if (session('IS_LOGIN') == 1) {
			$userid = session('userid');
			$model = new UserViewModel();
			$user = $model->where('user.id=' . $userid)->find();
			$this->assign('user', $user);

			if (0 < $user['refer_id']) {
				$refer_qq = '';
				$refer_user = M('user')->where('id=' . $user['refer_id'])->find();

				if (!empty($refer_user)) {
					$refer_qq = $refer_user['user_qq'];
				}

				$this->assign('refer_qq', $refer_qq);
			}
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
		echo 'document.write("<br/>div style=\'width:400px;margin:280 0 0 37%;padding-top:4px;height:24px;line-height: 24px;font-size:10pt;color:#fff;background:url(/Public/images/dl.png);\'>&nbsp;' . $tip . '</div>");' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;margin:0 0 0 37%;height:100;font-size:10pt;text-align: center;border:1px color:#0b4c6f;background:url(/Public/images/dlz.png);\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo 'document.write("<br/><br/><a href=\'' . $url . '\'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>");' . "\n" . '';
		echo 'setTimeout(\'JumpUrl()\',2000);</script>
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
		echo 'document.write("<br/><div style=\'width:400px;margin:280 0 0 37%;padding-top:4px;height:24px;line-height: 24px;font-size:10pt;color:#fff;background:url(/Public/images/dl.png);\'>&nbsp;' . $tip . '</div>");' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;margin:0 0 0 37%;height:100;font-size:10pt;text-align: center;border:1px color:#0b4c6f;background:url(/Public/images/dlz.png);\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo 'document.write("<br/><br/><a href=\'' . $url . '\'>如果你的浏览器没反应，请点击这里...</a><br/><br/></div>");' . "\n" . '';
		echo 'setTimeout(\'JumpUrl()\',2000);</script>
';
		exit();
	}

	public function getDate()
	{
		return date('Y-m-d H:i:s');
	}

	public function create_orderno($type)
	{
		$order_no = $type . date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), -8, 8);
		return $order_no;
	}

	public function message3($msg)
	{
		$config = M('config')->where('id=1')->find();
		$tip = $config['sitename'] . '提示信息:';
		echo '<script>
';
		echo 'document.write("<br/><div style=\'width:400px;margin:280 0 0 37%;padding-top:4px;height:24px;line-height: 24px;font-size:10pt;border:1px solid #cad9ea;background-color:#4ebfff;\'> ' . $tip . '</div>");' . "\n" . '';
		echo 'document.write("<div style=\'width:400px;margin:0 0 0 37%;height:100;font-size:10pt;text-align: center;border:1px solid #cad9ea;background-color:#e4f1fa\'><br/><br/>");
';
		echo 'document.write("' . $msg . '");' . "\n" . '';
		echo '</script>
';
		exit();
	}
}

?>
