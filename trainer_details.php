<?php
/**
 * Member Directory — View all registered members with search
 */
require_once 'func.php';

$page_title = "Member Directory";
$current_page = "members";

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-people-fill" style="margin-right:8px; color:var(--primary-500);"></i>Member Directory</h1>
        <p>View and search all registered gym members</p>
    </div>
    <a href="admin-panel.php" class="btn btn-primary" id="btn-add-new-member">
        <i class="bi bi-person-plus-fill"></i> Add New Member
    </a>
</div>

<!-- Search Toolbar -->
<div class="toolbar">
    <form action="trainer_search.php" method="post" id="form-member-search" class="d-flex align-items-center gap-2" style="flex:1; max-width:500px;">
        <div class="search-box" style="flex:1;">
            <i class="bi bi-search"></i>
            <input type="text" name="search" id="input-member-search" class="form-control" placeholder="Search by name or member ID...">
        </div>
        <button type="submit" name="patient_search_submit" class="btn btn-primary" id="btn-search-member">
            <i class="bi bi-search"></i> Search
        </button>
    </form>
</div>

<!-- Members Table Card -->
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-table" style="margin-right:8px; color:var(--primary-500);"></i>All Members</h5>
        </div>
        <div class="card-body-compact">
            <div class="table-container">
                <table class="table" id="tbl-members">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Member ID</th>
                            <th>Trainer ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php get_patient_details(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>