// 导航栏配置文件
var outlookbar=new outlook();
var t;

t=outlookbar.addtitle('基本设置','系统设置',1)
outlookbar.additem('管理首页',t,'../Public/manframe')
outlookbar.additem('系统设置',t,'../Config/config')
outlookbar.additem('更改密码',t,'../Config/repass')   

t=outlookbar.addtitle('其他','系统设置',1)
outlookbar.additem('模板管理',t,'../Other/moban')
outlookbar.additem('数据库管理',t,'../Other/database')

t=outlookbar.addtitle('退出系统','系统设置',1)
outlookbar.additem('退出登录',t,'../Public/logout')
 
 
t=outlookbar.addtitle('业务设置','业务管理',1) 
outlookbar.additem('业务设置',t,'../YW/kb_setting')
outlookbar.additem('页面设置',t,'../YW/pagesetting')

t=outlookbar.addtitle('空包管理','业务管理',1) 
outlookbar.additem('空包类型',t,'../KB/type')
outlookbar.additem('空包管理',t,'../KB/index')
outlookbar.additem('空包订单',t,'../KB/order_index')
outlookbar.additem('底单管理',t,'../KB/dd_index')
outlookbar.additem('代理文件',t,'../KB/uploadfiles')
 
t=outlookbar.addtitle('小号管理','业务管理',1) 
outlookbar.additem('小号类型',t,'../XH/type')
outlookbar.additem('小号管理',t,'../XH/index')  
outlookbar.additem('小号订单',t,'../XH/order_index')
   
t=outlookbar.addtitle('会员管理','会员管理',1)
outlookbar.additem('会员设置',t,'../User/setting') 
outlookbar.additem('会员列表',t,'../User/index')
outlookbar.additem('会员订单',t,'../Order/buy_order') 

t=outlookbar.addtitle('账户管理','会员管理',1)
outlookbar.additem('账户日志',t,'../User/account_log')
outlookbar.additem('会员提现',t,'../User/tixian_index')  
 
t=outlookbar.addtitle('文章管理','网站管理',1)
outlookbar.additem('类型管理',t,'../ArticleType/index') 
outlookbar.additem('文章管理',t,'../Article/index')
outlookbar.additem('公告管理',t,'../Gonggao/index')
outlookbar.additem('帮助中心',t,'../Help/index')
outlookbar.additem('关于我们',t,'../About/index')

t=outlookbar.addtitle('客服管理','网站管理',1) 
outlookbar.additem('客服管理',t,'../Kefu/index')

t=outlookbar.addtitle('网站统计','网站管理',1) 
outlookbar.additem('网站统计',t,'../Other/tongji')

t=outlookbar.addtitle('友情链接','网站管理',1) 
outlookbar.additem('友情链接',t,'../Link/index')
   
t=outlookbar.addtitle('广告设置','网站管理',1) 
outlookbar.additem('广告管理',t,'../Adv/index')
 
