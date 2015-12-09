<?php 

class DaohangModel extends Model{
 protected $_validate=array(
       array('name','require','请填写导航名称',1,'regex',1),
       array('link','require','请填写导航链接',1,'regex',1), 
   ); 
} 
?>