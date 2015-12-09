<?php
/***
 * 小号业务管理
 *QQ8201009323
*阿里空包制作
*http://www.alikongbao.com
 */
 Class XHAction extends CommonAction
 {
  	public function _initialize() //初始化
	{
		parent::_initialize();
		$this->judge_code_valid(); 
		$config = D('config')->where('id=1')->find();
		//判断是否已经授权
		 if($config['auth_code']=='')
		/* {
				$this->message2('系统尚未授权或者授权已过期(请先进行授权处理)',__APP__.'/Admin/Other/auth');		 	
		 }	*/	
		//获取小号业务配置信息
		$xiaohao_config = json_decode($config['xiaohao_config'],true);
		if($xiaohao_config['valid']==0)
		{
			$this->message2('系统未开启小号业务(如需开启请到业务配置中进行配置)',__APP__.'/Admin/YW/xh_setting');
		} 
	} 	
 	 public function type() //小号类型
	 {
			$model = M('xiaohao_type');
			 
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
		public function type_add() //小号类型新增
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
			$this->display();
		}	 
		 public function type_insert()//小号类型插入操作
		 {
		 	 if(!IS_POST) {$this->message2('无效请求','type');} 
			 $type_model = new XHTypeModel(); 
			 $_POST['config']  = json_encode($_POST['config']);
			   
			 if($data = $type_model->create())
			 {
				  $data['title'] = I('name','');
				  $data['comm'] = I('comm','');
				  $data['state'] = I('state',0);
				  $data['sort_order'] = I('sort_order',0); 
				  $data['config'] =  $_POST['config']; 
					
				  if(false!==$type_model->data($data)->add())
					{
						$this->message2('新增成功','type');
					} else {
						$this->message2('新增失败：'.$type_model->getError(),'type_add');
					}			 	
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
				//获取小号类型信息 
				$type = D('xiaohao_type')->where('id='.$id)->find();
				$config = json_decode($type['config'],true);
				  
				$this->assign('config',$config);
				$this->assign('type',$type);
				$this->display();	 	
		 }
    	public function type_update()//小号类型更新
		 { 
			 if(!IS_POST){$this->message2('无效请求!',__APP__.'/Admin');} 
			  
			    $id=I('id',null); 
				if(!empty($id))
				{ 
					 $model=new XHTypeModel(); 
					 $_POST['config'] = json_encode($_POST['config']);
					    	
					if($data=$model->create())
					{
					      $data['config'] = $_POST['config'] ;  
							if(false!==$model->where('id='.$id)->data($data)->save()){
								$this->message('编辑成功',__URL__.'/type');
							}else{
								$this->message('编辑失败：'.$model->getError(),__URL__.'/type');
							 } 
					}
					else {
						$this->message('编辑失败：'.$model->getError(),__URL__.'/type');
					} 
				}else{ 
					$this->message('请选择编辑对象',__URL__.'/type');
				} 
    		}	
		   public function  type_del() //删除小号类型信息
		   {
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
						//判断是否存在该类型的小号
						$count = M('xiaohao')->where($where_e)->count();
						if($count>0)
						{
							$this->message('已经存在该类型的小号，系统将不能删除!',__URL__.'/type'); 
						}
						$model=new XHTypeModel();
						if(false!==$model->where($where)->delete()){
							$this->message('删除成功',__URL__.'/type'); 
						}else{
							$this->message('删除失败：'.$model->getError(),__URL__.'/type');
						} 
					}else{
						$this->message('请选择删除对象',__URL__.'/type');
					}
	    }	 
		public function index() //小号管理列表
		{
				import('ORG.Util.Page');
				//获取小号类型信息 
				$type_list = $this->get_xiaohao_type(); 
				$this->assign('type_list',$type_list);
				//获取单据信息
		 	    $xh_model = new XiaohaoViewModel(); 
				
				$keyword=I('keyword');
				$ftype=I('ftype'); 
				if(!empty($keyword) && !empty($ftype)){
					$where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
				 $type_id=I('type_id',''); 
				 if($type_id!='') { $where['type_id']=$type_id; } 
				 $where['isused']=0; 
				$count= $xh_model->where($where)->count();
				$page = new Page($count,15);
			 
				$show = $page->show(); 
			 	$xiaohao= $xh_model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
	
				$this->assign('count',$count);
				$this->assign('show',$show);
				$this->assign('xiaohao_list',$xiaohao);	
				$this->display();  
		}
		public function edit() //小号编辑
		{
			//获取小号类型
			$type_list = $this->get_xiaohao_type(); 
			$this->assign('type_list',$type_list); 
			$id = I('id',null);
			if(!empty($id))
			{
				$xiaohao = M('xiaohao')->where('id='.$id)->find();
				$this->assign('xiaohao',$xiaohao);
			}
			else{
				$this->message2('请选择要编辑的项目','index');
			}
			$this->display();
		}
		public function update()//小号信息编辑处理
		{
			   if(!IS_POST){$this->message2('非法操作!',__APP__.'/Admin');} 
				$id=I('id',null);
				if(!empty($id))
				{ 
					$model=new XiaohaoModel();  
					
					if($data=$model->create())
					{
							if(false!==$model->save()){
								$this->message('编辑成功',__URL__.'/index');
							}else{
								$this->message('编辑失败：'.$model->getError(),__URL__.'/index');
							} 						
					} 
					else {
						$this->message('编辑失败：'.$model->getError(),__URL__.'/index');
					} 
				}else{
	
					$this->message('请选择编辑对象',__URL__.'/index');
				}			
		}	
		public function add() //小号新增
		{
			//获取小号类型
			$type_list = $this->get_xiaohao_type(); 
			$this->assign('type_list',$type_list);
			
			$this->display();
		} 	
		public function insert() //小号单个插入操作
		{
			if(!IS_POST) {$this->message2('无效请求','add');}
			 $model = new XiaohaoModel();  
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
					 $this->ajaxReturn('',"小号不能为空！",0);
				}
				$note_nos = trim($note_nos);
				if($note_nos =='')
				{
					 $this->ajaxReturn('',"小号不能为空！",0);
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
					  $count = M('xiaohao')->where($where)->find();
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
				$xiaohao = new XiaohaoModel();
				if(!empty($insert_data)) //如果插入数据不为空时 对数据进行处理
				{ 
					   foreach($insert_data as $data)
					   {
					   	  $id = $xiaohao->data($data)->add();
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
		public function del() //小号删除
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
	    		$xiaohao=new XiaohaoModel();
	    		if(false!==$xiaohao->where($where)->delete()){
	    			$this->message('删除成功',__URL__.'/index');
	
	    		}else{
	    			$this->message('删除失败：'.$xiaohao->getError(),__URL__.'/index');
	    		}
	    	}else{
	    		$this->message('请选择删除对象',__URL__.'/index');
	    	}
		}
		public function used_index() //已用小号
		{
				import('ORG.Util.Page');
				//获取小号类型信息 
				$type_list = $this->get_xiaohao_type(); 
				$this->assign('type_list',$type_list);
				//获取单据信息
		 	    $xh_model = new XiaohaoViewModel(); 
				
				$keyword=I('keyword');
				$ftype=I('ftype');
				if(!empty($keyword) && !empty($ftype)){
					$where[$ftype]=array('like','%'.$keyword.'%'); 
				} 
				 $type_id=I('type_id',''); 
				 if($type_id!='') { $where['type_id']=$type_id; } 
				 $where['isused']=1; 
				$count= $xh_model->where($where)->count();
				$page = new Page($count,15);
			 
				$show = $page->show(); 
			 	$xiaohao= $xh_model->where($where)->order('addtime desc')->limit($page->firstRow.",".$page->listRows)->select();
	
				$this->assign('count',$count);
				$this->assign('show',$show);
				$this->assign('xiaohao_list',$xiaohao);	
				$this->display();  			
		}
	     public function order_index() //小号订单列表
		 {
		 	 import('ORG.Util.Page');
		 	 $model = new XHOrderViewModel();
			 
			 $keyword=I('keyword');
			 $ftype=I('ftype');
			 $where  = array();
			 if(!empty($keyword) && !empty($ftype)){
			 	   if($ftype=='user_id')
				   {
				     	$where[$ftype]=$keyword; 
				   }
				   else {
					   $where[$ftype]=array('like','%'.$keyword.'%'); 
				   } 
			  }   
			    $type_id = I('type_id','');
				if($type_id !='') $where['type_id'] = $type_id;
				$count= $model->where($where)->count();
				$page = new Page($count,15);
			 
				$show = $page->show(); 
			 	$order= $model->where($where)->order('order_time desc')->limit($page->firstRow.",".$page->listRows)->select();
				//获取小号类型信息 
				$type_list = $this->get_xiaohao_type(); 
				$this->assign('type_list',$type_list);		 
			    $this->assign('show',$show);
				$this->assign('count',$count);
				$this->assign('order_list',$order);
		 	   $this->display();	 	    
		 }
		private function get_xiaohao_type() //获取小号类型
		{
			$type_model = new Model('xiaohao_type');
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
 }
?>