<?php
//dezend by 辰梦大人 QQ:30881917
echo "\r\n";
class TgAction extends CommonAction
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

	public function index()
	{
		$userid = session('userid');
		$user = M('user')->where('id=' . $userid)->find();
		$user_level = M('user_level')->where('id=' . $user['user_type'])->find();
		$level_config = json_decode($user_level['config'], true);

		if ($level_config['refer'] != 1) {
			$this->message2('<font color=red>你目前的会员级别无推广权限</font>请先升级会员!', 'uplevel');
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
		$kongbao_page = json_decode($config['kongbao_page'], true);
		$kongbao_page_temp = array();

		foreach ($kongbao_page as $key1 => $v1) {
			$kongbao_page_temp[$key1] = stripslashes(htmlspecialchars_decode($v1));
		}

		$this->assign('kongbao_page', $kongbao_page_temp);
		$refer_nums = D('user')->where('refer_id=' . session('userid'))->count();
		$this->assign('refer_nums', $refer_nums);
		$reg_setting = M('user')->where('id=' . session('userid'))->find();
		$wrefer_id = $reg_setting['refer_id'];
		$vip_setting = json_decode($reg_setting['vip_set'], true);
		$wrefer = M('user')->where('id=' . $wrefer_id)->find();
		$wusername = $wrefer['username'];
		$this->assign('wusername', $wusername);
		$this->assign('vip_setting', $vip_setting);
		$this->display();
	}

	public function uplevel()
	{
		$config = M('config')->where('id=1')->find();
		$daili_refer_mode = $config['daili_refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $daili_refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$this->assign('dali_refer_mode', $mode);
		$type_id = $this->user['user_type'];
		$level = M('user_level')->where('id=' . $type_id)->find();
		$current_level_order = $level['sort_order'];
		$user_level = M('user_level')->order('sort_order asc')->select();
		$level_list = array();

		foreach ($user_level as $k => $v) {
			$v['up_enabled'] = true;
			$v['config'] = json_decode($v['config'], true);
			if (isset($v['config']['level_view']) && ($v['config']['level_view'] == 1)) {
				continue;
			}

			if ($v['sort_order'] < $current_level_order) {
				$v['up_enabled'] = false;
			}

			$level_list[$v['id']] = $v;
		}

		$this->assign('level_list', $level_list);
		$user_type = $level_list[$type_id]['title'];
		$this->assign('user_type', $user_type);
		$this->assign('current_type_id', $type_id);
		$kongbao_type = M('kongbao_type')->order('id asc')->select();
		$type_list = array();

		foreach ($type_list as $type) {
			$type['config'] = json_decode($type['config'], true);
			$type_list[$type['id']] = $type;
		}

		$this->assign('type_list', $type_list);
		$this->display();
	}

	public function getLevelDetail()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$type_id = I('type_id', NULL);
		$width = I('width', '');

		if ($width == '') {
			$width = '900px';
		}

		$type_id_a = 'a' . $type_id;
		$where = array();
		$where['state'] = 0;
		$kongbao_type = M('kongbao_type')->where($where)->order('sort_order asc')->select();
		$kongbao_array = array();

		foreach ($kongbao_type as $kongbao) {
			$kongbao['config'] = json_decode($kongbao['config'], true);
			$kongbao_array[] = $kongbao;
		}

		$where = array();
		$where['state'] = 0;
		$danhao_type = M('danhao_type')->where($where)->order('sort_order asc')->select();
		$danhao_array = array();

		foreach ($danhao_type as $danhao) {
			$danhao['config'] = json_decode($danhao['config'], true);
			$danhao_array[] = $danhao;
		}

		$where = array();
		$where['state'] = 0;
		$xiaohao_type = M('xiaohao_type')->where($where)->order('sort_order asc')->select();
		$xiaohao_array = array();

		foreach ($xiaohao_type as $xiaohao) {
			$xiaohao['config'] = json_decode($xiaohao['config'], true);
			$xiaohao_array[] = $xiaohao;
		}

		$config = M('config')->where('id=1')->find();
		$refer_mode = $config['refer_mode'];
		$mode = array();

		for ($i = 1; $i <= $refer_mode; $i += 1) {
			$mode[$i] = 'a' . $i;
		}

		$count = $refer_mode + 1;
		$kongbao_config = json_decode($config['kongbao_config'], true);
		$danhao_config = json_decode($config['danhao_config'], true);
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$html = ' <table width=\'' . $width . '\'  class=\'t1\'> ' . "\r\n" . '		                <tr><td colspan=\'5\' height=30px aligin=left><font color=red size=3><b>假设A会员购买空包，价格为P元/单(计算结果小于等于0时不返佣金)</b></font></td></tr>' . "\r\n" . '		                <tr height=25px align=center bgcolor=#abcdef>  ' . "\r\n" . '		                   <td>空包类型</td> <td>对应价格</td><td colspan=\'3\'>购买佣金比例及计算公式</td>' . "\r\n" . '	 	 	 			</tr>';
		$content = '';

		foreach ($kongbao_array as $k => $v) {
			$tr_a = '';
			$tr = '<tr height=25px align=center><td rowspan=\'' . $count . '\'>' . $v['name'] . '</td>' . '<td rowspan=\'' . $count . '\'><font size=4 color=red>' . $v['config']['price'][$type_id_a] . '</font>元/单</td>' . '<td bgcolor=#abadbb><font color=blue>上线层级</font></td><td bgcolor=#abadbb><font color=blue>佣金比例%</font></td><td bgcolor=#abadbb ><font color=blue>佣金计算公式</font></td></tr>';

			foreach ($mode as $key => $m) {
				if ($kongbao_config['refer_default'] == 1) {
					$yongjinbl = $kongbao_config['buy_refer'][$m];
				}
				else {
					$yongjinbl = $v['config']['refer'][$m];
				}

				$tr_a .= '<tr><td>' . $key . '级上线</td>' . '<td><font size=4 color=red>' . $yongjinbl . '%</font></td>' . '<td>(P - ' . $v['config']['price'][$type_id_a] . ') * ' . $yongjinbl . ' / 100</td>' . '</tr>';
			}

			$content .= $tr . $tr_a;
		}

		if ($danhao_config['valid'] == 1) {
			$content .= '<tr height=25px align=center bgcolor=#abcdef>  ' . "\r\n" . '		                   				<td>单号类型</td> <td>对应价格</td><td colspan=\'3\'>购买佣金比例及计算公式</td>' . "\r\n" . '	 	 	 							</tr>';

			foreach ($danhao_array as $k => $v) {
				$tr_a = '';
				$tr = '<tr height=25px align=center><td rowspan=\'' . $count . '\'>' . $v['name'] . '</td>' . '<td rowspan=\'' . $count . '\'><font size=4 color=red>' . $v['config']['price'][$type_id_a] . '</font>元/单</td>' . '<td bgcolor=#abadbb><font color=blue>上线层级</font></td><td bgcolor=#abadbb><font color=blue>佣金比例%</font></td><td bgcolor=#abadbb ><font color=blue>佣金计算公式</font></td></tr>';

				foreach ($mode as $key => $m) {
					if ($danhao_config['refer_default'] == 1) {
						$yongjinbl = $danhao_config['buy_refer'][$m];
					}
					else {
						$yongjinbl = $v['config']['refer'][$m];
					}

					$tr_a .= '<tr><td>' . $key . '级上线</td>' . '<td><font size=4 color=red>' . $yongjinbl . '%</font></td>' . '<td>(P - ' . $v['config']['price'][$type_id_a] . ') * ' . $yongjinbl . ' / 100</td>' . '</tr>';
				}

				$content .= $tr . $tr_a;
			}
		}

		if ($xiaohao_config['valid'] == 1) {
			$content .= '<tr height=25px align=center bgcolor=#abcdef>  ' . "\r\n" . '		                   				<td>小号类型</td> <td>对应价格</td><td colspan=\'3\'>购买佣金比例及计算公式</td>' . "\r\n" . '	 	 	 							</tr>';

			foreach ($xiaohao_array as $k => $v) {
				$tr_a = '';
				$tr = '<tr height=25px align=center><td rowspan=\'' . $count . '\'>' . $v['name'] . '</td>' . '<td rowspan=\'' . $count . '\'><font size=4 color=red>' . $v['config']['price'][$type_id_a] . '</font>元/单</td>' . '<td bgcolor=#abadbb><font color=blue>上线层级</font></td><td bgcolor=#abadbb><font color=blue>佣金比例%</font></td><td bgcolor=#abadbb ><font color=blue>佣金计算公式</font></td></tr>';

				foreach ($mode as $key => $m) {
					if ($xiaohao_config['refer_default'] == 1) {
						$yongjinbl = $xiaohao_config['buy_refer'][$m];
					}
					else {
						$yongjinbl = $v['config']['refer'][$m];
					}

					$tr_a .= '<tr><td>' . $key . '级上线</td>' . '<td><font size=4 color=red>' . $yongjinbl . '%</font></td>' . '<td>(P - ' . $v['config']['price'][$type_id_a] . ') * ' . $yongjinbl . ' / 100</td>' . '</tr>';
				}

				$content .= $tr . $tr_a;
			}
		}

		$html .= $content . '</table>';
		$this->ajaxReturn($html, '获取成功', 1);
	}

	public function deal_uplevel()
	{
		if (!IS_AJAX) {
			$this->message2('无效请求!', __APP__ . '/Index');
		}

		$userid = session('userid');
		$level_id = I('id', NULL);

		if (empty($level_id)) {
			$this->ajaxReturn('', '升级失败1', 0);
		}

		$level_list = M('user_level')->order('sort_order asc')->select();
		$level_array = array();

		foreach ($level_list as $k => $v) {
			$level_array[$v['id']] = $v['title'];
		}

		$user_level = M('user_level')->where('id=' . $level_id)->find();

		if (empty($user_level)) {
			$this->ajaxReturn('', '升级失败2', 0);
		}

		$level_config = json_decode($user_level['config'], true);
		$up_money = $level_config['money'];

		if ($up_money <= 0) {
			$this->ajaxReturn('', '无升级费用', 0);
		}

		$user_model = new Model('user');
		$user = $user_model->where('id=' . $userid)->find();

		if (empty($user)) {
			$this->ajaxReturn('', '获取人员信息出错，或者尚未登录!', 0);
		}

		$current_type = $user['user_type'];

		if ($current_type == $level_id) {
			$this->ajaxReturn('', '你目前已经是' . $level_array[$current_type] . ',无需升级!', 0);
		}

		$money = $user['money'];

		if ($money < $up_money) {
			$this->ajaxReturn('', '您的账户可用余额不足！请先充值或者使用可用佣金转换成账户余额!', 0);
		}

		$userdata['id'] = $userid;
		$userdata['money'] = $user['money'] - $up_money;
		$userdata['used_money'] = $user['used_money'] + $up_money;
		$userdata['user_type'] = $level_id;
		$user_model->startTrans();
		$result = $user_model->data($userdata)->save();

		if ($result) {
			$reason = '(会员升级) 会员由 ' . $level_array[$current_type] . '升级为：' . $level_array[$level_id];
			$account_log = array();
			$account_log['user_id'] = $userid;
			$account_log['stage'] = 'upgrade';
			$account_log['money'] = 0 - $up_money;
			$account_log['comm'] = $reason;
			$account_log['addtime'] = $this->getDate();
			$account_log['remain_money'] = $userdata['money'];
			$account_log['remain_refer_money'] = $user['refer_money'];
			$account_log_model = new Model('account_log');
			$return_1 = D('account_log')->data($account_log)->add();

			if ($return_1) {
				$refer_users = calc_commission($userid, 2);
				$add_result = true;

				if (!empty($refer_users)) {
					$add_result = addrefer_money($refer_users, 2, 1, 1, $userid);
				}

				if (!$add_result) {
					$user_model->rollback();
					$this->ajaxReturn('', '升级失败-l！', 0);
				}

				$user_model->commit();
				$this->ajaxReturn('', '升级成功', 1);
			}
			else {
				$this->ajaxReturn('', '升级失败 -A', 0);
			}
		}
		else {
			$user_model->rollback();
			$this->ajaxReturn('', '升级失败 -U', 0);
		}
	}
}

?>
