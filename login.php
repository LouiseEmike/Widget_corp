<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>


<?php 
	if (logged_in()) {
		redirect_to("staff.php");
	}
	include_once("includes/form_functions.php");
	//START FORM PROCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		$errors = array();
		
		// perform validations on the form data
        $required_fields = array('username', 'password');
        $errors = array_merge($errors, check_required_fields($required_fields, 
        $_POST));

        $fields_with_lengths = array('username' => 30, 'password' => 30);
        $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, 
        $_POST));

        $username = trim(mysql_prep($_POST['username']));
        $password = trim(mysql_prep($_POST['password']));
		$hashed_password = sha1($password);
		
		if ( empty($errors) ) {
            $query = "SELECT id, username 
						FROM users 
						WHERE username ='{$username}' AND hashed_password = '{$hashed_password}' 
						LIMIT 1";
			$result_set = mysql_query($query);
			confirm_query($result_set);
            if (mysql_num_rows($result_set) == 1) {
				$found_user = mysql_fetch_array($result_set);
				$_SESSION['user_id'] = $found_user['id'];
				$_SESSION['username'] = $found_user['username'];
				redirect_to("staff.php");
				//$message = "Successful login.";
            } else {
                $message = "Usename or password does not match.";
                $message .= "<br />" . mysql_error();
            }
        } else {
            if ( count($errors) == 1) {
                $message = "The was 1 error in the form.";
            } else {
                $message = "There were " . count($errors) . " errors in the form.";
            }
		}
	
    } else { //Form has not been submitted.
		$username = "";
		$password = "";
		$message = "Connection error.";

}
	
?>

<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
            <a href="" class="red">Return to public site</a>
			
		</td>
		<td id="page">
        	<h2>Login Page</h2>
			<?php if (!empty($message)) { echo "<p clss=\"message\">" . $message .
            "</p>"; } ?>
            <?php if (!empty($errors)) {display_errors($errors); } ?>
            
            <form action="login.php" method="post">
				<table>
					<tr>
						<td>Username:</td>
						<td><input type ="text" name = "username" maxlenght="30"  /><br /></td>
					</tr>
					<tr>
                        <td>Password:</td>
                        <td><input type ="password" name = "password" maxlength="30" /></td> 
                    </tr>
					<tr>
                        <td colspan="2"><input type="submit" value="Login" name="submit" /></td>
                    </tr>
				</table>
            </form>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>