<?php
/**
 * Packages — View all gym packages
 */
require_once 'func.php';

$page_title = "Packages";
$current_page = "packages";

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-box-seam-fill" style="margin-right:8px; color:var(--info-500);"></i>Packages</h1>
        <p>View all available gym packages and pricing</p>
    </div>
</div>

<!-- Package Table Card -->
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-list-ul" style="margin-right:8px; color:var(--primary-500);"></i>Package List</h5>
        </div>
        <div class="card-body-compact">
            <div class="table-container">
                <table class="table" id="tbl-packages">
                    <thead>
                        <tr>
                            <th>Package ID</th>
                            <th>Package Name</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php get_package(); ?>
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