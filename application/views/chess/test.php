<style>
body{ text-align:center}
</style>

<h2> <?php echo "LC0 Chess"; ?> </h2>

<link rel="stylesheet" href="css/chessboard-0.3.0.min.css" />
	<div >
		<div id="board" style="width: 400px;margin:0 auto;"></div>
		<div float=right>
			<button type="button" id="rsb" width="100px" height="50px" onclick="restart()">restart</button>
			<button type="button" id="rpb" width="100px" height="50px" onclick="repent()">repent</button>
		</div>
	</div>
	<p>Status: <span id="status"></span></p>
	<p>AI_move: <span id="ai_move"></span></p>
	<p>FEN: <span id="fen"></span></p>
	<p>PGN: <span id="pgn"></span></p>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.2/chess.js"></script>
	<script src="https://cdn.staticfile.org/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/json3/3.3.2/json3.min.js"></script>
	<script src="js/chessboard-0.3.0.min.js"></script>
	<script>
		var board,
		  game = new Chess(),
		  statusEl = $('#status'),
		  aimoveEl=$('#ai_move'),
		  fenEl = $('#fen'),
		  pgnEl = $('#pgn');
		
		function restart(){
		  game.reset();
		  board.position(game.fen());
		  updateStatus();
		}
		
		function repent(){
		  game.undo();
		  board.position(game.fen());
		  updateStatus();
		}
		
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
		  onSnapEnd: onSnapEnd,
		  sparePieces: true
		};
		board = ChessBoard('board', cfg);

		updateStatus();
	</script>