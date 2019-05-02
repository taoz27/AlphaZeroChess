;(function() {
'use strict';

window['PgnBoard']=window['PgnBoard']||function(divElId,onclickFunc){
'use strict';

var divEl;
var widget={};

var divElWidth,divElHeight;
var height;
var width1,width2,width3;

var datas=[];

var CSS={
	//最外围的div
	pgnboard:'pgnboard-taoz27 pre-scrollable',
	pgnboardid:'pgnboard-taoz27',
	pgnboardstyle:'OVERFLOW-X:hidden;border:2px solid #a1a1a1;border-radius:5px;',
	//内部显示走子的一系列button
	contentboard:'contentboard-taoz27 row btn-group',
	contentboardstyle:'border:2px solid #a1a1a1;border-radius:5px',
	contentboardid:'contentboard-taoz27',
	controlboard:'controlboard-taoz27 btn-group',
	//button
	buttonhid:'button-taoz27-h-',
	buttonlid:'button-taoz27-l-',
	buttonrid:'button-taoz27-r-',
}

function getDivEl(){
	if (typeof divElId === 'string') {
		// cannot be empty
		if (divElId === '') {
		  window.alert('PgnBoard Error 1001: ' +
			'The first argument to PgnBoard() cannot be an empty string.' +
			'\n\nExiting...');
		  return false;
		}
		// make sure the container element exists in the DOM
		var el = document.getElementById(divElId);
		if (! el) {
		  window.alert('PgnBoard Error 1002: Element with id "' +
			divElId + '" does not exist in the DOM.' +
			'\n\nExiting...');
		  return false;
		}
		divEl = $(el);
	}
	return true;
}

function canculateSize(){
	divElWidth=parseInt(divEl.css('width'), 10);
	divElHeight=parseInt(divEl.css('height'),10);
	console.log("div height:"+divElHeight);
	
	height=divElHeight;//-100;
	
	divElWidth-=divElWidth/10;
	var temp=divElWidth/20;
	width1=temp*2;width2=temp*9;width3=temp*4;
}

function createBoardHtml(){
	canculateSize();
	
	var html='<div class="'+CSS.pgnboard+'" id="'+CSS.pgnboardid+'" ';
	html+='style="'+CSS.pgnboardstyle+'height:'+height+'px;">';
	
	html+='<div class="'+CSS.contentboard+'" id="'+CSS.contentboardid+'" ';
	html+='style="'+CSS.pgnboardstyle+'" ></div>';
	
	html+='</div>';
	
	
	/*
	html+='<div class="'+CSS.controlboard+'">';
	var temp={0:"<<",1:"<",2:"||",3:">",4:">>"};
	for(var i=0;i<5;i++){
		html+='<button type="button" class="btn btn-primary" style="width:'+width3+'px;" id="control'+i+'">'+temp[i]+'</button>';
	}
	html+='</div>';
	*/
	
	return html;
}

widget.resize=function(){
	$("#"+CSS.contentboardid).html("");
	var html='';
	for(var i=0;i<datas.length;i++){
		/**
		<button type="button" class="btn " style="width:50px;">1</button>
		<button type="button" class="btn btn-secondary" style="width:180px;">主要按钮</button>
		<button type="button" class="btn btn-ligth text-dark" style="width:180px;">次要按钮</button>
		**/
		html+='<button type="button" class="btn " id="'+CSS.buttonhid+(i+1)+'" style="width:'+width1+'px;">'+(i+1)+'</button>';
		html+='<button type="button" class="btn contentboard btn-secondary" id="'+CSS.buttonlid+(i+1)+'" style="width:'+width2+'px;" data-fen="'+datas[i].f1+'">'+datas[i].s1+'</button>';
		html+='<button type="button" class="btn contentboard btn-ligth text-dark" id="'+CSS.buttonrid+(i+1)+'" style="width:'+width2+'px;" data-fen="'+datas[i].f2+'">'+datas[i].s2+'</button>';
	}
	$("#"+CSS.contentboardid).html(html);
	
	//设法让滚动条滑动到底部，但目前没有成功
	var scdiv=$("#"+CSS.pgnboardid);
	scdiv.scrollTop=scdiv.scrollHeight;
	$("#"+CSS.pgnboardid).attr('scrollTop',$("#"+CSS.pgnboardid)[0].scrollHeight);
	
	$(".contentboard").on("click",function(){
		btnOnClick($(this).attr('data-fen'));
	});
}

//添加按钮
widget.add=function(step1,fen1,step2,fen2){
	if(step1==null&&step2==null)return;
	step1=step1==null?"":step1;
	step2=step2==null?"":step2;
	datas.push({s1:step1,f1:fen1,s2:step2,f2:fen2});
	
	widget.resize();
}
//改变通常都是改变右侧的，因为它默认是空
widget.change=function(step2,fen2){
	if(datas.length<1)return;
	if(step2!==null)datas[datas.length-1].s2=step2;
	datas[datas.length-1].f2=fen2;
	
	widget.resize();
}
//移除最后一个
widget.remove=function(){
	datas.pop();
	
	widget.resize();
}
//移除所有
widget.removeAll=function(){
	datas=[];
	
	widget.resize();
}

function initDom(){
	divEl.html(createBoardHtml);
	widget.resize();
}

function btnOnClick(fen){
	if(typeof onclickFunc !== "function")return;
	onclickFunc(fen);
}

function init(){
	if(getDivEl()!==true)return;
	initDom();
}
init();

return widget;
}

})();