<?php

class AdvModel extends Model{
      protected $_validate=array(
      array('typeid','require','请输入类型！',1,'regex',3),
      array('pic_url','require','请输入链接地址',1,'regex',1),
      array('pic','require','请输入图片信息',1,'regex',1),
    );
}

?>