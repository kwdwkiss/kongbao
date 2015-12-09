function swaptab(name, cls_show, cls_hide, cnt, cur,exp) {
	var mpre='tab_',spre='div_',mzone={},szone={};
	typeof(exp)=='object'?'':exp={};
	exp.mpre?mpre=exp.mpre:'';
	exp.spre?spre=exp.spre:'';
	for (i = 1; i <= cnt; i++) {
		szone = $('#'+spre + name + '_' + i);
		mzone = $('#'+mpre + name + '_' + i);
		if (i == cur) {
			szone.removeClass('hidden').addClass('block');
			mzone.attr('class', cls_show);
			(exp.ajax==1&&exp.url)&&ajaxTab(spre + name + '_' + i,exp.data,exp.url);
		} else {
			szone.removeClass('block').addClass('hidden');
			mzone.attr('class', cls_hide);
		}
	}
}


