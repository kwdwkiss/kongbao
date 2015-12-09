var currentindex = 1;
var flash_len = $("#flash a").length;
$("#flashBg").css("background-color", $("#flash1").attr("name"));
$("#flash a:gt(0)").hide();
function changeflash(i) {	
	currentindex=i;
	$("#flash"+i).fadeIn("normal").css("display","block").siblings("a").css("display","none");
	$(".flash_bar > span").removeClass();
	$("#f"+i).addClass("dq").siblings().addClass("no");
}

function startAm() {
	timerID = setInterval('timer_tick()', 5000);
}

function stopAm() {
    clearInterval(timerID);
}

function timer_tick() {
	currentindex=currentindex>=flash_len?1:currentindex+1;
	changeflash(currentindex);
}

$(document).ready(function () {
    $(".flash_bar span,#flash a").mouseover(function () {
        stopAm();
    }).mouseout(function () {
        startAm();
    });

	startAm();
});