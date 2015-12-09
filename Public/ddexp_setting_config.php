<?php
    return array(  
   							 array('title'=>'快递名','name'=>'type_name'),
                             array('title'=>'单号','name'=>'note_no'),
                             array('title'=>'发件地址','name'=>"concat(send_addr,'，',send_zipcode,'，',send_name,'，',send_shouji,'，',send_phone)"),
                             array('title'=>'收件地址','name'=>"concat(rec_name,'，',rec_shouji,'，',rec_phone,'，',rec_addr,'，',rec_zipcode)"),
                              array('title'=>'收件人','name'=>'rec_name'),
                              array('title'=>'收件人电话','name'=>'rec_shouji'),
                              array('title'=>'发件人','name'=>'send_name'),
                              array('title'=>'发件人电话','name'=>'send_shouji'),             
                              array('title'=>'重量','name'=>'weight'),   
                              array('title'=>'运费','name'=>'yunfei'),      
                              array('title'=>'时间','name'=>'order_time'),
                             array('title'=>'商品名称','name'=>'goods_name'),
		             ) ; 
?>