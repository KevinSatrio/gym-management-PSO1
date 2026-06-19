<?php
/**
 * Membership Programs — List / Read page
 *
 * Features: search by member name or ID, status filter,
 * sortable columns, edit/delete actions, soft-delete awareness.
 */
require_once 'db.php';

$page_title  = "Membership Programs";
$current_page = "memberships";

// --- GET parameters ---
$search        = trim($_GET['search'] ?? '');
$filter_status = trim($_GET['filter_status'] ?? '');
$sort          = $_GET['sort'] ?? 'membership_id';
$order         = strtolower($_GET['order'] ?? 'desc');

// Whitelist sortable columns
$allowed_sorts = ['membership_id', 'start_date', 'end_date', 'status'];
if (!in_array($sort, $allowed_sorts, true)) {
    $sort = 'membership_id';
}
$order = ($order === 'asc') ? 'ASC' : 'DESC';

// --- Build query ---
$where   = "mp.deleted_at IS NULL";
$params  = [];

if ($search !== '') {
    $where .= " AND (CONCAT(d.fname, ' ', d.lname) LIKE ? OR CAST(mp.membership_id AS CHAR) LIKE ?)";
    $like    = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
}

if ($filter_status !== '' && in_array($filter_status, ['ACTIVE', 'EXPIRED', 'PENDING'], true)) {
    $where   .= " AND mp.status = ?";
    $params[] = $filter_status;
}

$sql = "SELECT mp.membership_id, mp.start_date, mp.end_date, mp.status,
               CONCAT(d.fname, ' ', d.lname) AS member_name,
               p.Package_name
        FROM MembershipProgram mp
        LEFT JOIN doctorapp d ON mp.member_id = d.contact
        LEFT JOIN Package p   ON mp.package_id = p.Package_id
        WHERE {$where}
        ORDER BY mp.{$sort} {$order}";

$memberships = dbFetchAll($sql, $params);
$resultCount = count($memberships);

// --- Sort link helper ---
function sortUrl(string $column, string $currentSort, string $currentOrder): string
{
    $params = $_GET;
    $params['sort']  = $column;
    $params['order'] = ($currentSort === $column && $currentOrder === 'ASC') ? 'desc' : 'asc';
    return 'membership.php?' . http_build_query($params);
}

function sortIcon(string $column, string $currentSort, string $currentOrder): string
{
    if ($currentSort !== $column) {
        return '<i class="bi bi-chevron-expand"></i>';
    }
    return $currentOrder === 'ASC'
        ? '<i class="bi bi-chevron-up"></i>'
        : '<i class="bi bi-chevron-down"></i>';
}

ob_start();
?>

<!-- Page Header -->
<div class="page-header fade-in">
    <div>
        <h1><i class="bi bi-card-checklist" style="margin-right:8px; color:var(--primary-500);"></i>Membership Programs</h1>
        <p>Manage member subscriptions and packages</p>
    </div>
    <a href="membership_create.php" class="btn btn-primary" id="btn-add-membership">
        <i class="bi bi-plus-lg"></i> Add Membership
    </a>
</div>

<!-- Toolbar: Search + Filter -->
<form method="GET" action="membership.php" class="toolbar fade-in" id="form-membership-filter">
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" class="form-control" name="search" id="input-search"
               placeholder="Search by member name or ID…"
               value="<?php echo h($search); ?>">
    </div>
    <div class="filter-group">
        <select class="form-select" name="filter_status" id="select-filter-status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="ACTIVE"  <?php echo $filter_status === 'ACTIVE'  ? 'selected' : ''; ?>>Active</option>
            <option value="EXPIRED" <?php echo $filter_status === 'EXPIRED' ? 'selected' : ''; ?>>Expired</option>
            <option value="PENDING" <?php echo $filter_status === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
        </select>
        <button type="submit" class="btn btn-secondary" id="btn-search">
            <i class="bi bi-funnel"></i> Filter
        </button>
    </div>
</form>

<!-- Result count -->
<p class="fade-in" style="color:var(--gray-500); font-size:var(--font-size-sm); margin-bottom:var(--spacing-md);" id="result-count">
    Showing <strong><?php echo $resultCount; ?></strong> membership<?php echo $resultCount !== 1 ? 's' : ''; ?>
</p>

<!-- Memberships Table -->
<div class="card fade-in">
    <div class="card-body-compact">
        <?php if ($resultCount > 0): ?>
        <div class="table-container">
            <table class="table" id="tbl-memberships">
                <thead>
                    <tr>
                        <th>
                            <a href="<?php echo sortUrl('membership_id', $sort, $order); ?>"
                               class="th-sortable <?php echo $sort === 'membership_id' ? 'active' : ''; ?>"
                               style="text-decoration:none; color:inherit;" id="th-sort-id">
                                ID <?php echo sortIcon('membership_id', $sort, $order); ?>
                            </a>
                        </th>
                        <th>Member Name</th>
                        <th>Package</th>
                        <th>
                            <a href="<?php echo sortUrl('start_date', $sort, $order); ?>"
                               class="th-sortable <?php echo $sort === 'start_date' ? 'active' : ''; ?>"
                               style="text-decoration:none; color:inherit;" id="th-sort-start-date">
                                Start Date <?php echo sortIcon('start_date', $sort, $order); ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo sortUrl('end_date', $sort, $order); ?>"
                               class="th-sortable <?php echo $sort === 'end_date' ? 'active' : ''; ?>"
                               style="text-decoration:none; color:inherit;" id="th-sort-end-date">
                                End Date <?php echo sortIcon('end_date', $sort, $order); ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo sortUrl('status', $sort, $order); ?>"
                               class="th-sortable <?php echo $sort === 'status' ? 'active' : ''; ?>"
                               style="text-decoration:none; color:inherit;" id="th-sort-status">
                                Status <?php echo sortIcon('status', $sort, $order); ?>
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memberships as $m): ?>
                    <tr id="row-membership-<?php echo (int)$m['membership_id']; ?>">
                        <td><strong>#<?php echo h($m['membership_id']); ?></strong></td>
                        <td><?php echo h($m['member_name']); ?></td>
                        <td><?php echo h($m['Package_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($m['start_date'])); ?></td>
                        <td><?php echo date('M j, Y', strtotime($m['end_date'])); ?></td>
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
                        <td>
                            <div class="action-buttons">
                                <a href="membership_edit.php?id=<?php echo (int)$m['membership_id']; ?>"
                                   class="btn btn-sm btn-outline-primary btn-icon"
                                   title="Edit"
                                   id="btn-edit-<?php echo (int)$m['membership_id']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="POST" action="membership_delete.php" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this membership?');"
                                      id="form-delete-<?php echo (int)$m['membership_id']; ?>">
                                    <input type="hidden" name="membership_id" value="<?php echo (int)$m['membership_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger btn-icon"
                                            title="Delete"
                                            id="btn-delete-<?php echo (int)$m['membership_id']; ?>">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state" id="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>No memberships found</h4>
            <p>
                <?php if ($search !== '' || $filter_status !== ''): ?>
                    Try adjusting your search or filter criteria.
                <?php else: ?>
                    Create your first membership program to get started.
                <?php endif; ?>
            </p>
            <a href="membership_create.php" class="btn btn-primary btn-sm" style="margin-top:var(--spacing-md);" id="btn-add-empty">
                <i class="bi bi-plus-lg"></i> Add Membership
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>
