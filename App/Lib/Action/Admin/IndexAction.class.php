<?php
//dezend by 辰梦大人 QQ:30881917
class IndexAction extends CommonAction
{
	public function index()
	{
		$this->judge_code_valid();
		$this->display();
	}
}

?>
