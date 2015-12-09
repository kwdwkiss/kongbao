<?php
/**
 * 小号类型设置
 */
class XHTypeModel extends Model{
    //表单验证
    protected $tableName="xiaohao_type";
    protected $_validate=array(
      array('name','require','请输入类型名称！',1,'regex',3),
      array('name','','类型已经存在',1,'unique',1),
      array('config','require','请填写类型配置信息！',1,'regex',1), 
    ); 
}
?>