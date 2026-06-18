<?php
/**
 * Edit Membership — Update an existing MembershipProgram record.
 *
 * Loads the record by ?id=, shows member as read-only text,
 * allows editing package, dates, and status (Active/Expired/Pending).
 */
require_once 'db.php';

$page_title   = "Edit Membership";
$current_page = "memberships";

// --- Load existing record ---
$membership_id = (int)($_GET['id'] ?? $_POST['membership_id'] ?? 0);

if ($membership_id <= 0) {
    setFlashMessage('error', 'Invalid membership ID.');
    header('Location: membership.php');
    exit;
}

$membership = dbFetchOne(
    "SELECT mp.*, CONCAT(d.fname, ' ', d.lname) AS member_name, d.contact AS member_contact
     FROM MembershipProgram mp
     LEFT JOIN doctorapp d ON mp.member_id = d.contact
     WHERE mp.membership_id = ? AND mp.deleted_at IS NULL",
    [$membership_id]
);

if (!$membership) {
    setFlashMessage('error', 'Membership not found or has been deleted.');
    header('Location: membership.php');
    exit;
}

// Fetch packages for dropdown
$packages = dbFetchAll("SELECT Package_id, Package_name, Amount FROM Package ORDER BY Package_name");

// Form state — pre-populate from existing record
$errors = [];
$old    = [
    'package_id' => $membership['package_id'],
    'start_date' => $membership['start_date'],
    'end_date'   => $membership['end_date'],
    'status'     => $membership['status'],
];

// --- Handle POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['package_id'] = trim($_POST['package_id'] ?? '');
    $old['start_date'] = trim($_POST['start_date'] ?? '');
    $old['end_date']   = trim($_POST['end_date'] ?? '');
    $old['status']     = trim($_POST['status'] ?? 'ACTIVE');

    // Validation
    if ($old['package_id'] === '') {
        $errors[] = 'Please select a package.';
    }
    if ($old['start_date'] === '') {
        $errors[] = 'Start date is required.';
    }
    if ($old['end_date'] === '') {
        $errors[] = 'End date is required.';
    }
    if ($old['start_date'] !== '' && $old['end_date'] !== '' && $old['end_date'] < $old['start_date']) {
        $errors[] = 'End date must be on or after the start date.';
    }
    if (!in_array($old['status'], ['ACTIVE', 'EXPIRED', 'PENDING'], true)) {
        $errors[] = 'Invalid status selected.';
    }

    if (empty($errors)) {
        dbExecute(
            "UPDATE MembershipProgram
             SET package_id = ?, start_date = ?, end_date = ?, status = ?
             WHERE membership_id = ? AND deleted_at IS NULL",
            [
                $old['package_id'],
                $old['start_date'],
                $old['end_date'],
                $old['status'],
                $membership_id,
            ]
        );
        setFlashMessage('success', 'Membership program updated successfully.');
        header('Location: membership.php');
        exit;
    }
}

ob_start();
?>

<!-- Page Header -->
<div class="page-header fade-in">
    <div>
        <h1><i class="bi bi-pencil-square" style="margin-right:8px; color:var(--primary-500);"></i>Edit Membership #<?php echo (int)$membership_id; ?></h1>
        <p>Update membership program details</p>
    </div>
    <a href="membership.php" class="btn btn-secondary" id="btn-back-top">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card fade-in" style="max-width:720px;">
    <div class="card-body">

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert" id="alert-errors">
            <i class="bi bi-exclamation-circle-fill"></i>
            <div>
                <?php foreach ($errors as $err): ?>
                    <div><?php echo h($err); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="membership_edit.php?id=<?php echo (int)$membership_id; ?>" id="form-edit-membership">
            <input type="hidden" name="membership_id" value="<?php echo (int)$membership_id; ?>">

            <!-- Member (read-only) -->
            <div class="mb-3">
                <label class="form-label">Member</label>
                <input type="text" class="form-control" id="input-member-readonly"
                       value="<?php echo h($membership['member_name'] . ' (' . $membership['member_contact'] . ')'); ?>"
                       readonly disabled style="background:var(--gray-100); cursor:not-allowed;">
            </div>

            <!-- Package -->
            <div class="mb-3">
                <label for="select-package" class="form-label">Package <span style="color:var(--danger-500);">*</span></label>
                <select class="form-select" name="package_id" id="select-package" required>
                    <option value="">— Select Package —</option>
                    <?php foreach ($packages as $pkg): ?>
                    <option value="<?php echo h($pkg['Package_id']); ?>"
                            <?php echo $old['package_id'] === $pkg['Package_id'] ? 'selected' : ''; ?>>
                        <?php echo h($pkg['Package_name'] . ' ($' . $pkg['Amount'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Start Date -->
            <div class="mb-3">
                <label for="input-start-date" class="form-label">Start Date <span style="color:var(--danger-500);">*</span></label>
                <input type="date" class="form-control" name="start_date" id="input-start-date"
                       value="<?php echo h($old['start_date']); ?>" required>
            </div>

            <!-- End Date -->
            <div class="mb-3">
                <label for="input-end-date" class="form-label">End Date <span style="color:var(--danger-500);">*</span></label>
                <input type="date" class="form-control" name="end_date" id="input-end-date"
                       value="<?php echo h($old['end_date']); ?>" required>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="select-status" class="form-label">Status <span style="color:var(--danger-500);">*</span></label>
                <select class="form-select" name="status" id="select-status" required>
                    <option value="ACTIVE"  <?php echo $old['status'] === 'ACTIVE'  ? 'selected' : ''; ?>>Active</option>
                    <option value="EXPIRED" <?php echo $old['status'] === 'EXPIRED' ? 'selected' : ''; ?>>Expired</option>
                    <option value="PENDING" <?php echo $old['status'] === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                </select>
            </div>

            <!-- Buttons -->
            <div style="display:flex; gap:var(--spacing-md);">
                <button type="submit" class="btn btn-success" id="btn-submit-edit">
                    <i class="bi bi-check-lg"></i> Update Membership
                </button>
                <a href="membership.php" class="btn btn-secondary" id="btn-cancel-edit">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>
