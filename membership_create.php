<?php
/**
 * Create Membership — Form to add a new MembershipProgram record.
 *
 * Populates member & package dropdowns from doctorapp and Package tables.
 * Server-side validation: all fields required, end_date >= start_date.
 */
require_once 'db.php';

$page_title   = "Create Membership";
$current_page = "memberships";

// Fetch dropdown data
$members  = dbFetchAll("SELECT contact, fname, lname FROM doctorapp ORDER BY fname, lname");
$packages = dbFetchAll("SELECT Package_id, Package_name, Amount FROM Package ORDER BY Package_name");

// Form state
$errors    = [];
$old       = [
    'member_id'  => '',
    'package_id' => '',
    'start_date' => '',
    'end_date'   => '',
    'status'     => 'ACTIVE',
];

// --- Handle POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['member_id']  = trim($_POST['member_id'] ?? '');
    $old['package_id'] = trim($_POST['package_id'] ?? '');
    $old['start_date'] = trim($_POST['start_date'] ?? '');
    $old['end_date']   = trim($_POST['end_date'] ?? '');
    $old['status']     = trim($_POST['status'] ?? 'ACTIVE');

    // Validation
    if ($old['member_id'] === '') {
        $errors[] = 'Please select a member.';
    }
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
    if (!in_array($old['status'], ['ACTIVE', 'PENDING'], true)) {
        $errors[] = 'Invalid status selected.';
    }

    if (empty($errors)) {
        dbExecute(
            "INSERT INTO MembershipProgram (member_id, package_id, start_date, end_date, status)
             VALUES (?, ?, ?, ?, ?)",
            [
                $old['member_id'],
                $old['package_id'],
                $old['start_date'],
                $old['end_date'],
                $old['status'],
            ]
        );
        setFlashMessage('success', 'Membership program created successfully.');
        header('Location: membership.php');
        exit;
    }
}

ob_start();
?>

<!-- Page Header -->
<div class="page-header fade-in">
    <div>
        <h1><i class="bi bi-plus-circle" style="margin-right:8px; color:var(--primary-500);"></i>Create Membership</h1>
        <p>Add a new membership program for a member</p>
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

        <form method="POST" action="membership_create.php" id="form-create-membership">

            <!-- Member -->
            <div class="mb-3">
                <label for="select-member" class="form-label">Member <span style="color:var(--danger-500);">*</span></label>
                <select class="form-select" name="member_id" id="select-member" required>
                    <option value="">— Select Member —</option>
                    <?php foreach ($members as $mem): ?>
                    <option value="<?php echo h($mem['contact']); ?>"
                            <?php echo $old['member_id'] === $mem['contact'] ? 'selected' : ''; ?>>
                        <?php echo h($mem['fname'] . ' ' . $mem['lname'] . ' (' . $mem['contact'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
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
                    <option value="PENDING" <?php echo $old['status'] === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                </select>
            </div>

            <!-- Buttons -->
            <div style="display:flex; gap:var(--spacing-md);">
                <button type="submit" class="btn btn-success" id="btn-submit-create">
                    <i class="bi bi-check-lg"></i> Create Membership
                </button>
                <a href="membership.php" class="btn btn-secondary" id="btn-cancel-create">
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
