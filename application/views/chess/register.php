<style>
body {
  background-color:#f8f9fa;
}
  
.needs-validation {
  width: 100%;
  max-width: 420px;
  padding: 15px;
  margin: 0 auto;
}
</style>
    
  <form class="needs-validation" action="register" method="post" onsubmit="return toVaild()">
      <div class="text-center mb-4">
        <h1 class="h3 mb-3 font-weight-normal">AlphaZero Chess Register</h1>
      </div>
	<div class="row">
	  <div class="col-md-6 mb-3">
		<label for="firstName">First name</label>
		<input type="text" class="form-control" id="firstName" name="firstName" placeholder="firstName" value="" required>
	  </div>
	  <div class="col-md-6 mb-3">
		<label for="lastName">Last name</label>
		<input type="text" class="form-control" id="lastName" name="lastName" placeholder="lastName" value="" required>
	  </div>
	</div>

	<div class="mb-3">
	  <label for="username">Username</label>
	  <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
	</div>
	
	<div class="mb-3">
	  <label for="email">Email </label>
	  <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
	</div>
	
		<div class="mb-3">
	  <label for="password">Password </label>
	  <input type="password" class="form-control" id="password" name="password" placeholder="Password">
	</div>
	  
	</div>
		<div class="mb-3">
	  <label >rePassword </label>
	  <input type="password" class="form-control" id="repassword" placeholder="rePassword">
	</div>
	  
	</div>
		<div class="mb-3">
	  <label for="phone">Phone </label>
	  <input type="text" class="form-control" id="phone" name="phone" placeholder="phone number">
	</div>

	<p> <?php echo $msg; ?> </p>
	<p> <?php echo $getemail; ?> </p>
	
	<hr class="mb-4">
	<button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" >Click to Register</button>
  </form>

<script>
function toVaild(){
	var f1=document.getElementById("firstName").value;
	var f2=document.getElementById("lastName").value;
	var f3=document.getElementById("username").value;
	var f4=document.getElementById("email").value;
	var f5=document.getElementById("phone").value;
	var f6=document.getElementById("password").value;
	var f7=document.getElementById("repassword").value;
	if(f1==null||f1==""||f2==null||f2==""||f3==null||f3==""||f4==null||f4==""
		||f5==null||f5==""||f6==null||f6==""||f6!=f7){
		return false;
	}
	return true;
}
</script>