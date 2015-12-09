-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 07 月 23 日 21:29
-- 服务器版本: 5.5.40
-- PHP 版本: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ceshi`
--

-- --------------------------------------------------------

--
-- 表的结构 `about`
--

CREATE TABLE IF NOT EXISTS `about` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `content` text,
  `time` datetime DEFAULT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `desc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='关于我们' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `account_log`
--

CREATE TABLE IF NOT EXISTS `account_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `stage` enum('admin','return','buy','cash','refund','refer','upgrade','change','recharge') DEFAULT 'buy' COMMENT 'admin-管理员处理refund-退款操作buy-购买cash-提现recharge-充值return-提现失败冲回refer-佣金提成upgrade-升级-change-佣金转换',
  `money` decimal(12,4) DEFAULT '0.0000' COMMENT '变动金额',
  `remain_money` decimal(12,2) DEFAULT '0.00' COMMENT '可用余额',
  `remain_refer_money` decimal(12,4) DEFAULT '0.0000' COMMENT '可用佣金',
  `comm` varchar(255) DEFAULT NULL COMMENT '备注',
  `addtime` datetime DEFAULT NULL COMMENT '日志时间',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单编号(用于佣金冲减时检索使用)',
  `is_used` tinyint(3) DEFAULT '0' COMMENT '是否已用(退款时使用)',
  PRIMARY KEY (`id`),
  KEY `indx_stage` (`stage`),
  KEY `indx_refer_orderno` (`stage`,`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='账户金额变动日志' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk COMMENT='管理员列表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `last_login_time`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `admin_log`
--

CREATE TABLE IF NOT EXISTS `admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `opt_type` varchar(20) DEFAULT NULL COMMENT '操作类型',
  `comm` longtext COMMENT '备注',
  `opt_ip` varchar(255) DEFAULT NULL COMMENT '操作IP',
  `addtime` datetime DEFAULT NULL COMMENT '操作日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=gbk COMMENT='后台管理员用户操作日志表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `admin_log`
--

INSERT INTO `admin_log` (`id`, `user_id`, `opt_type`, `comm`, `opt_ip`, `addtime`) VALUES
(1, 1, '管理员登陆', '127.0.0.1', '127.0.0.1', '2015-07-23 20:45:24');

-- --------------------------------------------------------

--
-- 表的结构 `adv`
--

CREATE TABLE IF NOT EXISTS `adv` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `typeid` int(4) DEFAULT NULL,
  `pic_url` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  `ad_brief` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `pic` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type_id` int(5) DEFAULT NULL,
  `article_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `article_content` text CHARACTER SET utf8,
  `article_time` datetime DEFAULT NULL,
  `article_keyword` varchar(100) DEFAULT NULL COMMENT '文章关键词',
  `article_desc` varchar(100) DEFAULT NULL COMMENT '文章摘要',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `article_type`
--

CREATE TABLE IF NOT EXISTS `article_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `brief` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `bieming` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `auto_order`
--

CREATE TABLE IF NOT EXISTS `auto_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `order_no` varchar(50) DEFAULT NULL COMMENT '系统内支付订单',
  `outer_order_no` varchar(50) DEFAULT NULL COMMENT '外部订单编号',
  `outer_trade_no` varchar(50) DEFAULT NULL COMMENT '支付网站交易号',
  `order_type` tinyint(3) DEFAULT '1' COMMENT '订单类型1-支付宝',
  `duifang` varchar(50) DEFAULT NULL COMMENT '对方',
  `order_money` decimal(12,2) DEFAULT '0.00' COMMENT '交易金额',
  `order_status` varchar(50) DEFAULT NULL COMMENT '付款状态',
  `order_time` datetime DEFAULT NULL COMMENT '交易时间',
  `addtime` datetime DEFAULT NULL COMMENT '处理时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '未处理0已处理1',
  `comm` text COMMENT '备注信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `siteurl` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `site_logo` varchar(100) DEFAULT NULL COMMENT '网站logo',
  `metadesc` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `metakeys` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `qq1` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '新手客服',
  `qq2` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '代理商客服',
  `email` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `alipay_pid` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `alipay_Key` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `tongji` varchar(1200) CHARACTER SET utf8 DEFAULT NULL,
  `beian` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `alipay` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `tg_title` varchar(50) DEFAULT NULL COMMENT '推广页面标题',
  `tg_content1` text COMMENT '推广内容1用于qq或者msn推广',
  `tg_content2` text COMMENT '推广内容2-用于论坛、博客推广',
  `tg_content3` text COMMENT '推广内容3-用于分享组件推广',
  `tg_images` varchar(100) DEFAULT NULL COMMENT '推广图片',
  `payonline_setting` text COMMENT '在线支付设置',
  `fetion_setting` text COMMENT '飞信设置',
  `tixian_setting` varchar(255) DEFAULT NULL COMMENT '提现设置信息',
  `reg_setting` varchar(255) DEFAULT NULL COMMENT '会员注册设置',
  `refer_mode` tinyint(3) DEFAULT '0' COMMENT '佣金模式0-无推广佣金1-一级佣金2-二级佣金3-三级佣金4-四级佣金',
  `daili_refer_mode` tinyint(3) DEFAULT '1' COMMENT '代理佣金模式',
  `cz_refer_mode` tinyint(3) DEFAULT '0' COMMENT '充值佣金模式0-无推广佣金1-一级佣金2-二级佣金3-三级佣金4-四级佣金',
  `kongbao_config` text COMMENT '空包相关设置(是否启用默认配置等)',
  `danhao_config` text COMMENT '单号业务设置',
  `xiaohao_config` text COMMENT '小号业务设置',
  `auth_code` varchar(100) DEFAULT NULL COMMENT '系统授权码',
  `auth_keycode` varchar(50) DEFAULT NULL,
  `auth_host` varchar(100) DEFAULT NULL,
  `copy_right` text COMMENT '版权信息',
  `kongbao_page` text COMMENT '空包下单页面描述',
  `led_content` text COMMENT '首页LED滚动屏显示',
  `site_template` varchar(50) DEFAULT NULL COMMENT '前台模板',
  `tuiguang_valid` tinyint(3) DEFAULT '1' COMMENT '推广程序设置0-不推广1-推广',
  `kefu_javascript` text COMMENT '在线客服js代码',
  `backbind_ip` text COMMENT '后台绑定IP,每行一条',
  `tuiguang_shorturl` tinyint(3) DEFAULT '0' COMMENT '是否启用短地址推广',
  `site_status` tinyint(3) DEFAULT '1' COMMENT '站点状态0关闭1-开启',
  `close_reason` text COMMENT '关闭原因',
  `email_setting` text COMMENT '邮件相关设置',
  `metatitle` varchar(100) DEFAULT NULL COMMENT '首页标题',
  `back_url` varchar(50) DEFAULT NULL COMMENT '后台地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=gbk COMMENT='网站配置信息' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `config`
--

INSERT INTO `config` (`id`, `sitename`, `siteurl`, `site_logo`, `metadesc`, `metakeys`, `phone`, `qq1`, `qq2`, `email`, `alipay_pid`, `alipay_Key`, `tongji`, `beian`, `alipay`, `tg_title`, `tg_content1`, `tg_content2`, `tg_content3`, `tg_images`, `payonline_setting`, `fetion_setting`, `tixian_setting`, `reg_setting`, `refer_mode`, `daili_refer_mode`, `cz_refer_mode`, `kongbao_config`, `danhao_config`, `xiaohao_config`, `auth_code`, `auth_keycode`, `auth_host`, `copy_right`, `kongbao_page`, `led_content`, `site_template`, `tuiguang_valid`, `kefu_javascript`, `backbind_ip`, `tuiguang_shorturl`, `site_status`, `close_reason`, `email_setting`, `metatitle`, `back_url`) VALUES
(1, '全天自动售单系统', 'www.very-good-soft.com', 'Public/Uploads/admin/55b0e4c272c91.png', '本店出售快递空单包,提供能达快递单号,全峰快递单号,申通快递单号,圆通快递单号,等各大快递单号出售,全部记录真实有效,全国地址任意发,24小时自助下单,快速免费提供底单', '空包网,快递单号,申通快递单号,圆通快递单号,代发快递空包,空包代理,代理空包', '123456789', '2231909019', '2231909019', 'auto_order@foxmail.com', '', '', '<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id=''cnzz_stat_icon_1000118289''%3E%3C/span%3E%3Cscript src=''" + cnzz_protocol + "s22.cnzz.com/z_stat.php%3Fid%3D1000118289%26show%3Dpic'' type=''text/javascript''%3E%3C/script%3E"));</script>', '鲁ICP备13021625号', '', '好软件 好推广 就在顶好软件推广联盟  高额佣金等你拿 两级分佣模式 国内首创', '{refer_url} 好软件 好推广 就在顶好软件推广联盟 免费注册会员 拿高额佣金，赶快来吧！', '<a href="{refer_url}" target="_blank">好软件 好推广 就在顶好软件推广联盟 免费注册会员 拿高额佣金，赶快来吧！！</a>', '国内首创二级分佣模式，免费注册会员推广软件即可赚取高额佣金，让你轻松月赚万元，还等什么，赶快来赚钱吧！！ {refer_url}', '/vgsoft/Public/kindeditor/attached/image/20140412/20140412004811_89351.jpg', '{"alipay_zz":{"receiver":"abcdefgh@gmail.com","name":"\\u586b\\u5199\\u4f60\\u7684\\u59d3\\u540d","url":"https:\\/\\/shenghuo.alipay.com\\/send\\/payment\\/fill.htm","comm":"\\u4ee5\\u4e0a\\u4fe1\\u606f\\u5207\\u52ff\\u66f4\\u6539","min_money":"0"}}', NULL, '{"refernums":"0","used_money":"100","money":"100","min_money":"10","zsb":"10"}', '{"verycode":"0","shenhe":"0"}', 3, 3, 0, '{"daili_refer_default":"1","daili_refer":{"a1":"0","a2":"0","a3":"0"},"tixing":"100","valid":"1","refer_default":"1","buy_refer":{"a1":"2","a2":"1","a3":"0.5"}}', '{"tixing":"0","valid":"0","refer_default":"0","buy_refer":{"a1":"","a2":"","a3":""}}', '{"tixing":"0","valid":"0","refer_default":"1","buy_refer":{"a1":"","a2":"","a3":""}}', '', '', '', 'Copyright@ 2011-2014 物流空包网（代发空包）版权所有', '{"gonggao":"<span style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;font-size:16px;font-weight:bold;line-height:normal;background-color:#FFFFFF;\\">\\u5904\\u7406\\u65f6\\u95f4\\uff1a\\u6bcf\\u5929\\u665a\\u4e0a 21:00\\u70b9\\u4e4b\\u524d\\u63d0\\u4ea4\\u7684\\u8ba2\\u5355\\u53ef\\u4ee5\\u53ef\\u4ee5\\u5f53\\u665a23:00\\u6709\\u7269\\u6d41\\u4fe1\\u606f(\\u663e\\u793a\\u4e0b\\u5355\\u5f53\\u65e5\\u53d1\\u8d27)<\\/span><br \\/>\\r\\n<span style=\\"background-color:#FFFFFF;color:#E53333;font-size:14px;\\"><strong>\\u53d1\\u4f18\\u901f\\u6ce8\\u610f\\uff1a\\u53d1\\u8d27\\u591a\\u4e00\\u4e2a\\u6b65\\u9aa4=&gt;\\u5728\\u6dd8\\u5b9d\\u4e0a\\u8f93\\u5165\\u5355\\u53f7\\u53d1\\u8d27\\u524d\\uff0c\\u8981\\u8bbe\\u7f6e\\u9ed8\\u8ba4\\u53d1\\u8d27\\u5730\\u5740\\u4e3a\\u672c\\u5e73\\u53f0\\u63d0\\u4ea4\\u7684\\u53d1\\u8d27\\u5730\\u5740\\uff1b\\u5426\\u5219\\u7269\\u6d41\\u4f1a\\u9519\\u8bef\\uff01<\\/strong><\\/span>","shuoming":"<span style=\\"color:#E53333;\\"><strong>\\u4e0b\\u5355\\u8bf4\\u660e<\\/strong><\\/span><span style=\\"color:#E53333;\\"><strong><\\/strong><\\/span>","true_gonggao":"","true_shuoming":"","pf_gonggao":"<span style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;font-size:16px;font-weight:bold;line-height:normal;background-color:#FFFFFF;\\">\\u6279\\u53d1\\u540e,\\u4e0b\\u8f7d\\u5bf9\\u5e94\\u8868\\u683c,\\u586b\\u5199\\u5b8c\\u6574,\\u5728\\u5e73\\u53f0\\u4e0a\\u4f20<\\/span><br \\/>\\r\\n<span style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;font-size:16px;font-weight:bold;line-height:normal;background-color:#FFFFFF;\\">\\u5904\\u7406\\u65f6\\u95f4\\uff1a\\u6bcf\\u5929\\u665a\\u4e0a 21:00\\u70b9\\u4e4b\\u524d\\u63d0\\u4ea4\\u7684\\u8ba2\\u5355\\u53ef\\u4ee5\\u53ef\\u4ee5\\u5f53\\u665a23:00\\u6709\\u7269\\u6d41\\u4fe1\\u606f(\\u663e\\u793a\\u4e0b\\u5355\\u5f53\\u65e5\\u53d1\\u8d27)<\\/span><br \\/>\\r\\n<span style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;font-size:16px;font-weight:bold;line-height:normal;background-color:#FFFFFF;\\">\\u53d1\\u4f18\\u901f\\u8bf7\\u6ce8\\u610f\\uff1a18\\u70b9\\u524d\\u4e0b\\u5355\\uff0c\\u5f53\\u5929\\u663e\\u793a\\u7269\\u6d41\\uff0c\\u5426\\u5219\\u7b2c\\u4e8c\\u5929\\u4e0a\\u5348\\u663e\\u793a\\u7269\\u6d41\\uff01<\\/span>","pf_shuoming":"","dh_gonggao":"","dh_shuoming":"","xh_gonggao":"","xh_shuoming":"","dd_gonggao":"<p style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;text-align:center;background-color:#FFFFFF;\\">\\r\\n\\t<br \\/>\\r\\n<\\/p>\\r\\n<p style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;text-align:center;background-color:#FFFFFF;\\">\\r\\n\\t<span class=\\"STYLE3\\" style=\\"font-size:18px;font-weight:bold;font-family:\\u65b0\\u5b8b\\u4f53;color:#FF0000;\\">\\u7533\\u8bf7\\u65f6\\u95f4:\\u6bcf\\u592911:00--16:00 \\u7533\\u8bf7\\u4e4b\\u524d\\u8bf7\\u68c0\\u67e5\\u4e0b\\u6dd8\\u5b9d\\u964d\\u6743\\u5355\\u53f7\\u662f\\u5426\\u6709\\u53d8,\\u786e\\u8ba4\\u540e\\u518d\\u7533\\u8bf7 \\u8bf7\\u914d\\u5408<\\/span>&nbsp;\\r\\n<\\/p>\\r\\n<p style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;text-align:center;background-color:#FFFFFF;\\">\\r\\n\\t<span class=\\"STYLE3\\" style=\\"font-size:18px;font-weight:bold;font-family:\\u65b0\\u5b8b\\u4f53;color:#FF0000;\\">\\u7533\\u8bf7\\u5e95\\u5355,<span><b>\\u624b\\u5199\\u5e95\\u5355,\\u4fdd\\u8bc1\\u901a\\u8fc7 \\u4e00\\u5f8b\\u514d\\u8d39<\\/b><\\/span>&nbsp;\\u6bcf\\u592917:00\\u5de6\\u53f3\\u6765\\u5e73\\u53f0\\u9886\\u53d6\\u5e95\\u5355<\\/span>&nbsp;\\r\\n<\\/p>\\r\\n<p style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;text-align:center;background-color:#FFFFFF;\\">\\r\\n\\t<span class=\\"STYLE3\\" style=\\"font-size:18px;font-weight:bold;font-family:\\u65b0\\u5b8b\\u4f53;color:#FF0000;\\">\\u4fdd\\u8bc1\\u901a\\u8fc7(\\u6ce8\\u91ca:\\u53ea\\u8981\\u4e0d\\u5237\\u5f97\\u592a\\u660e\\u663e \\u90fd\\u80fd\\u901a\\u8fc7. \\u6bd4\\u5982 \\u4ee3\\u4ed8\\u5237 \\u7ea2\\u5305\\u5237 \\u81ea\\u5df1\\u767b\\u9646\\u5c0f\\u53f7\\u8d2d\\u4e70\\u5237<br \\/>\\r\\n\\u65e0\\u5047\\u804a\\u7b49\\u7b49\\u662f\\u80af\\u5b9a\\u8fc7\\u4e0d\\u4e86\\u7684,\\u6240\\u4ee5\\u81ea\\u5df1\\u5bf9\\u5237\\u6cd5\\u6709\\u4fe1\\u5fc3\\u7684\\u6765\\u7533\\u8bf7,\\u81ea\\u5df1\\u8981\\u6709\\u81ea\\u77e5\\u4e4b\\u660e)<\\/span> \\r\\n<\\/p>\\r\\n<p style=\\"font-family:Tahoma, Helvetica, Arial, Simsun;text-align:center;background-color:#FFFFFF;\\">\\r\\n\\t<span class=\\"STYLE4\\" style=\\"font-size:18px;font-weight:bold;font-family:\\u65b0\\u5b8b\\u4f53;color:green;\\">\\u975e\\u672c\\u5e73\\u53f0\\u5355\\u53f7,\\u4e0d\\u7ed9\\u514d\\u8d39\\u5904\\u7406!<\\/span>&nbsp;\\r\\n<\\/p>","upload_gonggao":"<p class=\\"STYLE2\\" style=\\"font-size:18px;font-weight:bold;color:#FF0000;font-family:\\u5b8b\\u4f53;text-align:-webkit-center;background-color:#FFFFFF;\\">\\r\\n\\t\\u4e0a\\u4f20\\u8868\\u683c:(\\u6bcf\\u5929\\u53ea\\u6709\\u4e24\\u6b21\\u63d0\\u4ea4\\u673a\\u4f1a,\\u4ee3\\u7406\\u4e0a\\u4f20\\u8868\\u683c\\u6587\\u4ef6\\u540d\\u4e0d\\u80fd\\u91cd\\u590d,\\u5426\\u5219\\u5c06\\u88ab\\u8986\\u76d6)\\r\\n<\\/p>\\r\\n<p class=\\"STYLE3\\" style=\\"font-size:16px;font-weight:bold;font-family:\\u5b8b\\u4f53;text-align:-webkit-center;background-color:#FFFFFF;\\">\\r\\n\\t\\u4ee3\\u7406\\u8868\\u683c\\u5343\\u4e07\\u4e0d\\u80fd\\u79c1\\u81ea\\u4fee\\u6539\\u8868\\u683c\\uff0c\\u5426\\u5219\\u65e0\\u6cd5\\u5f55\\u5165\\uff01\\u8bf7\\u5148\\u6838\\u5bf9\\u81ea\\u5df1\\u7684\\u8868\\u683c\\u65e0\\u8bef\\u518d\\u4e0a\\u4f20\\uff0c\\u8bf7\\u914d\\u5408,\\u8c22\\u8c22\\uff01\\r\\n<\\/p>\\r\\n<p class=\\"STYLE3\\" style=\\"font-size:16px;font-weight:bold;font-family:\\u5b8b\\u4f53;text-align:-webkit-center;background-color:#FFFFFF;\\">\\r\\n\\t\\uff08\\u63d0\\u4ea4\\u65f6\\u95f4\\uff1a\\u5f53\\u592912:00---21:00\\uff09\\r\\n<\\/p>","index_youshi":"<div class=\\"content\\">\\r\\n\\t<table style=\\"width:1006px;\\" class=\\"ke-zeroborder\\" border=\\"0\\" cellspacing=\\"0\\" bordercolor=\\"#000000\\" cellpadding=\\"0\\">\\r\\n\\t\\t<tbody>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td width=\\"178\\">\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p class=\\"ysw01\\">\\r\\n\\t\\t\\t\\t\\t\\t<img alt=\\"\\" src=\\"\\/Public\\/kindeditor\\/attached\\/image\\/20141230\\/20141230204629_42464.gif\\" \\/> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td width=\\"302\\">\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:24px;\\">\\u514d\\u8d39\\u8bd5\\u7528<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p class=\\"ysw02\\">\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:14px;\\">\\u514d\\u8d39\\u6ce8\\u518c\\u540e\\u5373\\u53ef\\u83b7\\u5f972\\u5143\\u4f53\\u9a8c\\u91d1\\u3002<\\/span> <br \\/>\\r\\n<span style=\\"font-size:14px;\\">\\u5148\\u8bd5\\u7528\\uff0c\\u8bd5\\u7528\\u6ee1\\u610f\\u540e\\u518d\\u8d2d\\u4e70\\u3002<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td width=\\"46\\">\\r\\n\\t\\t\\t\\t\\t&nbsp;<br \\/>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td width=\\"178\\">\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p class=\\"ysw01\\">\\r\\n\\t\\t\\t\\t\\t\\t<img alt=\\"\\" src=\\"\\/Public\\/kindeditor\\/attached\\/image\\/20141230\\/20141230204643_55672.gif\\" \\/> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td width=\\"302\\">\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:24px;\\">\\u4f4e\\u4ef7\\u5b9e\\u60e0<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p class=\\"ysw02\\">\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:14px;\\">\\u9876\\u7ea7\\u6e20\\u9053\\u5546\\uff0c\\u5feb\\u9012\\u4ef7\\u683c\\u6700\\u4f4e\\u52301.2\\u5143\\u4e00\\u5355\\uff01<\\/span> <br \\/>\\r\\n<span style=\\"font-size:14px;\\">\\u4ef7\\u683c\\u4f53\\u73b0\\u5b9e\\u529b\\uff01\\u884c\\u4e1a\\u7b2c\\u4e00\\uff0c\\u6b22\\u8fce\\u5927\\u5bb6\\u6bd4\\u4ef7\\uff01<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<img alt=\\"\\" src=\\"\\/Public\\/kindeditor\\/attached\\/image\\/20141230\\/20141230204715_98972.gif\\" \\/> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:24px;\\">\\u81ea<\\/span><span style=\\"font-size:24px;\\">\\u52a9<\\/span><span style=\\"font-size:24px;\\">\\u4e0b\\u5355<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:14px;\\">24\\u5c0f\\u65f6\\u81ea\\u52a9\\u53d1\\u8d27\\uff0c\\u81ea\\u52a9\\u4e0b\\u5355\\uff0c\\u8282\\u5047\\u65e5\\u65e0\\u4f11\\u3002<\\/span><br \\/>\\r\\n<span style=\\"font-size:14px;\\">\\u652f\\u6301\\u6279\\u91cf\\u53d1\\u8d27\\uff01<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<img alt=\\"\\" src=\\"\\/Public\\/kindeditor\\/attached\\/image\\/20141230\\/20141230204725_30376.gif\\" \\/> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:24px;\\">\\u4e00\\u5355\\u4e00\\u7528<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:14px;\\">\\u6240\\u6709\\u5355\\u53f7\\u552e\\u51fa\\u540e\\u5373\\u4e0b\\u67b6\\uff0c\\u771f\\u6b63\\u505a\\u5230\\u4e00\\u5355\\u4e00\\u7528\\uff0c\\u7edd\\u4e0d\\u91cd\\u590d\\uff0c\\u66f4\\u5b89\\u5168\\u53ef\\u9760\\uff01<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<img alt=\\"\\" src=\\"\\/Public\\/kindeditor\\/attached\\/image\\/20141230\\/20141230204735_10769.gif\\" \\/> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:24px;\\">\\u65e0<\\/span><span style=\\"font-size:24px;\\">\\u9700\\u6539\\u5740<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:14px;\\">\\u5168\\u56fd\\u53d1\\u5168\\u56fd\\uff0c\\u4efb\\u610f\\u6307\\u5b9a\\u53d1\\u8d27\\u533a\\u57df\\uff0c\\u6536\\u8d27\\u533a\\u57df\\u65e0\\u9650\\u5236\\uff01<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<img alt=\\"\\" src=\\"\\/Public\\/kindeditor\\/attached\\/image\\/20141230\\/20141230204743_93982.gif\\" \\/> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<br \\/>\\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t\\t<td>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:24px;\\">\\u771f\\u5b9e\\u7269\\u6d41<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t\\t<p>\\r\\n\\t\\t\\t\\t\\t\\t<span style=\\"font-size:14px;\\">\\u652f\\u6301\\u6dd8\\u5b9d\\u663e\\u793a\\u7269\\u6d41\\u8ddf\\u8e2a\\u4fe1\\u606f\\uff0c\\u591a\\u5bb6\\u6dd8\\u5b9d\\u5408\\u4f5c\\u7269\\u6d41\\u4efb\\u9009\\uff0c\\u53d1\\u8d27\\u4e0d\\u7528\\u6101\\uff01<\\/span> \\r\\n\\t\\t\\t\\t\\t<\\/p>\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t<\\/tbody>\\r\\n\\t<\\/table>\\r\\n<\\/div>","ddsq_tixing":"<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\">\\u4e00\\uff1a\\u5e95\\u5355\\u7533\\u8bf7\\u8bf7\\u4e00\\u5b9a\\u5728\\u4e0b\\u534816\\u70b9\\u524d\\u63d0\\u4ea4\\uff0c16\\u70b9\\u540e\\u63d0\\u4ea4\\u65e0\\u6548\\uff0c\\u63d0\\u4ea4\\u8ba2\\u5355\\u540e\\u8bf7\\u4e00\\u5b9a\\u5f53\\u5929\\u665a\\u4e0a\\u53bb\\u6dd8\\u5b9d\\u7533\\u8bc9\\uff0c\\u4e0d\\u7136\\u7b2c\\u4e8c\\u5929\\u8ba2\\u5355\\u53d8\\u52a8\\uff0c\\u6211\\u4eec\\u5c06\\u4e0d\\u63d0\\u4f9b\\u5e95\\u5355\\u3002<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\"><br \\/>\\r\\n<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\">\\u4e8c\\uff1a\\u5fc5\\u987b\\u4e3a\\u964d\\u6743\\u5b9d\\u8d1d\\u624d\\u53ef\\u4ee5\\u7533\\u8bf7\\u5e95\\u5355\\uff0c\\u586b\\u5199\\u7533\\u8bf7\\u8868\\u7684\\u65f6\\u5019\\u5fc5\\u987b\\u628a\\u5b9d\\u8d1d\\u94fe\\u63a5\\u586b\\u4e0a\\uff0c\\u6ca1\\u6709\\u964d\\u6743\\u5b9d\\u8d1d\\u5c06\\u4e0d\\u4f1a\\u51fa\\u5e95\\u5355\\u3002<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\"><br \\/>\\r\\n<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\">\\u4e09\\uff1a\\u63d0\\u4ea4\\u5165\\u53e3\\uff0c\\u4e00\\u5b9a\\u8981\\u628a\\u4f60\\u7684QQ\\u53f7\\u7801\\u586b\\u4e0a\\uff0c\\u5426\\u5219\\u65e0\\u6cd5\\u51fa\\u5e95\\u5355\\u3002<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\"><br \\/>\\r\\n<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"left15\\" style=\\"font-size:13px;font-family:Simsun;\\">\\r\\n\\t<span class=\\"ft12 redb\\" style=\\"font-size:12px;color:#CD131A;font-weight:bold;\\">\\u56db\\uff1a\\u63d0\\u4ea4\\u540e\\u8bf7\\u4e0d\\u8981\\u50ac\\u5ba2\\u670d\\uff0c\\u559c\\u6b22\\u50ac\\u5ba2\\u670d\\u7684\\u4e00\\u5f8b\\u5ef6\\u8fdf\\u4e00\\u5929\\u51fa\\u5e95\\u5355\\u3002<\\/span> \\r\\n<\\/p>","ddsq_defaultcontent":"<p class=\\"p0\\">\\r\\n\\tA:<span>\\u964d\\u6743\\u5b9d\\u8d1d\\u94fe\\u63a5<\\/span> \\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\tB:\\u964d\\u6743\\u5b9d\\u8d1d\\u540e\\u53f0\\u622a\\u56fe(\\u663e\\u793a\\u6709\\u865a\\u5047\\u4ea4\\u6613\\u7684\\u5730\\u65b9)\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\tC:\\u8ba2\\u5355\\u8d44\\u6599\\u586b\\u5199\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\t-----------------------------------------------------------------------------\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\t\\u5feb\\u9012\\u540d\\u79f0\\uff1a\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\t\\u5feb\\u9012\\u5355\\u53f7\\uff1a\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\t\\u53d1\\u8d27\\u65e5\\u671f\\uff1a\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\t\\u53d1\\u8d27\\u5730\\u5740\\u548c\\u59d3\\u540d\\u7535\\u8bdd\\uff1a\\r\\n<\\/p>\\r\\n<p class=\\"p0\\">\\r\\n\\t\\u6536\\u8d27\\u5730\\u5740\\u548c\\u59d3\\u540d\\u7535\\u8bdd\\uff1a\\r\\n<\\/p>","recharge_tixing":"","recharge_shuoming":"","tuiguang_fangfa":""}', '新增全峰空包\r\n城市100空包:会员价2.5 代理价1.5\r\n龙邦空包:会员价3.2 代理价2.5\r\n全峰空包:会员价3.0 代理价2.2（比优速还更好！）\r\n都是全国发全国,降权给底单!\r\n空包订单由平台统一在21:00左右处理.', 'kbk', 1, '', '', NULL, 1, '', NULL, '', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `danhao`
--

CREATE TABLE IF NOT EXISTS `danhao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_no` varchar(50) NOT NULL COMMENT '单据号',
  `type_id` int(5) NOT NULL COMMENT '所属类型',
  `type` tinyint(3) DEFAULT '0' COMMENT '插入方式0-单条添加1-批量导入',
  `isused` tinyint(3) DEFAULT '0' COMMENT '是否使用',
  `order_no` varchar(50) DEFAULT NULL COMMENT '使用过的数据对应的订单编号',
  `order_type` tinyint(3) DEFAULT '0' COMMENT '订单类型0-普通用户订单1-批发会员订单',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `weiyi_dh` (`note_no`,`type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='单号列表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `danhao_order`
--

CREATE TABLE IF NOT EXISTS `danhao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type_id` tinyint(5) NOT NULL COMMENT '单号类型',
  `order_no` varchar(20) NOT NULL COMMENT '订单编号',
  `order_time` datetime DEFAULT NULL COMMENT '下单时间',
  `order_status` tinyint(3) DEFAULT '0' COMMENT '1-订单已完成0-未完成',
  `note_no` longtext COMMENT '单号列表以,号分开',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='单号订单' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `danhao_type`
--

CREATE TABLE IF NOT EXISTS `danhao_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `name` varchar(50) NOT NULL COMMENT '类型名称',
  `comm` varchar(255) DEFAULT NULL COMMENT '描述',
  `state` tinyint(3) DEFAULT '0' COMMENT '0-正常1-暂停',
  `config` varchar(255) NOT NULL COMMENT '价格设置-以及佣金提成设置',
  `sort_order` int(5) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='单号类型-单号快递类型' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `daohang`
--

CREATE TABLE IF NOT EXISTS `daohang` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET gbk COLLATE gbk_bin DEFAULT NULL COMMENT '导航名称',
  `link` varchar(100) CHARACTER SET gbk COLLATE gbk_bin DEFAULT NULL,
  `paixu` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='首页导航' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `didan_uploadfiles`
--

CREATE TABLE IF NOT EXISTS `didan_uploadfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `username` tinyint(5) NOT NULL COMMENT '空包类型',
  `input_content` text NOT NULL COMMENT '文件名',
  `addtime` datetime DEFAULT NULL COMMENT '上传时间',
  `outer_content` text COMMENT '底单文件地址',
  `status` tinyint(3) DEFAULT '1' COMMENT '1-上传2-已处理3-失败',
  `comm` varchar(255) DEFAULT NULL COMMENT '失败原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `exp_log`
--

CREATE TABLE IF NOT EXISTS `exp_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL COMMENT '空包类型ID',
  `type_name` varchar(50) NOT NULL COMMENT '空包类型名称',
  `exp_counts` int(11) DEFAULT '0' COMMENT '导出个数',
  `exp_filename` varchar(100) DEFAULT NULL COMMENT '导出文件名',
  `exp_fileurl` varchar(100) DEFAULT NULL COMMENT '导出文件地址',
  `last_time` datetime DEFAULT NULL COMMENT '上次导出时间',
  `exp_time` datetime DEFAULT NULL COMMENT '本次导出时间',
  `exp_date` varchar(8) DEFAULT NULL COMMENT '导出日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='订单导出日志' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `exp_rule`
--

CREATE TABLE IF NOT EXISTS `exp_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '格式名称',
  `comm` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `exp_rule` text COMMENT '导出格式规则',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='订单导出格式规则' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `gonggao`
--

CREATE TABLE IF NOT EXISTS `gonggao` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `time` datetime DEFAULT NULL,
  `keyword` varchar(100) DEFAULT NULL COMMENT '公告关键词',
  `desc` varchar(100) DEFAULT NULL COMMENT '公告简述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='公告信息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `help`
--

CREATE TABLE IF NOT EXISTS `help` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `time` datetime DEFAULT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `desc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='帮助中心' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `imp_rule_xh`
--

CREATE TABLE IF NOT EXISTS `imp_rule_xh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '格式名称',
  `comm` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `separator` varchar(10) DEFAULT NULL COMMENT '分隔符',
  `imp_rule` text COMMENT '导入格式规则',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='订单导出格式规则' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `kefu`
--

CREATE TABLE IF NOT EXISTS `kefu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kf_type` tinyint(5) NOT NULL COMMENT '客服类型',
  `kf_name` varchar(50) NOT NULL COMMENT '客服名称',
  `kf_qq` varchar(50) NOT NULL COMMENT '客服qq',
  `sort_order` tinyint(5) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='客服信息表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `kefu_type`
--

CREATE TABLE IF NOT EXISTS `kefu_type` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT COMMENT '客服类型id',
  `title` varchar(50) NOT NULL COMMENT '客服组名称',
  `sort_order` tinyint(5) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=gbk AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `kefu_type`
--

INSERT INTO `kefu_type` (`id`, `title`, `sort_order`) VALUES
(1, '销售组', 0),
(2, '业务咨询', 1);

-- --------------------------------------------------------

--
-- 表的结构 `kongbao`
--

CREATE TABLE IF NOT EXISTS `kongbao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_no` varchar(50) NOT NULL COMMENT '单据号',
  `type_id` int(5) NOT NULL COMMENT '所属类型',
  `type` tinyint(3) DEFAULT '0' COMMENT '插入方式0-单条添加1-批量导入',
  `isused` tinyint(3) DEFAULT '0' COMMENT '是否使用0-未使用1-已使用2-已作废',
  `order_no` varchar(50) DEFAULT NULL COMMENT '使用过的数据对应的订单编号',
  `order_type` tinyint(3) DEFAULT '0' COMMENT '订单类型0-普通用户订单1-批发会员订单',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `weiyi` (`note_no`,`type_id`) USING BTREE,
  KEY `indx_isused` (`isused`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='空包单据号列表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `kongbao_daili_order`
--

CREATE TABLE IF NOT EXISTS `kongbao_daili_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type_id` tinyint(5) NOT NULL COMMENT '空包类型',
  `order_no` varchar(20) NOT NULL COMMENT '订单编号',
  `order_time` datetime DEFAULT NULL COMMENT '下单时间',
  `order_status` tinyint(3) DEFAULT '0' COMMENT '1-订单已完成0-未完成2-订单已取消',
  `note_no` longtext COMMENT '空包单号列表以,号分开',
  `order_money` decimal(12,2) DEFAULT '0.00' COMMENT '订单金额',
  `order_nums` int(11) DEFAULT '0' COMMENT '空包个数',
  `in_price` decimal(12,2) DEFAULT '0.00' COMMENT '进价',
  `user_type` int(5) DEFAULT '1' COMMENT '会员类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='空包代理批发订单' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `kongbao_didan`
--

CREATE TABLE IF NOT EXISTS `kongbao_didan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type_id` tinyint(5) NOT NULL COMMENT '空包类型',
  `type_name` varchar(100) DEFAULT NULL COMMENT '类型名称',
  `note_no` varchar(50) NOT NULL COMMENT '空包单号',
  `dd_price` decimal(12,2) DEFAULT NULL COMMENT '底单价格',
  `jietu` varchar(100) DEFAULT NULL COMMENT '降权截图',
  `send_addr` varchar(255) NOT NULL COMMENT '发货地址',
  `send_name` varchar(50) DEFAULT NULL COMMENT '发货人',
  `send_phone` varchar(50) DEFAULT NULL COMMENT '发件人电话',
  `send_shouji` varchar(50) DEFAULT NULL COMMENT '发货人手机',
  `send_zipcode` varchar(20) DEFAULT NULL COMMENT '发货人邮编',
  `rec_addr` varchar(255) DEFAULT NULL COMMENT '收件人地址',
  `rec_name` varchar(50) DEFAULT NULL COMMENT '收件人姓名',
  `rec_phone` varchar(50) DEFAULT NULL COMMENT '收件人电话',
  `rec_shouji` varchar(50) DEFAULT NULL COMMENT '收货人手机',
  `rec_zipcode` varchar(20) DEFAULT NULL COMMENT '收货人邮编',
  `weight` varchar(20) DEFAULT NULL COMMENT '重量',
  `yunfei` decimal(12,2) DEFAULT '0.00' COMMENT '运费',
  `order_time` varchar(20) DEFAULT NULL COMMENT '时间',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `addtime` datetime DEFAULT NULL COMMENT '申请提交时间',
  `status` tinyint(3) DEFAULT '1' COMMENT '1-申请中2-已完成 ',
  `didan_image` varchar(100) DEFAULT NULL COMMENT '底单图片',
  `order_no` varchar(50) DEFAULT NULL COMMENT '对应订单编号(如果订单收费的话)',
  `comm` text COMMENT '失败原因',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_note_no` (`type_id`,`note_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='空包底单申请' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `kongbao_order`
--

CREATE TABLE IF NOT EXISTS `kongbao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type_id` tinyint(5) DEFAULT NULL COMMENT '空包类型',
  `note_no` varchar(50) NOT NULL COMMENT '单据号',
  `order_no` varchar(20) NOT NULL COMMENT '订单编号',
  `order_time` datetime DEFAULT NULL COMMENT '下单时间',
  `order_status` tinyint(3) DEFAULT '0' COMMENT '1-订单已完成0-未完成2-订单已取消',
  `send_province` varchar(50) DEFAULT NULL COMMENT '发货人所在省份',
  `send_city` varchar(50) DEFAULT NULL COMMENT '发货人所在市',
  `send_district` varchar(50) DEFAULT NULL COMMENT '发货人所在区',
  `send_address` varchar(100) DEFAULT NULL COMMENT '发货人具体地址',
  `send_name` varchar(100) DEFAULT NULL COMMENT '发货人姓名',
  `send_shouji` varchar(20) DEFAULT NULL,
  `send_phone` varchar(50) DEFAULT NULL COMMENT '发货人联系方式',
  `send_zipcode` varchar(20) DEFAULT NULL,
  `rec_address` varchar(100) DEFAULT NULL COMMENT '收货人地址',
  `rec_name` varchar(100) DEFAULT NULL COMMENT '收货人姓名',
  `rec_shouji` varchar(20) DEFAULT NULL,
  `rec_phone` varchar(50) DEFAULT NULL COMMENT '收货人联系方式',
  `rec_zipcode` varchar(50) DEFAULT NULL,
  `rec_province` varchar(50) DEFAULT NULL COMMENT '收货省份',
  `rec_city` varchar(50) DEFAULT NULL COMMENT '收货市',
  `rec_district` varchar(50) DEFAULT NULL COMMENT '收货区',
  `user_name` varchar(50) DEFAULT NULL COMMENT '用户名',
  `user_qq` varchar(50) DEFAULT NULL COMMENT '用户QQ',
  `exp_status` tinyint(3) DEFAULT '0' COMMENT '导出标志0-未导出1-已导出',
  `user_type` int(5) DEFAULT '1' COMMENT '会员类型',
  `order_money` decimal(12,2) DEFAULT '0.00' COMMENT '订单金额',
  `in_price` decimal(12,2) DEFAULT '0.00' COMMENT '进价',
  PRIMARY KEY (`id`),
  KEY `indx_kbtype` (`type_id`),
  KEY `indx_kbordertime` (`order_time`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='空包订单' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `kongbao_type`
--

CREATE TABLE IF NOT EXISTS `kongbao_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `name` varchar(50) NOT NULL COMMENT '类型名称',
  `comm` varchar(255) DEFAULT NULL COMMENT '描述',
  `state` tinyint(3) DEFAULT '0' COMMENT '0-正常1-暂停',
  `config` text NOT NULL COMMENT '价格设置-以及佣金提成设置',
  `exp_id` varchar(20) DEFAULT NULL COMMENT '导出模板ID',
  `exp_config` text COMMENT '导出字段及顺序设置',
  `file_url` varchar(100) DEFAULT NULL COMMENT '代理模板文件',
  `image_url` varchar(100) DEFAULT NULL COMMENT '代理模板图片实例',
  `sort_order` int(5) DEFAULT '0' COMMENT '排序',
  `is_true` tinyint(3) DEFAULT '0' COMMENT '是否真实空包0-否1-是',
  `last_down_time` int(11) DEFAULT '0' COMMENT '上次导出单号时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='空包类型-空包快递类型' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `siteurl` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `sitebrief` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `addtime` datetime NOT NULL,
  `is_sys` tinyint(3) DEFAULT '0' COMMENT '1系统内置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='友情链接' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_order`
--

CREATE TABLE IF NOT EXISTS `pay_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `user_type` int(11) DEFAULT '1' COMMENT '用户等级',
  `order_no` varchar(30) DEFAULT NULL COMMENT '订单编号',
  `outer_order_no` varchar(50) DEFAULT NULL COMMENT '外部订单编号-支付宝等交易号',
  `pay_money` decimal(12,2) DEFAULT '0.00' COMMENT '支付金额',
  `type` tinyint(3) DEFAULT '0' COMMENT '订单类型0-购买1-充值',
  `comm` varchar(255) DEFAULT NULL COMMENT '订单详情',
  `status` tinyint(3) DEFAULT '0' COMMENT '订单状态0-未完成1-已完成2-待审核3-订单已取消',
  `addtime` datetime DEFAULT NULL COMMENT '订单生成日期',
  `pay_title` varchar(100) DEFAULT NULL COMMENT '支付宝页面付款说明项',
  `order_type` tinyint(3) DEFAULT '0' COMMENT '充值订单类型0-支付宝1-财付通',
  PRIMARY KEY (`id`),
  KEY `indx_paytype` (`type`),
  KEY `indx_orderno` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='订单信息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `suggest`
--

CREATE TABLE IF NOT EXISTS `suggest` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) DEFAULT NULL,
  `user_name` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `content` varchar(800) CHARACTER SET utf8 DEFAULT NULL,
  `huifu` varchar(800) CHARACTER SET utf8 DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='留言信息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tixian`
--

CREATE TABLE IF NOT EXISTS `tixian` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `money` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `cwsz_config` text COMMENT '财务设置配置信息',
  `addtime` datetime DEFAULT NULL COMMENT '提现申请时间',
  `ip` varchar(40) DEFAULT NULL COMMENT '申请IP',
  `status` tinyint(3) DEFAULT '1' COMMENT '提现状态1-申请2-成功3-失败',
  `comm` varchar(255) DEFAULT NULL COMMENT '备注',
  `error_msg` varchar(255) DEFAULT NULL COMMENT '失败原因',
  `backtime` datetime DEFAULT NULL COMMENT '回复时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='提现信息表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `uploadfiles`
--

CREATE TABLE IF NOT EXISTS `uploadfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type_id` tinyint(5) NOT NULL COMMENT '空包类型',
  `filename` varchar(100) NOT NULL COMMENT '文件名',
  `fileurl` varchar(100) DEFAULT NULL COMMENT '文件地址',
  `addtime` datetime DEFAULT NULL COMMENT '上传时间',
  `downcounts` int(11) DEFAULT '0' COMMENT '下载次数',
  `status` tinyint(3) DEFAULT '0' COMMENT '0-上传1-已处理2-失败',
  `comm` varchar(255) DEFAULT NULL COMMENT '失败原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `nickname` varchar(50) DEFAULT NULL COMMENT '用户昵称',
  `password` varchar(100) NOT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱地址',
  `telphone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `user_qq` varchar(20) DEFAULT NULL COMMENT '用户QQ号',
  `user_type` tinyint(3) DEFAULT '1' COMMENT '用户级别1-默认普通会员',
  `isvalid` tinyint(3) DEFAULT '1' COMMENT '是否有效0-无效1-有效',
  `money` decimal(12,2) DEFAULT '0.00' COMMENT '用户当前可用金额',
  `invalid_money` decimal(12,4) DEFAULT '0.0000' COMMENT '用户冻结金额',
  `used_money` decimal(12,2) DEFAULT '0.00' COMMENT '用户累计消费金额',
  `refer_money` decimal(12,4) DEFAULT '0.0000' COMMENT '佣金金额',
  `cwsz_config` varchar(255) DEFAULT NULL COMMENT '会员财务设置信息',
  `refer_id` int(11) DEFAULT '0' COMMENT '推荐人ID',
  `create_time` datetime DEFAULT NULL COMMENT '注册日期',
  `last_login_ip` varchar(100) DEFAULT NULL COMMENT '上次登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '上次登录时间',
  `login_counts` int(11) DEFAULT '0' COMMENT '登录次数',
  `create_ip` varchar(50) DEFAULT NULL COMMENT '注册IP',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=gbk COMMENT='用户信息表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `nickname`, `password`, `email`, `telphone`, `user_qq`, `user_type`, `isvalid`, `money`, `invalid_money`, `used_money`, `refer_money`, `cwsz_config`, `refer_id`, `create_time`, `last_login_ip`, `last_login_time`, `login_counts`, `create_ip`) VALUES
(1, '123', NULL, 'e10adc3949ba59abbe56e057f20f883e', '123456@qq.com', NULL, '123456', 1, 1, '0.00', '0.0000', '0.00', '0.0000', NULL, 0, '2015-07-23 21:00:10', '127.0.0.1', '2015-07-23 21:21:10', 2, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `user_address`
--

CREATE TABLE IF NOT EXISTS `user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT '电话号码',
  `shouji` varchar(20) NOT NULL COMMENT '手机号码',
  `address_province` varchar(50) NOT NULL COMMENT '省份',
  `address_city` varchar(50) NOT NULL COMMENT '城市',
  `address_district` varchar(50) NOT NULL COMMENT '地区',
  `address` varchar(100) DEFAULT NULL COMMENT '具体地址',
  `zipcode` varchar(10) DEFAULT NULL COMMENT '邮编',
  `is_default` tinyint(3) DEFAULT '0' COMMENT '是否默认0-否1-是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='用户发货地址' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_del`
--

CREATE TABLE IF NOT EXISTS `user_del` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `nickname` varchar(50) DEFAULT NULL COMMENT '用户昵称',
  `password` varchar(100) NOT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱地址',
  `telphone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `user_qq` varchar(20) DEFAULT NULL COMMENT '用户QQ号',
  `user_type` tinyint(3) DEFAULT '1' COMMENT '用户级别1-默认普通会员',
  `isvalid` tinyint(3) DEFAULT '1' COMMENT '是否有效0-无效1-有效',
  `money` decimal(12,2) DEFAULT '0.00' COMMENT '用户当前可用金额',
  `invalid_money` decimal(12,4) DEFAULT '0.0000' COMMENT '用户冻结金额',
  `used_money` decimal(12,2) DEFAULT '0.00' COMMENT '用户累计消费金额',
  `refer_money` decimal(12,4) DEFAULT '0.0000' COMMENT '佣金金额',
  `cwsz_config` varchar(255) DEFAULT NULL COMMENT '会员财务设置信息',
  `refer_id` int(11) DEFAULT '0' COMMENT '推荐人ID',
  `create_time` datetime DEFAULT NULL COMMENT '注册日期',
  `last_login_ip` varchar(100) DEFAULT NULL COMMENT '上次登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '上次登录时间',
  `login_counts` int(11) DEFAULT '0' COMMENT '登录次数'
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='删除用户信息备份表';

-- --------------------------------------------------------

--
-- 表的结构 `user_level`
--

CREATE TABLE IF NOT EXISTS `user_level` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '用户级别',
  `title` varchar(255) NOT NULL COMMENT '用户级别名称',
  `comm` varchar(255) DEFAULT NULL COMMENT '级别描述',
  `is_sys` tinyint(3) DEFAULT '0' COMMENT '是否为系统级别0-否1-是（不可删除）',
  `config` varchar(255) DEFAULT NULL COMMENT '级别设置',
  `sort_order` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=gbk COMMENT='会员级别信息' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `user_level`
--

INSERT INTO `user_level` (`id`, `title`, `comm`, `is_sys`, `config`, `sort_order`) VALUES
(1, '普通会员', '普通会员', 1, '{"refer":"1","money":"0","refer_buy":"1","refer_daili":"1","refer_daili_money":{"a1":"50","a2":"40","a3":"30"},"pifa":"0","tixian":"1"}', 1);

-- --------------------------------------------------------

--
-- 表的结构 `user_log`
--

CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `opt_type` varchar(20) DEFAULT NULL COMMENT '操作类型',
  `comm` varchar(255) DEFAULT NULL COMMENT '备注',
  `opt_ip` varchar(255) DEFAULT NULL COMMENT '操作IP',
  `addtime` datetime DEFAULT NULL COMMENT '操作日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=gbk COMMENT='用户操作日志表' AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `user_log`
--

INSERT INTO `user_log` (`id`, `user_id`, `opt_type`, `comm`, `opt_ip`, `addtime`) VALUES
(11, 1, '用户登陆', NULL, '127.0.0.1', '2015-07-23 21:21:11');

-- --------------------------------------------------------

--
-- 表的结构 `xiaohao`
--

CREATE TABLE IF NOT EXISTS `xiaohao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_no` varchar(300) NOT NULL COMMENT '单据号',
  `type_id` int(5) NOT NULL COMMENT '所属类型',
  `type` tinyint(3) DEFAULT '0' COMMENT '插入方式0-单条添加1-批量导入',
  `isused` tinyint(3) DEFAULT '0' COMMENT '是否使用',
  `order_no` varchar(50) DEFAULT NULL COMMENT '使用过的数据对应的订单编号',
  `order_type` tinyint(3) DEFAULT '0' COMMENT '订单类型0-普通用户订单1-批发会员订单',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `account` varchar(100) DEFAULT NULL COMMENT '账号',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `email` varchar(200) DEFAULT NULL COMMENT '邮箱',
  `email_pass` varchar(100) DEFAULT NULL COMMENT '邮箱密码',
  `pay_pass` varchar(100) DEFAULT NULL COMMENT '支付密码',
  `shenfenzheng` varchar(100) DEFAULT NULL COMMENT '身份证',
  `truename` varchar(100) DEFAULT NULL COMMENT '姓名',
  `bank_account` varchar(100) DEFAULT '' COMMENT '银行卡号',
  `bank_yue` varchar(50) DEFAULT NULL COMMENT '卡余额',
  `pay_account` varchar(100) DEFAULT NULL COMMENT '支付账号',
  `mobile_phone` varchar(100) DEFAULT NULL COMMENT '手机号',
  `encry_key` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `weiyi_xh` (`note_no`,`type_id`) USING BTREE,
  KEY `indx_isused_xh` (`isused`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='小号列表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `xiaohao_order`
--

CREATE TABLE IF NOT EXISTS `xiaohao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type_id` tinyint(5) NOT NULL COMMENT '单号类型',
  `order_no` varchar(30) NOT NULL COMMENT '订单编号',
  `order_time` datetime DEFAULT NULL COMMENT '下单时间',
  `order_status` tinyint(3) DEFAULT '0' COMMENT '1-订单已完成0-未完成',
  `note_no` longtext COMMENT '单号列表以,号分开',
  `user_type` int(5) DEFAULT '1' COMMENT '会员类型',
  `order_money` decimal(12,2) DEFAULT '0.00' COMMENT '订单金额',
  `in_price` decimal(12,2) DEFAULT '0.00' COMMENT '进价',
  PRIMARY KEY (`id`),
  KEY `indx_xhtype` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='小号订单' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `xiaohao_type`
--

CREATE TABLE IF NOT EXISTS `xiaohao_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `name` varchar(50) NOT NULL COMMENT '类型名称',
  `comm` varchar(255) DEFAULT NULL COMMENT '描述',
  `state` tinyint(3) DEFAULT '0' COMMENT '0-正常1-暂停',
  `imp_id` varchar(20) DEFAULT NULL COMMENT '导入模板ID',
  `config` varchar(255) NOT NULL COMMENT '价格设置-以及佣金提成设置',
  `sort_order` int(5) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='小号类型-小号类型' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
