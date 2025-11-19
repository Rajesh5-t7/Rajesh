<?php
// /turnament-pro/admin/user.php
require_once 'common/header.php';

// Fetch all users
$users_result = $conn->query("SELECT id, username, email, wallet_balance, created_at FROM users ORDER BY created_at DESC");
?>

<h1 class="text-3xl font-bold text-white mb-6">Manage Users</h1>

<div class="bg-gray-800 rounded-lg shadow-lg overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-300">
        <thead class="text-xs text-gray-400 uppercase bg-gray-700">
            <tr>
                <th scope="col" class="px-6 py-3">Username</th>
                <th scope="col" class="px-6 py-3">Email</th>
                <th scope="col" class="px-6 py-3">Wallet Balance</th>
                <th scope="col" class="px-6 py-3">Registered On</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($users_result->num_rows > 0): ?>
            <?php while($row = $users_result->fetch_assoc()): ?>
            <tr class="border-b border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium whitespace-nowrap"><?php echo htmlspecialchars($row['username']); ?></th>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['email']); ?></td>
                <td class="px-6 py-4 font-bold text-yellow-400"><?php echo format_currency($row['wallet_balance']); ?></td>
                <td class="px-6 py-4"><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center py-4">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
require_once 'common/bottom.php';
?>