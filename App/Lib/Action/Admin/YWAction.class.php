<?php
/***
 * 业务相关设置
 *QQ8201009323
*阿里空包制作
*http://www.alikongbao.com
 */
 Class YWAction extends  CommonAction
 { 
 	   public function kb_setting()
	   { 
		   //获取会员类型
		   $model_level = D('user_level');
		   $level = $model_level->select();
		   $level_arr = array();
		   foreach($level as $k=>$v)
		   {
		   	    $level_arr['a'.$v['id']] = $v;
		   }    
		   $this->assign('level_list',$level_arr);
		
	   	   $config = D('config')->where('id=1')->find();
	       $refer_mode = $config['refer_mode'];  
		   $daili_refer_mode = $config['daili_refer_mode']; //代理佣金模式
		    $mode=array();
			for ($i=1;$i<=$refer_mode;$i+=1)
			   {
				   $mode[$i] = 'a'.$i;
			   }
		    $this->assign('refer_mode',$mode); 
			$daili_mode = array();
			for ($i=1;$i<=$daili_refer_mode;$i+=1)
			   {
				   $daili_mode[$i] = 'a'.$i;
			   }
		    $this->assign('daili_refer_mode',$daili_mode); 			
			//获取空包设置
		   $kongbao_config = json_decode($config['kongbao_config'],true); 
		   $this->assign('config',$kongbao_config);
	   	   $this->display();
	   }
	   public function kbsave() //空包业务相关设置保存
	   {
		 	if(!IS_POST) $this->message2('无效请求!','kb_setting');
			$config = M('config');
			$kongbao_config = $_POST['kongbao_config']; // 多维数组使用I函数获取不到 
			$data = $config->create();
			//对数组进行序列化  
			$data['kongbao_config'] = json_encode($kongbao_config); 
			
			if(false !== $config->where('id=1')->save($data))
			{
				$this->message2('配置修改成功','kb_setting');
			}
			else {
				$this->message2('配置修改失败：'.$config->getError(),'kb_setting');
			}
	   }
      public function pagesetting()
	  {
	  	   $config = D('config')->where('id=1')->find();
		   
           $kongbao_page = json_decode($config['kongbao_page'],true);
		   $kongbao_page_temp = array();
		   foreach($kongbao_page as $k=>$v)
		   {
		   	   $kongbao_page_temp[$k]= stripslashes(htmlspecialchars_decode($v));
		   }
		   $this->assign('kongbao_page',$kongbao_page_temp);
	  	   $this->display();
	  }
	  public function pagesave()
	  {
		 	if(!IS_POST) $this->message2('无效请求!','kb_setting');
			$config = M('config');
			$kongbao_page = $_POST['kongbao_page']; // 多维数组使用I函数获取不到 
			$data = $config->create();
			//对数组进行序列化  
			$data['kongbao_page'] = json_encode($kongbao_page); 
			
			if(false !== $config->where('id=1')->save($data))
			{
				$this->message2('配置修改成功','pagesetting');
			}
			else {
				$this->message2('配置修改失败：'.$config->getError(),'pagesetting');
			}	  	
	  }
	 public function dh_setting()//单号业务设置
	   { 
		   //获取会员类型
		   $model_level = D('user_level');
		   $level = $model_level->select();
		   $level_arr = array();
		   foreach($level as $k=>$v)
		   {
		   	    $level_arr['a'.$v['id']] = $v;
		   }    
		   $this->assign('level_list',$level_arr);
		
	   	   $config = D('config')->where('id=1')->find();
	       $refer_mode = $config['refer_mode'];   
		    $mode=array();
			for ($i=1;$i<=$refer_mode;$i+=1)
			   {
				   $mode[$i] = 'a'.$i;
			   }
		    $this->assign('refer_mode',$mode);  	 
			//获取空包设置
		   $danhao_config = json_decode($config['danhao_config'],true); 
		   $this->assign('config',$danhao_config);
	   	   $this->display();
	   }
	 public function xh_setting()//小号业务设置
	   { 
		   //获取会员类型
		   $model_level = D('user_level');
		   $level = $model_level->select();
		   $level_arr = array();
		   foreach($level as $k=>$v)
		   {
		   	    $level_arr['a'.$v['id']] = $v;
		   }    
		   $this->assign('level_list',$level_arr);
		
	   	   $config = D('config')->where('id=1')->find();
	       $refer_mode = $config['refer_mode'];   
		    $mode=array();
			for ($i=1;$i<=$refer_mode;$i+=1)
			   {
				   $mode[$i] = 'a'.$i;
			   }
		    $this->assign('refer_mode',$mode);  	 
			//获取小号设置
		   $xiaohao_config = json_decode($config['xiaohao_config'],true); 
		   $this->assign('config',$xiaohao_config);
	   	   $this->display();
	   }	   
	  public function dhsave() //单号业务相关设置保存
	   {
		 	if(!IS_POST) $this->message2('无效请求!','dh_setting');
			$config = M('config');
			$danhao_config = $_POST['danhao_config']; // 多维数组使用I函数获取不到 
			$data = $config->create();
			//对数组进行序列化  
			$data['danhao_config'] = json_encode($danhao_config); 
			
			if(false !== $config->where('id=1')->save($data))
			{
				$this->message2('配置修改成功','dh_setting');
			}
			else {
				$this->message2('配置修改失败：'.$config->getError(),'dh_setting');
			}
	   }
	  public function xhsave() //小号业务相关设置保存
	   {
		 	if(!IS_POST) $this->message2('无效请求!','xh_setting');
			$config = M('config');
			$xiaohao_config = $_POST['xiaohao_config']; // 多维数组使用I函数获取不到 
			$data = $config->create();
			//对数组进行序列化  
			$data['xiaohao_config'] = json_encode($xiaohao_config); 
			
			if(false !== $config->where('id=1')->save($data))
			{
				$this->message2('配置修改成功','xh_setting');
			}
			else {
				$this->message2('配置修改失败：'.$config->getError(),'xh_setting');
			}
	   }
 }
?>