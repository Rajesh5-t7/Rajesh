<?php
// /turnament-pro/admin/index.php
require_once 'common/header.php';

// Fetch stats
$total_users = $conn->query("SELECT COUNT(id) as count FROM users")->fetch_assoc()['count'];
$total_tournaments = $conn->query("SELECT COUNT(id) as count FROM tournaments")->fetch_assoc()['count'];
$pending_deposits = $conn->query("SELECT COUNT(id) as count FROM deposits WHERE status = 'Pending'")->fetch_assoc()['count'];
$pending_withdrawals = $conn->query("SELECT COUNT(id) as count FROM withdrawals WHERE status = 'Pending'")->fetch_assoc()['count'];

?>

<h1 class="text-3xl font-bold text-white mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-500/20 text-blue-400">
                <i class="fa-solid fa-users fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-400">Total Users</p>
                <p class="text-2xl font-bold"><?php echo $total_users; ?></p>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-500/20 text-green-400">
                <i class="fa-solid fa-trophy fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-400">Total Tournaments</p>
                <p class="text-2xl font-bold"><?php echo $total_tournaments; ?></p>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-500/20 text-yellow-400">
                <i class="fa-solid fa-arrow-down-to-bracket fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-400">Pending Deposits</p>
                <p class="text-2xl font-bold"><?php echo $pending_deposits; ?></p>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-500/20 text-red-400">
                <i class="fa-solid fa-arrow-up-from-bracket fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-400">Pending Withdrawals</p>
                <p class="text-2xl font-bold"><?php echo $pending_withdrawals; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 bg-gray-800 p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
    <div class="flex space-x-4">
        <a href="tournament.php" class="bg-yellow-500 text-gray-900 font-bold py-2 px-4 rounded-lg hover:bg-yellow-400 transition-transform transform hover:scale-105">
            <i class="fa-solid fa-plus mr-2"></i>New Tournament
        </a>
        <a href="deposit_requests.php" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-400 transition-transform transform hover:scale-105">
            View Deposits
        </a>
    </div>
</div>

<?php
$conn->close();
require_once 'common/bottom.php';
?>