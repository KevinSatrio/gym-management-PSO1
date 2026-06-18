<?php
/**
 * Payments — View payment history and record new payments
 */
require_once 'func.php';

$page_title = "Payments";
$current_page = "payments";

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-credit-card-fill" style="margin-right:8px; color:var(--primary-500);"></i>Payments</h1>
        <p>View payment history and record new payments</p>
    </div>
</div>

<div class="row g-4">
    <!-- Payment History Table -->
    <div class="col-lg-8 fade-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clock-history" style="margin-right:8px; color:var(--primary-500);"></i>Payment History</h5>
            </div>
            <div class="card-body-compact">
                <div class="table-container">
                    <table class="table" id="tbl-payments">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php get_payment(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- New Payment Form -->
    <div class="col-lg-4 fade-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-plus-circle-fill" style="margin-right:8px; color:var(--success-500);"></i>New Payment</h5>
            </div>
            <div class="card-body">
                <form action="func.php" method="post" id="form-new-payment">
                    <div class="mb-3">
                        <label for="input-payment-id" class="form-label">Payment ID</label>
                        <input type="text" name="Payment_id" id="input-payment-id" class="form-control" placeholder="Enter payment ID" required>
                    </div>

                    <div class="mb-3">
                        <label for="input-amount" class="form-label">Amount</label>
                        <input type="text" name="Amount" id="input-amount" class="form-control" placeholder="Enter amount" required>
                    </div>

                    <div class="mb-3">
                        <label for="input-customer-id" class="form-label">Customer ID</label>
                        <input type="text" name="customer_id" id="input-customer-id" class="form-control" placeholder="Enter customer ID" required>
                    </div>

                    <div class="mb-3">
                        <label for="input-customer-name" class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" id="input-customer-name" class="form-control" placeholder="Enter customer name" required>
                    </div>

                    <div class="mb-3">
                        <label for="input-payment-type" class="form-label">Payment Type</label>
                        <input type="text" name="payment_type" id="input-payment-type" class="form-control" placeholder="e.g. Cash, Card, Transfer" required>
                    </div>

                    <button type="submit" name="pay_submit" class="btn btn-primary w-100" id="btn-submit-payment">
                        <i class="bi bi-credit-card"></i> Record Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>