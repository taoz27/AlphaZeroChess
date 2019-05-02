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
var start=false;
var myturn='w';
var end='u';
var addr=window.location.pathname.substring(1);

/*
info{
	w:name;
	b:name;
	end:w/b/u;
	turn:w/b;
	curpos:start/fen;
}
/**/
getpgn.onreadystatechange=function(){
	if(getpgn.readyState==4&&getpgn.status==200){
		var res=getpgn.responseText;
		console.log("get pgn:"+res);
		info=JSON.parse(res);
		if(info.end=='u')start=true;
		else game.game_over=true;
		$("#whitename").html(info.whitename);
		$("#blackname").html(info.blackname);
		if(info.curpos!=="start"){
			game.load(info.curpos);
			board.position(info.curpos);
		}
		if(info.idw==-1)
			myturn='b';
		game.myturn=info.turn;
		updateStatus();
	}
}
{getpgn.open("POST","home/getinfo",true);
getpgn.setRequestHeader("Content-type","application/x-www-form-urlencoded");
getpgn.send("addr="+addr);
}

var board,pgnboard,game = new Chess();
  
var getaimove=new XMLHttpRequest();
getaimove.onreadystatechange=function(){
	if (getaimove.readyState==4 && getaimove.status==200){
		var res=getaimove.responseText;
		console.log("get good res:"+res);
		var jsonres=JSON.parse(res);
		var test=game.move(jsonres.move,{sloppy: true});
		if(test===null)console.log("illegal");
		var fen=game.fen();

		if(myturn=='w')
			pgnboard.change(jsonres.move,fen);
		else
			pgnboard.add(jsonres.move,fen,"","");
		board.position(fen);
		updateStatus();
	}
}
function getBestMove(move){
	getaimove.open("POST","chess/get_bestmove",true);
	getaimove.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	getaimove.send("fen="+game.fen()+"&&move="+move);
}

var updateinfo=new XMLHttpRequest();
function updateGameInfo(){
	updateinfo.open("POST","home/updateinfo",true);
	updateinfo.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	updateinfo.send("id="+info.pgnid+"&&addr="+addr+"&&pgn="+game.pgn()+"&&res="+end+"&&cur="+game.fen()+"&&turn="+game.turn());
}

// do not pick up pieces if the game is over
// only pick up pieces for the side to move
var onDragStart = function(source, piece, position, orientation) {
	if(!start||game.game_over() === true 
		||(game.turn() !== myturn)
		||(piece.indexOf(myturn) !== 0) 
	){
		console.log("can't move:"+start+" "+myturn);
		return false;
	}
};

var lastmove="";
var onDrop = function(source, target) {
  // see if the move is legal
  var move = game.move({
	from: source,
	to: target,
	promotion: 'q' // NOTE: always promote to a queen for example simplicity
  });

  // illegal move
  if (move === null) return 'snapback';

  console.log("turns:"+game.history().length);
  console.log("move:"+JSON.stringify(move));
  lastmove=game.move_to_san(move);
  if(myturn=='w')
  	pgnboard.add(lastmove,game.fen(),"","");
  else
  	pgnboard.change(lastmove,game.fen());
  updateStatus();
};

// update the board position after the piece snap 
// for castling, en passant, pawn promotion
var onSnapEnd = function() {
  board.position(game.fen());
};

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
	if(game.turn()!=myturn)
		getBestMove(lastmove);
};

var btnclick=function(fen){
	//game.load(fen);
	start=(game.fen()==fen);
	board.position(fen);
}

var cfg = {
  draggable: true,
  position: 'start',
  onDragStart: onDragStart,
  onDrop: onDrop,
  onSnapEnd: onSnapEnd
};
board = ChessBoard('board', cfg);

pgnboard= PgnBoard('pgnboard',btnclick);

updateStatus();

</script>
