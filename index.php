<?php
/**
 * 系统单一入口文件
 * 李强 20140310
 */
 //if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
 /**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
   define ( 'APP_DEBUG', false);
/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
   define('APP_NAME','App');
   define('APP_PATH','./App/'); 
  /**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
   define ( 'RUNTIME_PATH', './App/Runtime/' ); 
   include './ThinkPHP/ThinkPHP.php';
?>
