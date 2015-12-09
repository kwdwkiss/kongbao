<?php

Class UserOrderViewModel extends ViewModel
{
    protected $viewFields = array(
	      'pay_order'=>array('id','user_id','user_type','order_no','type','pay_money','comm','status','addtime','outer_order_no','pay_title','order_type'),
	      'user'=>array('username','money','nickname','_on'=>'user.id=pay_order.user_id'),
	      'user_level'=>array('title','_on'=>'user_level.id=pay_order.user_type'), 
	);
}
?>