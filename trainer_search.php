<?php
/**
 * Member Search Results — Display members matching search criteria
 * Uses prepared statements via db.php for secure queries
 */
require_once 'db.php';

$page_title = "Search Results";
$current_page = "members";

// Get search term
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$results = [];

if (isset($_POST['patient_search_submit']) && $search !== '') {
    $likeTerm = '%' . $search . '%';
    $results = dbFetchAll(
        "SELECT * FROM doctorapp WHERE contact = ? OR fname LIKE ? OR lname LIKE ?",
        [$search, $likeTerm, $likeTerm]
    );
}

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-search" style="margin-right:8px; color:var(--primary-500);"></i>Search Results</h1>
        <p>
            <?php if ($search !== ''): ?>
                Showing results for "<strong><?php echo h($search); ?></strong>"
            <?php else: ?>
                No search term provided
            <?php endif; ?>
        </p>
    </div>
    <a href="trainer_details.php" class="btn btn-outline-primary" id="btn-back-to-directory">
        <i class="bi bi-arrow-left"></i> Back to Member Directory
    </a>
</div>

<?php if (!empty($results)): ?>
<!-- Results Table Card -->
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-table" style="margin-right:8px; color:var(--primary-500);"></i>Found <?php echo count($results); ?> Result<?php echo count($results) !== 1 ? 's' : ''; ?></h5>
        </div>
        <div class="card-body-compact">
            <div class="table-container">
                <table class="table" id="tbl-search-results">
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
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo h($row['fname']); ?></td>
                            <td><?php echo h($row['lname']); ?></td>
                            <td><?php echo h($row['email']); ?></td>
                            <td><?php echo h($row['contact']); ?></td>
                            <td><?php echo h($row['docapp']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php elseif ($search !== ''): ?>
<!-- No Results -->
<div class="fade-in">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox" style="font-size:3rem; color:var(--gray-300);"></i>
            <h4 class="mt-3" style="color:var(--gray-600);">No Members Found</h4>
            <p style="color:var(--gray-500);">No members match your search for "<strong><?php echo h($search); ?></strong>". Try a different search term.</p>
            <a href="trainer_details.php" class="btn btn-primary mt-2" id="btn-try-again">
                <i class="bi bi-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>
</div>
<?php else: ?>
<!-- No Search Term -->
<div class="fade-in">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-search" style="font-size:3rem; color:var(--gray-300);"></i>
            <h4 class="mt-3" style="color:var(--gray-600);">No Search Term</h4>
            <p style="color:var(--gray-500);">Please enter a search term to find members.</p>
            <a href="trainer_details.php" class="btn btn-primary mt-2" id="btn-go-back-directory">
                <i class="bi bi-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>