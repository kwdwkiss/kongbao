<?php
 /**
  * 公用方法
  * 李强 20140315
  */
  /**
   * 佣金计算-传入参数 
   * 用户ID ,
   * 佣金类型1-购买佣金2-代理提成,
   * 业务类型1-空包业务2-单号业务3-小号业务
   * 空包类型-指快递类型
   * 返回数据：(数组)
   * level-下线级别
   * userid-用户id username-用户名
   * refer_money-推广佣金
   */
  function calc_commission($userid,$type='1',$yw_type='1',$type_id=0)
  {
  	  $refer_array = array(); 
  	  //获取用户级别信息
  	  $user_level = M('user_level')->select();
	  $level_list = array();
	  foreach($user_level as $level)
	  {
	  	   $level_list[$level['id']] = json_decode($level['config'],true);
	  }
  	  //获取系统配置信息
  	  $config = M('config')->where('id=1')->find();
	  
	  //获取用户信息
  	  $user = M('user')->where('id='.$userid)->find();
	  
	  $user_type_id = "a".$user['user_type']; //此人的用户级别
	  
	  if($type==1) //购买佣金
	  {
	  	   $refer_mode = $config['refer_mode'];
		   if($refer_mode==0) //无佣金模式
		   {
		   	   return $refer_array;
		   }
		   else //如果是有佣金模式,根据佣金模式获取此人的上线人员
		   {
		   	    $temp_array = array();
			    $refer_users = get_refer_id($temp_array, $userid, $refer_mode);
                if(empty($refer_users['a1'])) //如果上级即为空那么直接退出
				{
					 return $refer_array;
				}
				else 
				{
						   if($yw_type==1) //空包业务
						   {
						   	  //获取空包类型信息
						   	  $type_detail = M('kongbao_type')->where('id='.$type_id)->find();
							  //获取系统空包设置
							   $system_config = json_decode($config['kongbao_config'],true);
						   }
						   else if($yw_type==2)//单号业务
						   {
						   	   //获取单号类型信息
						   	   $type_detail = M('danhao_type')->where('id='.$type_id)->find();
							   //获取系统单号设置
							   $system_config = json_decode($config['danhao_config'],true);
						   }
						   else if($yw_type==3)//小号业务
						   {
						   	   //获取小号类型信息
						   	   $type_detail = M('xiaohao_type')->where('id='.$type_id)->find();
							   //获取系统小号设置
							   $system_config = json_decode($config['xiaohao_config'],true);						   	
						   }
						   $type_config = json_decode($type_detail['config'],true);	//获取业务类型中的设置信息
						    //获取各级会员对应的价格
						    $level_price = $type_config['price'];//各级别会员对应的价格
						    //获取该会员所对应的购买单价
							$buy_price = $level_price[$user_type_id];
		 					//如果使用默认的佣金设置比例则各类型单独设置不起作用
						   if($system_config['refer_default']==1) 
							 {
							  		$yw_config = $system_config['buy_refer']; 
						    }   
						   else 
						   {
								//获取业务类型所对应的配置信息
								if($type_id==0) //如果未设置业务类型ID则直接退出
								 {
									return $refer_array;
								 }			  
								 $yw_config = $type_config['refer'];						  
						   } 	 				
						/**
						 * $k 是级别代码
						 */
						foreach($refer_users as $k=>$v) //逐一对上线进行
						{
							 if(empty($v))  {continue;}
							 $user_type = $v['user_type']; //会员的级别
							 $refer_buy = $level_list[$user_type]['refer_buy'];//此级别会员是否享有购买佣金
							 if($refer_buy==0)  {continue;} //如果该级别会员不享受购买佣金则跳过
						     //获取该级别会员购买此产品的价格
						     $temp_type_key = 'a'.$v['user_type'];
							 $single_price = $level_price[$temp_type_key];
							 //获取这个上线的佣金:(会员的购买价格-该上线对应的购买价格)*该层级会员对应的佣金比例
							 $refer_money = ( $buy_price - $single_price )* $yw_config[$k]; 
							 if($refer_money<=0) $refer_money=0; 
							 $refer_money = round($refer_money/100,4);
							 $refer_array[] = array('level'=>substr($k,1),
							                                   'userid'=>$v['id'],
															   'username'=>$v['username'],
															   'user_type'=>$v['user_type'],
															   'refer_money'=>$refer_money);
											
						} 
						return $refer_array;
				}
		   }
	  }
	 else if($type==2)//代理佣金
	 {
	 	   //获取升级佣金模式   
	  	   $daili_refer_mode = $config['daili_refer_mode'];
		   if($daili_refer_mode==0) //无佣金模式
		   {
		   	   return $refer_array;
		   }
		   else  //如果有佣金模式获取模式信息
		   {
		   	    $temp_array = array();
			    $refer_users = get_refer_id($temp_array, $userid, $daili_refer_mode);
                if(empty($refer_users['a1'])) //如果上级即为空那么直接退出
				{
					 return $refer_array;
				}
				else 
				{
			       /**
					 * $k 是层级别代码
					 */
					foreach($refer_users as $k=>$v) //逐一对上线进行
					{
						 if(empty($v)) continue;
						 $user_type = $v['user_type']; //会员的级别
						 $refer_daili= $level_list[$user_type]['refer_daili'];//此级别会员是否享有升级佣金 
						 if($refer_daili==0) continue;
						 //根据上线人员的等级获取相应的层级比例
						$yongjinbi =  $level_list[$user_type]['refer_daili_money'][$k]; 
						if(empty($yongjinbi)) $yongjinbi=0;
						//获取要升级的级别所用的金额
						$up_money = $level_list[$user['user_type']]['money'];
						if(empty($up_money)) $up_money=0;
						$refer_money = round( $up_money * $yongjinbi/100 ,4);
	 					$refer_array[] = array('level'=>substr($k,1),
						                                   'userid'=>$v['id'],
														   'username'=>$v['username'],
														   'user_type'=>$v['user_type'],
														   'refer_money'=>$refer_money);
					}
				  return $refer_array; 
				}
		   }	 	 
	   } 
  }
  /**
   * 获取人员的上线会员列表
   *传入参数 ：level - 模式级别-$c_level-当前层
   * 返回为一个人员数组
   */
  function get_refer_id($array,$userid,$level,$c_level=1) //获取上级推荐人名单
  {
  	   if($c_level<=$level)
	   {
	   	     $user = M('user')->where('id='.$userid)->find();
			 $refer_id = $user['refer_id'];
			 if($refer_id>0)
			 {
			 	   $refer_user = M('user')->where('id='.$refer_id)->find(); 
				   $array['a'.$c_level] = array('id'=>$refer_id,
				                                           'username'=>$refer_user['username'],
														   'user_type'=>$refer_user['user_type']
														   );  
			 }
			 else {
			 	$array['a'.$c_level] = array();   
			 }
			 $c_level ++; 
			 return get_refer_id($array,$refer_id,$level,$c_level);
	   }
	   else 
	   {
		   	return  $array;
	   }
  }
	   /**
	    * 增加佣金处理
	    * 传入参数：users-增加佣金用户数组 count:个数,type=1购买佣金2-升级佣金
	    * yw_type:业务类型1-空包(默认)2-单号3-小号
	    * 返回参数：true or false
	    */
	   function addrefer_money($users,$type,$count=0,$yw_type=1,$order_no) //增加佣金
	   {
	   	   $user_model = new Model('user');
		   $account_model = new Model('account_log');
	   	    foreach($users as $k=>$v)
			{
				//更新用户余额信息
				$userid = $v['userid']; 
				$user = $user_model->where('id='.$userid)->find();
				if(empty($user)) continue;
				if(empty($v['refer_money']) || $v['refer_money']=='') $v['refer_money']=0;
				$refer_money = $count * $v['refer_money'];
				if($refer_money<=0) continue;
				$userdata = array();
				$userdata['id'] = $userid;
				$userdata['refer_money'] = $user['refer_money'] + $refer_money; 
				$result_1 = $user_model->data($userdata)->save(); 
							
				if(false !==$result_1)
				{
					if($type ==1)
					{ 
						$reason = $v['level']."级上线获取下线购买佣金";
						   if($yw_type==1)
						    {
						   	   $reason .= "(空包购买)";
							}
						   elseif($yw_type==2)
						   {
						   	   $reason .= "(单号购买)";
						   }
						   elseif($yw_type==3)
						   {
						   		$reason .= "(小号购买)";
						   }						
					}
					elseif($type==2)
					{
						$reason = $v['level']."级上线获取下线升级佣金";
					}
					//生成账户日志记录 
					$account_log=array(); 
					$account_log['user_id']=$userid;  
					$account_log['stage']='refer';
					$account_log['money']= $refer_money;
					$account_log['comm'] = $reason;
					$account_log['addtime']=date('Y-m-d H:i:s',time());
					$account_log['remain_money'] = $user['money'];			
					$account_log['remain_refer_money'] = $userdata['refer_money'];		
					$account_log['order_no'] = $order_no;	
					if(false !==$account_model->data($account_log)->add())
					{
						 continue;
					}
					else 
					{ 
						 return false;
					}
				}
				else {
					return false;
				} 
			}
            return true;  
	 }  
    /**
	 *  根据模板获取sql语句查询字段
	 * 李强 20140319
	 */
	 function getFieldFromFile($exp_id)
	 { 
	       $fields = ""; 
		   $moban_list = require("Public/exp_setting_config.php");  
		   $fileds_array  = $moban_list[$exp_id]['fields'];
		   if(empty($fileds_array))
		   {
		   	   //输出默认
		   		$fields = "*";   	
		   } 
		   foreach($fileds_array as $k=>$v)
		   { 
			  $fields .=$v['name'].","; 
		   }
		   return rtrim($fields,',');  
	 }
    /**
	 *  根据模板获取表格表头名称
	 * 李强 20140319
	 */
	 function getHeaderFromFile($exp_id)
	 { 
	       $header =array(); 
		   $moban_list = require("Public/exp_setting_config.php");  
		   $fileds_array  = $moban_list[$exp_id]['fields']; 
		   foreach($fileds_array as $k=>$v)
		   { 
			  $header[$k]=$v['title']; 
		   }
		   return array($header);  
	 }
	 /**
	  * 导出数据到excel表格
	  * 李强 20140322
	  */
	  function dataToExcel($header,$data,$filename)
	  {
	  	  include('Public/PHPExcel/excel_xml.class.php');
		  $xls = new Excel_XML('GB2312',true,'文件');
		  $xls->addArray($header); 
		  $xls->addArray($data); 
		  $xls->generateXML($filename);
	  }
	 function MkdirAll($truepath) 
	  {
				if (!file_exists($truepath)) {
					mkdir($truepath, 0777);
					chmod($truepath, 0777);
					return true;
				} else {
					return true;
				}
 } 
	/***
	 * 字符串截取
	 */
	function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
	{
			    if($code == 'UTF-8')
			    {
			        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			        preg_match_all($pa, $string, $t_string); 
			        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
			        return join('', array_slice($t_string[0], $start, $sublen));
			    }
			    else
			    {
			        $start = $start*2;
			        $sublen = $sublen*2;
			        $strlen = strlen($string);
			        $tmpstr = '';
			
			        for($i=0; $i< $strlen; $i++)
			        {
			            if($i>=$start && $i< ($start+$sublen))
			            {
			                if(ord(substr($string, $i, 1))>129)
			                {
			                    $tmpstr.= substr($string, $i, 2);
			                }
			                else
			                {
			                    $tmpstr.= substr($string, $i, 1);
			                }
			            }
			            if(ord(substr($string, $i, 1))>129) $i++;
			        }
			        if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
			        return $tmpstr;
			    } 
	}	   
    function send_fetion($content) //发送飞信信息
	{
		if(!class_exists('PHPFetion'))
		{
			 require_once('Public/PHPFetion.php');
		}
		//获取系统发新人地址
		$config = M('config')->where('id=1')->find();
		$fetion_setting = json_decode($config['fetion_setting'],true);
		$fetion = new PHPFetion($fetion_setting["sender"], $fetion_setting["password"]); // 手机号、飞信密码
        //获取当前域名
        $domain = "[".$_SERVER['HTTP_HOST']."]";   
 		$content = $content.$domain;
		$fetion->send($fetion_setting["receiver"],$content); // 接收人手机号、飞信内容
	}
	function refund_money($userid,$order_no,$money,$type='1') //退款给用户 type=1账户余额
	{ 
		 $user_model = new Model('user');
		 $user = $user_model->where('id='.$userid)->find();
		 $userdata['id'] = $userid;
		 $remain_money = 0;
		 $remain_refer_money = 0;
		 if($type=='1') //扣减账户余额
		 {
		 	 $userdata['money'] = $user['money']+$money;
		     $userdata['used_money'] = $user['used_money'] - $money;
			 $remain_money = $userdata['money'];
			 $remain_refer_money = $user['refer_money'];
		 }elseif($type=='2') //扣减佣金
		 {
			 $userdata['refer_money'] = $user['refer_money'] - $money; 
			 $remain_money = $user['money'];
			 $remain_refer_money = $userdata['refer_money'];			 
		 } 
		 if(false !== $user_model->data($userdata)->save())
		 { 
		 	  //生成账户日志
		 	    if($type=='1')
				{
					$reason = "(取消订单账户增加) 订单号 ".$order_no;
				}
				else
				 {
					 $reason = "(取消订单佣金冲减) 订单号 ".$order_no;	
				 }
				 //生成账户变动日志 
				$account_log = array();
				$account_log['user_id']=$userid;  
				$account_log['stage']='refund';
				$account_log['money']=  $money;
				$account_log['comm'] = $reason;
				$account_log['addtime']=date('Y-m-d H:i:s',time());
				$account_log['remain_money'] = $remain_money;
				$account_log['remain_refer_money'] = $remain_refer_money;
				$account_log['order_no'] = $order_no; 
				$return_1 = D('account_log')->data($account_log)->add();
				if(!$return_1) return false;
		 }	
		else {
			return false;
		}	
		return true; 
	}	
    function order_cancel($id,$type='1') //取消订单函数 type=1普通订单 type=2批发订单
	{
			   if($type=='1')
			   {
			   	   $order = M('kongbao_order')->where('id='.$id)->find();
			   }elseif($type=='2')
			   {
			   	   $order = M('kongbao_daili_order')->where('id='.$id)->find();
			   } 
		 	    if(empty($order))
		  	    {
			      return false;
		        }
			    $order_no = $order['order_no'];
				$userid = $order['user_id'];  
				$where=array();
				$where['order_no'] = $order_no;
				$where['type']=0;
				$where['status'] = 1;
				$pay_order = M('pay_order')->where($where)->find();		
				if(empty($pay_order)) 
				{
					$money =0;
				}
				else
				{
					$money = $pay_order['pay_money']; 	
				} 
				 //根据订单编号获取支付订单
				 $model_pay = new Model('pay_order');
				 $model_pay->startTrans(); 
				 $pay_order_data = array();
				 $pay_order_data['status']=2;
				 $result_yongjin = true;
				 $rtn_5 = true;
		         if(false !== $model_pay->where($where)->save($pay_order_data))
				 {
			 		 //给用户退款//生成账户日志 
			 		 $rtn_1 = true;
			 		 if($money>0)
					 {
					 	$rtn_1 = refund_money($userid,$order_no,$money,'1');
					 } 
			 		 //对上级用户冲减佣金
					 $where= array();
					 $where['order_no'] = $order_no;
					 $where['stage'] = 'refer';
					 $where['is_used'] = 0; //是否已经做过退款标志
					 $refer_list = M('account_log')->where($where)->select();
					 if(!empty($refer_list))
					 {
					 	 foreach($refer_list as $k=>$v)
						 {
							  $user_id = $v['user_id']; 
							  $refer_money = $v['money']; 	
							  $rtn_2 = refund_money($user_id,$order_no,$refer_money,'2');
							 if(!$rtn_2) $result_yongjin=false;		
							 //修改这一笔操作日志
							 $logupdate_array=array();
							 $logupdate_array['id']=$v['id']; 
							 $logupdate_array['is_used']=1;
							 $rtn_5 = M('account_log')->data($logupdate_array)->save();
							 if(!$rtn_2) $result_yongjin=false;					 	  
						 }
					 }
			        $rtn_2 = $result_yongjin; 
					if($type=='1') //如果是普通订单
					{
						 //空包单号重置状态
						 $where = array();
						 $where['note_no'] = $order['note_no'];
						 $where['type_id'] = $order['type_id'];
						 $where['isused'] = 1;
						 $kongbao_data = array();
						 $kongbao_data['isused']=2;
						 $rtn_3 = M('kongbao')->where($where)->data($kongbao_data)->save();
						 //重置空包订单状态
						 $kongbao_order_data['id']= $id;
						 $kongbao_order_data['order_status']=2;
						 $rtn_4 = M('kongbao_order')->data($kongbao_order_data)->save();						
					}
					elseif($type=='2') //如果是批发订单
					{
						 //空包单号重置状态
						 $where = array(); 
						 $where['order_no'] = $order_no;
						 $where['type_id'] = $order['type_id'];
						 $where['isused'] = 1;
						 $kongbao_data = array();
						 $kongbao_data['isused']=2;
						 $rtn_3 = M('kongbao')->where($where)->data($kongbao_data)->save();
						 //重置空包订单状态
						 $kongbao_order_data['id']= $id;
						 $kongbao_order_data['order_status']=2;
						 $rtn_4 = M('kongbao_daili_order')->data($kongbao_order_data)->save();							
					}  
					 if($rtn_1 && $rtn_2 && $rtn_3 && $rtn_4 && $rtn_5 )
					 {
					 	    $model_pay->commit();
							return true;							 	
					 }
					 else
				     {
				     	     $model_pay->rollback();
							 return false;					 		
				     }	 
	          }	
			else {
				 $model_pay->rollback();
				 return false;
			} 
 }	 
?>