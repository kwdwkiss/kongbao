<?php
/**
 * 空包业务相关
 *QQ8201009323
*阿里空包制作
*http://www.alikongbao.com
 */
 Class KBAction extends  CommonAction
 {
 	public function _initialize() //初始化
	{
		 parent::_initialize();
		$this->judge_code_valid(); 
		$config = D('config')->where('id=1')->find();
		//判断是否已经授权
		 if($config['auth_code']=='')
	  /* {
				$this->message2('系统尚未授权或授权已过期(请先进行授权处理)',__APP__.'/Admin/Other/auth');		 	
		 }*/
		//获取空包业务配置信息
		$kongbao_config = json_decode($config['kongbao_config'],true);
		if($kongbao_config['valid']==0)
		{
			$this->message2('系统未开启空包业务(如需开启请到业务配置中进行配置)',__APP__.'/Admin/YW/kb_setting');
		} 
	} 
 	public function type() //空包类型列表
	{
		$model = M('kongbao_type'); 
		$type_list = $model->order('sort_order asc')->select();
		$list = array();
		foreach($type_list as $k=>$v)
		{
			$v['config'] = json_decode($v['config'],true);
			$list[] = $v;
		}
		$this->assign('type_list',$list);
		$this->display();
	}
	public function type_add() //空包类型新增
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
		//获取系统设置的提成模式
		$config = M('config')->where('id=1')->find();
		$refer_mode = $config['refer_mode'];  
		$mode=array();
		for ($i=1;$i<=$refer_mode;$i+=1)
		   {
			   $mode[$i] = 'a'.$i;
		   }
		$this->assign('refer_mode',$mode);   
			//模板
		 $moban_list = M('exp_rule')->select();
		//$moban_list = require("Public/exp_setting_config.php"); 
		$this->assign('moban_list',$moban_list);		
		   
		$this->display();
	}
	 public function type_insert()//空包类型插入操作
	 {
	 	 if(!IS_POST) {$this->message2('无效请求','type');}
		 import("ORG.Net.UploadFile"); 
		 $type_model = new KBTypeModel(); 
		 //取类别是否为真实空包
		 $is_true = I('is_true',0);//判断是否为真包类型
		 if($is_true==1) //如果是真包
		 {
				$address_province = $_POST['config']['address']['province'];
			 	if($address_province=='省份')
			 	{
			 		$this->message2('请正确填写省份信息!','type_add');
				 }
		    	$address_province = $_POST['config']['address']['city'];
				 if($address_province=='地级市')
				 {
				 	$this->message2('请正确填写地级市信息!','type_add');
				 }
				 $address_province =$_POST['config']['address']['district'];
				 if($address_province=='市、县级市')
			 	{
			 		$this->message2('请正确填写区县信息!','type_add');
				 }		 	
		 }
		 $_POST['config']  = json_encode($_POST['config']);
		 
		 $exp_id = I('exp_id','');
		 if($exp_id=='')
		 {
		 	  $this->message2('请选择订单导出模板格式!','type_add');
		 }
		   $data = $type_model->create();
		   if(!$data)
		   {
		   	   $this->message2($type_model->getError(),'type_add');
		   } 
		  $upload = new UploadFile(); // 实例化上传类   
		  if(!empty($_FILES['file_url']) && $_FILES['file_url']['name']!='' )
		  {  
	        $upload->maxSize = 3145728 ; // 设置附件上传大小
	        $upload->allowExts = array("xls"); // 设置附件上传类型  
	        $upload->savePath = "Public/Uploads/kb/"; 
			$upload->saveRule = ""; 
	    	// 设置附件上传目录   
	    	if($upload->upload()) 
	    	{ // 上传成功 获取上传文件信息 
	    	    $info = $upload->getUploadFileInfo();  
			}else{
				 // 上传错误 提示错误信息  
		 		$this->error($upload->getErrorMsg());  
			} 
			 $data['file_url'] = $upload->savePath.$info[0]["savename"]; 
		  }
		  if(!empty($_FILES['image_url']) && $_FILES['image_url']['name']!='' )
		  {  
	        $upload->maxSize = 3145728 ; // 设置附件上传大小
	        $upload->allowExts = array("jpg", "gif", "png", "jpeg"); // 设置附件上传类型  
	        $upload->savePath = "Public/Uploads/kb/"; 
	    	// 设置附件上传目录   
	    	if($upload->upload()) 
	    	{ // 上传成功 获取上传文件信息 
	    	    $info1 = $upload->getUploadFileInfo();  
			}else{
				 // 上传错误 提示错误信息  
		 		$this->error($upload->getErrorMsg());  
			} 
			 $data['image_url'] = $upload->savePath.$info1[0]["savename"]; 
		  }	  	 
			$data['title'] = I('name','');
			$data['comm'] = I('comm','');
			$data['state'] = I('state',0);
			$data['sort_order'] = I('sort_order',0); 
			$data['config'] =  $_POST['config']; 
			$data['is_true'] = I('is_true',0);
			if(false!==$type_model->data($data)->add())
			{
				$this->message2('新增成功','type');
			}
			else {
				$this->message2('新增失败：'.$type_model->getError(),'type_add');
			}	 		 
	 }
	 public function type_edit() //类型编辑页面
	 {
	 	  $id = I('id',null);
		  if(empty($id))
		  {
		  	$this->message2('无效请求!','type');
		  }
		//获取会员类型
			$model_level = D('user_level');
			$level = $model_level->select();
			   $level_arr = array();
			   foreach($level as $k=>$v)
			   {
			   	    $level_arr['a'.$v['id']] = $v;
			   }    
			   $this->assign('level_list',$level_arr);
			//获取系统设置的提成模式
			$config = M('config')->where('id=1')->find();
			$refer_mode = $config['refer_mode'];  
			$mode=array();
			for ($i=1;$i<=$refer_mode;$i+=1)
			   {
				   $mode[$i] = 'a'.$i;
			   }
			$this->assign('refer_mode',$mode);
			//获取空包类型信息 
			$type = D('kongbao_type')->where('id='.$id)->find();
			$config = json_decode($type['config'],true);  
			//模板
		   $moban_list = M('exp_rule')->select(); 
		   $this->assign('moban_list',$moban_list);		
		   	
			$this->assign('config',$config);
			$this->assign('type',$type);
			$this->display();	 	
	 }
    public function type_update()//空包类型更新
	 { 
		 if(!IS_POST){$this->message2('无效请求!',__APP__.'/Admin');} 
		  
		    $id=I('id',null); 
			if(!empty($id))
			{
				import("ORG.Net.UploadFile"); 
				$model=new KBTypeModel();  
			 	$exp_id = I('exp_id','');
				 if($exp_id=='')
				 {
				 	  $this->message2('请选择订单导出模板格式!','type');
				 }	  
				 $is_update = I('is_update',0);
				 $is_true = I('is_true',0);
				 if($is_update==1 && $is_true==1 ) //如果是真包并且更新了发货地址的话
				 {
						$address_province = $_POST['config']['address']['province'];
					 	if($address_province=='省份')
					 	{
					 		$this->message2('请正确填写省份信息!','type');
						 }
				    	$address_province = $_POST['config']['address']['city'];
						 if($address_province=='地级市')
						 {
						 	$this->message2('请正确填写地级市信息!','type');
						 }
						 $address_province =$_POST['config']['address']['district'];
						 if($address_province=='市、县级市')
					 	{
					 		$this->message2('请正确填写区县信息!','type');
						 }					 	
				 }
				 else
				 {
				 	/**获取之前的数据**/
				 	$old_type =$model->where('id='.$id)->find();
					$old_config = json_decode($old_type['config'],true) ;
					 $_POST['config']['address']['province'] = $old_config['address']['province'];
					 $_POST['config']['address']['city'] = $old_config['address']['city'];
					 $_POST['config']['address']['district'] =$old_config['address']['district'];
				 }
				 $_POST['config'] = json_encode($_POST['config']);
				 unset($_POST['is_update']);
				 $data=$model->create(); 
				  if(!empty($_FILES['file_url']) && $_FILES['file_url']['name']!='' )
				  { 
				    $upload = new UploadFile(); // 实例化上传类  
			        $upload->maxSize = 3145728 ; // 设置附件上传大小
			        $upload->allowExts = array("xls"); // 设置附件上传类型  
			        $upload->savePath = "Public/Uploads/kb/"; 
					$upload->saveRule = ""; 
			    	// 设置附件上传目录   
			    	if($upload->upload()) 
			    	{ // 上传成功 获取上传文件信息 
			    	    $info = $upload->getUploadFileInfo();  
					}else{
						 // 上传错误 提示错误信息  
				 		$this->error($upload->getErrorMsg());  
					}  
				  $data['file_url'] = $upload->savePath.$info[0]["savename"]; 
			    }		
			  if(!empty($_FILES['image_url']) && $_FILES['image_url']['name']!='' )
			  { 
			    $upload1= new UploadFile(); // 实例化上传类  
		        $upload1->maxSize = 3145728 ; // 设置附件上传大小
		        $upload1->allowExts = array("jpg", "gif", "png", "jpeg"); // 设置附件上传类型  
		        $upload1->savePath = "Public/Uploads/kb/"; 
		    	// 设置附件上传目录   
		    	if($upload1->upload()) 
		    	{ // 上传成功 获取上传文件信息 
		    	    $info1 = $upload1->getUploadFileInfo();  
				}else{
					 // 上传错误 提示错误信息  
			 		$this->error($upload1->getErrorMsg());  
				} 
				 $data['image_url'] = $upload1->savePath.$info1[0]["savename"]; 
			  }			 
				$data['config'] = $_POST['config'] ;  
				$data['is_true'] = $is_true;
				if(false!==$model->where('id='.$id)->save($data)){
					$this->message('编辑成功',__URL__.'/type');
				}else{
					$this->message('编辑失败：'.$model->getError(),__URL__.'/type');
				} 
			}else{ 
				$this->message('请选择编辑对象',__URL__.'/type');
			} 
    	}
		 public function  type_del(){ //删除空包类型信息
			$id=I('id',null);
			if(!empty($id)){
			   if(is_array($id))
			   {
			   	    $where_e = "type_id in (".implode(',', $id).")";
					$where = "id in  (".implode(',', $id).")";
			   }
			   else
			   	{
			   		$where_e = "type_id = ".$id;
					$where = " id = ".$id;
			   	}
				//判断是否存在该类型的空包单号
				$count = M('kongbao')->where($where_e)->count();
				if($count>0)
				{
					$this->message('已经存在该类型的空包单号，系统将不能删除!',__URL__.'/type'); 
				}
				$model=new KBTypeModel();
				if(false!==$model->where($where)->delete()){
					$this->message('删除成功',__URL__.'/type'); 
				}else{
					$this->message('删除失败：'.$model->getError(),__URL__.'/type');
				} 
			}else{
				$this->message('请选择删除对象',__URL__.'/type');
			}
		}	
		public function index() //空包号管理列表
		{
			import('ORG.Util.Page');
			//获取空包类型信息 
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);
			//获取空包单据信息
	 	    $kb_model = new KongbaoViewModel(); 
			
			$keyword=I('keyword');
			$ftype=I('ftype');
			$where = array();
			if(!empty($keyword) && !empty($ftype)){
				$where[$ftype]=array('like','%'.$keyword.'%'); 
			}  
			 $type_id=I('type_id',''); 
			 if($type_id!='') { $where['type_id']=$type_id; } 
			 $where['isused']=0; 
			$count= $kb_model->where($where)->count();
			$page = new Page($count,15);
		 
			$show = $page->show(); 
		 	$kongbao= $kb_model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();

			$this->assign('count',$count);
			$this->assign('show',$show);
			$this->assign('kongbao_list',$kongbao);	
			$this->display();  
		}
		public function edit() //空包单号编辑
		{
			//获取空包单类型
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list); 
			$id = I('id',null);
			if(!empty($id))
			{
				$kongbao = M('kongbao')->where('id='.$id)->find();
				$this->assign('kongbao',$kongbao);
			}
			else{
				$this->message2('请选择要编辑的项目','index');
			}
			$this->display();
		}
		public function update()//空包信息编辑处理
		{
			   if(!IS_POST){$this->message2('非法操作!',__APP__.'/Admin');} 
				$id=I('id',null);
				if(!empty($id))
				{ 
					$model=new KongbaoModel(); 
					 
					$data=$model->create();
					if(false!==$model->save()){
						$this->message('编辑成功',__URL__.'/index');
					}else{
						$this->message('编辑失败：'.$model->getError(),__URL__.'/index');
					} 
				}else{
	
					$this->message('请选择编辑对象',__URL__.'/index');
				}			
		}
		
		public function add() //空包单号新增
		{
			//获取空包单类型
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);
			
			$this->display();
		} 
		public function insert() //空包单个插入操作
		{
			if(!IS_POST) {$this->message2('无效请求','add');}
			 $model = new KongbaoModel();  
			 if($data = $model->create())
			 { 
					if(false!==$model->data($data)->add())
					{
						$this->message2('新增成功','index');
					}
					else {
						$this->message2('新增失败：'.$model->getError(),'add');
					}			
			 }
			 else {
					$this->message2('新增失败：'.$model->getError(),'add');
		      } 
		}
		public function insert_multi() //多行插入操作
		{
			if(!IS_AJAX) {$this->message2('无效请求',__APP__.'/Admin');}
			//获取插入的数据
			$note_nos = I('note_nos',null);
			$type_id = I('type_id',null);
			if(empty($note_nos))
			{
				 $this->ajaxReturn('',"单据号不能为空！",0);
			}
			$note_nos = trim($note_nos);
			if($note_nos =='')
			{
				 $this->ajaxReturn('',"单据号不能为空！",0);
			}
			//将数据按行组成数组
			$error_msg = array();
			$note_nos= preg_replace("/([\s]{2,})/","\n",$note_nos); 
			$note_array =explode("\n", $note_nos);
			 
			$error_count = 0; //错误数据
			$succ_count = 0;
			$insert_data = array();  
			$return_array = array();
			$counts =count($note_array); //总数
			foreach($note_array as $k=>$v)
			{
				  $where =array();
				  $where['note_no'] = $v;
				  $count = M('kongbao')->where($where)->find();
				  if($count>0)
				  {
				  	   $error_count ++;   
				  	   $error_msg[]=$v."已存在";
						continue;
				  } 
				  //插入数据
				  $temp_data = array();
				  $temp_data['note_no'] = $v;
				  $temp_data['type_id'] = $type_id;
				  $temp_data['type'] = 1;
				  $temp_data['isused'] = 0;
				  $temp_data['addtime']=$this->getDate();
				  $insert_data[] =$temp_data;
			}
			$kongbao = new KongbaoModel();
			if(!empty($insert_data)) //如果插入数据不为空时 对数据进行处理
			{ 
				   foreach($insert_data as $data)
				   {
				   	  $id = $kongbao->data($data)->add();
					  if($id>0)
					  {
					  	$succ_count ++; 
					  }
					  else{
						   $error_count ++;
						   $error_msg[]=$data['note_no'].'已存在';
					  }
				   } 
			}
			$return_array['error_count'] = $error_count;
			$return_array['error_msg'] = implode('|',$error_msg);
			$info = "处理完成！导入总数：".$counts."，成功数：".$succ_count.",失败数：".$error_count;
			$this->ajaxReturn($return_array,$info,1);
		} 
		private function get_kongbao_type() //获取空包类型
		{
			$type_model = new Model('kongbao_type');
			$type = $type_model->select();
			$type_list = array();
			foreach($type as $k=>$v)
			{
				if($v['state']==1) $v['name'] = $v['name']."(业务暂停)";
				$v['config'] = json_decode($v['config'],true);
				$type_list[] = $v;
			}
			return $type_list;		
		}
		public  function getDate(){
			return date('Y-m-d H:i:s');
		}
		public function del()
		{
			$id = I('id',null);
			if(!empty($id))
			{
	    		if(is_array($id)) //如果是数组
				{
					$where = "id  in (".implode(',',$id).")";
				}
				else {
					$where = "id = ".$id;
				}
	    		$kongbao=new KongbaoModel();
	    		if(false!==$kongbao->where($where)->delete()){
	    			$this->message('删除成功',__URL__.'/index');
	
	    		}else{
	    			$this->message('删除失败：'.$kongbao->getError(),__URL__.'/index');
	    		}
	    	}else{
	    		$this->message('请选择删除对象',__URL__.'/index');
	    	}
		}
		public function used_index() //已用空包单号
		{
				import('ORG.Util.Page');
				//获取空包类型信息 
				$type_list = $this->get_kongbao_type(); 
				$this->assign('type_list',$type_list);
				//获取空包单据信息
		 	    $kb_model = new KongbaoViewModel(); 
				
				$keyword=I('keyword');
				$ftype=I('ftype');
				$where = array();
				if(!empty($keyword) && !empty($ftype)){
					$where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
				 $type_id=I('type_id',''); 
				 if($type_id!='') { $where['type_id']=$type_id; } 
				 $where['isused']=1; 
				$count= $kb_model->where($where)->count();
				$page = new Page($count,15);
			 
				$show = $page->show(); 
			 	$kongbao= $kb_model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
	
				$this->assign('count',$count);
				$this->assign('show',$show);
				$this->assign('kongbao_list',$kongbao);	
				$this->display();  			
		}
     public function order_index() //普通订单列表
	 {
	 	 import('ORG.Util.Page');
	 	 $model = new KongbaoOrderViewModel();
		 
		 $keyword=I('keyword');
		 $ftype=I('ftype');
		 $where = array();
		 if(!empty($keyword) && !empty($ftype)){
		 	   if($ftype=='user_id')
			   {
			   		$where[$ftype]=$keyword; 
			   }
			else
				{
				    $where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
		  } 
		    $starttime = I('starttime','');
			$endtime = I('endtime','');   
			if($starttime!='')
			{
				$_POST['starttime'] = urldecode($starttime); 
			}
			if($endtime!='')
			{
				$_POST['endtime'] = urldecode($endtime); 
			}
			if($starttime=='') $starttime="2014-01-01 00:00:00";
			if($endtime=='') $endtime="2099-12-30 23:59:59"; 
			$where['order_time']=array('between',array($starttime,$endtime));  
		    $type_id = I('type_id','');
			if($type_id !='') $where['type_id'] = $type_id;
			$count= $model->where($where)->count();
			$page = new Page($count,15);
		 
			$show = $page->show(); 
		 	$order= $model->where($where)->order('order_time desc')->limit($page->firstRow.",".$page->listRows)->select();
			//获取空包类型信息 
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);		 
		    $this->assign('show',urldecode($show));
			$this->assign('count',$count);
			$this->assign('order_list',$order);
	 	   $this->display();
	 }
     public function order_daili() //批发订单列表
	 {
	 	 import('ORG.Util.Page');
	 	 $model = new KBOrderViewModel();
		 
		 $keyword=I('keyword');
		 $ftype=I('ftype');
		 $where = array();
		 if(!empty($keyword) && !empty($ftype)){
		 	   if($ftype=='user_id')
			   {
			   		$where[$ftype]=$keyword; 
			   }
			else
				{
				    $where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
		  } 
		    $type_id = I('type_id','');
			if($type_id !='') $where['type_id'] = $type_id;
			$count= $model->where($where)->count();
			$page = new Page($count,15);
		 
			$show = $page->show(); 
		 	$order= $model->where($where)->order('order_time desc')->limit($page->firstRow.",".$page->listRows)->select();
			//获取空包类型信息 
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);		 
		    $this->assign('show',$show);
			$this->assign('count',$count);
			$this->assign('order_list',$order);
	 	   $this->display();	 	    
	 }
     public function dd_index() //底单申请列表
	  {
	 	 import('ORG.Util.Page');
	 	 $model = new KBDidanViewModel();
		 
		 $keyword=I('keyword');
		 $ftype=I('ftype');
		 $where = array();
		 if(!empty($keyword) && !empty($ftype)){
		 	   if($ftype=='user_id')
			   {
			   		$where[$ftype]=$keyword; 
			   }
			else
				{
				    $where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
		  }  
		    $type_id = I('type_id','');
			if($type_id !='') $where['type_id'] = $type_id;
		    $status = I('status','');
			if($status!='') $where['status'] = $status;			
			$count= $model->where($where)->count();
			$page = new Page($count,15);

			$show = $page->show(); 
		 	$didan= $model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
			//获取空包类型信息 
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);		 
		    $this->assign('show',$show);
			$this->assign('count',$count);
			$this->assign('order_list',$didan); 	  	
	 	   $this->display();
	  }
      public function dd_edit() //底单编辑处理页面
	  {
	  	   $id = I('id',null);
		   if(empty($id)){$this->message2('未选择编辑项!','dd_index');}
		   
		   $didan_model = new KongbaoDidanViewModel();
		   $where['id'] = $id;
		   $didan = $didan_model->where($where)->find();
		   $this->assign('didan',$didan);
		   $this->display();
	  }
	  public function dd_update() //底单申请处理
	  {
	  	  if(!IS_POST) {$this->message2('无效请求!','dd_index');}
		  $id = I('id',null);
		 if(empty($id)) {$this->message2('未指定更新项!','dd_index');}
		 //判断是否存在dd图片
		 $dd_image = I('didan_image',null);
		 if(empty($dd_image))
		 {
		 	$this->message2('请上传底单截图!',U('dd_edit',array('id'=>$id)));
		 } 
		 $update_array = array();
		 $update_array['id'] = $id;
		 $update_array['didan_image'] = $dd_image;
		 $update_array['status'] = 2;
		 $model  = M('kongbao_didan');
		 if(false !==$model->data($update_array)->save())
		 {
		 	$this->message2('底单处理成功!','dd_index');
		 }
		 else {
			$this->message2('底单处理失败：'.$model->getError(),'dd_index');	 
		 }
	  }
     public function dd_uploads() //表格底单申请列表
	  {
	 	 import('ORG.Util.Page');
	 	 $model = new Model('didan_uploadfiles');
		 
		 $keyword=I('keyword');
		 $ftype=I('ftype');
		 $where = array();
		 if(!empty($keyword) && !empty($ftype)){
		 	   if($ftype=='user_id')
			   {
			   		$where[$ftype]=$keyword; 
			   }
			else
				{
				    $where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
		  }   
		    $status = I('status','');
			if($status!='') $where['status'] = $status;			
			$count= $model->where($where)->count();
			$page = new Page($count,15);

			$show = $page->show(); 
		 	$didan= $model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
		    $this->assign('show',$show);
			$this->assign('count',$count);
			$this->assign('order_list',$didan); 	  	
	 	   $this->display();
	  }
	 public function dd_uploads_edit() //表格底单编辑处理页面
	  {
	  	   $id = I('id',null);
		   if(empty($id)){$this->message2('未选择编辑项!','dd_uploads');}
		   
		   $didan_model = new Model('didan_uploadfiles');
		   $where['id'] = $id;
		   $didan = $didan_model->where($where)->find();
		   $this->assign('didan',$didan);
		   $this->display();
	  }
	  public function dd_uploads_update() //表格底单申请处理
	  {
	  	  if(!IS_POST) {$this->message2('无效请求!','dd_uploads');}
		  $id = I('id',null);
		 if(empty($id)) {$this->message2('未指定更新项!','dd_uploads');} 
		 $type = I('type',2);
		 $outer_content = I('outer_content','');
		 $comm = I('comm','');
		 if($type==3)
		 {
		 	  if($comm==''){$this->message2('请填写失败原因!','dd_uploads');}
		 }
		 $update_array = array();
		 $update_array['id'] = $id; 
		 $update_array['status'] = $type;
		 $update_array['comm'] = $comm;
		 $update_array['outer_content'] =$outer_content;
		 $model  = M('didan_uploadfiles');
		 if(false !==$model->data($update_array)->save())
		 {
		 	$this->message2('表格底单处理成功!','dd_uploads');
		 }
		 else {
			$this->message2('表格底单处理失败：'.$model->getError(),'dd_uploads');	 
		 }
	  }	
	public function dd_uploads_del() //表格底单内容删除处理
	{ 
		 $id = I('id',null);
			if(!empty($id))
			{
	    		if(is_array($id)) //如果是数组
				{
					$where = "id  in (".implode(',',$id).")";
					$where_a =  "id  in (".implode(',',$id).") and status=2";
				}
				else {
					$where = "id = ".$id;
					$where_a = "id = ".$id." and status=2";
				}
	    		$model=new Model('didan_uploadfiles');
				$count = $model->where($where_a)->count();
				if($count>0)
				{
					$this->message('不能删除已经处理完成的底单',__URL__.'/dd_uploads');
				}
	    		if(false!==$model->where($where)->delete()){
	    			$this->message('删除成功',__URL__.'/dd_uploads'); 
	    		}else{
	    			$this->message('删除失败：'.$kongbao->getError(),__URL__.'/dd_uploads');
	    		}
	    	}else{
	    		$this->message('请选择删除对象',__URL__.'/dd_uploads');
	    	} 
	}
    public function dddocexp()//导出底单内容
	{ 
		$id = I('id','');
		$type_id = I('type',1);//查看内容1-申请内容2-处理结果 
		$where['id']=$id; 
		$order = M('didan_uploadfiles')->where($where)->find(); 
		//获取业务类型 
		if($type_id==1) //申请内容
		{
			$exp_content=$order['input_content']; 
			$title = "申请内容";
		}
	   elseif($type_id==2) {//处理结果
			$exp_content=$order['outer_content'];
	  		 $title = "处理结果"; 
		}   
	    $html = "<html><head><title>".$title."</title></head><body>".$exp_content."</body></html>";  
		$html = htmlspecialchars_decode($html);
		echo $html; 
		exit;
	}  
       public function  uploadfiles()
	   {
	 	 import('ORG.Util.Page');
	 	 $model = new UserFilesViewModel();
		 
		 $keyword=I('keyword');
		 $ftype=I('ftype');
		 $where = array();
		 if(!empty($keyword) && !empty($ftype)){
		 	   if($ftype=='user_id')
			   {
			   		$where[$ftype]=$keyword; 
			   }
			else
				{
				    $where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
		  } 
		    $type_id = I('type_id','');
			if($type_id !='') $where['type_id'] = $type_id;
		    $status = I('status','');
			if($status !='') $where['status'] = $status;			
			$count= $model->where($where)->count();
			$page = new Page($count,15);
		 
			$show = $page->show(); 
		 	$didan= $model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
			//获取空包类型信息 
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);		 
		    $this->assign('show',$show);
			$this->assign('count',$count);
			$this->assign('order_list',$didan); 	  	
	 	    $this->display();	   	
	   }
	   public function down_daili() //下载代理文件
	   {
	 	    $id = I('id',null);  
			$where['id'] = $id; 
			$uploadfile = M('uploadfiles')->where($where)->find();
  
		     if(!empty($uploadfile) && $uploadfile['fileurl']!='')
			  {
			  	 $file_name = $uploadfile['filename'].".xls"; 
			  	//获取会员用户名
			  	  $user = M('user')->where('id='.$uploadfile['user_id'])->find();
				  if(!empty($user))
				  {
				  	 $file_name = "[".$user['username']."]".$uploadfile['filename'].".xls"; 
				  } 
			  	  $file_path= $uploadfile['fileurl']; 
			  }
			  else 
			  {
				  $this->message2('文件不存在',U('uploadfiles'));
			  } 
		     $filedata = array();
		     $filedata['id'] = $id; 
		     $filedata['downcounts'] =$uploadfile['downcounts'] +1;
		     $result = M('uploadfiles')->data($filedata)->save();
			 
			$file_path = iconv("utf-8",'gb2312',$file_path);  
      		import("ORG.Net.Http");	
			$file_name = urlencode($file_name);
			$download=new Http();	
			$download->download($file_path,$file_name);
		}      
	   public function file_edit()
	   {
	   	   $id = I('id',null);
		   if(empty($id)){$this->message2('未指定编辑项!',__APP__.'/Admin');}
		   $model = new UserFilesViewModel();
		   $where['id'] = $id;
		   $file = $model->where($where)->find();
		   $this->assign('file',$file);
		   $this->display();
	   }
	   public function file_update() //代理文件处理操作
	   {
           if(!IS_POST){$this->message2('无效请求',__APP__.'/Admin');}
		   $id = I('id',null);
		   if(empty($id)) {$this->message2('未指定编辑项!',__APP__.'/Admin');}	   	
		   $deal_type = I('deal_type',0);
		   $comm = I('comm','');
		   if($deal_type==2)
		   {
		   	   if($comm=='')
			   {
			   	   $this->message2('请填写失败原因!',U('file_edit',array('id'=>$id)));
			   }
		   }
		   $model = new Model('uploadfiles');
		   $filedata = array();
		   $filedata['id'] = $id;
		   $filedata['status'] = $deal_type;
		   $filedata['comm'] =$comm;
		   $result = $model->data($filedata)->save();
		   if($result)
		   {
		   	  $this->message2('处理成功!','uploadfiles');
		   }
		   else
		   	{
		   		$this->message2('处理失败：'.$model->getError(),U('file_edit',array('id'=>$id)));
		   	}
	   } 
	 public function order_exp() //会员空包订单导出
	 {
	 	   import('ORG.Util.Page');
	 	   $model = new KongbaoOrderViewModel();
		  
		    $starttime = I('starttime','');
			$endtime = I('endtime',''); 
		   if($starttime!='')
			{
				$_POST['starttime'] = urldecode($starttime); 
			}
			if($endtime!='')
			{
				$_POST['endtime'] = urldecode($endtime); 
			}
			$where['order_time']=array('between',array($starttime,$endtime)); 
			
		    $type_id = I('type_id','');
			if($type_id !='') $where['type_id'] = $type_id;
			$submit = I('form_submit','');
			$count = 0;
		    //获取空包类型信息 
			 $type_list = $this->get_kongbao_type(); 
			 $this->assign('type_list',$type_list);		
			 $order = array();
			if($submit=='ok')
			{
			   //根据类型动态获取内容用于导出
			   $kb_type = M('kongbao_type')->where('id='.$type_id)->find();
			   if($kb_type['exp_id']!='')//如果导出模板格式ID不为空
			   {  
					$count= $model->where($where)->count(); 
					$page = new Page($count,15); 
					$show = $page->show();  
				 	$order= $model->where($where)->order('note_no asc,order_time desc')->limit($page->firstRow.",".$page->listRows)->select(); 	 				   	
				    $this->assign('show',urldecode($show));
					$this->assign('count',$count);
					$this->assign('order_list',$order);			
					 $this->display();	
					 exit;   
			   }
			   else 
			   {
				   $this->message2('请先维护快递类型的导出模板格式!',U('type_edit',array('id'=>$type_id)));
			   } 
			}  
		   $this->assign('show','');
		   $this->assign('count',0);
		   $this->assign('order_list',array());			
	 	   $this->display();
	 }
    public function export()
	{ 
		if(!IS_POST){$this->message2('无效请求',__APP__.'/Admin');}
	    $starttime = I('start','');
		$endtime = I('end','');
		$where['order_time']=array('between',array($starttime,$endtime));  
	    $type_id = I('tid','');
		if($type_id !='') $where['type_id'] = $type_id;		
		$model = new KongbaoOrderViewModel();
		$kb_type = M('kongbao_type')->where('id='.$type_id)->find(); 
		 if($kb_type['exp_id']!='')//如果导出模板格式ID不为空
		 {
		 	    //$headers = getHeaderFromFile($kb_type['exp_id']);  
		   	    //$fields = getFieldFromFile($kb_type['exp_id']);   
				$headers = $this->getHeaderFromFile_kb($kb_type['exp_id']);  
		   	    $fields = $this->getFieldFromFile_kb($kb_type['exp_id']);   
			 	$exp_list_temp= M('kongbao_order')->field($fields)->where($where)->order('note_no asc,order_time desc')->select();
		        $exp_list = array();
				$i=0;
				foreach($exp_list_temp as $k=>$v)
				 { 
				 	   $i++;
					   if(isset($v['id'])) $v['id'] = $i;    
					   $exp_list[] = $v;
				 }
				 //更新订单导出状态
				 $update_array = array();
				 $update_array['exp_status']=1;
				  M('kongbao_order')->where($where)->data($update_array)->save();
		 }    
	    //生成文件名
	    $file_name = "【".$kb_type['name']."】".$starttime.'-'.$endtime;
	    dataToExcel($headers,$exp_list,$file_name) ;
		exit;
	}
	 public function dd_exp() //会员底单申请导出
	 {
	 	   import('ORG.Util.Page');
	 	   $model = new DidanViewModel();
		  
		    $starttime = I('starttime','');
			$endtime = I('endtime',''); 
			$where['status'] = 1;
			$where['addtime']=array('between',array($starttime,$endtime)); 
			
			 $type_id = I('type_id','');
			if($type_id !='') $where['type_id'] = $type_id; 
				
			$count= $model->where($where)->count();
			$page = new Page($count,15); 
			$show = $page->show(); 
		 	$didan= $model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
			//获取空包类型信息 
			$type_list = $this->get_kongbao_type(); 
			$this->assign('type_list',$type_list);		 
			$submit = I('form_submit','');  
			$this->assign('show',$show);
			$this->assign('count',$count);
			$this->assign('order_list',$didan); 
	 	   $this->display();
	 }
    public function dd_export()//底单导出
	{ 
		if(!IS_POST){$this->message2('无效请求',__APP__.'/Admin');}
		$where['status'] = 1;
	    $starttime = I('start','');
		$endtime = I('end','');
		$where['addtime']=array('between',array($starttime,$endtime));  
	    $type_id = I('tid','');
		if($type_id !='') $where['type_id'] = $type_id;		
		$model = new DidanViewModel();
		$kb_type = M('kongbao_type')->where('id='.$type_id)->find(); 
		//获取导出模板格式
		$geshi = $this->getHeaderFromFile();  
		$headers = array($geshi['title']); 
		$fields =  implode(',', $geshi['name']); 
		
		$exp_list= M('kongbao_didan')->field($fields)->where($where)->order('addtime desc')->select();  
        
	    //生成文件名
	    $file_name = "【".$kb_type['name']."】".$starttime.'-'.$endtime;
	    dataToExcel($headers,$exp_list,$file_name) ;
		exit;
	}	 
	private function getHeaderFromFile()
	{
	       $return_array =array(); 
		   $fileds_array = require("Public/ddexp_setting_config.php");   
		   foreach($fileds_array as $k=>$v)
		   { 
			  $return_array['title'][]=$v['title']; 
			  $return_array['name'][]=$v['name'];
		   }
		   return $return_array;  		  
	}
	public function rule_index() //导出规则列表
	{
		 $rule_list = M('exp_rule')->select(); 
		 $this->assign('rule_list',$rule_list);
		 $this->display();
	}
	public function rule_add() //导出规则新增
	{
		  $exp_field_file = "Public/exp_fields.php";
		  $filed_list = include($exp_field_file);
		  $fields_comm="";
		   foreach($filed_list as $k=>$v)
		   {
		   	   if($k%6==0 && $k>0)
			   {
			   	  $fields_comm.= $k."=".$v['name']."<br/>";
			   }
			   else
			   	{
			   		$fields_comm.= $k."=".$v['name'].",";
			   	} 
		   }
		   $fields_comm = rtrim($fields_comm,',');
		   $fields_comm = rtrim($fields_comm,'<br/>');
		   $this->assign('fields_comm',$fields_comm);
		   $this->display();
	}
	public function rule_insert()
	{
	     if(!IS_POST){$this->message2('无效请求',__APP__.'/Admin');}
	     $title = I('title','');
		 if($title =='')
		 {
		 	  $this->message2('规则名称不能为空！','rule_add');
		 }
		 if(empty($_POST['rule_col']))
		 {
		 	  $this->message2('请增加单元表格！','rule_add');
		 } 
		 else {
		 	    $exp_field_file = "Public/exp_fields.php";
		         $filed_list = include($exp_field_file); 
				 foreach($filed_list as $key=>$field)
				 {
				 	$key_list[]=$key;
				 } 
			   //判断各项值是否符合
			   $rule_col = $_POST['rule_col'];
			   foreach($rule_col as $k=>$v)
			   {
			   	    if(trim($v['name'])=='')
					{
						$this->message2('增加的单元格表头名不能为空！','rule_add');
						break;
					}
					$content = $v['content'];
			   	    if(trim($content)=='')
					{
						$this->message2('增加的单元格显示项目不能为空！','rule_add');
						break;
					}					
					$c_array = explode(',', $content);
					foreach($c_array as $con)
					{
						 if(!in_array($con,$key_list))
						 {
						   	  $this->message2('显示项目中存在不合法的字段！','rule_add');
							  break;
						  }
					 } 
			   }
			   //校验没有问题的话，则按顺序生成数据并保存规则
			   $rule_array = array();
			   foreach($rule_col as $rule)
			   {
			   	     $rule_array[]=$rule;
			    } 
			  $data = array();
			  $data['title'] = $title;
			  $data['comm'] = $_POST['comm'];
			  $data['exp_rule'] = json_encode($rule_array);
			  $model = new Model('exp_rule');
			  if(false !== $model->data($data)->add())
			  {
			  	   $this->message2('规则新增成功！','rule_index');
			  }
			  else {
				   $this->message2('规则新增失败：'.$model->getError(),'rule_index');
			  } 
		 }
	}
     public function rule_edit()//规则编辑
	 {
	 	 $id = I('id',null);
		 if(empty($id)){$this->message2('未指定编辑项','rule_index');}
		 $rule = M('exp_rule')->where('id='.$id)->find();
		 if(empty($rule)){$this->message2('未找到指定的编辑项','rule_index');} 
		 $exp_rule = json_decode($rule['exp_rule'],true);
		 $cols_count = count($exp_rule);
		 $this->assign('exp_rule',$exp_rule);
		 $this->assign('cols_count',$cols_count);
		 $this->assign('rule',$rule);
 		  $exp_field_file = "Public/exp_fields.php";
		  $filed_list = include($exp_field_file);
		  $fields_comm="";
		   foreach($filed_list as $k=>$v)
		   {
		   	   if($k%6==0 && $k>0)
			   {
			   	  $fields_comm.= $k."=".$v['name']."<br/>";
			   }
			   else
			   	{
			   		$fields_comm.= $k."=".$v['name'].",";
			   	} 
		   }
		   $fields_comm = rtrim($fields_comm,',');
		   $fields_comm = rtrim($fields_comm,'<br/>');
		   $this->assign('fields_comm',$fields_comm);		 
		 $this->display();
	 }
     public function rule_update()//规则保存处理
	 {
         if(!IS_POST){$this->message2('无效请求',__APP__.'/Admin');}
		 $id = I('id',null); 
		 if(empty($id)) $this->message2('未指定更新项！','rule_index');
		 $url = U('rule_edit',array('id'=>$id));
	     $title = I('title','');
		 if($title =='')
		 {
		 	  $this->message2('规则名称不能为空！',$url);
		 }
		 if(empty($_POST['rule_col']))
		 {
		 	  $this->message2('请增加单元表格！',$url);
		 } 
		 else {
		 	    $exp_field_file = "Public/exp_fields.php";
		         $filed_list = include($exp_field_file); 
				 foreach($filed_list as $key=>$field)
				 {
				 	$key_list[]=$key;
				 } 
			   //判断各项值是否符合
			   $rule_col = $_POST['rule_col'];
			   foreach($rule_col as $k=>$v)
			   {
			   	    if(trim($v['name'])=='')
					{
						$this->message2('增加的单元格表头名不能为空！',$url);
						break;
					}
					$content = $v['content'];
			   	    if(trim($content)=='')
					{
						$this->message2('增加的单元格显示项目不能为空！',$url);
						break;
					}					
					$c_array = explode(',', $content);
					foreach($c_array as $con)
					{
						 if(!in_array($con,$key_list))
						 {
						   	  $this->message2('显示项目中存在不合法的字段！',$url);
							  break;
						  }
					 } 
			   }
			   //校验没有问题的话，则按顺序生成数据并保存规则
			   $rule_array = array();
			   foreach($rule_col as $rule)
			   {
			   	     $rule_array[]=$rule;
			    } 
			  $data = array();
			  $data['id'] = $id;
			  $data['title'] = $title;
			  $data['comm'] = $_POST['comm'];
			  $data['exp_rule'] = json_encode($rule_array);
			  $model = new Model('exp_rule');
			  if(false !== $model->data($data)->save())
			  {
			  	   $this->message2('规则修改成功！','rule_index');
			  }
			  else {
				   $this->message2('规则修改失败：'.$model->getError(),$url);
			  } 
		 }	 	  
	 }
	 public function  rule_del(){ //删除空包类型信息
	 $id=I('id',null);
			if(!empty($id)){
			   if(is_array($id))
			   {
			   	    $where_e = "exp_id in (".implode(',', $id).")";
					$where = "id in  (".implode(',', $id).")";
			   }
			   else
			   	{
			   		$where_e = "exp_id = ".$id;
					$where = " id = ".$id;
			   	}
				//判断是否存在该类型的空包单号
				$count = M('kongbao_type')->where($where_e)->count();
				if($count>0)
				{
					$this->message('该导出规则正在被使用，系统将不能删除!',__URL__.'/rule_index'); 
				}
				$model=new Model('exp_rule');
				if(false!==$model->where($where)->delete()){
					$this->message('删除成功',__URL__.'/rule_index'); 
				}else{
					$this->message('删除失败：'.$model->getError(),__URL__.'/rule_index');
				} 
			}else{
				$this->message('请选择删除对象',__URL__.'/rule_index');
			}
		}
	    private function getHeaderFromFile_kb($rule_id)
		{
			 $rule = M('exp_rule')->where('id='.$rule_id)->find();
			 $exp_rule = json_decode($rule['exp_rule'],true);
			 $header = array();
			 foreach($exp_rule as $k=>$v)
			 {
			 	 $header[] = $v['name'];
			 }
			 return array($header);
		}
		private function getFieldFromFile_kb($rule_id)
		{
			 $rule = M('exp_rule')->where('id='.$rule_id)->find();
			 $exp_rule = json_decode($rule['exp_rule'],true);	
			 //从模板文件获取字段
			 $exp_field_file = "Public/exp_fields.php";
		     $filed_list = include($exp_field_file);
			 $filed_array=array();
			 foreach($filed_list as $key=>$field)
			 {
			 	  $filed_array[$key]=$field['field'];
			 } 
			 $fields="";
			 foreach($exp_rule as $k=>$v)
			 {
			 		 $content=explode(',', $v['content']);
					 if(count($content)>1)
					 {
					 	   $temp="concat(";
						   $temp1 ="";
						    if($v['sign']=='') {$v['sign']=' ';}
					 	    foreach($content as $con)
							 {
							 	  $temp1 .= $filed_array[$con].",'".$v['sign']."',";
							 }
							 $temp1 = rtrim($temp1,"'".$v['sign']."',");
							 $temp .=$temp1.")";
							 $fields .=",".$temp;
					 }  else {
						 $fields .=",".$filed_array[$v['content']];
					 } 
			 }
			 $fields = ltrim($fields,',');
			return $fields;			 		  
		}
	 public function order_exp_new() //会员空包订单批量导出
	 {
	 	   import('ORG.Util.Page');
	 	    $model = new Model('exp_log'); 
			//获取一下上次批量导出时间
			$last_time = M('kongbao_type')->max('last_down_time');
			if(empty($last_time))
			{
				$last_time="系统尚未进行过批量导出！";
			} 
			else
		    {
					$last_time = date('Y-m-d H:i:s',$last_time);
			 }
			$count= $model->count(); 
			$page = new Page($count,15); 
			$show = $page->show();  
		 	$log_list= $model->order('exp_time desc')->limit($page->firstRow.",".$page->listRows)->select(); 
		    $this->assign('show',$show);
			$this->assign('count',$count);
			$this->assign('log_list',$log_list);		 
			$this->assign('last_time',$last_time);		
			 //获取空包类型信息 
			 $type_list = $this->get_kongbao_type(); 
			 $this->assign('type_list',$type_list);			   	
	
	 	    $this->display();
	 }	 
	 public function order_exp_post()
	 {
	 	 if(!IS_AJAX){$this->message2('无效请求',__APP__.'/Admin');}
		 //循环
		 $type_list = M('kongbao_type')->where('state=0')->order('is_true desc,id asc')->select();
		 //本次导出时间
		 $exp_time = time(); 
		 $exp_date = date('Ymd',$exp_time);
         $exp_datetime = date('Y-m-d H:i:s',$exp_time);
		 $exp_date_new = date('YmdHis',$exp_time);
		 $where = array();
		 $where['exp_date'] = $exp_date;
		 $sys_config = M('config')->where('id=1')->find();
		 $sys_config = json_decode($sys_config['kongbao_config'],true);
		 $count = M('exp_log')->where($where)->count();
		 if($count >0 && $sys_config['exp_setting']!=1)
		 {
		 	 $this->ajaxReturn('','今日已经做过批量导出！',0);
		 }
 		include("Public/PHPExcel/PHPExcel.php"); // 生成excel的基本类定义(注意文件名的大小写)
		// 如果直接输出excel文件，则要包含此文件
		include("Public/PHPExcel/PHPExcel/Writer/Excel5.php");
		// 创建phpexcel对象，此对象包含输出的内容及格式
		$m_objPHPExcel = new PHPExcel();	 
		$output_path = "Public/Uploads/kb_log/";
		MkdirAll($output_path);
		 $post_type_id = I('type_id','');
		 foreach($type_list as $k=>$type)
		 {
		 	   if($post_type_id!='')
			   {
			   	    if($type['id'] !=$post_type_id) continue;
			   }
		 	   $type_id = $type['id'];
			   $type_name = $type['name'];
			   $type['last_down_time'] = $type['last_down_time'] +1;
			   $last_datetime = date('Y-m-d H:i:s',$type['last_down_time']); 
			   $last_date = date('YmdHis',$type['last_down_time']); 
			   $filename =  $type_name.'-'.$last_date.'-'.$exp_date_new.".xls";
			   $fileurl = $output_path.md5($type_name.'-'.$last_date.'-'.$exp_date_new).".xls";
			    //获取相应的空包导出数据 
			    $where = array();
				$where['type_id'] = $type_id;
		        $where['order_time']=array('between',array($last_datetime,$exp_datetime));  
		
				$headers = $this->getHeaderFromFile_kb($type['exp_id']);  
		   	    $fields = $this->getFieldFromFile_kb($type['exp_id']);   
			 	$exp_list_temp= M('kongbao_order')->field($fields)->where($where)->order('note_no asc,order_time desc')->select();
		        $exp_list = array();
				$i=0;
				foreach($exp_list_temp as $k=>$v)
				 { 
				 	   $i++;
					   if(isset($v['id'])) $v['id'] = $i;    
					   $exp_list[] = $v;
				 }	  
				 $update_array = array();
				 $update_array['exp_status']=1;
				 M('kongbao_order')->where($where)->data($update_array)->save();
				 //把数据写入文件
				$this->write_xls($m_objPHPExcel,$fileurl,$headers,$exp_list); 
				//生成日志信息
				$logdata = array();
				$logdata['type_id'] = $type_id;
				$logdata['type_name'] = $type_name;
				$logdata['exp_counts'] = count($exp_list);
				$logdata['exp_filename'] = basename($filename);
				$logdata['exp_fileurl'] =  $fileurl; 
				$logdata['last_time'] = $last_datetime;
				$logdata['exp_time'] = $exp_datetime;
				$logdata['exp_date'] = $exp_date;
				M('exp_log')->data($logdata)->add();
				 //更新该类型的上次导出时间
				 $updatedata = array();
				 $updatedata['id'] = $type_id;
				 $updatedata['last_down_time'] =   $exp_time;
				 M('kongbao_type')->data($updatedata)->save();
		 }
          $this->ajaxReturn('','执行完毕！',1);
	 }
     private function write_xls($m_objPHPExcel,$filename,$header,$data_list)
	 {
	 	       $m_objPHPExcel->setActiveSheetIndex(0);  
				$col = 0; //写表格表头
				foreach($header[0] as $header)
				{
					$m_objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $header);
					$col++;					
				} 
				$row = 2;//写表数据 
				if(!empty($data_list))
				{
					foreach($data_list as $data)
					{
						 $col = 0;
						 foreach ($data as $v)
						 {
							$m_objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, " ".$v." ");
							$col++;
						 }
						 $row++;
					}					
				}
				$objWriter = new PHPExcel_Writer_Excel5($m_objPHPExcel);
				$objWriter->save($filename); 
	 }
	public function downfile() //下载文件
	{
		  $id = I('id','');
		  if($id==''){ $this->message2('未指定下载项!',U('order_exp_new')); }
		  $file = M('exp_log')->where('id='.$id)->find();
		  if(empty($file)){$this->message2('指定下载项不存在!',U('order_exp_new'));}
		  $fileurl = $file['exp_fileurl']; 
		  $filename = $file['exp_filename'];
		  if($fileurl =='')
			{
				$this->message2('无效请求!',U('order_exp_new'));
			}	 
			$filepath = $fileurl; 	
			if(!file_exists($filepath))	 
			{
				$this->message2('未找到对应的导出文件！',U('order_exp_new'));	
			}  
			//$file_path = iconv("utf-8",'gb2312',$filepath); 
			$file_path = $filepath; 
            import("ORG.Net.Http");	
			ob_end_clean();
			$download=new Http();	
			$download->download($file_path,$filename);		
	}
 }
?>