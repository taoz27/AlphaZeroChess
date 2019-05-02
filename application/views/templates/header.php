<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>AlphaZero Chess</title>
		<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/chessboard-0.3.0.min.css" />
		<!--ctrl+f5可以强制刷新，清空浏览器缓存-->
		<script src="https://cdn.staticfile.org/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdn.staticfile.org/json3/3.3.2/json3.min.js"></script>
		<script src="js/chess.js"></script>
		<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js"></script>
		<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
		<script src="js/chessboard-0.3.0.js"></script>
		<script src="js/pgnboard.js"></script>
	</head>
	<body>
	<style>
		.border-bottom { border-bottom: 1px solid #e5e5e5; }
		.box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
	</style>

	<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
	  <a class="my-0 mr-md-auto font-weight-normal" style="font-size:25px" href="/">AlphaZero Chess</a>
	  <nav class="my-2 my-md-0 mr-md-3">
		<a class="p-2 text-dark" href="#">人机对弈</a>
		<a class="p-2 text-dark" href="#">人人对弈</a>
		<a class="p-2 text-dark" href="#">象棋训练</a>
		<a class="p-2 text-dark" href="#">象棋设置</a>
	  </nav>
	  <script>
		var username="Anonymous";
		var userid=1;
	  </script>
	  <?php
	  if($this->session->login){
		echo 	'<script>
					username="'.$this->session->name.'";
					userid='.$this->session->id.';
				</script>';
		
		echo '<div class="dropdown">
				<img class="dropdown-toggle img-rounded img-responsive center-block" data-toggle="dropdown"
					style="width:40px; height:40px; border-radius:10px; background-color: transparent;box-shadow:0px 0px 3px 3px #ccc;"
					src="img/test.png" alt="头像"/>
				<div class="dropdown-menu dropdown-menu-right">
				  <a class="dropdown-item" >'.$_SESSION['name'].'</a>
				  <a class="dropdown-item" href="/###">Profile</a>
				  <a class="dropdown-item" href="/###">Setting</a>
				  <div class="dropdown-divider"></div>
				  <a class="dropdown-item" href="/logout">Logout</a>
				</div>
			 </div>';
		
	  }else
		echo '<a class="btn btn-outline-primary" data-toggle="modal" data-target="#login" href="">Login</a>';
	  ?>
	</div>
	
	<!-- 注册窗口 -->
    <div id="register" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-title">
                    <h1 class="text-center">AlphaZero Chess Register</h1>
                </div>
                <div class="modal-body">
                    <form class="form-group" action="/register" method="post">
                            <div class="form-group">
                                <label for="">用户名</label>
                                <input class="form-control" id="username" name="username" class="form-control" type="text" placeholder="6-15位字母或数字">
                            </div>
                            <div class="form-group">
                                <label for="">密码</label>
                                <input class="form-control" id="password" name="password" class="form-control" type="password" placeholder="至少6位字母或数字">
                            </div>
                            <div class="form-group">
                                <label for="">再次输入密码</label>
                                <input class="form-control" type="password" placeholder="至少6位字母或数字">
                            </div>
                            <div class="form-group">
                                <label for="">邮箱</label>
                                <input class="form-control" id="email" name="email" class="form-control" type="email" placeholder="例如:123@123.com">
                            </div>
                            <div class="form-group">
                                <label for="">手机</label>
                                <input class="form-control" id="phone" name="phone" class="form-control" type="phone" placeholder="例如:18712341234">
                            </div>
                            <div class="text-right">
                                <button class="btn btn-primary" type="submit">提交</button>
                                <button class="btn btn-danger" data-dismiss="modal">取消</button>
                            </div>
                            <a href="" data-toggle="modal" data-dismiss="modal" data-target="#login">已有账号？点我登录</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- 登录窗口 -->
    <div id="login" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-title">
                    <h1 class="text-center">AlphaZero Chess Login</h1>
                </div>
                <div class="modal-body">
                    <form class="form-group" action="/login" method="post">
                   	 <div class="form-group">
				<input type="text" id="inputName" name="inputName" class="form-control" placeholder="User name"  autofocus>
				<label for="inputName">User name</label>
                   	 </div>
                         <div class="form-group">
				<input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" >
				<label for="inputPassword">Password</label>
                         </div>
							
			<div class="checkbox mb-3">
				<label>
					<input type="checkbox" id="remember" name="remember" >Remember me
				<label for="remember"></label>
				</label>
			</div>
                        <div class="text-right">
                                <button class="btn btn-primary" type="submit">登录</button>
                                <button class="btn btn-danger" data-dismiss="modal">取消</button>
                         </div>
                         <a href="" data-toggle="modal" data-dismiss="modal" data-target="#register">还没有账号？点我注册</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
