<?php
//dezend by 辰梦大人 QQ:30881917
echo "\r\n";
class OtherAction extends CommonAction
{
	public function moban()
	{
		$config = M('config')->where('id=1')->find();
		$config_url = APP_PATH . '/Tpl/template_config.php';
		$moban_list = include $config_url;
		$current_moban = $config['site_template'];
		$img_url = __ROOT__ . '/App/Tpl/';
		$this->assign('img_path', $img_url);
		$this->assign('current_moban', $current_moban);
		$this->assign('moban_list', $moban_list);
		$this->display();
	}

	public function mobansave()
	{
		if (!IS_POST) {
			$this->message2('无效请求!', __APP__ . '/Admin');
		}

		$site_template = I('site_template', 'default');
		$data = array();
		$data['site_template'] = $site_template;
		$config_model = new Model('config');

		if (false !== $config_model->where('id=1')->data($data)->save()) {
			$this->message2('模板设置成功!', 'moban');
		}
		else {
			$this->message2('模板设置失败：' . $config_model->getError(), 'moban');
		}
	}

	public function database()
	{
		$this->db_operate();
		$dbName = C('DB_NAME');
		$dbPrefix = C('DB_PREFIX');
		$bkdir = 'Public/Uploads/backupdata';
		$a = glob($bkdir . '/*.sql');
		$filelists = array();

		foreach ($a as $filename) {
			$filename = str_replace($bkdir . '/', '', $filename);

			if (preg_match('/tables_struct/', $filename)) {
				$structfile = $filename;
			}
			else if (0 < filesize($bkdir . '/' . $filename)) {
				$filelists[] = array('filename' => $filename, 'filepath' => $bkdir . '/' . $filename, 'filesize' => abs(filesize($bkdir . '/' . $filename)));
			}
		}

		$this->assign('filelists', $filelists);
		$otherTables = array();
		$dedeSysTables = array();
		$index_name = 'Tables_in_' . $dbName;
		$sql = 'Show Tables';
		$list = M()->query($sql);

		foreach ($list as $row) {
			if (preg_match('/^' . $dbPrefix . '/', $row[$index_name])) {
				$duoduoSysTables[] = $row[$index_name];
			}
			else {
				$otherTables[] = $row[$index_name];
			}
		}

		$systable_list = array();

		foreach ($duoduoSysTables as $table) {
			$count = M($table)->count();
			$tablename_n = preg_replace('/^' . $dbPrefix . '/', '', $table);
			$sql = 'SHOW TABLE STATUS LIKE \'' . $table . '\'';
			$states = M()->query($sql);
			$size = round($states[0]['Data_free'] / 1024, 2) . 'kb';
			$systable_list[] = array('tablename' => $table, 'counts' => $count, 'size' => $size, 'tablename_n' => $tablename_n);
		}

		foreach ($otherTables as $table) {
			$count = M($table)->count();
			$tablename_n = preg_replace('/^' . $dbPrefix . '/', '', $table);
			$sql = 'SHOW TABLE STATUS LIKE \'' . $table . '\'';
			$states = M()->query($sql);
			$size = round($states[0]['Data_free'] / 1024, 2) . 'kb';
			$othertable_list[] = array('tablename' => $table, 'counts' => $count, 'size' => $size, 'tablename_n' => $tablename_n);
		}

		$this->assign('systable_count', count($systable_list));
		$this->assign('othertable_count', count($othertable_list));
		$mysql_version = M()->query('SELECT VERSION();');
		$this->assign('mysql_version', $mysql_version[0]);
		$this->assign('duoduoSysTables', $systable_list);
		$this->assign('otherTables', $othertable_list);
		$this->assign('date', date('Ymd', time()));
		$this->display();
	}

	public function db_operate()
	{
		$dopost = I('dopost', '');
		$tablename = I('tablename', '');

		if ($dopost == 'viewinfo') {
			echo '<div style=\'float:right\'>[<a onclick=\'javascript:HideObj("_mydatainfo")\'><u>关闭</u></a>]</div><br/><pre>';

			if (empty($tablename)) {
				echo '没有指定表名！';
			}
			else {
				$sql = 'SHOW CREATE TABLE ' . $tablename;
				$row2 = M()->query($sql);
				$ctinfo = $row2[0]['Create Table'];
				echo trim($ctinfo);
			}

			echo '</pre>';
			exit();
		}
		else if ($dopost == 'opimize') {
			echo '<div style=\'float:right\'>[<a onclick=\'javascript:HideObj("_mydatainfo")\'><u>关闭</u></a>]</div><br/><pre>';

			if (empty($tablename)) {
				echo '没有指定表名！';
			}
			else {
				$rs = M()->query('OPTIMIZE TABLE `' . $tablename . '` ');

				if ($rs) {
					echo '执行优化表： ' . $tablename . '  OK！';
				}
				else {
					echo '执行优化表： ' . $tablename . '  失败，原因是：' . M()->getError();
				}
			}

			echo '</pre>';
			exit();
		}
		else if ($dopost == 'repair') {
			echo '<div style=\'float:right\'>[<a onclick=\'javascript:HideObj("_mydatainfo")\'><u>关闭</u></a>]</div><br/><pre>';

			if (empty($tablename)) {
				echo '没有指定表名！';
			}
			else {
				$rs = M()->query('REPAIR TABLE `' . $tablename . '` ');

				if ($rs) {
					echo '修复表： ' . $tablename . '  OK！';
				}
				else {
					echo '修复表： ' . $tablename . '  失败，原因是：' . M()->getError();
				}
			}

			echo '</pre>';
			exit();
		}
	}

	public function dbbak()
	{
		if (!IS_POST) {
			$this->message2('无效请求！', __APP__ . '/Admin');
		}

		$tablearr = I('tablearr', NULL);

		if (empty($tablearr)) {
			echo '你没选中任何表！';
			exit();
		}

		$bkdir = 'Public/Uploads/backupdata';

		if (!is_dir($bkdir)) {
			MkdirAll($bkdir);
		}

		if (empty($_POST['tablearr'])) {
			$table = $this->getTable();
		}
		else {
			$table = explode(',', $_POST['tablearr']);
		}

		$struct = $this->bakStruct($table);
		$record = $this->bakRecord($table);
		$sqls = $struct . $record;
		$dir = $bkdir . '/' . date('y-m-d') . '.sql';

		if (file_exists($dir)) {
			unlink($dir);
		}

		file_put_contents($dir, $sqls);

		if (file_exists($dir)) {
			echo '备份成功';
			exit();
		}
		else {
			echo '备份失败';
			exit();
		}
	}

	protected function getTable()
	{
		$dbName = C('DB_NAME');
		$result = M()->query('show tables from ' . $dbName);

		foreach ($result as $v) {
			$tbArray[] = $v['Tables_in_' . C('DB_NAME')];
		}

		return $tbArray;
	}

	protected function bakStruct($array)
	{
		$sql .= '-------------------------------------' . "\r\n" . '';
		$sql .= '------------珑翔系统-----------' . "\r\n" . '';
		$sql .= '---------' . date('Y-m-d H:i:s') . '---------' . "\r\n" . '';
		$sql .= '-------------------------------------' . "\r\n" . '';

		foreach ($array as $v) {
			$tbName = $v;
			$result = M()->query('show columns from ' . $tbName);
			$sql .= '--' . "\r\n" . '';
			$sql .= '-- 数据表结构: `' . $tbName . '`' . "\r\n" . '';
			$sql .= '--' . "\r\n" . '' . "\r\n" . '';
			$sql .= 'DROP TABLE IF EXISTS `' . $tbName . '`;' . "\r\n" . '';
			$sql .= 'create table `' . $tbName . '` (' . "\r\n" . '';
			$rsCount = count($result);

			foreach ($result as $k => $v) {
				$field = $v['Field'];
				$type = $v['Type'];
				$default = $v['Default'];
				$extra = $v['Extra'];
				$null = $v['Null'];

				if (!($default == '')) {
					$default = 'default ' . $default;
				}

				if ($null == 'NO') {
					$null = 'not null';
				}
				else {
					$null = 'null';
				}

				if ($v['Key'] == 'PRI') {
					$key = 'primary key';
				}
				else {
					$key = '';
				}

				if ($k < ($rsCount - 1)) {
					$sql .= '`' . $field . '` ' . $type . ' ' . $null . ' ' . $default . ' ' . $key . ' ' . $extra . ' ,' . "\r\n" . '';
				}
				else {
					$sql .= '`' . $field . '` ' . $type . ' ' . $null . ' ' . $default . ' ' . $key . ' ' . $extra . ' ' . "\r\n" . '';
				}
			}

			$sql .= ')engine=innodb charset=utf8;' . "\r\n" . '' . "\r\n" . '';
		}

		return str_replace(',)', ')', $sql);
	}

	protected function bakRecord($array)
	{
		foreach ($array as $v) {
			$tbName = $v;
			$rs = M()->query('select * from ' . $tbName);

			if (count($rs) <= 0) {
				continue;
			}

			$sql .= '--' . "\r\n" . '';
			$sql .= '-- 数据表中的数据: `' . $tbName . '`' . "\r\n" . '';
			$sql .= '--' . "\r\n" . '' . "\r\n" . '';

			foreach ($rs as $k => $v) {
				$sql .= 'INSERT INTO `' . $tbName . '` VALUES (';

				foreach ($v as $key => $value) {
					if ($value == '') {
						$value = 'null';
					}

					$type = gettype($value);

					if ($type == 'string') {
						$value = '\'' . addslashes($value) . '\'';
					}

					$sql .= $value . ',';
				}

				$sql .= ');' . "\r\n" . '' . "\r\n" . '';
			}
		}

		return str_replace(',)', ')', $sql);
	}

	public function delfile()
	{
		$filename = I('file', '');

		if ($filename == '') {
			$this->message2('无效请求!', 'database');
		}

		$bkdir = 'Public/Uploads/backupdata';
		$filepath = $bkdir . '/' . $filename;

		if (file_exists($filepath)) {
			unlink($filepath);
			$this->message2('删除成功！', U('database'));
		}
		else {
			$this->message2('未找到备份文件！', U('database'));
		}
	}

	public function downfile()
	{
		$filename = I('file', '');

		if ($filename == '') {
			$this->message2('无效请求!', U('database'));
		}

		$bkdir = 'Public/Uploads/backupdata';
		$filepath = $bkdir . '/' . $filename;

		if (!file_exists($filepath)) {
			$this->message2('未找到备份文件！', U('database'));
		}

		$file_path = iconv('utf-8', 'gb2312', $filepath);
		import('ORG.Net.Http');
		$download = new Http();
		$download->download($file_path, $filename);
	}

	public function auth()
	{
		$config = M('config')->where('id=1')->find();
		$auth_code = $config['auth_code'];
		$key_code = $config['auth_keycode'];
		$auth_code = $this->passport_decrypt($auth_code, $key_code);
		$auth_code = explode('&', $auth_code);
		$this->assign('config', $config);
		$this->assign('auth', $auth_code);
		$domain = $_SERVER['HTTP_HOST'];
		$auth_url = C('AUTH_URL');
		$this->assign('auth_url', $auth_url);
		$this->assign('domain', $domain);
		$this->display();
	}

	public function deal_auth()
	{
		if (!IS_AJAX) {
			$this->message2('无效访问!', __APP__ . '/Admin');
		}

		$auth_code = I('acode', '');
		$auth_keycode = I('keycode', '');
		$host = $_SERVER['HTTP_HOST'];
		$result = $this->opt_auth($auth_code, $auth_keycode, $host);

		if ($result) {
			$url = U('auth');
			$this->ajaxReturn($url, '授权成功', 1);
		}
		else {
			$this->ajaxReturn('', '授权失败2!', 0);
		}
	}

	public function refer()
	{
		$this->display();
	}

	public function tongji()
	{
		set_time_limit(0);
		$form_submit = I('form_submit', '');

		if ($form_submit == 'ok') {
			if (!IS_POST) {
				$this->message2('无效请求！', __APP__ . '/Admin');
			}

			$yw_type = I('yw_type', 'all_tongji');
			$starttime = I('starttime', '');

			if ($starttime == '') {
				$this->message2('统计开始时间段不能为空！', 'tongji');
			}

			$starttime = $starttime . ' 00:00:00';
			$endtime = I('endtime', '');
			$current_date = date('Y-m-d', time());

			if ($endtime == '') {
				$endtime = $current_date . ' 23:59:59';
				$_POST['endtime'] = $current_date;
			}
			else {
				$endtime = $endtime . ' 23:59:59';
			}

			$yw_result = array();
			$hy_result = array();

			switch ($yw_type) {
			case 'yw_tongji':
				$yw_result = $this->get_yw_tongji($starttime, $endtime);
				break;

			case 'hy_tongji':
				$hy_result = $this->get_hy_tongji($starttime, $endtime);
				break;

			default:
				$yw_result = $this->get_yw_tongji($starttime, $endtime);
				$hy_result = $this->get_hy_tongji($starttime, $endtime);
				break;
			}

			$user_type_list = M('user_level')->order('sort_order asc')->select();
			$level_list = array();

			foreach ($user_type_list as $k => $v) {
				$level_list[$v['id']] = $v['title'];
			}

			$hy_type = array('tixian' => '提现', 'recharge' => '充值');
			$this->assign('hy_type', $hy_type);
			$this->assign('utype_rows', count($level_list) + 1);
			$this->assign('level_list', $level_list);
			$kb_visible = 0;
			$dh_visible = 0;
			$xh_visible = 0;
			$yh_visible = 0;

			if (!empty($yw_result['kb'])) {
				$type_list = array();
				$kongbao_type_temp = M('kongbao_type')->order('sort_order asc')->select();

				foreach ($kongbao_type_temp as $k => $v) {
					$type_list[$v['id']] = $v['name'];
				}

				$this->assign('type_list', $type_list);
				$kb_rows = (count($type_list) * (count($level_list) + 1)) + 1;
				$this->assign('kb_rows', $kb_rows);
				$kb_visible = 1;
			}

			if (!empty($yw_result['dh'])) {
				$type_list = array();
				$danhao_type_temp = M('danhao_type')->order('sort_order asc')->select();

				foreach ($danhao_type_temp as $k => $v) {
					$type_list[$v['id']] = $v['name'];
				}

				$this->assign('dhtype_list', $type_list);
				$dh_rows = (count($type_list) * (count($level_list) + 1)) + 1;
				$this->assign('dh_rows', $dh_rows);

				if (1 < $dh_rows) {
					$xh_visible = 1;
				}
			}

			if (!empty($yw_result['xh'])) {
				$type_list = array();
				$xiaohao_type_temp = M('xiaohao_type')->order('sort_order asc')->select();

				foreach ($xiaohao_type_temp as $k => $v) {
					$type_list[$v['id']] = $v['name'];
				}

				$this->assign('xhtype_list', $type_list);
				$xh_rows = (count($type_list) * (count($level_list) + 1)) + 1;
				$this->assign('xh_rows', $xh_rows);

				if (1 < $xh_rows) {
					$xh_visible = 1;
				}
			}

			if (!empty($hy_result)) {
				$hy_rows = count($hy_type) * (count($level_list) + 1);
				$this->assign('hy_rows', $hy_rows);

				if (0 < $hy_rows) {
					$hy_visible = 1;
				}
			}

			$this->assign('kb_visible', $kb_visible);
			$this->assign('dh_visible', $dh_visible);
			$this->assign('xh_visible', $xh_visible);
			$this->assign('hy_visible', $hy_visible);
			$this->assign('yw_result', $yw_result);
			$this->assign('hy_result', $hy_result);
			$this->assign('yw_type', $yw_type);
			$this->assign('have_result', 1);
		}
		else {
			$this->assign('have_result', 0);
		}

		$this->display();
	}

	private function get_yw_tongji($starttime, $endtime)
	{
		$result = array();
		$config = M('config')->where('id=1')->find();
		$danhao_config = json_decode($config['danhao_config'], true);
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$model = new Model();
		$user_type_list = M('user_level')->order('sort_order asc')->select();
		$kb_type_list = M('kongbao_type')->order('sort_order asc')->select();
		$sum_sum_count = 0;
		$sum_sum_money = 0;
		$sum_sum_refer_money = 0;
		$sum_sum_lirun = 0;
		$sum_count = 0;
		$sum_money = 0;
		$sum_refer_money = 0;
		$sum_lirun = 0;

		foreach ($kb_type_list as $kk => $kb_type) {
			$result['kb'][$kb_type['id']]['type_name'] = $kb_type['name'];
			$kb_config = json_decode($kb_type['config'], true);
			$count_sum = 0;
			$money_sum = 0;
			$refer_money_sum = 0;
			$lirun_sum = 0;

			foreach ($user_type_list as $user_type) {
				$user_config = json_decode($user_type['config'], true);
				$utype_id = 'a' . $user_type['id'];
				$result['kb'][$kb_type['id']][$user_type['id']]['level_name'] = $user_type['title'];
				$result['kb'][$kb_type['id']][$user_type['id']]['price'] = isset($kb_config['price'][$utype_id]) ? $kb_config['price'][$utype_id] : 0;
				$count = 0;
				$money = 0;
				$refer_money = 0;
				$in_price = 0;
				$lirun = 0;
				$sql = 'select order_no ,sum(ifnull(order_money,0)) order_money, sum(ifnull(in_price,0)) in_price,count(1) counts  from kongbao_order  where  ' . ' type_id=' . $kb_type['id'] . ' and  order_status=1 and user_type=' . $user_type['id'] . ' and (order_time between \'' . $starttime . '\' and \'' . $endtime . '\') group by order_no';
				$order_list = $model->query($sql);

				if (!empty($order_list)) {
					foreach ($order_list as $order) {
						$count += $order['counts'];
						$money += (empty($order['order_money']) ? 0 : $order['order_money']);
						$in_price += (empty($order['in_price']) ? 0 : $order['in_price']);
						$refer_money_temp = M('account_log')->where('order_no=\'' . $order['order_no'] . '\' and stage=\'refer\'  ')->sum('money');
						$refer_money += $refer_money_temp;
					}
				}

				$sql = 'select order_no ,order_money,order_num,in_price from kongbao_daili_order  where  ' . ' type_id=' . $kb_type['id'] . ' and order_status=1 and user_type=' . $user_type['id'] . ' and (order_time between \'' . $starttime . '\' and \'' . $endtime . '\')';
				$order_list = $model->query($sql);

				if (!empty($order_list)) {
					foreach ($order_list as $order) {
						$count += $order['order_nums'];
						$order_money = (empty($order['order_money']) ? 0 : $order['order_money']);
						$in_price_1 = (empty($order['in_price']) ? 0 : $order['in_price']);
						$money += $order_money * $order['order_nums'];
						$in_price += $in_price_1 * $order['order_nums'];
						$refer_money_temp = M('account_log')->where('order_no=\'' . $order['order_no'] . '\' and stage=\'refer\'  ')->sum('money');
						$refer_money += $refer_money_temp;
					}
				}

				$count_sum += $count;
				$money_sum += $money;
				$refer_money_sum += $refer_money;
				$result['kb'][$kb_type['id']][$user_type['id']]['counts'] = $count;
				$result['kb'][$kb_type['id']][$user_type['id']]['money'] = $money;
				$result['kb'][$kb_type['id']][$user_type['id']]['refer_money'] = $refer_money;
				$lirun = $money - $refer_money - $in_price;
				$result['kb'][$kb_type['id']][$user_type['id']]['lirun'] = $lirun;
				$lirun_sum += $lirun;
			}

			$result['kb'][$kb_type['id']]['count_sum'] = $count_sum;
			$result['kb'][$kb_type['id']]['money_sum'] = $money_sum;
			$result['kb'][$kb_type['id']]['refer_money_sum'] = $refer_money_sum;
			$result['kb'][$kb_type['id']]['lirun_sum'] = $lirun_sum;
			$result['kb'][$kb_type['id']]['in_price'] = isset($kb_config['in_price']) ? $kb_config['in_price'] : 0;
			$sum_count += $count_sum;
			$sum_money += $money_sum;
			$sum_refer_money += $refer_money_sum;
			$sum_lirun += $lirun_sum;
		}

		$result['kb']['count_sum'] = $sum_count;
		$result['kb']['money_sum'] = $sum_money;
		$result['kb']['refer_money_sum'] = $sum_refer_money;
		$result['kb']['lirun_sum'] = $sum_lirun;
		$sum_sum_count += $sum_count;
		$sum_sum_money += $sum_money;
		$sum_sum_refer_money += $sum_refer_money;
		$sum_sum_lirun += $sum_lirun;
		$sum_count = 0;
		$sum_money = 0;
		$sum_refer_money = 0;
		$sum_lirun = 0;

		if ($xiaohao_config['valid'] == 1) {
			$xh_type_list = M('xiaohao_type')->order('sort_order asc')->select();

			foreach ($xh_type_list as $xk => $xh_type) {
				$count_sum = 0;
				$money_sum = 0;
				$refer_money_sum = 0;
				$lirun_sum = 0;
				$result['xh'][$xh_type['id']]['type_name'] = $xh_type['name'];
				$xh_config = json_decode($xh_type['config'], true);

				foreach ($user_type_list as $user_type) {
					$utype_id = 'a' . $user_type['id'];
					$result['xh'][$xh_type['id']][$user_type['id']]['level_name'] = $user_type['title'];
					$result['xh'][$xh_type['id']][$user_type['id']]['price'] = isset($xh_config['price'][$utype_id]) ? $xh_config['price'][$utype_id] : 0;
					$count = 0;
					$money = 0;
					$in_price = 0;
					$refer_money = 0;
					$sql = 'select order_no,order_money,in_price from xiaohao_order where ' . ' type_id=' . $xh_type['id'] . ' and order_status=1 and user_type=' . $user_type['id'] . ' and (order_time between \'' . $starttime . '\' and \'' . $endtime . '\')';
					$order_list = $model->query($sql);

					if (!empty($order_list)) {
						foreach ($order_list as $order) {
							$count += 1;
							$money += (empty($order['order_money']) ? 0 : $order['order_money']);
							$in_price += (empty($order['in_price']) ? 0 : $order['in_price']);
							$refer_money_temp = M('account_log')->where('order_no=\'' . $order['order_no'] . '\' and stage=\'refer\'  ')->sum('money');
							$refer_money += $refer_money_temp;
						}
					}

					$count_sum += $count;
					$money_sum += $money;
					$refer_money_sum += $refer_money;
					$result['xh'][$xh_type['id']][$user_type['id']]['counts'] = $count;
					$result['xh'][$xh_type['id']][$user_type['id']]['money'] = $money;
					$result['xh'][$xh_type['id']][$user_type['id']]['refer_money'] = $refer_money;
					$lirun = $money - $refer_money - $in_price;
					$result['xh'][$xh_type['id']][$user_type['id']]['lirun'] = $lirun;
					$lirun_sum += $lirun;
				}

				$result['xh'][$xh_type['id']]['count_sum'] = $count_sum;
				$result['xh'][$xh_type['id']]['money_sum'] = $money_sum;
				$result['xh'][$xh_type['id']]['refer_money_sum'] = $refer_money_sum;
				$result['xh'][$xh_type['id']]['lirun_sum'] = $lirun_sum;
				$result['xh'][$xh_type['id']]['in_price'] = isset($xh_config['in_price']) ? $xh_config['in_price'] : 0;
				$sum_count += $count_sum;
				$sum_money += $money_sum;
				$sum_refer_money += $refer_money_sum;
				$sum_lirun += $lirun_sum;
			}

			$result['xh']['count_sum'] = $sum_count;
			$result['xh']['money_sum'] = $sum_money;
			$result['xh']['refer_money_sum'] = $sum_refer_money;
			$result['xh']['lirun_sum'] = $sum_lirun;
			$sum_sum_count += $sum_count;
			$sum_sum_money += $sum_money;
			$sum_sum_refer_money += $sum_refer_money;
			$sum_sum_lirun += $sum_lirun;
		}

		$result['count_sum'] = $sum_sum_count;
		$result['money_sum'] = $sum_sum_money;
		$result['refer_money_sum'] = $sum_sum_refer_money;
		$result['lirun_sum'] = $sum_sum_lirun;
		return $result;
	}

	private function get_hy_tongji($starttime, $endtime)
	{
		$result = array();
		$model = new Model();
		$user_type_list = M('user_level')->order('sort_order asc')->select();
		$counts_sum = 0;
		$money_sum = 0;
		$refer_money_sum = 0;
		$invalid_money_sum = 0;

		foreach ($user_type_list as $k => $user_type) {
			$user_config = json_decode($user_type['config'], true);
			$result[$user_type['id']]['uplevel_fee'] = $user_config['money'];
			$counts = M('user')->where('user_type=' . $user_type['id'])->count();
			$money = M('user')->where('user_type=' . $user_type['id'])->sum('money');
			$refer_money = M('user')->where('user_type=' . $user_type['id'])->sum('refer_money');
			$invalid_money = M('user')->where('user_type=' . $user_type['id'])->sum('invalid_money');
			$result[$user_type['id']]['counts'] = $counts;
			$result[$user_type['id']]['money'] = $money;
			$result[$user_type['id']]['refer_money'] = $refer_money;
			$result[$user_type['id']]['invalid_money'] = $invalid_money;
			$invalid_money_sum += $invalid_money;
			$refer_money_sum += $refer_money;
			$money_sum += $money;
			$counts_sum += $counts;
			$sql = 'select  t.status status,count(1) count, sum(t.money) money  from tixian t,user u where t.user_id=u.id and u.user_type=' . $user_type['id'] . ' and (t.addtime between \'' . $starttime . '\' and \'' . $endtime . '\') group by t.status  ';
			$tixian_list = $model->query($sql);
			$tixian_counts = 0;
			$tixian_money = 0;
			$tixian_counts_f = 0;
			$tixian_money_f = 0;
			$tixian_counts_s = 0;
			$tixian_money_s = 0;
			$tixian_counts_w = 0;
			$tixian_money_w = 0;

			if (!empty($tixian_list)) {
				foreach ($tixian_list as $kt => $tixian) {
					switch ($tixian['status']) {
					case 1:
						$tixian_counts_s += $tixian['count'];
						$tixian_money_s += $tixian['money'];
						break;

					case 2:
						$tixian_counts_f += $tixian['count'];
						$tixian_money_f += $tixian['money'];
						break;

					default:
						$tixian_counts_w += $tixian['count'];
						$tixian_money_w += $tixian['money'];
						break;
					}
				}
			}

			$tixian_counts = $tixian_counts_f + $tixian_counts_s + $tixian_counts_w;
			$tixian_money = $tixian_money_f + $tixian_money_s + $tixian_money_w;
			$result[$user_type['id']]['tixian']['counts'] = $tixian_counts;
			$result[$user_type['id']]['tixian']['money'] = $tixian_money;
			$result[$user_type['id']]['tixian']['counts_w'] = $tixian_counts_w;
			$result[$user_type['id']]['tixian']['money_w'] = $tixian_money_w;
			$result[$user_type['id']]['tixian']['counts_f'] = $tixian_counts_f;
			$result[$user_type['id']]['tixian']['money_f'] = $tixian_money_f;
			$result[$user_type['id']]['tixian']['counts_s'] = $tixian_counts_s;
			$result[$user_type['id']]['tixian']['money_s'] = $tixian_money_s;
			$sql = 'select   status,count(1) count, sum(pay_money) money  from pay_order where user_type=' . $user_type['id'] . ' and (addtime between \'' . $starttime . '\' and \'' . $endtime . '\') and type=1 group by status  ';
			$recharge_list = $model->query($sql);
			$recharge_counts = 0;
			$recharge_money = 0;
			$recharge_counts_f = 0;
			$recharge_money_f = 0;
			$recharge_counts_s = 0;
			$recharge_money_s = 0;
			$recharge_counts_w = 0;
			$recharge_money_w = 0;

			if (!empty($recharge_list)) {
				foreach ($recharge_list as $kr => $recharge) {
					switch ($recharge['status']) {
					case 1:
						$recharge_counts_s += $recharge['count'];
						$recharge_money_s += $recharge['money'];
						break;

					case 2:
						$recharge_counts_f += $recharge['count'];
						$recharge_money_f += $recharge['money'];
						break;

					default:
						$recharge_counts_w += $recharge['count'];
						$recharge_money_w += $recharge['money'];
						break;
					}
				}
			}

			$recharge_counts = $recharge_counts_f + $recharge_counts_s + $recharge_counts_w;
			$recharge_money = $recharge_money_f + $recharge_money_s + $recharge_money_w;
			$result[$user_type['id']]['recharge']['counts'] = $recharge_counts;
			$result[$user_type['id']]['recharge']['money'] = $recharge_money;
			$result[$user_type['id']]['recharge']['counts_w'] = $recharge_counts_w;
			$result[$user_type['id']]['recharge']['money_w'] = $recharge_money_w;
			$result[$user_type['id']]['recharge']['counts_f'] = $recharge_counts_f;
			$result[$user_type['id']]['recharge']['money_f'] = $recharge_money_f;
			$result[$user_type['id']]['recharge']['counts_s'] = $recharge_counts_s;
			$result[$user_type['id']]['recharge']['money_s'] = $recharge_money_s;
		}

		$result['counts'] = $counts_sum;
		$result['money'] = $money_sum;
		$result['refer_money'] = $refer_money_sum;
		$result['invalid_money'] = $invalid_money_sum;
		return $result;
	}

	public function disauth()
	{
		$data['id'] = 1;
		$data['auth_code'] = '';
		$data['auth_keycode'] = '';
		$data['auth_host'] = '';
		M('config')->data($data)->save();
	}

	public function cwgk()
	{
		$tongji_data = $this->getTongjiInfo();
		$this->assign('tongji', $tongji_data);
		$this->display();
	}

	private function getTongjiInfo()
	{
		$rtn_tjdata = array();
		$today = date('Y-m-d');
		$today_start = $today . ' 00:00:00';
		$today_end = $today . ' 23:59:59';
		$yesterday = date('Y-m-d', strtotime('-1 day'));
		$yesterday_start = $yesterday . ' 00:00:00';
		$yesterday_end = $yesterday . ' 23:59:59';
		$month_start = date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))) . ' 00:00:00';
		$total_data = M('user')->where($where)->field('count(1) counts,sum(ifnull(money, 0)) money,sum(ifnull(refer_money, 0)) refer_money,sum(ifnull(invalid_money, 0)) invalid_money,sum(ifnull(used_money, 0)) used_money')->find();
		$rtn_tjdata['total']['user']['counts'] = $total_data['counts'];
		$rtn_tjdata['total']['user']['money'] = $total_data['money'];
		$rtn_tjdata['total']['user']['refer_money'] = $total_data['refer_money'];
		$rtn_tjdata['total']['user']['used_money'] = $total_data['used_money'];
		$rtn_tjdata['total']['user']['invalid_money'] = $total_data['invalid_money'];
		$where = array();
		$where['type'] = 1;
		$where['status'] = 1;
		$where['addtime'] = array(
	'between',
	array($today_start, $today_end)
	);
		$recharge_data = M('pay_order')->where($where)->field('count(1) counts,sum(ifnull(pay_money, 0)) money')->find();
		$rtn_tjdata['today']['recharge']['money'] = empty($recharge_data['money']) ? 0 : $recharge_data['money'];
		$rtn_tjdata['today']['recharge']['counts'] = $recharge_data['counts'];
		$where = array();
		$where['type'] = 1;
		$where['status'] = 1;
		$where['addtime'] = array(
	'between',
	array($yesterday_start, $yesterday_end)
	);
		$recharge_data = M('pay_order')->where($where)->field('count(1) counts,sum(ifnull(pay_money, 0)) money')->find();
		$rtn_tjdata['yesterday']['recharge']['money'] = empty($recharge_data['money']) ? 0 : $recharge_data['money'];
		$rtn_tjdata['yesterday']['recharge']['counts'] = $recharge_data['counts'];
		$where = array();
		$where['type'] = 1;
		$where['status'] = 1;
		$where['addtime'] = array(
	'between',
	array($month_start, $today_end)
	);
		$recharge_data = M('pay_order')->where($where)->field('count(1) counts,sum(ifnull(pay_money, 0)) money')->find();
		$rtn_tjdata['month']['recharge']['money'] = empty($recharge_data['money']) ? 0 : $recharge_data['money'];
		$rtn_tjdata['month']['recharge']['counts'] = $recharge_data['counts'];
		$where = array();
		$where['status'] = 2;
		$where['addtime'] = array(
	'between',
	array($today_start, $today_end)
	);
		$tixian_data = M('tixian')->where($where)->field('count(1) counts,sum(ifnull(money, 0)) money')->find();
		$rtn_tjdata['today']['tixian']['money'] = empty($tixian_data['money']) ? 0 : $tixian_data['money'];
		$rtn_tjdata['today']['tixian']['counts'] = $tixian_data['counts'];
		$where = array();
		$where['status'] = 2;
		$where['addtime'] = array(
	'between',
	array($yesterday_start, $yesterday_end)
	);
		$tixian_data = M('tixian')->where($where)->field('count(1) counts,sum(ifnull(money, 0)) money')->find();
		$rtn_tjdata['yesterday']['tixian']['money'] = empty($tixian_data['money']) ? 0 : $tixian_data['money'];
		$rtn_tjdata['yesterday']['tixian']['counts'] = $tixian_data['counts'];
		$where = array();
		$where['status'] = 2;
		$where['addtime'] = array(
	'between',
	array($month_start, $today_end)
	);
		$tixian_data = M('tixian')->where($where)->field('count(1) counts,sum(ifnull(money, 0)) money')->find();
		$rtn_tjdata['month']['tixian']['money'] = empty($tixian_data['money']) ? 0 : $tixian_data['money'];
		$rtn_tjdata['month']['tixian']['counts'] = $tixian_data['counts'];
		$where = array();
		$where['stage'] = 'upgrade';
		$where['addtime'] = array(
	'between',
	array($today_start, $today_end)
	);
		$upgrade_data = M('account_log')->where($where)->field('count(1) counts,sum( ifnull(abs(money), 0)) money')->find();
		$rtn_tjdata['today']['upgrade']['money'] = empty($upgrade_data['money']) ? 0 : $upgrade_data['money'];
		$rtn_tjdata['today']['upgrade']['counts'] = $upgrade_data['counts'];
		$where = array();
		$where['stage'] = 'upgrade';
		$where['addtime'] = array(
	'between',
	array($yesterday_start, $yesterday_end)
	);
		$upgrade_data = M('account_log')->where($where)->field('count(1) counts,sum( ifnull(abs(money), 0)) money')->find();
		$rtn_tjdata['yesterday']['upgrade']['money'] = empty($upgrade_data['money']) ? 0 : $upgrade_data['money'];
		$rtn_tjdata['yesterday']['upgrade']['counts'] = $upgrade_data['counts'];
		$where = array();
		$where['stage'] = 'upgrade';
		$where['addtime'] = array(
	'between',
	array($month_start, $today_end)
	);
		$upgrade_data = M('account_log')->where($where)->field('count(1) counts,sum( ifnull(abs(money), 0)) money')->find();
		$rtn_tjdata['month']['upgrade']['money'] = empty($upgrade_data['money']) ? 0 : $upgrade_data['money'];
		$rtn_tjdata['month']['upgrade']['counts'] = $upgrade_data['counts'];
		$where = array();
		$where['stage'] = 'refer';
		$where['addtime'] = array(
	'between',
	array($today_start, $today_end)
	);
		$refer_data = M('account_log')->where($where)->field('count(1) counts,sum( ifnull(money, 0)) money')->find();
		$rtn_tjdata['today']['refer']['money'] = empty($refer_data['money']) ? 0 : $refer_data['money'];
		$rtn_tjdata['today']['refer']['counts'] = $refer_data['counts'];
		$where = array();
		$where['stage'] = 'refer';
		$where['addtime'] = array(
	'between',
	array($yesterday_start, $yesterday_end)
	);
		$refer_data = M('account_log')->where($where)->field('count(1) counts,sum( ifnull(money, 0)) money')->find();
		$rtn_tjdata['yesterday']['refer']['money'] = empty($refer_data['money']) ? 0 : $refer_data['money'];
		$rtn_tjdata['yesterday']['refer']['counts'] = $refer_data['counts'];
		$where = array();
		$where['stage'] = 'refer';
		$where['addtime'] = array(
	'between',
	array($month_start, $today_end)
	);
		$refer_data = M('account_log')->where($where)->field('count(1) counts,sum( ifnull(money, 0)) money')->find();
		$rtn_tjdata['month']['refer']['money'] = empty($refer_data['money']) ? 0 : $refer_data['money'];
		$rtn_tjdata['month']['refer']['counts'] = $refer_data['counts'];
		$kb_data = $this->getKongbaoData($today_start, $today_end);
		$rtn_tjdata['today']['kb']['counts'] = $kb_data['kb']['count_sum'];
		$rtn_tjdata['today']['kb']['money'] = $kb_data['kb']['money_sum'];
		$rtn_tjdata['today']['kb']['lirun'] = $kb_data['kb']['lirun_sum'];
		$kb_data = $this->getKongbaoData($yesterday_start, $yesterday_end);
		$rtn_tjdata['yesterday']['kb']['counts'] = $kb_data['kb']['count_sum'];
		$rtn_tjdata['yesterday']['kb']['money'] = $kb_data['kb']['money_sum'];
		$rtn_tjdata['yesterday']['kb']['lirun'] = $kb_data['kb']['lirun_sum'];
		$kb_data = $this->getKongbaoData($month_start, $today_end);
		$rtn_tjdata['month']['kb']['counts'] = $kb_data['kb']['count_sum'];
		$rtn_tjdata['month']['kb']['money'] = $kb_data['kb']['money_sum'];
		$rtn_tjdata['month']['kb']['lirun'] = $kb_data['kb']['lirun_sum'];
		$xh_data = $this->getXiaohaoData($today_start, $today_end);
		$rtn_tjdata['today']['xh']['counts'] = $xh_data['xh']['count_sum'];
		$rtn_tjdata['today']['xh']['money'] = $xh_data['xh']['money_sum'];
		$rtn_tjdata['today']['xh']['lirun'] = $xh_data['xh']['lirun_sum'];
		$xh_data = $this->getXiaohaoData($yesterday_start, $yesterday_end);
		$rtn_tjdata['yesterday']['xh']['counts'] = $xh_data['xh']['count_sum'];
		$rtn_tjdata['yesterday']['xh']['money'] = $xh_data['xh']['money_sum'];
		$rtn_tjdata['yesterday']['xh']['lirun'] = $xh_data['xh']['lirun_sum'];
		$xh_data = $this->getXiaohaoData($month_start, $today_end);
		$rtn_tjdata['month']['xh']['counts'] = $xh_data['xh']['count_sum'];
		$rtn_tjdata['month']['xh']['money'] = $xh_data['xh']['money_sum'];
		$rtn_tjdata['month']['xh']['lirun'] = $xh_data['xh']['lirun_sum'];
		$rtn_tjdata['today']['lirun'] = $rtn_tjdata['today']['kb']['lirun'] + $rtn_tjdata['today']['xh']['lirun'];
		$rtn_tjdata['yesterday']['lirun'] = $rtn_tjdata['yesterday']['kb']['lirun'] + $rtn_tjdata['yesterday']['xh']['lirun'];
		$rtn_tjdata['month']['lirun'] = $rtn_tjdata['month']['kb']['lirun'] + $rtn_tjdata['month']['xh']['lirun'];
		return $rtn_tjdata;
	}

	private function getKongbaoData($starttime, $endtime)
	{
		$result = array();
		$config = M('config')->where('id=1')->find();
		$model = new Model();
		$user_type_list = M('user_level')->order('sort_order asc')->select();
		$kb_type_list = M('kongbao_type')->order('sort_order asc')->select();
		$sum_count = 0;
		$sum_money = 0;
		$sum_lirun = 0;

		foreach ($kb_type_list as $kk => $kb_type) {
			$kb_config = json_decode($kb_type['config'], true);
			$count_sum = 0;
			$money_sum = 0;
			$lirun_sum = 0;

			foreach ($user_type_list as $user_type) {
				$user_config = json_decode($user_type['config'], true);
				$count = 0;
				$money = 0;
				$in_price = 0;
				$refer_money = 0;
				$lirun = 0;
				$sql = 'select order_no,sum(ifnull(order_money,0)) order_money, sum(ifnull(in_price,0)) in_price,count(1) counts from kongbao_order  where ' . ' type_id=' . $kb_type['id'] . ' and order_status=1  and  user_type=' . $user_type['id'] . ' and (order_time between \'' . $starttime . '\' and \'' . $endtime . '\') group by order_no';
				$order_list = $model->query($sql);

				if (!empty($order_list)) {
					foreach ($order_list as $order) {
						$count += $order['counts'];
						$money += (empty($order['order_money']) ? 0 : $order['order_money']);
						$in_price += (empty($order['in_price']) ? 0 : $order['in_price']);
						$refer_money_temp = M('account_log')->where('order_no=\'' . $order['order_no'] . '\' and stage=\'refer\'  ')->sum('money');
						$refer_money += (empty($refer_money_temp) ? 0 : $refer_money_temp);
					}
				}

				$sql = 'select  order_no,order_money,in_price,order_nums from kongbao_daili_order  where ' . ' type_id=' . $kb_type['id'] . ' and order_status=1 and user_type=' . $user_type['id'] . ' and (order_time between \'' . $starttime . '\' and \'' . $endtime . '\')';
				$order_list = $model->query($sql);

				if (!empty($order_list)) {
					foreach ($order_list as $order) {
						$count += $order['order_nums'];
						$order_money = (empty($order['order_money']) ? 0 : $order['order_money']);
						$in_price_1 = (empty($order['in_price']) ? 0 : $order['in_price']);
						$money += $order_money * $order['order_nums'];
						$in_price += $in_price_1 * $order['order_nums'];
						$refer_money_temp = M('account_log')->where('order_no=\'' . $order['order_no'] . '\' and stage=\'refer\'  ')->sum('money');
						$refer_money += (empty($refer_money_temp) ? 0 : $refer_money_temp);
					}
				}

				$count_sum += $count;
				$money_sum += $money;
				$lirun = $money - $refer_money - $in_price;
				$lirun_sum += $lirun;
			}

			$sum_count += $count_sum;
			$sum_money += $money_sum;
			$sum_lirun += $lirun_sum;
		}

		$result['kb']['count_sum'] = $sum_count;
		$result['kb']['money_sum'] = $sum_money;
		$result['kb']['lirun_sum'] = $sum_lirun;
		return $result;
	}

	private function getXiaohaoData($starttime, $endtime)
	{
		$config = M('config')->where('id=1')->find();
		$model = new Model();
		$xiaohao_config = json_decode($config['xiaohao_config'], true);
		$user_type_list = M('user_level')->order('sort_order asc')->select();
		$sum_count = 0;
		$sum_money = 0;
		$sum_refer_money = 0;
		$sum_lirun = 0;

		if ($xiaohao_config['valid'] == 1) {
			$xh_type_list = M('xiaohao_type')->order('sort_order asc')->select();

			foreach ($xh_type_list as $xk => $xh_type) {
				$count_sum = 0;
				$money_sum = 0;
				$refer_money_sum = 0;
				$lirun_sum = 0;
				$xh_config = json_decode($xh_type['config'], true);

				foreach ($user_type_list as $user_type) {
					$count = 0;
					$money = 0;
					$in_price = 0;
					$refer_money = 0;
					$sql = 'select order_no ,order_money,in_price from xiaohao_order where ' . '  type_id=' . $xh_type['id'] . ' and order_status=1   and user_type=' . $user_type['id'] . ' and (order_time between \'' . $starttime . '\' and \'' . $endtime . '\')';
					$order_list = $model->query($sql);

					if (!empty($order_list)) {
						foreach ($order_list as $order) {
							$count += 1;
							$money += (empty($order['order_money']) ? 0 : $order['order_money']);
							$in_price += (empty($order['in_price']) ? 0 : $order['in_price']);
							$refer_money_temp = M('account_log')->where('order_no=\'' . $order['order_no'] . '\' and stage=\'refer\'  ')->sum('money');
							$refer_money += (empty($refer_money_temp) ? 0 : $refer_money_temp);
						}
					}

					$count_sum += $count;
					$money_sum += $money;
					$lirun = $money - $refer_money - $in_price;
					$lirun_sum += $lirun;
				}

				$sum_count += $count_sum;
				$sum_money += $money_sum;
				$sum_lirun += $lirun_sum;
			}

			$result['xh']['count_sum'] = $sum_count;
			$result['xh']['money_sum'] = $sum_money;
			$result['xh']['lirun_sum'] = $sum_lirun;
		}

		return $result;
	}
}


?>
