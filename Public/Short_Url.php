<?php
    class Short_Url {
    	 
	      public function url_short($url, $key = '1335371408')
		 {
                    $opts['http'] = array('method' => "GET", 'timeout'=>60,);
				    $context = stream_context_create($opts);
				    $url = "http://api.t.sina.com.cn/short_url/shorten.json?source=$key&url_long=$url";
				    $html =  @file_get_contents($url,false,$context);
				    $url = json_decode($html,true);
				    if (!empty($url[0]['url_short'])) 
				    {
				        return $url[0]['url_short'];
				    }
     		 }
	}
?>