<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// Login Handler
// ============================================
if (isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    $user = dbFetchOne("SELECT * FROM logintb WHERE username=? AND password=?", [$username, $password]);
    
    if ($user !== null) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['login_error'] = 'Invalid username or password.';
        header("Location: index.php");
        exit;
    }
}

// ============================================
// Member Registration Handler
// ============================================
if (isset($_POST['pat_submit'])) {
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $docapp = $_POST['docapp'] ?? '';
    
    $affected = dbExecute(
        "INSERT INTO doctorapp (fname, lname, email, contact, docapp) VALUES (?, ?, ?, ?, ?)",
        [$fname, $lname, $email, $contact, $docapp]
    );
    
    if ($affected > 0) {
        setFlashMessage('success', 'Member registered successfully.');
    } else {
        setFlashMessage('error', 'Failed to register member.');
    }
    header("Location: admin-panel.php");
    exit;
}

// ============================================
// Trainer Add Handler
// ============================================
if (isset($_POST['tra_submit'])) {
    $Trainer_id = $_POST['Trainer_id'] ?? '';
    $Name = $_POST['Name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    $affected = dbExecute(
        "INSERT INTO Trainer (Trainer_id, Name, phone) VALUES (?, ?, ?)",
        [$Trainer_id, $Name, $phone]
    );
    
    if ($affected > 0) {
        setFlashMessage('success', 'Trainer added successfully.');
    } else {
        setFlashMessage('error', 'Failed to add trainer.');
    }
    header("Location: trainer.php");
    exit;
}

// ============================================
// Payment Handler
// ============================================
if (isset($_POST['pay_submit'])) {
    $Payment_id = $_POST['Payment_id'] ?? '';
    $Amount = $_POST['Amount'] ?? '';
    $customer_id = $_POST['customer_id'] ?? '';
    $payment_type = $_POST['payment_type'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    
    $affected = dbExecute(
        "INSERT INTO Payment (Payment_id, Amount, customer_name, customer_id, payment_type) VALUES (?, ?, ?, ?, ?)",
        [$Payment_id, $Amount, $customer_name, $customer_id, $payment_type]
    );
    
    if ($affected > 0) {
        setFlashMessage('success', 'Payment recorded successfully.');
    } else {
        setFlashMessage('error', 'Failed to record payment.');
    }
    header("Location: payment.php");
    exit;
}

// ============================================
// Data Retrieval Functions
// ============================================
function get_patient_details()
{
    $rows = dbFetchAll("SELECT * FROM doctorapp ORDER BY created_at DESC");
    foreach ($rows as $row) {
        $fname = h($row['fname']);
        $lname = h($row['lname']);
        $email = h($row['email']);
        $contact = h($row['contact']);
        $docapp = h($row['docapp']);
        echo "<tr>
          <td>{$fname}</td>
          <td>{$lname}</td>
          <td>{$email}</td>
          <td>{$contact}</td>
          <td>{$docapp}</td>
        </tr>";
    }
}

function get_package()
{
    $rows = dbFetchAll("SELECT * FROM Package");
    foreach ($rows as $row) {
        $Package_id = h($row['Package_id']);
        $Package_name = h($row['Package_name']);
        $Amount = h($row['Amount']);
        echo "<tr>
          <td>{$Package_id}</td>
          <td>{$Package_name}</td>
          <td>\${$Amount}</td>
        </tr>";
    }
}

function get_trainer()
{
    $rows = dbFetchAll("SELECT * FROM Trainer");
    foreach ($rows as $row) {
        $Trainer_id = h($row['Trainer_id']);
        $Name = h($row['Name']);
        $phone = h($row['phone']);
        echo "<tr>
          <td>{$Trainer_id}</td>
          <td>{$Name}</td>
          <td>{$phone}</td>
        </tr>";
    }
}

function get_payment()
{
    $rows = dbFetchAll("SELECT * FROM Payment ORDER BY Payment_id DESC");
    foreach ($rows as $row) {
        $Payment_id = h($row['Payment_id']);
        $Amount = h($row['Amount']);
        $payment_type = h($row['payment_type']);
        $customer_id = h($row['customer_id']);
        $customer_name = h($row['customer_name'] ?? '');
        echo "<tr>
          <td>{$Payment_id}</td>
          <td>\${$Amount}</td>
          <td>{$payment_type}</td>
          <td>{$customer_id}</td>
          <td>{$customer_name}</td>
        </tr>";
    }
}
