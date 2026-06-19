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
