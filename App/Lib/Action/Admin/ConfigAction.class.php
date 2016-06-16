<?php
//dezend by 辰梦大人 QQ:308819170
class ConfigAction extends CommonAction
{
	public function config()
	{
		$config = D('Config')->where('id=1')->find();
		$this->assign('config', $config);
		$this->display();
	}

	public function server()
	{
		$this->display();
	}

	public function repass()
	{
		$this->display();
	}

	public function savepass()
	{
		$model = new AdminModel();
		$id = session('adminid');

		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$password = I('password', '');

		if ($password == '') {
			$this->message2('密码不能为空，请重新输入!', __URL__ . '/repass');
		}

		if ($model->create()) {
			if (!empty($id)) {
				$data['password'] = md5(I('password'));

				if (false !== $model->where('id=\'' . $id . '\'')->data($data)->save()) {
					session('adminid', NULL);
					session('adminname', NULL);
					$this->message2('操作成功,请重新登陆系统', __APP__ . '/Admin');
				}
				else {
					$this->message2('操作失败：' . $model->getDbError());
				}
			}
			else {
				$this->message2('请选择编辑用户', __APP__ . '/Admin');
			}
		}
		else {
			$this->message2('操作失败：数据验证( ' . $model->getError() . ' )', __URL__ . '/repass');
		}
	}

	public function save()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$model = new Model('config');
		$config = $model->where('id=1')->find();
		$data = $model->create();
		$is_update_backurl = false;
		$new_back_url = '';

		if ($data) {
			$site_status = I('site_status', 1);

			if ($site_status == 0) {
				$close_reason = I('close_reason', '');

				if ($close_reason == '') {
					$this->message('站点关闭，请填写关闭原因!', __URL__ . '/config');
				}
			}

			$back_url = I('back_url', '');
			$back_url = trim($back_url);
			$data['back_url'] = $back_url;

			if (strtolower(substr($back_url, 0, 5)) == 'admin') {
				$this->message('自定义后台地址不能以admin开头!', U('config'));
			}

			if (($back_url != '') && ($back_url != $config['back_url'])) {
				$this->recreateFile($back_url, 0);
				$is_update_backurl = true;
			}

			if (($back_url == '') && ($config['back_url'] != '')) {
				$this->recreateFile($back_url, 1);
				$is_update_backurl = true;
			}

			if (!empty($_FILES['site_logo']) && ($_FILES['site_logo']['name'] != '')) {
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 3145728;
				$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
				$upload->savePath = 'Public/Uploads/admin/';

				if ($upload->upload()) {
					$info = $upload->getUploadFileInfo();
				}
				else {
					$this->error($upload->getErrorMsg());
				}

				$data['site_logo'] = $upload->savePath . $info[0]['savename'];
			}

			if (false !== $model->where('id=1')->save($data)) {
				if ($is_update_backurl == true) {
					session('adminid', NULL);
					session('adminname', NULL);
					cookie('encry_code', NULL);

					if ($back_url == '') {
						$reurl = U(C('USER_AUTH_GATEWAY'));
					}
					else {
						$reurl = __APP__ . '/' . $back_url;
					}

					$this->message('配置修改成功,且后台地址已修改，请重新登录!', $reurl);
				}

				$this->message('配置修改成功', __URL__ . '/config');
			}
			else {
				$this->message('配置修改失败( ' . $model->getError() . ' )', __URL__ . '/config');
			}
		}
		else {
			$this->message('配置修改失败( ' . $model->getError() . ' )', __URL__ . '/config');
		}
	}

	public function payonline_setting()
	{
		$config = D('Config')->where('id=1')->find();
		$payonline_setting = json_decode($config['payonline_setting'], true);
		$config['payonline_setting'] = $payonline_setting;
		$this->assign('config', $config);
		$this->display();
	}

	public function payonline_save()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$config = M('config');
		$data['alipay_Key'] = I('alipay_Key', '');
		$config_old = $config->where('id=1')->find();
		$payonline_setting_old = json_decode($config_old['payonline_setting'], true);
		$payonline_setting = $_POST['payonline_setting'];
		if (($payonline_setting['alipay_zz']['receiver'] != $payonline_setting_old['alipay_zz']['receiver']) || ($payonline_setting['alipay_zz']['name'] != $payonline_setting_old['alipay_zz']['name'])) {
			$content = '您的支付宝收款账户发生变化，若为本人操作，请忽略该条提醒！';
			send_fetion($content);
		}

		if (($payonline_setting['tenpay_zz']['receiver'] != $payonline_setting_old['tenpay_zz']['receiver']) || ($payonline_setting['tenpay_zz']['name'] != $payonline_setting_old['tenpay_zz']['name'])) {
			$content = '您的财付通收款账户发生变化，若为本人操作，请忽略该条提醒！';
			send_fetion($content);
		}

		$data['payonline_setting'] = json_encode($payonline_setting);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message('配置修改成功', __URL__ . '/payonline_setting');
		}
		else {
			$this->message('配置修改失败( ' . $config->getError() . ' )', __URL__ . '/payonline_setting');
		}
	}

	public function fetion_setting()
	{
		$config = D('Config')->where('id=1')->find();
		$payonline_setting = json_decode($config['fetion_setting'], true);
		$config['fetion_setting'] = $payonline_setting;
		$this->assign('config', $config);
		$this->display();
	}

	public function fetion_save()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$config = M('config');
		$data['fetion_setting'] = json_encode($_POST['fetion_setting']);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message('配置修改成功', __URL__ . '/fetion_setting');
		}
		else {
			$this->message('配置修改失败( ' . $config->getError() . ' )', __URL__ . '/fetion_setting');
		}
	}

	public function bindip()
	{
		$config = D('Config')->where('id=1')->find();
		$this->assign('config', $config);
		$this->display();
	}

	public function ipsave()
	{
		$config = M('config');
		$data['backbind_ip'] = trim($_POST['backbind_ip']);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message('配置修改成功', __URL__ . '/bindip');
		}
		else {
			$this->message('配置修改失败( ' . $config->getError() . ' )', __URL__ . '/bindip');
		}
	}

	private function recreateFile($back_url, $flag = 0)
	{
		$setfile = 'App/Conf/config.ini.php';

		if ($flag == 0) {
			$settingstr = '<?php ' . "\n" . ' ' . "\r\n" . '			  	   if (!defined(\'THINK_PATH\')) exit(); ' . "\n" . '' . "\r\n" . '			  	  return array(' . "\r\n" . '			  	  ' . "\n" . ' \'URL_ROUTER_ON\' =>true,' . "\r\n" . '			  	  ' . "\n" . ' \'URL_ROUTE_RULES\' =>array(' . "\n" . '';
			$settingstr .= '	\'/^' . $back_url . '/\'=>\'Admin/Index/index\',' . "\n" . '';
			$settingstr .= '),' . "\n" . ');' . "\n" . '?>' . "\n" . '';
		}
		else {
			$settingstr = '<?php ' . "\n" . ' ' . "\r\n" . '			  	   if (!defined(\'THINK_PATH\')) exit(); ' . "\n" . '' . "\r\n" . '			  	  return array();' . "\n" . '?>' . "\n" . '';
		}

		file_put_contents($setfile, $settingstr);

		if (file_exists(RUNTIME_FILE)) {
			unlink(RUNTIME_FILE);
		}

		$cachedir = RUNTIME_PATH . '/Cache/';

		if ($dh = opendir($cachedir)) {
			while (($file = readdir($dh)) !== false) {
				unlink($cachedir . $file);
			}

			closedir($dh);
		}
	}

	public function sj_setting()
	{
		$config = D('Config')->where('id=1')->find();
		$sj_setting = json_decode($config['sj_setting'], true);
		$config['sj_setting'] = $sj_setting;
		$this->assign('config', $config);
		$this->display();
	}

	public function sj_save()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$config = M('config');
		$data['sj_setting'] = json_encode($_POST['sj_setting']);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message('配置修改成功', __URL__ . '/sj_setting');
		}
		else {
			$this->message('配置修改失败( ' . $config->getError() . ' )', __URL__ . '/sj_setting');
		}
	}

	public function admin_log()
	{
		import('ORG.Util.Page');
		$log_model = new AdminLogViewModel();
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

	public function admin_log_del()
	{
		$model = new Model('admin_log');

		if (IS_POST) {
			$ids = I('del_id', '');

			if ($ids == '') {
				$this->message2('请选择要删除的信息!', __URL__ . '/admin_log');
			}

			$where = 'id in (' . implode(',', $ids) . ')';
		}
		else {
			$id = I('id', '');

			if ($id == '') {
				$this->message2('请选择要删除的信息!', __URL__ . '/admin_log');
			}
			else {
				$where = 'id =' . $id;
			}
		}

		if (false !== $model->where($where)->delete()) {
			$this->message('日志删除成功！', __URL__ . '/admin_log');
		}
		else {
			$this->message('日志删除失败：' . $model->getError(), __URL__ . '/admin_log');
		}
	}

	public function qq_setting()
	{
		$config = D('Config')->where('id=1')->find();
		$qq_setting = json_decode($config['qq_setting'], true);
		$config['qq_setting'] = $qq_setting;
		$this->assign('config', $config);
		$this->display();
	}

	public function qq_save()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$config = M('config');
		$data['qq_setting'] = json_encode($_POST['qq_setting']);

		if (false !== $config->where('id=1')->save($data)) {
			$this->message('配置修改成功', __URL__ . '/qq_setting');
		}
		else {
			$this->message('配置修改失败( ' . $config->getError() . ' )', __URL__ . '/qq_setting');
		}
	}
}

echo '			  	  		';

?>
