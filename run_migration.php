<?php
/**
 * Auto Migration Script
 * Resolves the issue of manual database execution.
 */
require_once 'db.php';
session_start();


$conn = getDbConnection();
$messages = [];

try {
    // 1. Create MembershipProgram table
    $sql1 = "CREATE TABLE IF NOT EXISTS MembershipProgram (
        membership_id INT AUTO_INCREMENT PRIMARY KEY,
        member_id VARCHAR(40) NOT NULL COMMENT 'References doctorapp.contact',
        package_id VARCHAR(40) NOT NULL COMMENT 'References Package.Package_id',
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
        deleted_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_member_id (member_id),
        INDEX idx_package_id (package_id),
        INDEX idx_status (status),
        INDEX idx_deleted_at (deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (!$conn->query($sql1)) {
        throw new Exception("Failed to create MembershipProgram: " . $conn->error);
    }
    $messages[] = "Checked/Created MembershipProgram table.";

    // 2. Add customer_name to Payment (Using SHOW COLUMNS to support older MySQL versions that lack IF NOT EXISTS for ADD COLUMN)
    $result = $conn->query("SHOW COLUMNS FROM Payment LIKE 'customer_name'");
    if ($result->num_rows == 0) {
        if (!$conn->query("ALTER TABLE Payment ADD COLUMN customer_name VARCHAR(40) DEFAULT '' AFTER Amount")) {
            throw new Exception("Failed to add customer_name to Payment: " . $conn->error);
        }
        $messages[] = "Added customer_name column to Payment table.";
    } else {
        $messages[] = "Column customer_name already exists in Payment table.";
    }

    // 3. Add created_at to doctorapp
    $result = $conn->query("SHOW COLUMNS FROM doctorapp LIKE 'created_at'");
    if ($result->num_rows == 0) {
        if (!$conn->query("ALTER TABLE doctorapp ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP")) {
            throw new Exception("Failed to add created_at to doctorapp: " . $conn->error);
        }
        $messages[] = "Added created_at column to doctorapp table.";
    } else {
        $messages[] = "Column created_at already exists in doctorapp table.";
    }

    // 4. Insert sample data if empty
    $res = $conn->query("SELECT COUNT(*) as c FROM MembershipProgram");
    $row = $res->fetch_assoc();
    if ($row['c'] == 0) {
        $sqlData = "INSERT INTO MembershipProgram (member_id, package_id, start_date, end_date, status) VALUES
        ('201', '1', '2025-01-01', '2025-12-31', 'ACTIVE'),
        ('202', '2', '2025-03-15', '2025-09-15', 'ACTIVE'),
        ('203', '3', '2024-06-01', '2024-12-01', 'EXPIRED'),
        ('204', '1', '2025-06-01', '2025-12-01', 'PENDING'),
        ('205', '2', '2025-02-01', '2025-08-01', 'ACTIVE')";
        
        if (!$conn->query($sqlData)) {
            throw new Exception("Failed to insert sample data: " . $conn->error);
        }
        $messages[] = "Inserted sample Membership data.";
    } else {
        $messages[] = "Membership data already exists.";
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}

$page_title = "Database Migration";
$current_page = "dashboard";
ob_start();
?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-database-check" style="margin-right:8px; color:var(--primary-500);"></i>Database Migration</h1>
        <p>Automated database structure updates</p>
    </div>
</div>

<div class="card fade-in">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-x-circle-fill"></i> <strong>Migration Failed:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> <strong>Migration Completed Successfully!</strong>
            </div>
            
            <ul class="list-group mb-4">
                <?php foreach ($messages as $msg): ?>
                    <li class="list-group-item"><i class="bi bi-check2 text-success me-2"></i><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>
