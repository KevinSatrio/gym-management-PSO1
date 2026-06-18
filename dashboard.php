<?php
/**
 * Dashboard — Admin overview with summary statistics
 */
require_once 'db.php';

$page_title = "Dashboard";
$current_page = "dashboard";

// Fetch summary counts
$totalMembers = dbCountRows("SELECT COUNT(*) FROM doctorapp");
$activeMemberships = dbCountRows("SELECT COUNT(*) FROM MembershipProgram WHERE status = 'ACTIVE' AND deleted_at IS NULL");
$totalTrainers = dbCountRows("SELECT COUNT(*) FROM Trainer");
$totalPackages = dbCountRows("SELECT COUNT(*) FROM Package");
$totalPayments = dbCountRows("SELECT COUNT(*) FROM Payment");

// Recent memberships (last 5)
$recentMemberships = dbFetchAll(
    "SELECT mp.membership_id, mp.start_date, mp.end_date, mp.status,
            CONCAT(d.fname, ' ', d.lname) AS member_name,
            p.Package_name
     FROM MembershipProgram mp
     LEFT JOIN doctorapp d ON mp.member_id = d.contact
     LEFT JOIN Package p ON mp.package_id = p.Package_id
     WHERE mp.deleted_at IS NULL
     ORDER BY mp.created_at DESC
     LIMIT 5"
);

// Expiring soon (within 7 days)
$expiringSoon = dbCountRows(
    "SELECT COUNT(*) FROM MembershipProgram
     WHERE status = 'ACTIVE'
       AND deleted_at IS NULL
       AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
);

ob_start();
?>

<!-- Stat Cards -->
<div class="stat-cards">
    <div class="stat-card members fade-in fade-in-delay-1">
        <div class="stat-card-info">
            <h3><?php echo $totalMembers; ?></h3>
            <p>Total Members</p>
        </div>
        <div class="stat-card-icon">
            <i class="bi bi-people-fill"></i>
        </div>
    </div>

    <div class="stat-card memberships fade-in fade-in-delay-2">
        <div class="stat-card-info">
            <h3><?php echo $activeMemberships; ?></h3>
            <p>Active Memberships</p>
        </div>
        <div class="stat-card-icon">
            <i class="bi bi-card-checklist"></i>
        </div>
    </div>

    <div class="stat-card trainers fade-in fade-in-delay-3">
        <div class="stat-card-info">
            <h3><?php echo $totalTrainers; ?></h3>
            <p>Total Trainers</p>
        </div>
        <div class="stat-card-icon">
            <i class="bi bi-person-badge-fill"></i>
        </div>
    </div>

    <div class="stat-card packages fade-in fade-in-delay-4">
        <div class="stat-card-info">
            <h3><?php echo $totalPackages; ?></h3>
            <p>Total Packages</p>
        </div>
        <div class="stat-card-icon">
            <i class="bi bi-box-seam-fill"></i>
        </div>
    </div>

    <div class="stat-card payments fade-in fade-in-delay-5">
        <div class="stat-card-info">
            <h3><?php echo $totalPayments; ?></h3>
            <p>Total Payments</p>
        </div>
        <div class="stat-card-icon">
            <i class="bi bi-credit-card-fill"></i>
        </div>
    </div>
</div>

<!-- Expiring Soon Alert -->
<?php if ($expiringSoon > 0): ?>
<div class="alert alert-warning fade-in" role="alert">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong><?php echo $expiringSoon; ?> membership<?php echo $expiringSoon > 1 ? 's' : ''; ?></strong> expiring within 7 days.
    <a href="membership.php?filter_status=ACTIVE" style="margin-left:auto; color:inherit; font-weight:600;">View →</a>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Recent Memberships -->
    <div class="col-lg-8 fade-in fade-in-delay-3">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clock-history" style="margin-right:8px; color:var(--primary-500);"></i>Recent Memberships</h5>
                <a href="membership.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body-compact">
                <?php if (count($recentMemberships) > 0): ?>
                <div class="table-container">
                    <table class="table" id="tbl-recent-memberships">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Package</th>
                                <th>Period</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMemberships as $m): ?>
                            <tr>
                                <td>
                                    <strong><?php echo h($m['member_name']); ?></strong>
                                </td>
                                <td><?php echo h($m['Package_name']); ?></td>
                                <td>
                                    <span style="color:var(--gray-500); font-size:var(--font-size-xs);">
                                        <?php echo date('M j, Y', strtotime($m['start_date'])); ?> —
                                        <?php echo date('M j, Y', strtotime($m['end_date'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match (strtoupper($m['status'])) {
                                        'ACTIVE'  => 'badge-active',
                                        'EXPIRED' => 'badge-expired',
                                        'PENDING' => 'badge-pending',
                                        default   => 'badge-pending',
                                    };
                                    ?>
                                    <span class="badge-status <?php echo $statusClass; ?>"><?php echo h($m['status']); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h4>No memberships yet</h4>
                    <p>Create your first membership program to get started.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 fade-in fade-in-delay-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-lightning-charge-fill" style="margin-right:8px; color:var(--warning-500);"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="quick-actions" style="flex-direction:column;">
                    <a href="admin-panel.php" class="quick-action-btn" id="qa-add-member">
                        <i class="bi bi-person-plus-fill" style="color:var(--primary-500);"></i>
                        Add New Member
                    </a>
                    <a href="membership_create.php" class="quick-action-btn" id="qa-add-membership">
                        <i class="bi bi-plus-circle-fill" style="color:var(--success-500);"></i>
                        Create Membership
                    </a>
                    <a href="trainer.php" class="quick-action-btn" id="qa-add-trainer">
                        <i class="bi bi-person-badge" style="color:var(--warning-500);"></i>
                        Add Trainer
                    </a>
                    <a href="payment.php" class="quick-action-btn" id="qa-add-payment">
                        <i class="bi bi-credit-card" style="color:var(--info-500);"></i>
                        Record Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>
