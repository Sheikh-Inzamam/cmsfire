<script type="text/javascript" src="/js/libraries/jquery.min.js"></script>



Create a Master User.

<br/>
<br/>
TO SET UP:
<br/><br/>
1].  Create a new Database in MySQL called "cmsfire"
<br/>
2].  Import the table scructure from "\application\migrations\tableStructure.sql"
<br/>
3].  Navigate to "application\config\database.php" then add your proper MySQL credentials for "username", "password" and "database".
<br/>
4].  Set write permissions on in "application\controllers\", this is because once the script runs it will delete "initial.php"
<br/>

5].  Change the default salt value in "application\models\core\login_model.php" and "application\models\core\user_model.php", it will be named <i>$salt</i>.  **These values should be equal to each other
<br/>

6].  Next load in the browser: "YOUR_SERVER_NAME" and create your user.
<br/><br/>
You are now set up to use CMS Fire!  Create a category to start bookmarking and discussing topics.
<br/>
<br/>
<form id="create-account-form">
<label>Username</label>
<div><input type="text" name="name" value="" id="name"><label id="account-available"></label></div>
<label>Password</label><div><input type="password" name="password" value="" id="password"></div>
<label>Re-Password</label><div><input type="password" name="repassword" value="" id="repassword"></div>
<label>Email (optional)</label><div><input type="text" name="email" value="" id="email"></div>
<input type="button" id="submit-button-create" name="submit" value="Create Account">
<label id="error-create-account" style="color:red;"></label>
</form>

<script type="text/javascript">
$(document).ready(
	function() {		
	initCreateAccountHandler();
	function initCreateAccountHandler(){
		var dataToBeSent;
		$("#submit-button-create").click(function() {	
			dataToBeSent = $("#create-account-form").serialize();
			$.ajax({
				type: "POST",
				url: "/initial/submit",				    
				data: dataToBeSent,				    
				dataType: "json",
				success: function(data){			    	
					if(data.result == 'Success!'){
						window.location.href = '/';
					}else{
						$("#error-create-account").html(data.result);
					}
				},
				failure: function(errMsg) {
					alert(errMsg);
				}
			});				
		});
	}	
	});
</script>
