<?php
error_reporting(0);
require_once 'config.php';

session_start();
header("Content-type:text/html;charset=gb2312");

$memo="�ð���";  //��������֧�������󶨵��Ա��˺�
$user=$_SESSION['userid'];

$pay_money = $_POST['payAmount'];
$order_no = date('ymdHis') . rand(10, 99);
$type = 1;
$addtime = date('Y-m-d H:i:s', time());
$comm = "alipay";
$insert = "INSERT INTO pay_order (user_id,order_no,pay_money,type,addtime,comm) VALUES ('$user','$order_no','$pay_money','$type','$addtime','$comm')";
$xr = mysql_query($insert);
if(!$xr)
	exit('����');

$money=htmlspecialchars($_POST['payAmount']);
$pay=$_POST['optEmail'];
$url="http://pay.336hyw.com/api.php?optEmail=".$pay."&title=".$order_no."&memo=".$memo."&payAmount=".$money;
$urlurl="http://pay.336hyw.com/apiapi.php?title=".$user;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>����֧��</title>
	<style type="text/css">
<!--
* { margin:0; padding:0; font-size:14px; font-weight:normal; font-style: normal;}
.jj  { font-weight:bolder!important; }
.box { font-weight:bolder!important; }
.pr  { font-weight:bolder!important; }
*.box { border-top-color:#c00!important; }
*.pr { color:#060!important; }
#content{margin:0 auto;width:960px;padding: 5px;border: 5px solid;border-color: #66C2FF;margin-top:10px;} 
#tab01 {position:relative; width:900px; height:450px; padding-top:15px; margin:5px;margin-left:65px; overflow:hidden; }
#tab01 h3 {font-size:18px;position:relative; z-index:2; float:left; height:23px; padding:4px 7px 0 8px; margin-left:-1px; border-left:solid 1px #ccc; border-right:solid 1px #fff; text-align:center; background:#fff; cursor:pointer; }
#tab01 h3.up { height:31px; padding:7px 7px 0 7px; margin:-6px 0 0 0; border:solid #ccc; border-width:1px 1px 0; color:#c00; }
#tab01 i {font-family: "΢���ź�",Arial, Helvetica, sans-serif;display:none; position:absolute; left:0; top:47px; z-index:1; width:824px; height:454px; padding:5px; border:solid 1px #ccc; color:#666; }
#tab01 i.up { display:block; }
ul, li {list-style: none;display: block;}
  
-->
</style>
</head>
<body>




<div id="content">
<div id="tab01">



		
	<h3 class="box">֧����ɨ�븶��</h3>
		<i>
		
		<ul style="float:left;width:400px;margin-left:10px;font-size:14px;margin-top:20px;">
					<li><span class="bdr" style="color: #060;">ɨ�������д���ͱ�ע�������뽫�����Զ���ɳ�ֵ</span></li>
					<li><br></li>
					<li><span class="bdl">���&nbsp;&nbsp;</span><span style="color: #E30074;"><em><?php echo $money;?></em></span></li>
					<li><span class="bdl">��ע&nbsp;&nbsp;</span><span style="color: #E30074;"><em><?php echo $order_no;?></em></span><br><br></li>
					<li><span class="bdr"><img src="code.png"  width="150" height="150"/></span></li>
					<li><span class="bdr" style="color: #060;">ɨ�������д��ע��<span style="color: #E30074;"><em><?php echo $order_no;?></em></span>���������뽫�����Զ���ɳ�ֵ</span></li>
	    </ul>		
		<ul style="float:right;width:400px;margin-left:10px;font-size:14px;margin-top:20px;">			
			        <li>
					<div style="text-align:center; height:30px; line-height:30px; font-size:14px;color:#F00;">ɨ�븶��ʾ��ͼ</div >
                    <div style="background:url(http://pay.336hyw.com/ceshi/images/alipay.png) no-repeat; width:400px; height:360px;">
                    <div style="color: #E30074;font-size:16px; margin-left:130px; padding-top:65px;"><?php echo $money;?></div>
                    <div style="color: #E30074;font-size:16px; margin-left:130px; padding-top:55px;"><?php echo $order_no;?></div></div>
                    </li>	
			
		</ul>	
		</i>
		
		
	<h3 class="box">֧������ҳ���߸���</h3>
		<i><ul style="float:left;width:320px;margin-left:10px;font-size:14px;margin-top:20px;">
				    <li style="height:25px; line-height:25px; font-size:15px;color:#060 ">����1.����·���ť��¼֧�����󷵻ر�ҳ�档(֧��������Ѿ���¼�˵Ŀ������˲�) </li> 
					<li style="height:25px; line-height:25px; font-size:15px;color:#f00 ">(֧��������Ѿ���¼�˵Ŀ������˲�) </li> 
					<li><a href="http://www.alipay.com" target="_blank"><img src="http://pay.336hyw.com/ceshi/images/sub1.png"  width="150" height="30"/></a></li>
					 <li><br></li>
				    <li style="height:25px; line-height:25px; font-size:15px;color:#060 ">����2.����·���ť�󼴿ɵ����ֵҳ�档 </li> 
					<li><a href="<?php echo $url;?>" target="_blank"><img src="http://pay.336hyw.com/ceshi/images/sub2.png"  width="150" height="30"/></a></li>
					 <li><br></li>
					<li style="height:25px; line-height:25px; font-size:15px;color:#F00 ">������ɺ��Զ�Ϊ����ֵ��</li> 
					 </ul>
					
					<ul style="float:right;width:480px;margin-left:10px;font-size:14px;margin-top:20px;">
                    <li>
					<div style="text-align:center; height:30px; line-height:30px; font-size:14px;color:#F00 ">֧��������ʾ��ͼ</div>
                    <div style="background:url(http://pay.336hyw.com/ceshi/images/alipayy.jpg) no-repeat; width:450px; height:300px;  ">
                    <div style=" font-size:16px; margin-left:180px; padding-top:120px;"><?php echo $pay;?></div>
                    <div style=" font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $money;?></div>
                    <div style=" font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $order_no;?></div></div>
					</li>
 
         
                </ul></i>
		
		
 	<h3 class="pr">֧�����ֶ�ת�˸���</h3>
		<i><ul style="float:left;width:380px;margin-left:10px;font-size:14px;margin-top:20px;">
	                <li><p style="font-size:14px; color:#666; padding-left:10px; line-height:25px;">���ֶ������տ����˺ź͸���˵������������֧��������ҳ��,������ɼ����Զ�����!</p></li>
                    <li><br></li>
                    <li><span class="bdl">�տ��˺�&nbsp;&nbsp;</span><span style="color: #E30074;"><input style="color: #E30074;width:250px;" type="text" value="<?php echo $pay;?>"></span></li>
                    <li><br></li>
					<li><span class="bdl">֧�����&nbsp;&nbsp;</span><span style="color: #E30074;"><input type="text" value="<?php echo $money;?>"></span></li>
                    <li><br></li>
					<li><span class="bdl">����˵��&nbsp;&nbsp;</span><span style="color: #E30074;"><input type="text" value="<?php echo $order_no;?>"></span></li>
					<li><br></li>
					<li><a href="<?php echo $urlurl;?>" target="_blank"><img src="http://pay.336hyw.com/ceshi/images/sub1.png"  width="150" height="30"/></a></li>
					 </ul>
					
					<ul style="float:right;width:420px;margin-left:10px;font-size:14px;margin-top:20px;">
                    <li>
					<div style="text-align:center; height:30px; line-height:30px; font-size:14px;color:#F00 ">֧��������ʾ��ͼ</div>
                    <div style="background:url(http://pay.336hyw.com/ceshi/images/alipay.jpg) no-repeat; width:420px; height:300px;  ">
                    <div style="color: #E30074;font-size:16px; margin-left:180px; padding-top:120px;"><?php echo $pay;?></div>
                    <div style="color: #E30074;font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $money;?></div>
                    <div style="color: #E30074;font-size:16px; margin-left:180px; padding-top:55px;"><?php echo $order_no;?></div></div>
					</li>
 
         
                </ul></i>

 
 
 
 

</div>
</div>


<script type="text/javascript">
<!--
function Pid(id,tag){
	if(!tag){
		return document.getElementById(id);
		}
	else{
		return document.getElementById(id).getElementsByTagName(tag);
		}
}

function tab(id,hx,box,iClass,s,pr){
	var hxs=Pid(id,hx);
	var boxs=Pid(id,box);
	if(!iClass){ // �����ָ��class����
		boxsClass=boxs; // ֱ��ʹ��box��Ϊ����
	}
	else{ // ���ָ��class����
		var boxsClass = [];
		for(i=0;i<boxs.length;i++){
			if(boxs[i].className.match(/\btab\b/)){// �ж�������classƥ��
				boxsClass.push(boxs[i]);
			}
		}
	}
	if(!pr){ // �����ָ��Ԥչ����������
		go_to(0); // Ĭ��չ������
		yy();
	}
	else {
		go_to(pr);
		yy();
	}
	function yy(){
		for(var i=0;i<hxs.length;i++){
			hxs[i].temp=i;
			if(!s){// �����ָ���¼�����
				s="onmouseover"; // ʹ��Ĭ���¼�
				hxs[i][s]=function(){
					go_to(this.temp);
				}
			}
			else{
				hxs[i][s]=function(){
					go_to(this.temp);
				}
			}
		}
	}
	function go_to(pr){
		for(var i=0;i<hxs.length;i++){
			if(!hxs[i].tmpClass){
				hxs[i].tmpClass=hxs[i].className+=" ";
				boxsClass[i].tmpClass=boxsClass[i].className+=" ";
			}
			if(pr==i){
				hxs[i].className+=" up"; // չ��״̬������
				boxsClass[i].className+=" up"; // չ��״̬������
			}
			else {
				hxs[i].className=hxs[i].tmpClass;
				boxsClass[i].className=boxsClass[i].tmpClass;
			}
		}
	}
}
tab("tab01","h3","i","","onclick",0); tab("tab02","h4","ol");tab("tab03","h3","i","tab"); 
//-->
</script>


</body>
</html>