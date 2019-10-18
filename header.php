<?php
require_once("config.php");
require_once("user.php");
require_once("customer.php");

// Handle hidden menu and navbar render (note that is related to the user status (loggedin/typeOfUser))
$hidden_menu = "";
if(!is_logged() && !has_pending_ticket()) {
	$navbar_edit = '<li class="nav-item"><a class="nav-link" data-toggle="modal" href="#myModal"> Login </a></li>';

}
else{
	$navbar_edit = '
		<li class="nav-item">
			<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Login</a>
		</li>
		';
    $navbar_edit .= '<li class="nav-item"><a class="nav-link" href="'.PLATFORM_PATH.'logout.php"> Logout </a></li>';
}
if(is_admin()){
	//$navbar_edit .= '<li class="nav-item"><a class="nav-link" data-toggle="modal" href="#registerModal"> Register new clerk</a></li>';
	//$navbar_edit .= '<li class="nav-item"><a class="nav-link" data-toggle="modal" href="#newServiceModal"> Register new service</a></li>';
    $hidden_menu .= '
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin actions</a>
		<div class="dropdown-menu" aria-labelledby="dropdown01">
		<a class="dropdown-item" data-toggle="modal" href="#registerModal">Register new clerk</a>
		<a class="dropdown-item" href="#newServiceModal">Register new service</a>
		</div>
	</li>
';
}
elseif(is_logged()){// if not admin and logged => clerk
	$hidden_menu .= '
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Clerk actions</a>
		<div class="dropdown-menu" aria-labelledby="dropdown01">
		<a class="dropdown-item" href="./clerkAction.php?action=nextTicket">Next customer</a>
		</div>
	</li>
';
}

print '<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="./css/bootstrap.min.css" crossorigin="anonymous">
		<link rel="stylesheet" href="./css/styleTicket.css">
		<!-- Other resources -->
		<meta charset="UTF-8">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!--Custom css/jss-->
		<link href="./css/style.css" rel="stylesheet">
		<title>Softeng2 System</title>
	</head>
	

	<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
	<a class="navbar-brand" href="#">Navbar</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarsExampleDefault">
		<ul class="navbar-nav mr-auto">
		<li class="nav-item active">
			<a class="nav-link" href="'.PLATFORM_PATH.'">Home <span class="sr-only">(current)</span></a>
		</li>
		'.$navbar_edit
		.$hidden_menu.'
		</ul>
		<form class="form-inline my-2 my-lg-0">
		<input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
		<button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
		</form>
	</div>
	</nav>
';
/* Render the 2 modal view: Login and Register */
echo '<!-- Modal Login -->
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-body" style="padding:40px 50px;">
						<form role="form" method="POST" action="'.PLATFORM_PATH.'index.php">
						<div class="form-group">
							<label for="front_office"><span class="glyphicon glyphicon-user"></span> front_office</label>
							<input type="text" class="form-control" name="front_office" id="front_office" placeholder="Enter front_office">
						</div>
						<div class="form-group">
							<label for="password"><span class="glyphicon glyphicon-eye-open"></span> Password</label>
							<input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
						</div>
						<div class="checkbox">
							<label><input type="checkbox" value="" checked>Remember me</label>
						</div>
							<button type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span> Login</button>
						</form>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
						<!--<p>Not a member? <a href="#">Sign Up</a></p>-->
						<p>Forgot <a href="#">Password?</a></p>
					</div>
				</div>
			</div>
		</div> 
		';
echo '<!-- Modal for registration(signup) -->
	<div class="modal fade" id="registerModal" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-body" style="padding:40px 50px;">
					<form method="POST" action="./register.php">
					<div class="form-group">
						<label for="rfront_office"><span class="glyphicon glyphicon-user"></span> front_office:</label>
						<input type="text" class="form-control" name="front_office" id="rfront_office" placeholder="Your email">
					</div>
					<div class="form-group">
						<label for="rpassword"><span class="glyphicon glyphicon-eye-open"></span> Password</label>
						<input type="password" class="form-control" name="password" id="rpassword" placeholder="Enter password">
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name = "tos" value="yes" checked>Privacy consense</label>
					</div>
						<button type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span>Register</button>
					</form>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
				</div>
			</div>
		</div>
	</div>';
?>
