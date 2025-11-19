<?php
// /turnament-pro/admin/withdrawal_requests.php
require_once 'common/header.php';

$message = '';
$error = '';

// Handle request action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'complete' or 'reject'
    
    $stmt = $conn->prepare("SELECT user_id, amount FROM withdrawals WHERE id = ? AND status = 'Pending'");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $withdrawal = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if($withdrawal){
        if($action === 'complete') {
            $conn->begin_transaction();
            try {
                // Deduct amount from user's wallet
                $stmt_wallet = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                $stmt_wallet->bind_param("di", $withdrawal['amount'], $withdrawal['user_id']);
                $stmt_wallet->execute();

                // Create a debit transaction record
                $desc = "Withdrawal completed (ID: " . $request_id . ")";
                $stmt_trans = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'debit', ?)");
                $stmt_trans->bind_param("ids", $withdrawal['user_id'], $withdrawal['amount'], $desc);
                $stmt_trans->execute();

                // Update withdrawal status
                $stmt_status = $conn->prepare("UPDATE withdrawals SET status = 'Completed' WHERE id = ?");
                $stmt_status->bind_param("i", $request_id);
                $stmt_status->execute();
                
                $conn->commit();
                $message = "Withdrawal #" . $request_id . " marked as completed.";
            } catch (mysqli_sql_exception $e) {
                $conn->rollback();
                $error = "Transaction failed: " . $e->getMessage();
            }
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE withdrawals SET status = 'Rejected' WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->close();
            $message = "Withdrawal #" . $request_id . " rejected.";
        }
    } else {
        $error = "Request not found or already processed.";
    }
}


$requests = $conn->query("SELECT w.*, u.username, u.upi_id FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.status = 'Pending' ORDER BY w.created_at ASC");
?>

<h1 class="text-3xl font-bold text-white mb-6">Withdrawal Requests</h1>

<?php if ($message): ?>
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4 text-sm"><p><?php echo htmlspecialchars($message); ?></p></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm"><p><?php echo htmlspecialchars($error); ?></p></div>
<?php endif; ?>

<div class="bg-gray-800 rounded-lg shadow-lg overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-300">
        <thead class="text-xs text-gray-400 uppercase bg-gray-700">
            <tr>
                <th class="px-6 py-3">User</th>
                <th class="px-6 py-3">Amount</th>
                <th class="px-6 py-3">UPI ID</th>
                <th class="px-6 py-3">Time</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($requests->num_rows > 0): ?>
            <?php while($row = $requests->fetch_assoc()): ?>
            <tr class="border-b border-gray-700">
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['username']); ?></td>
                <td class="px-6 py-4 font-bold"><?php echo format_currency($row['amount']); ?></td>
                <td class="px-6 py-4 font-mono"><?php echo htmlspecialchars($row['upi_id']); ?></td>
                <td class="px-6 py-4"><?php echo date("d M, h:i A", strtotime($row['created_at'])); ?></td>
                <td class="px-6 py-4 flex space-x-2">
                    <form method="POST" action="withdrawal_requests.php" onsubmit="return confirm('Confirm you have sent the money manually before completing.')">
                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="action" value="complete" class="font-medium text-green-400 hover:underline">Complete</button>
                    </form>
                     <form method="POST" action="withdrawal_requests.php">
                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="action" value="reject" class="font-medium text-red-400 hover:underline">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center py-4">No pending withdrawal requests.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
require_once 'common/bottom.php';
?>