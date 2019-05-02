<link rel="stylesheet" href="css/chessboard-0.3.0.min.css" />

<div class="container" >
	<div class="row" style="margin:150px">
		<!--显示对弈双方-->
		<div class="col-md-2">
			<p>Status: <span id="status"></span></p>
		</div>
		<!--显示棋盘-->
		<div class="col-md-8">
			<div id="board" style="width: 500px;margin:0 auto;"></div>
		</div>
		
		<!--显示棋局信息-->
		<div class="col-md-2">
			<p>FEN: <span id="fen"></span></p>
			<p>PGN: <span id="pgn"></span></p>
		</div>
	</div>
</div>

<div class="container" style="width: 800px;height: 300px;margin: 30px auto;text-align: center">
    <h1>对弈详情</h1>
    <div style="width: 800px;border: 1px solid gray;height: 300px;">
        <div style="width: 200px;height: 300px;float: left;text-align: left;">
            <p><span>当前在线:</span><span id="user_num">0</span></p>
            <div id="user_list" style="overflow: auto;">

            </div>
        </div>
        <div id="msg_list" style="width: 598px;border:  1px solid gray; height: 300px;overflow: scroll;float: left;">
        </div>
    </div>
	</br>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.2/chess.js"></script>
<script src="https://cdn.staticfile.org/json3/3.3.2/json3.min.js"></script>
<script src="js/chessboard-0.3.0.min.js"></script>

<script type="text/javascript">
    // 存储用户名到全局变量,握手成功后发送给服务器
    var uname = prompt('请输入用户名', 'user' + uuid(8, 16));
    var ws = new WebSocket("ws://127.0.0.1:8080");
    ws.onopen = function () {
        var data = "系统消息：建立连接成功";
        listMsg(data);
    };
    /**
     * 分析服务器返回信息
     *
     * msg.type : user 普通信息;system 系统信息;handshake 握手信息;login 登陆信息; logout 退出信息;
     * msg.from : 消息来源
     * msg.content: 消息内容
     */
    ws.onmessage = function (e) {
        var msg = JSON.parse(e.data);
        var sender, user_name, name_list, change_type;
        switch (msg.type) {
            case 'system':
                sender = '系统消息: ';
                break;
            case 'user':
                sender = msg.from + ': ';
                break;
			case 'fen':
				sender = '';
				var move=JSON.parse(msg.content);
				game.move(move);
				board.position(game.fen());
				updateStatus();
				break;
            case 'handshake':
				var i=Math.floor(Math.random()*2); 
				console.log(i);
                var user_info = {'type': 'login', 'content': uname, 'room':i};
                sendMsg(user_info);
                return;
            case 'login':
            case 'logout':
                user_name = msg.content;
                name_list = msg.user_list;
                change_type = msg.type;
                dealUser(user_name, change_type, name_list);
                return;
        }
        var data = sender + msg.content;
        listMsg(data);
    };
    ws.onerror = function () {
        var data = "系统消息 : 出错了,请退出重试.";
        listMsg(data);
    };
    /**
     * 在输入框内按下回车键时发送消息
     *
     * @param event
     *
     * @returns {boolean}
     *
    function confirm(event) {
        var key_num = event.keyCode;
        if (13 == key_num) {
            send();
        } else {
            return false;
        }
    }
    /**
     * 发送并清空消息输入框内的消息
     */
    function send() {
        var msg_box = document.getElementById("msg_box");
        var content = msg_box.value;
        var reg = new RegExp("\r\n", "g");
        content = content.replace(reg, "");
        var msg = {'content': content.trim(), 'type': 'user'};
        sendMsg(msg);
        msg_box.value = '';
        // todo 清除换行符
    }
    /**
     * 发送并清空消息输入框内的消息
     */
    function sendFen(fen) {
        var msg = {'content': fen, 'type': 'fen'};
        sendMsg(msg);
    }
    /**
     * 将消息内容添加到输出框中,并将滚动条滚动到最下方
     */
    function listMsg(data) {
        var msg_list = document.getElementById("msg_list");
        var msg = document.createElement("p");
        msg.innerHTML = data;
        msg_list.appendChild(msg);
        msg_list.scrollTop = msg_list.scrollHeight;
    }
    /**
     * 处理用户登陆消息
     *
     * @param user_name 用户名
     * @param type  login/logout
     * @param name_list 用户列表
     */
    function dealUser(user_name, type, name_list) {
        var user_list = document.getElementById("user_list");
        var user_num = document.getElementById("user_num");
        while(user_list.hasChildNodes()) {
            user_list.removeChild(user_list.firstChild);
        }
        for (var index in name_list) {
            var user = document.createElement("p");
            user.innerHTML = name_list[index];
            user_list.appendChild(user);
        }
        user_num.innerHTML = name_list.length;
        user_list.scrollTop = user_list.scrollHeight;
        var change = type == 'login' ? '上线' : '下线';
        var data = '系统消息: ' + user_name + ' 已' + change;
        listMsg(data);
    }
    /**
     * 将数据转为json并发送
     * @param msg
     */
    function sendMsg(msg) {
        var data = JSON.stringify(msg);
        ws.send(data);
    }
    /**
     * 生产一个全局唯一ID作为用户名的默认值;
     *
     * @param len
     * @param radix
     * @returns {string}
     */
    function uuid(len, radix) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
        var uuid = [], i;
        radix = radix || chars.length;
        if (len) {
            for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
        } else {
            var r;
            uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
            uuid[14] = '4';
            for (i = 0; i < 36; i++) {
                if (!uuid[i]) {
                    r = 0 | Math.random() * 16;
                    uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
                }
            }
        }
        return uuid.join('');
    }
	
	var board,
	  game = new Chess(),
	  statusEl = $('#status'),
	  fenEl = $('#fen'),
	  pgnEl = $('#pgn');
	
	// do not pick up pieces if the game is over
	// only pick up pieces for the side to move
	var onDragStart = function(source, piece, position, orientation) {
	  if (game.game_over() === true 
			||(game.turn() === 'w' && piece.search(/^b/) !== -1) 
			||(game.turn() === 'b' && piece.search(/^w/) !== -1)
		  ) {
		return false;
	  }
	};

	var onDrop = function(source, target) {
	  // see if the move is legal
	  var move = game.move({
		from: source,
		to: target,
		promotion: 'q' // NOTE: always promote to a queen for example simplicity
	  });

	  // illegal move
	  if (move === null) return 'snapback';

	  updateStatus();
	  sendFen(JSON.stringify(move));
	  //getBestMove();
	};

	// update the board position after the piece snap 
	// for castling, en passant, pawn promotion
	var onSnapEnd = function() {
	  board.position(game.fen());
	};

	var updateStatus = function() {
	  var status = '';

	  var moveColor = 'White';
	  if (game.turn() === 'b') {
		moveColor = 'Black';
	  }

	  // checkmate?
	  if (game.in_checkmate() === true) {
		status = 'Game over, ' + moveColor + ' is in checkmate.';
	  }

	  // draw?
	  else if (game.in_draw() === true) {
		status = 'Game over, drawn position';
	  }

	  // game still on
	  else {
		status = moveColor + ' to move';

		// check?
		if (game.in_check() === true) {
		  status += ', ' + moveColor + ' is in check';
		}
	  }

	  statusEl.html(status);
	  fenEl.html(game.fen());
	  pgnEl.html(game.pgn());
	};

	var cfg = {
	  draggable: true,
	  position: 'start',
	  onDragStart: onDragStart,
	  onDrop: onDrop,
	  onSnapEnd: onSnapEnd
	};
	board = ChessBoard('board', cfg);

	updateStatus();
</script>