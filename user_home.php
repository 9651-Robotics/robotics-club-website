<?php include_once("functions/import_info.php") ?>
<?php
	require("functions/common.php");
	if(empty($_SESSION['user'])){
		header("Location: login.php");
		die("Redirecting to login.php");
	}
?>
<?php
// string declarations
$timestamp = getdate();
$date = (string) $timestamp['year'] .  "-" . (string) $timestamp['mon'] . "-" . (string) $timestamp['mday'];
$day = (string) $timestamp['weekday'] . " " . (string) $timestamp['month'] .  " " . (string) $timestamp['mday'] . " " . (string) $timestamp['year'];
//attendance check in
function checkIn(){
	if (isset($_GET['checkIn'])) {
		require("functions/common.php");
		require("functions/import_info.php");
		if(empty($_POST['attendance_code'])) {
			die("You forgot to enter a code!");
			header("Location: ".$_SERVER['SCRIPT_NAME']);
		}
		//string redeclarations (doesn't work without these)
		$timestamp = getdate();
		$date = (string) $timestamp['year'] .  "-" . (string) $timestamp['mon'] . "-" . (string) $timestamp['mday'];

		$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

		$email = $_SESSION['user']['email'];
		$attendance_code = $_POST['attendance_code'];
		// gets the correct code
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

		//checks if code is correct
		if($today_code != $attendance_code) {
			die("You entered the wrong code!");
			header("Location: ".$_SERVER['SCRIPT_NAME']);
		}

		//inserts attendance record
		$query = "
		INSERT INTO attendance (
			email,
			date
		) VALUES (
			'$email',
			'$date'
		);";

		try {
			$stmt = $db->prepare($query);
			$stmt->execute();
			$_SESSION['checkedIn'] = $date;
			header("Location: ".$_SERVER['SCRIPT_NAME']);
		}

		catch(PDOException $ex)
		{
			die("Failed to run query: " . $ex->getMessage());
			header("Location: ".$_SERVER['SCRIPT_NAME']);
		}

	}
}
checkIn();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="description" content="UCC Robotics">
		<title><?php echo $row_info['first_name']; ?>'s Homepage | UCC Robotics</title>
		<link rel="icon" href="css/favicon.ico" />
		<?php include_once("functions/stylesheet.php") ?>
		<script src="js/jquery.js"></script>
	</head>

	<body>
		<?php include_once("navbar.php")  ?>
		<?php include_once("footer.php")  ?>
		<div class="container">
			<h1 class="page-header">Welcome <b><?php echo $row_info['first_name']; echo " "; echo $row_info['last_name']; ?></b> <small>to your account homepage</small></h1>
			<h5>Here, you can check in for attendance, see your achievements, and get personalized club news!</h3>

			<?php if($_SESSION['user']['admin'] === "1"){ ?>
			<h3>Need to do admin stuff? <a href="user_admin.php">Click here.</a></h3>
			<?php }elseif ($_SESSION['user']['admin'] === "2") { ?>
			<h3>Need to view attendance? <a href="user_attendance.php">Click here.</a></h3>
			<?php }?>
			<div class="well well-lg">
				<h2>Today is <b><?php echo $day; ?></b></h2>
				<?php if(isset($_SESSION['checkedIn']) && $_SESSION['checkedIn'] == $date){ ?>
					<h5>You've already checked in today!</h5>
				<?php } else { ?>
				<form class="form-signin" action="?checkIn" method="post">
					<div class="input-group">
						<input type="text" id="attendance_code" name="attendance_code" class="form-control" placeholder="Blank Space." required="">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit" id="submitbutton" value="Login">Check in <span class="glyphicon glyphicon-ok"></span></button>
						</span>
					</div>
				</form>
				<?php } ?>
			</div>
		</div>
		<script src="js/jquery.easing.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/nav-collapse.js"></script>
	</body>
</html>
