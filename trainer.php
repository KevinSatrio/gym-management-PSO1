<?php
/**
 * Trainers — View trainer list and add new trainers
 */
require_once 'func.php';

$page_title = "Trainers";
$current_page = "trainers";

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-person-badge-fill" style="margin-right:8px; color:var(--warning-500);"></i>Trainers</h1>
        <p>Manage gym trainers and add new ones</p>
    </div>
</div>

<div class="row g-4">
    <!-- Trainer List Table -->
    <div class="col-lg-8 fade-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-ul" style="margin-right:8px; color:var(--primary-500);"></i>Trainer List</h5>
            </div>
            <div class="card-body-compact">
                <div class="table-container">
                    <table class="table" id="tbl-trainers">
                        <thead>
                            <tr>
                                <th>Trainer ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php get_trainer(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Trainer Form -->
    <div class="col-lg-4 fade-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person-plus-fill" style="margin-right:8px; color:var(--success-500);"></i>Add Trainer</h5>
            </div>
            <div class="card-body">
                <form action="func.php" method="post" id="form-add-trainer">
                    <div class="mb-3">
                        <label for="input-trainer-id" class="form-label">Trainer ID</label>
                        <input type="text" name="Trainer_id" id="input-trainer-id" class="form-control" placeholder="Enter trainer ID" required>
                    </div>

                    <div class="mb-3">
                        <label for="input-trainer-name" class="form-label">Name</label>
                        <input type="text" name="Name" id="input-trainer-name" class="form-control" placeholder="Enter trainer name" required>
                    </div>

                    <div class="mb-3">
                        <label for="input-trainer-phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="input-trainer-phone" class="form-control" placeholder="Enter phone number" required>
                    </div>

                    <button type="submit" name="tra_submit" class="btn btn-primary w-100" id="btn-submit-trainer">
                        <i class="bi bi-check-circle-fill"></i> Register Trainer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>