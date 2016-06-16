<?php
//dezend by 辰梦大人 QQ:30881917
class CashAction extends CommonAction
{
	public function _initialize()
	{
		parent::_initialize();

		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登陆!', __APP__ . '/User/uslogin.html');
		}

		if (session('isvalid') != 1) {
			$this->message2('账号需等待管理员审核!', __APP__ . '/Index');
		}
	}

	public function recharge()
	{
		$config = M('config')->where('id=1')->find();
		$pay_setting = json_decode($config['payonline_setting'], true);
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
		$username = session('username');
		$username = trim($username);
		$auth_type = C('AUTH_TYPE');
		$current_time = time();
		$sign = substr(md5($userid . $username . $auth_type), 0, 10) . $current_time;
		$sign_qq = substr(md5($userid . $username . $auth_type), 0, 10) . mt_rand(100, 999);
		$this->assign('title', 'Apay-' . session('userid') . '-' . $sign);
		$this->assign('title_qq', 'T' . session('userid') . 'S' . $sign_qq);
		$this->assign('pay_setting', $pay_setting);
		$this->display();
	}

	public function alipay_save()
	{
		if (!IS_POST) {
			$this->message2('无效访问!', __APP__ . '/Index');
		}

		$userid = session('userid');
		if (empty($userid) || ($userid <= 0)) {
			$this->message2('', '请先登录!', __APP__);
		}

		$user_id = I('user_id', '');

		if ($user_id != $userid) {
			$this->message2('', '请重新登陆网站!', __APP__);
		}

		$order_type = I('order_type', 0);
		$payAmount = I('price', 0);

		if ($payAmount <= 0) {
			$this->message2('请正确填写交易号对应的转账金额!', 'recharge');
		}

		$outer_order_no = I('outer_order_no', '');
		$outer_order_no = trim($outer_order_no);

		if ($outer_order_no == '') {
			$this->message2('请正确填写转账交易号!', 'recharge');
		}

		$where = array();
		$where['outer_order_no'] = $outer_order_no;
		$where['type'] = 1;
		$count = M('pay_order')->where($where)->count();

		if (0 < $count) {
			if (1 < $count) {
				$this->message2('该笔交易号已在系统中存在，请勿重复提交!', 'recharge');
			}

			$order_pay = M('pay_order')->where($where)->find();

			if ($order_pay['status'] == 0) {
				$this->message2('该交易号已提交，等待审核!', 'recharge');
			}

			if ($order_pay['status'] == 1) {
				$this->message2('该笔交易号已经充值成功!', 'recharge');
			}
		}

		$user = M('user')->where('id=' . $userid)->find();
		$paydata = array();
		$paydata['user_id'] = $user['id'];
		$paydata['user_type'] = $user['user_type'];
		$paydata['pay_money'] = $payAmount;
		$paydata['outer_order_no'] = $outer_order_no;
		$paydata['order_no'] = $this->create_orderno('APay');
		$paydata['type'] = 1;

		if ($order_type == 0) {
			$paydata['comm'] = '支付宝充值订单';
		}
		else if ($order_type == 1) {
			$paydata['comm'] = '财付通充值订单';
		}

		$paydata['status'] = 0;
		$paydata['order_type'] = $order_type;
		$paydata['addtime'] = $this->getDate();

		if (false !== M('pay_order')->data($paydata)->add()) {
			$this->send_fetion($user, $paydata, 1);
			$this->message2('提交成功，请稍加等待!', 'recharge');
		}
		else {
			$this->message2('提交失败，请联系网站客服手工加款!', 'recharge');
		}
	}

	public function tixian()
	{
		if (session('IS_LOGIN') != 1) {
			$this->message2('请先登录!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$user = D('user')->where('id=' . $userid)->find();
		$user_type = $user['user_type'];
		$user_level = D('user_level')->where('id=' . $user_type)->find();
		$level_config = json_decode($user_level['config'], true);

		if ($level_config['tixian'] == 0) {
			$this->message2('你目前的会员级别为:' . $user_level['title'] . ',不具有提现权限!', __APP__ . '/User/Home');
		}

		$cwsz_config = json_decode($user['cwsz_config'], true);
		$this->assign('userInfo', $user);
		$this->assign('cwsz_config', $cwsz_config);
		$config = M('config')->where('id=1')->find();
		$tixian_setting = json_decode($config['tixian_setting'], true);
		$this->assign('tixian_setting', $tixian_setting);
		$this->display();
	}

	public function deal_tixian()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/User/Home');
		}

		$userid = session('userid');
		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();
		$_POST['cwsz_config'] = $user['cwsz_config'];
		$tixian = new TixianModel();
		$data = $tixian->create();
		$data['cwsz_config'] = $user['cwsz_config'];
		$config = M('config')->where('id=1')->find();
		$tixian_setting = json_decode($config['tixian_setting'], true);

		if (0 < $tixian_setting['money']) {
			if ($user['refer_money'] < $tixian_setting['money']) {
				$this->message2('您的账户目前可用佣金不满足提现条件!', __APP__ . '/User/Home');
			}
		}

		if (0 < $tixian_setting['refernums']) {
			$refer_nums = D('user')->where('refer_id=' . $userid)->count();

			if ($refer_nums < $tixian_setting['refernums']) {
				$this->message2('您目前的下线会员少于' . $tixian_setting['refernums'] . '人!', __APP__ . '/User/Home');
			}
		}

		if (0 < $tixian_setting['used_money']) {
			if ($user['used_money'] < $tixian_setting['used_money']) {
				$this->message2('您目前的累计消费少于' . $tixian_setting['used_money'] . '元!', __APP__ . '/User/Home');
			}
		}

		$money = floatval($_POST['money']);

		if (0 < $tixian_setting['min_money']) {
			if ($money < $tixian_setting['min_money']) {
				$this->message2('最低提现金额不能少于' . $tixian_setting['min_money'] . '元!', __APP__ . '/User/Home');
			}
		}

		if (0 < $tixian_setting['zsb']) {
			if (($money % $tixian_setting['zsb']) != 0) {
				$this->message2('提现金额必须为' . $tixian_setting['zsb'] . '的整数倍!', __APP__ . '/User/Home');
			}
		}

		$temp_money = $user['refer_money'] - $money;

		if ($temp_money < 0) {
			$this->message2('您的账户目前可用佣金已不足!', 'tixian');
		}

		$user_model->startTrans();
		$userdata = array();
		$userdata['id'] = $userid;
		$userdata['refer_money'] = $user['refer_money'] - $money;
		$userdata['invalid_money'] = $user['invalid_money'] + $money;
		$temp_money = $user['refer_money'] - $money;
		$result_1 = $user_model->data($userdata)->save();

		if (false !== $result_1) {
			$reason = '佣金提现';
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'cash';
			$account_log['money'] = 0 - $money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $user['money'];
			$account_log['remain_refer_money'] = $userdata['refer_money'];
			$account_log_model = new Model('account_log');
			$result_2 = D('account_log')->data($account_log)->add();

			if ($result_2) {
				if (false !== $tixian->add($data)) {
					$user_model->commit();
					$data = array();
					$data['addtime'] = $account_log['addtime'];
					$data['money'] = $money;
					$this->send_fetion($user, $data, 2);
					$this->message2('提现申请已提交，等待结算!', U('Log/tixia_log'));
				}
				else {
					$user_model->rollback();
					$this->message2('提现申请提交失败1：' . $user_model->getError(), __APP__ . '/User/Home');
				}
			}
			else {
				$user_model->rollback();
				$this->message2('提现申请提交失败2：' . $user_model->getError(), __APP__ . '/User/Home');
			}
		}
		else {
			$user_model->rollback();
			$this->message2('提现申请提交失败3：' . $user_model->getError(), __APP__ . '/User/Home');
		}
	}

	public function change()
	{
		$this->display();
	}

	public function deal_change()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$money = I('money', 0);

		if ($money <= 0) {
			$this->message2('转换金额必须大于0', __APP__ . '/User/Home');
		}

		if (($money % 1) != 0) {
			$this->message2('转换金额必须为整数!', __APP__ . '/User/Home');
		}

		$userid = session('userid');
		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();

		if (empty($user)) {
			$this->message2('获取用户信息出错!', __APP__ . '/User/Home');
		}

		$refer_money = $user['refer_money'];

		if ($refer_money < $money) {
			$this->message2('转换金额不能高于当前可用佣金!', __APP__ . '/User/Home');
		}

		$userdata['id'] = $userid;
		$userdata['refer_money'] = $user['refer_money'] - $money;
		$userdata['money'] = $user['money'] + $money;
		$user_model->startTrans();
		$result = $user_model->data($userdata)->save();

		if ($result) {
			$account_log_model = new Model('account_log');
			$reason = '佣金转换成账户余额-佣金减少';
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'change';
			$account_log['money'] = 0 - $money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $userdata['refer_money'];
			$result_1 = D('account_log')->data($account_log)->add();
			$reason = '佣金转换成账户余额-账户余额增加';
			$account_log['money'] = $money;
			$account_log['comm'] = $reason;
			$result_2 = D('account_log')->data($account_log)->add();
			if ($result_1 && $result_2) {
				$user_model->commit();
				$this->message2('转换成功!', __APP__ . '/User/Home');
			}
			else {
				$user_model->rollback();
				$this->message2('佣金转换失败：' . $user_model->getError(), __APP__ . '/User/Home');
			}
		}
		else {
			$user_model->rollback();
			$this->message2('佣金转换失败：' . $user_model->getError(), __APP__ . '/User/Home');
		}
	}

	private function send_fetion($user, $paydata, $type = 1)
	{
		if (!class_exists('PHPFetion')) {
			require_once 'Public/PHPFetion.php';
		}

		$config = M('config')->where('id=1')->find();
		$fetion_setting = json_decode($config['fetion_setting'], true);
		$fetion = new PHPFetion($fetion_setting['sender'], $fetion_setting['password']);
		$domain = '[' . $_SERVER['HTTP_HOST'] . ']';

		if ($type == 1) {
			$replace_array = array('{username}' => $user['username'], '{outer_order_no}' => $paydata['outer_order_no'], '{order_money}' => round($paydata['pay_money'], 2), '{user_qq}' => $user['user_qq'], '{money}' => $paydata['money'], '{addtime}' => $paydata['addtime']);
			$content = $fetion_setting['jk_tixing'];
		}
		else if ($type == 2) {
			$replace_array = array('{username}' => $user['username'], '{user_qq}' => $user['user_qq'], '{money}' => $paydata['money'], '{addtime}' => $paydata['addtime']);
			$content = $fetion_setting['tx_tixing'];
		}

		$content = strtr($content, $replace_array);
		$content = $content . $domain;
		$fetion->send($fetion_setting['receiver'], $content);
	}

	public function tenpay()
	{
		$pay_money = htmlspecialchars($_POST['payAmount']);
		$subject = $_POST['title'];
		$email = $_POST['optEmail'];
		$tenpay_name = $_POST['tenpay_name'];
		$optemail = $email;
		$ddh = $subject;
		$md5 = md5($optemail . '&' . $pay_money . '&' . $ddh);
		$pay_url = 'https://www.tenpay.com/v2/account/pay/paymore_cft.shtml?data=' . $optemail . '%26' . $pay_money . '%26' . $ddh . '&validate=' . $md5;
		$this->assign('pay_url', $pay_url);
		$this->assign('pay_money', $pay_money);
		$this->assign('email', $email);
		$this->assign('tenpay_name', $tenpay_name);
		$this->assign('subject', $subject);
		$this->assign('md5', $md5);
		$this->display();
	}
}

?>
