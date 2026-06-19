<<<<<<< HEAD
<?php
/**
 * Register Member — Form to register new gym members
 */
require_once 'func.php';
require_once 'db.php';

$page_title = "Register Member";
$current_page = "members";

// Fetch trainers for dropdown using db.php prepared statements
$trainers = dbFetchAll("SELECT Trainer_id, Name FROM Trainer");

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-person-plus-fill" style="margin-right:8px; color:var(--primary-500);"></i>Register Member</h1>
        <p>Add a new member to the gym system</p>
    </div>
    <a href="trainer_details.php" class="btn btn-outline-primary" id="btn-view-members">
        <i class="bi bi-people-fill"></i> View All Members
    </a>
</div>

<!-- Registration Form Card -->
<div class="row justify-content-center">
    <div class="col-lg-8 fade-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clipboard-plus" style="margin-right:8px; color:var(--primary-500);"></i>Member Registration Form</h5>
            </div>
            <div class="card-body">
                <form action="func.php" method="post" id="form-register-member">
                    <div class="row g-3">
                        <!-- First Name -->
                        <div class="col-md-6">
                            <label for="input-fname" class="form-label">First Name</label>
                            <input type="text" name="fname" id="input-fname" class="form-control" placeholder="Enter first name" required>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <label for="input-lname" class="form-label">Last Name</label>
                            <input type="text" name="lname" id="input-lname" class="form-control" placeholder="Enter last name" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="input-email" class="form-label">Email</label>
                            <input type="email" name="email" id="input-email" class="form-control" placeholder="Enter email address" required>
                        </div>

                        <!-- Member ID -->
                        <div class="col-md-6">
                            <label for="input-contact" class="form-label">Member ID</label>
                            <input type="text" name="contact" id="input-contact" class="form-control" placeholder="Enter member ID" required>
                        </div>

                        <!-- Trainer -->
                        <div class="col-md-6">
                            <label for="input-docapp" class="form-label">Trainer</label>
                            <select name="docapp" id="input-docapp" class="form-select" required>
                                <option value="" disabled selected>Select a trainer</option>
                                <?php foreach ($trainers as $trainer): ?>
                                    <option value="<?php echo h($trainer['Trainer_id']); ?>">
                                        <?php echo h($trainer['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 mt-4">
                            <button type="submit" name="pat_submit" class="btn btn-primary" id="btn-register-member">
                                <i class="bi bi-check-circle-fill"></i> Register Member
                            </button>
                            <a href="trainer_details.php" class="btn btn-secondary ms-2" id="btn-cancel-register">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>
=======
<!DOCTYPE html>
<?php

// php select option value from database

$hostname = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$databaseName = getenv('DB_NAME') ?: 'loginsystem';

// connect to mysql database

$connect = mysqli_connect($hostname, $username, $password, $databaseName);

// mysql select query
$query = "SELECT * FROM `Trainer`";

// for method 1

$result1 = mysqli_query($connect, $query);



?>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  </head>
  <body>
      
   
       
 <div class="jumbotron" style="border-radius:0;background:url('images/3.jpg');background-size:cover;height:400px;"></div>
   <div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="" class="list-group-item active"
                   >Members</a>
                <a href="trainer_details.php" class="list-group-item">Member details</a>
                <a href="package.php" class="list-group-item">Package details</a>
                <a href="payment.php" class="list-group-item">Payments</a>
            </div>
            <hr>
            <div class="list-group">
              <a href="trainer.php" class="list-group-item active">Trainer</a>
              <a href="trainer.php" class="list-group-item active">Trainer details</a>             
              <a href="trainer.php" class="list-group-item active">Add new Trainer</a>
			  <a href="membership.php" class="list-group-item">Membership</a>
            </div>      
            
        </div>
            <div class="col-md-8">
            <div class="card">
                
     <div class="card-body" style="background-color:#3498DB;color:FFFFFF;">
                <h3>Register new members</h3>
                </div> 
                <div class="card-body"></div>
                <form class="form-group" action="func.php" method="post">
                <label>first name:</label>
<input type="text" name="fname" class="form-control"><br>
                    <label>last name:</label>
<input type="text" name="lname" class="form-control"><br> 
 <label>email</label>
                    <input type="text" name="email" class="form-control"><br>
                    <label>Member ID</label>
<input type="text" name="contact" class="form-control"><br>        
 <label>Trainer </label> 
 <select class="form-control" name="docapp">

            <?php while($row1 = mysqli_fetch_array($result1)):;?>

            <option value="<?php echo $row1[0];?>"><?php echo $row1[1];?></option>

            <?php endwhile;?>

        </select>
        <br>
                                        
  <input type="submit" class="btn btn-primary" name="pat_submit" value="Register">                  <a href="func.php" class="btn btn-light"></a>
                    
                    
                </form>
                </div>
      </div>
       </div>
      <div class="col-md-1"></div>
      </div>
    <header>
 <nav>
     <div class="main-wrapper">
	      
		       <div class="nav-login">
			       <?php
				        if (isset($_SESSION['u_id'])) {
						  echo '<form action="includes/index.php" method="POST">
					            <button type="submit" name="submit">logout</button>
					              </form>';	
                                 } else{
							
							echo '<form action="includes/index.php" method="POST">
                              
                               						
				                </form>
				              <a href="index.php" class="btn btn-light" style="background-color:#3498DB;color:FFFFFF">Logout</a>';
							
						}
				   
				    ?>
					
				
		       </div>
	 </div>
 </nav>

</header>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

     </body>
    
</html>
   
>>>>>>> 8b3ff91d80b45f9faf1854c43ca1ea6e51608943
