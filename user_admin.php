<?php include_once("functions/import_info.php") ?>
<?php
	require("functions/common.php");
	//redir on no login
	if(empty($_SESSION['user'])){
		header("Location: login.php");
		die("Redirecting to login.php");
	}
	//redir if not admin
	if($_SESSION['user']['admin'] === 0){
		header("Location: user_home.php");
		die("Redirecting to user_home.php");
	}
	//gets the correct code
	require("functions/import_info.php");
	$query = "SELECT * FROM attendance WHERE email='code@robotics.ucc.on.ca';";

	try
	{
		$stmt = $db->prepare($query);
		$stmt->execute();
	}

	catch(PDOException $ex)
	{
		die("Failed to run query: " . $ex->getMessage());
	}

	$code_info = $stmt->fetch();
	$today_code = $code_info['date'];
	$timestamp = getdate();
	$date = (string) $timestamp['year'] .  "-" . (string) $timestamp['mon'] . "-" . (string) $timestamp['mday'];
	$day = (string) $timestamp['weekday'] . " " . (string) $timestamp['month'] .  " " . (string) $timestamp['mday'] . " " . (string) $timestamp['year'];

	//change attn code function
	function changeCode(){
		if (isset($_GET['changeCode'])) {
			require("functions/common.php");
			require("functions/import_info.php");
			if(empty($_POST['new_code'])) {
				die("You forgot to enter a code!");
				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}
			$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
			$new_code = $_POST['new_code'];
			$query = "
			REPLACE INTO attendance (
				email,
				date,
				id
			) VALUES (
				'code@robotics.ucc.on.ca',
				'$new_code',
				1
			);";
			try {
				$stmt = $db->prepare($query);
				$stmt->execute();
				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}

			catch(PDOException $ex)
			{
				die("Failed to run query: " . $ex->getMessage());
				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}
		}
	}
	changeCode();
	//create new bulletin announcement function
	function newBulletin(){
		require("functions/common.php");
		require("functions/import_info.php");
		if (isset($_GET['newBulletin'])) {

			$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

			$creator = $row_info['first_name'] . ' ' . $row_info['last_name'];
			$title = $_POST['bulletin-title'];
			$tag = $_POST['bulletin-tag'];
			$content = $_POST['bulletin-content'];
			$expire = time()+604800; //+ 2weeks of Unix time converted to YYYY MM DD

			$query = "
		 	INSERT INTO bulletin (
				creator,
				title,
				tag,
				content,
				expire
			) VALUES (
				'$creator',
				'$title',
				'$tag',
				'$content',
				'$expire'
			);";

			try {
				$stmt = $db->prepare($query);
				$stmt->execute();

				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}

			catch(PDOException $ex)
			{
				die("Failed to run query: " . $ex->getMessage());
				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}
		}
	}
	newBulletin();

	//create new alert function
	function newAlert(){
		require("functions/common.php");
		require("functions/import_info.php");
		if (isset($_GET['newAlert'])) {

			$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

			$creator = $row_info['first_name'] . ' ' . $row_info['last_name'];
			$type = $_POST['alert-type'];
			$glyph = $_POST['alert-glyph'];
			$content = $_POST['alert-content'];

			$query = "
		 	INSERT INTO alert (
				type,
				glyph,
				content,
				creator
			) VALUES (
				'$type',
				'$glyph',
				'$content',
				'$creator'
			);";

			try {
				$stmt = $db->prepare($query);
				$stmt->execute();

				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}

			catch(PDOException $ex)
			{
				die("Failed to run query: " . $ex->getMessage());
				header("Location: ".$_SERVER['SCRIPT_NAME']);
			}
		}
	}
	newAlert();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="description" content="UCC Robotics">
		<title>Admin Panel | UCC Robotics</title>
		<link rel="icon" href="css/favicon.ico" />
		<?php include_once("functions/stylesheet.php") ?>
		<script src="js/jquery.js"></script>
	</head>

	<body>
		<?php include_once("navbar.php")  ?>
		<?php include_once("footer.php")  ?>
		<div class="container">
			<div class="well well-lg">
				<h2>Today is <b><?php echo $day; ?></b></h2>
				<form class="form-signin" action="?changeCode" method="post">
					<div class="row">
						<div class="col-sm-4">
							<h4>The current code is: <b><?php echo $today_code; ?></b></h4>
						</div>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" id="new_code" name="new_code" class="form-control" placeholder="Blank Space." required="">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit" id="submitbutton" value="Login">Set New Code</button>
								</span>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<script src="js/jquery.easing.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/nav-collapse.js"></script>
	</body>
</html>
