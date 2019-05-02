<link href="https://v4.bootcss.com/docs/4.0/examples/floating-labels/floating-labels.css" rel="stylesheet">
	

    <form class="form-signin" action="/login" method="post" onsubmit="return toVaild()">
      <div class="text-center mb-4">
        <h1 class="h3 mb-3 font-weight-normal">AlphaZero Chess Login</h1>
      </div>
	  
	  <!--在表单中label的for属性必须与对应的控件的name属性相同-->
      <div class="form-label-group">
        <input type="text" id="inputName" name="inputName" class="form-control" placeholder="User name" required autofocus>
        <label for="inputName">User name</label>
      </div>

      <div class="form-label-group">
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
        <label for="inputPassword">Password</label>
      </div>

      <div class="checkbox mb-3">
        <label >
          <input type="checkbox" value="remember-me"> Remember me
        </label>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" >Sign in</button>
	</form>


<script>
function toVaild(){
	var f1=document.getElementById("inputUserName").value;
	var f2=document.getElementById("inputPassword").value;
	if(f1==null||f1==""||f2==null||f2==""){
		return false;
	}
	return true;
}
</script>