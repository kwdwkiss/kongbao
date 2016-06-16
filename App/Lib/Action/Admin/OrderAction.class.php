<?php
//dezend by 辰梦大人 QQ:30881917
class OrderAction extends CommonAction
{
	public function buy_order()
	{
		import('ORG.Util.Page');
		$where['type'] = 0;
		$order_model = new UserOrderViewModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			if ($ftype == 'user_id') {
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

		if ($_REQUEST['action'] == '删除成功') {
			$mod = new Model('pay_order');

			if (false !== $mod->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/buy_order');
			}
			else {
				$this->message('删除失败：' . $mod->getError(), __URL__ . '/buy_order');
			}
		}

		$count = $order_model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$order_list = $order_model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_user_type();
		$this->assign('type_list', $type_list);
		$this->assign('order_list', $order_list);
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->display();
	}

	public function pay_order()
	{
		import('ORG.Util.Page');
		$where['type'] = 1;
		$order_model = new UserOrderViewModel();
		$keyword = I('keyword', NULL);
		$ftype = I('ftype', NULL);
		if (!empty($keyword) && !empty($ftype)) {
			if ($ftype == 'user_id') {
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

		$status = I('status', '');

		if ($status != '') {
			$where['status'] = $status;
		}

		$order_type = I('order_type', '');

		if ($order_type != '') {
			$where['order_type'] = $order_type;
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

		if ($_REQUEST['action'] == '删除成功') {
			$mod = new Model('pay_order');
			$mod->where($where)->delete();

			if (false !== $mod->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/pay_order');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/pay_order');
			}
		}

		$count = $order_model->where($where)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$order_list = $order_model->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$type_list = $this->get_user_type();
		$this->assign('type_list', $type_list);
		$this->assign('order_list', $order_list);
		$this->assign('count', $count);
		$this->assign('show', $show);
		$this->display();
	}

	private function get_user_type()
	{
		$model = new Model('user_level');
		$user_level = $model->select();
		return $user_level;
	}

	public function order_edit()
	{
		$id = I('id', 0);

		if ($id == 0) {
			$this->message2('未指定订单信息！', __URL__ . '/pay_order');
		}

		$order_model = new UserOrderViewModel();
		$order = $order_model->where('pay_order.id=' . $id)->find();

		if (empty($order)) {
			$this->message2('未找到指定的订单信息！', __URL__ . '/pay_order');
		}

		$this->assign('order', $order);
		$this->display();
	}

	public function order_update()
	{
		if (!IS_POST) {
			$this->message2('非法操作!', __APP__ . '/Admin');
		}

		$id = I('id', 0);

		if ($id == 0) {
			$this->message2('未找到指定的订单信息！', __URL__ . '/pay_order');
		}

		$user_id = I('user_id', 0);

		if ($user_id == 0) {
			$this->message2('未找到订单对应的用户信息！', __URL__ . '/pay_order');
		}

		$pay_money = I('pay_money', 0);
		$order = M('pay_order')->where('id=' . $id)->find();

		if (empty($order)) {
			$this->message2('未找到指定的订单信息！', __URL__ . '/pay_order');
		}

		if ($order['status'] == 1) {
			$this->message2('该笔交易已完成，不能重复提交！', __URL__ . '/pay_order');
		}

		$user = M('user')->where('id=' . $user_id)->find();

		if (empty($user)) {
			$this->message2('未找到订单对应的用户信息！', __URL__ . '/pay_order');
		}

		if ($order['pay_money'] != $pay_money) {
			$order_data['pay_money'] = $pay_money;
		}
		else {
			$order_data['pay_money'] = $order['pay_money'];
		}

		$order_model = M('pay_order');
		$user_model = M('user');
		$order_data['id'] = $id;
		$order_data['status'] = 1;
		$order_model->startTrans();

		if (false !== $order_model->save($order_data)) {
			$userdata['id'] = $user_id;
			$money = $order_data['pay_money'];
			$userdata['money'] = $user['money'] + $money;

			if ($reg_setting['reg_zengsong'] == 1) {
				$config = M('config')->where('id=1')->find();
				$payrule_setting = json_decode($config['payrule_setting'], true);

				foreach ($payrule_setting as $row) {
					if (((double) $row['start'] <= $money) && ($money <= (double) $row['end'])) {
						$give = (double) $row['give'];
						$grade = (int) $row['grade'];
						$userdata['money'] = $user['money'] + $money + $give;
						$songtext = '送' . $give . '块';
						$pay_title = '送' . $give . '块';
						$rs = M('user')->where('id=' . $user_id)->find();

						if ($rs) {
							$user_type = (int) $rs['user_type'];

							if ($user_type < $grade) {
								$userdata['user_type'] = $grade;
								$level = M('user_level')->where('id=' . $grade)->find();

								if ($level) {
									$songtext .= '+' . $level['title'];
									$level_name = '+' . $level['title'];
								}
							}
						}

						$zs_order_no = 'ZengSong' . date('YmdHis');
						$paydata['user_id'] = $user_id;
						$paydata['user_type'] = $user['user_type'];
						$paydata['pay_money'] = $give;
						$paydata['order_no'] = $zs_order_no;
						$paydata['outer_order_no'] = $zs_order_no;
						$paydata['pay_title'] = $pay_title . $level_name;
						$paydata['type'] = 1;
						$paydata['comm'] = 'JFB[' . $songtext . ']';
						$paydata['status'] = 1;
						$paydata['order_type'] = 2;
						$paydata['addtime'] = $this->getDate();
						$order_model->data($paydata)->add();
						break;
					}
				}
			}

			if (false !== $user_model->where('id=' . $user_id)->save($userdata)) {
				$reason = '(用户充值) 订单号 ' . $order['order_no'] . $songtext;
				$account_log = array();
				$account_log['user_id'] = $user_id;
				$account_log['stage'] = 'recharge';
				$account_log['money'] = $order_data['pay_money'];
				$account_log['comm'] = $reason;
				$account_log['addtime'] = $this->getDate();
				$account_log['remain_money'] = $userdata['money'];
				$account_log['remain_refer_money'] = $user['refer_money'];

				if (false !== D('account_log')->data($account_log)->add()) {
					$order_model->commit();
					$content = '对会员' . $user['username'] . ',交易号' . $order['outer_order_no'] . '的充值记录审核通过并注入账户，若为本人操作请忽略该条提醒！';
					send_fetion($content);
					$this->message('操作成功！', __URL__ . '/pay_order');
				}
				else {
					$order_model->rollback();
					$this->message('操作失败2', __URL__ . '/pay_order');
				}
			}
			else {
				$order_model->rollback();
				$this->message('增加会员可用金额失败！', __URL__ . '/pay_order');
			}
		}
		else {
			$order_model->rollback();
			$this->message('操作失败！', __URL__ . '/pay_order');
		}
	}

	public function order_del()
	{
		$id = I('id', NULL);
		$model = M('pay_order');

		if (!empty($id)) {
			if (is_array($id)) {
				$where = 'id in (' . implode(',', $id) . ') and type=1 and status=0';
			}
			else {
				$where = 'id = ' . $id . ' and type=1  and status=0';
			}

			if (false !== $model->where($where)->delete()) {
				$this->message('删除成功', __URL__ . '/pay_order');
			}
			else {
				$this->message('删除失败：' . $model->getError(), __URL__ . '/pay_order');
			}
		}
		else {
			$this->message('请选择删除对象', __URL__ . '/pay_order');
		}
	}

	public function getDate()
	{
		return date('Y-m-d H:i:s');
	}
}

?>
