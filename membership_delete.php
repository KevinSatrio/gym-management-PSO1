<?php
/**
 * Delete Membership — Soft-delete handler (POST only).
 *
 * Sets deleted_at = NOW() on the MembershipProgram record.
 * Redirects to membership.php with a flash message.
 */
require_once 'db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: membership.php');
    exit;
}

$membership_id = (int)($_POST['membership_id'] ?? 0);

if ($membership_id <= 0) {
    setFlashMessage('error', 'Invalid membership ID.');
    header('Location: membership.php');
    exit;
}

// Verify the record exists and is not already deleted
$existing = dbFetchOne(
    "SELECT membership_id FROM MembershipProgram WHERE membership_id = ? AND deleted_at IS NULL",
    [$membership_id]
);

if (!$existing) {
    setFlashMessage('error', 'Membership not found or has already been deleted.');
    header('Location: membership.php');
    exit;
}

// Soft delete
$affected = dbExecute(
    "UPDATE MembershipProgram SET deleted_at = NOW() WHERE membership_id = ? AND deleted_at IS NULL",
    [$membership_id]
);

if ($affected > 0) {
    setFlashMessage('success', 'Membership program deleted successfully.');
} else {
    setFlashMessage('error', 'Failed to delete the membership program.');
}

header('Location: membership.php');
exit;
