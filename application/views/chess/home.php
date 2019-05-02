<div class="container" style="padding-top:50px;">
<div class="row">
	<div class="col-sm-3" style="padding-top:50px">
		<h2>AlphaZero Chess</h2>
		<strong>20</strong> players
		</br>
		<strong>10</strong> games in play
	</div>
	<div class="col-sm-5">
		<div id="board"></div>
		<div class="row">
			<button type="button" id="setStartBtn" class="btn btn-primary btn-lg btn-block">Start Position</button>
			<button type="button" id="setClearBtn" class="btn btn-primary btn-lg btn-block">Clear All</button>
		</div>
	</div>
	<div class="col-sm-4" style="padding-left:60px;padding-top:50px" >
		<button value="Create a game" onclick="setup(this);return false;" type="button" class="btn btn-primary btn-lg btn-block" name="game"
		data-toggle="modal" data-target="#create_game">Create a game</button>
		<button value="Play with friends" onclick="setup(this);return false;" type="button" class="btn btn-primary btn-lg btn-block" name="friend"
		data-toggle="modal" data-target="#create_game">Play with friends</button>
		<button value="Play with AI" onclick="setup(this);return false;" type="button" class="btn btn-primary btn-lg btn-block" name="ai"
		data-toggle="modal" data-target="#create_game">Play with AI</button>
	</div>
	<p id="error_res"></p>
</div>
</div>
				
<!-- 窗口 -->
<div id="create_game" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<button class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-title">
				<h1 class="text-center" id="modal_creategame_title"></h1>
			</div>
			<div class="modal-body" >
			
			<div id="board2" style="width:200px;float: none;display: block;margin-left: auto;margin-right: auto;"></div>
			
			<div class="row" style="margin-top:20px;"> 
				<div class="col-sm-5">
					<p class="text-center">
						<img class="rounded" src="img/chessselect/b.png" alt="black" name="b" onclick="create_game(this);" data-toggle="tooltip" title="Black" data-placement="bottom"/>
					</p>
				</div>
				<div class="col-sm-2">
					<p class="text-center">
						<img class="rounded" src="img/chessselect/r.png" alt="random" name="r" onclick="create_game(this);" data-toggle="tooltip" title="Random" data-placement="bottom"/>
					</p>
				</div>
				<div class="col-sm-5">
					<p class="text-center">
						<img class="rounded" src="img/chessselect/w.png" alt="white" name="w" onclick="create_game(this);" data-toggle="tooltip" title="White" data-placement="bottom"/>
					</p>
				</div>  			
			</div>
			<div class="progress" id="pgs" hidden>
				<div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
			</div>
			</div>
		</div>
	</div>
</div>

<script>
$(function () { $("[data-toggle='tooltip']").tooltip(); });

var xmlhttp=new XMLHttpRequest();
xmlhttp.onreadystatechange=function(){
	if (xmlhttp.readyState==4 && xmlhttp.status==200){
		var res=xmlhttp.responseText;
		console.log("get good res:"+res);
		var code=JSON.parse(res);
		if(code.type==="success"){
			window.location.href = 'http://localhost/'+code.addr;
		}else{
			$("#error_res").html(code.msg);
		}
	}
}

var create_game_type=null;
var select=null;
var setup=function(obj){
	$("#pgs").prop("hidden",true);
	create_game_type=obj.name;
	$("#modal_creategame_title").html(obj.value);
	if(create_game_type==="game")
		board2.start(false);
	else
		board2.position(board.position(),false);
}

var repeat=new XMLHttpRequest();
function uuid(len, radix) {
	var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
	var uuid = [], i;
	radix = radix || chars.length;
	for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
	return uuid.join('');
}
var resend=function(){
	repeat.open("POST","home/query",true);
	repeat.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	var tempname=username;
	if(userid==1)
		tempname=uuid(6,64);
	repeat.send("name="+tempname+"&select="+select+"&id="+userid);
}
repeat.onreadystatechange=function(){
	if(repeat.readyState==4&&repeat.status==200){
		var res=JSON.parse(repeat.responseText);
		if(res.type!='success'){
			setTimeout(function(){resend();},500);
		}else{
			window.location.href = 'http://localhost/'+res.addr;
		}
	}
}

var create_game=function(obj){
	select=obj.name;
	
	if(create_game_type=='game'){
		$("#pgs").prop("hidden",false);
		resend();
		return;
	}
	
	if(select==='r'){
		var i=Math.floor(Math.random()*2); 
		select=i==0?'w':'b';
	}
	console.log("ready to send");
	xmlhttp.open("POST","/create",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("type="+create_game_type+"&select="+select+"&fen="+board2.fen());
	console.log("send");
}
</script>

<script>
var board;

var cfg = {
  draggable: true,
  position: 'start',
  sparePieces: true,
  dropOffBoard: 'trash'
};

board = ChessBoard('board', cfg);
board2=ChessBoard('board2',{position:'start'});

$("#setStartBtn").on("click",function(){
	board.start(false);
});
$('#setClearBtn').on('click', function() {
	board.position({}, false);
});
</script>
