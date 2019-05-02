<script src="js/chesssocket.js"></script>
<link rel="stylesheet" href="css/chessboard-0.3.0.min.css" />
<div class="container" style="padding:80px 0">
<div class="row" style="margin-top:100px;margin-left:100px;margin-right:100px;">
	<!--显示棋盘-->
	<div class="col-md-8">
		<div id="board" style="width: 500px;margin:0 auto;"></div>
	</div>
	
	<!--显示棋局信息-->
	<div class="col-md-4">
		<p id="statusb"></p>
		<p><h3 id="blackname"></h3></p>
		<div id="pgnboard" class="col-md-12" style="height:300px"></div>
		<p><h3 id="whitename"></h3></p>
		<p id="statusw"></p>
	</div>
</div>
</div>

<script>
var getpgn=new XMLHttpRequest();
var info;
var start=false,myturn='w',othername,end='u';
var addr=window.location.pathname.substring(1);

getpgn.onreadystatechange=function(){
if(getpgn.readyState==4&&getpgn.status==200){
	var res=getpgn.responseText;
	console.log("get pgn:"+res);
	info=JSON.parse(res);
	if(info.idw==userid){
		myturn='w';
		$("#whitename").html(info.whitename);
	}else{
		myturn='b';
		$("#blackname").html(info.blackname);
	}
	if(info.end!='u')
		game.game_over=true;
	if(info.curpos!=="start"){
		game.load(info.curpos);
		board.position(info.curpos);
	}
	game.myturn=info.turn;
	updateStatus();
}
}
{getpgn.open("POST","home/getinfo",true);
getpgn.setRequestHeader("Content-type","application/x-www-form-urlencoded");
getpgn.send("addr="+addr);
}

var board,pgnboard,game = new Chess(),socket;

var updateinfo=new XMLHttpRequest();
function updateGameInfo(){
updateinfo.open("POST","home/updateinfo",true);
updateinfo.setRequestHeader("Content-type","application/x-www-form-urlencoded");
updateinfo.send("id="+info.id+"&&addr="+addr+"&&pgn="+game.pgn()+"&&res="+end+"&&cur="+game.fen()+"&&turn="+game.turn());
}

var updateStatus = function() {
var status = '';
var move=game.turn()==myturn?"you":"the opponet";
if (game.in_checkmate() === true) {
	status = 'Game over, ' + move + ' is in checkmate.';
	end=game.turn()=='w'?'b':'w';
}else if (game.in_draw() === true) {
	status = 'Game over, drawn position';
	end='d';
}else {
	if(game.turn()==myturn)
		status="your turn";
	else
		status="waiting for the opponent";

	// check?
	if (game.in_check() === true) {
	  status += ', ' + move + ' is in check';
	}
}

if(game.turn()==='w'){
	$("#statusw").html(status);
	$("#statusb").html("");
}else{
	$("#statusb").html(status);
	$("#statusw").html("");
}

if(!start)return;
updateGameInfo();
};

var onDragStart = function(source, piece, position, orientation) {
if(!start||game.game_over() === true 
	||(game.turn() !== myturn)
	||(piece.indexOf(myturn) !== 0) 
){
	console.log("can't move:"+start+" "+myturn);
	return false;
}
};
var onDrop = function(source, target) {
var move = game.move({
	from: source,
	to: target,
	promotion: 'q'
});
if (move === null) return 'snapback';
if(myturn=='w')
	pgnboard.add(game.move_to_san(move),game.fen(),"","");
else
	pgnboard.change(game.move_to_san(move),game.fen());
updateStatus();
socket.sendMove(game.move_to_san(move));
};
var onSnapEnd = function() {
  board.position(game.fen());
};
var cfg = {
  draggable: true,
  position: 'start',
  onDragStart: onDragStart,
  onDrop: onDrop,
  onSnapEnd: onSnapEnd
};
board = ChessBoard('board', cfg);

var btnclick=function(fen){
	//game.load(fen);
	start=(game.fen()==fen);
	board.position(fen);
}
pgnboard= PgnBoard('pgnboard',btnclick);

function onLink(msg){
	console.log(msg);
}
function onReady(name1,name2){
if(name1==username)
	othername=name2;
else
	othername=name1;
if(myturn=='w'){
	$("#blackname").html(othername);
}else{
	$("#whitename").html(othername);
}
start=true;
}
function onReceiveMsg(msg){
	console.log(msg);
}
function onReceiveMove(move){
var test=game.move(move,{sloppy: true});
if(test===null){
	console.log("illegal:"+move);
	//return;
}
var fen=game.fen();
if(myturn=='w')
pgnboard.change(move,fen);
else
pgnboard.add(move,fen,"","");
board.position(fen);
updateStatus();
}
function onDisconnect(msg){
	console.log(msg);
	start=false;
}
function onError(msg){
	console.log(msg);
}
var socCfg={
	name:			username,
	addr:			addr,
	onLink:			onLink,
	onReady:		onReady,
	onReceiveMsg:	onReceiveMsg,
	onReceiveMove:	onReceiveMove,
	onDisconnect:	onDisconnect,
	onError:		onError
};
socket=new ChessSocket(socCfg);

updateStatus();
</script>
