//单笔购买小号
function chaxiaohao(id){
	var Rand = Math.random(); 
	$.layer({
		type : 2,
		title : ['查询小号',true],
		iframe : {src : 'index.php?m=member&c=buy&a=chaxiaohao&id='+id+'&rand='+Rand},
		area : ['580px' , '180px'],
		offset : ['260px', ''],
		close : function(index){
			layer.close(index);
		}
	});
}
function buygoumai(id,unit,usernote,username,groupname){
	var Rand = Math.random();  
	var result = confirm('您购买此小号需要'+unit+"元，您确定要购买吗?")
		if (username==''){
		 alert('请登录后操作');
        }
	if (result){
			$.ajax({
		   url:"index.php",
		   type:'post',
		   data:'m=member&c=buy&a=goumai&unit='+unit+'&usernote='+usernote+'&username='+username+'&id='+id+'&rand='+Rand,
		   async: true,//设置为异步
		   //dataType:'json',
		   success:function(json)
		   {
			   json = eval('(' + json + ')');  
               var str=json.code;
			
			  if (str==200){
				  alert('购买成功（请到管理中心->左侧栏目->我购买的小号中查看）',1);
				$('#tr'+id).css('display','none');
			  }else if(str==404){
			       alert('购买失败,请检查余额是否充足',0);
				   window.location.href='deposit_pay_1';
			  }else if(str==400){
				  alert('小号ID为'+id+'购买失败,请重试',0);
				  parent.location.reload(); 
			  }
		   }
		   });
	}
}

//设置默认发货地
function morenfd(id){
	var Rand = Math.random(); 
	var result = confirm('您确定要将此地址设置为默认发货地吗？')
	if (result){	
        $.ajax({
        url:"index.php",
        type:'get',
        data:'m=member&c=buy&a=morenfd&id='+id+'&Rand='+Rand,
        //async: true,//设置为异步
        //dataType:'json',
		success:function(content){
			alert('设置成功' , 5, 1);
			parent.location.reload();//刷新
		}
	}); 
	}
}
//删除发货地
function Delete(id){
	var Rand = Math.random(); 
	var result = confirm('您确定要删除此发货地吗,删除后将无法恢复？')
	if (result){	
        $.ajax({
        url:"index.php",
        type:'get',
        data:'m=member&c=buy&a=delete&id='+id+'&Rand='+Rand,
        //async: true,//设置为异步
        //dataType:'json',
		success:function(content){
			alert('成功删除一条发货地址' , 5, 1);
			parent.location.reload();//刷新
		}
	}); 
	}
}

//批量购买（入ID，进行购买）
function pi_buy(id){
	var varid=$(".var"+id).val();//获取JSON
	var varid=eval('('+varid+')');//然后转成对象
	//id,unit,usernote,username,groupname
	var id=varid.id;
	var unit=varid.jiage;
	var usernote=varid.usernote;
	var username=varid.username;
	var groupname=varid.groupname;
    var Rand = Math.random();  

		if (username==''){
		 alert('请登录后操作');
        }

			$.ajax({
		   url:"index.php",
		   type:'post',
		   data:'m=member&c=buy&a=goumai&unit='+unit+'&usernote='+usernote+'&username='+username+'&id='+id+'&rand='+Rand,
		   //async: true,//设置为异步
		   //dataType:'json',
		   success:function(json)
		   {
			   json = eval('(' + json + ')');  
               var str=json.code;
			
			  if (str==200){
				  // alert('购买成功（请到管理中心>>左侧>>已购买单号中查看）');
				$('#tr'+id).css('display','none');
			  }else if(str==404){
			       alert('购买失败,请检查余额是否充足',0);
				   window.location.href='deposit_pay_1';
			  }else if(str==400){
				  alert('小号ID为'+id+'购买失败,请重试',0);
				  //parent.location.reload(); 
			  }

		   }
		   });

		   //return r;

}



//批量购买
function  piliangbuy(){
var ssss=$("input[name=baobei]:checked").attr("checked");//选中的
var result = confirm('您确定要批量购买选中的淘宝小号吗？');

      if (!ssss){
		   alert('亲，请最少勾选一个淘宝小号哦');
		 }

      if (result){

				if (!ssss){
					//alert('没选中');
							 $("input[name=baobei]:checked").each(function(){
								var aa=$(this).attr("id");
									if (!isNaN(aa)){
										//没选中不操作
									}
							  })
				}else{
				//alert('选中啦');
							$("input[name=baobei]:checked").each(function(){
								var aa=$(this).attr("id");
									if (!isNaN(aa)){
										pi_buy(aa);
									 }
							  })
				alert('购买成功（购买成功（请到管心->左侧栏目->我购买的小号查看）',1);
			   // window.location.href='index.php?m=member&c=buy&a=buylist&p=1';
				document.getElementById('tipct').style.display='';
                setTimeout("window.location.href='trumpetlist-1.html'",3600)
								 
			   }
             
		}

}

//全选/取消JS
function SelectAll() {
 var checkboxs=document.getElementsByName("baobei");
 for (var i=0;i<checkboxs.length;i++) {
  var e=checkboxs[i];
  e.checked=!e.checked;
 }
}