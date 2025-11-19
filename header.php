<?php
// /turnament-pro/admin/common/header.php
require_once __DIR__ . '/../../common/config.php';

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: /turnament-pro/admin/login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username FROM admin WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin - Turnament Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans flex h-full select-none">
    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-gray-800 p-4 flex-shrink-0 flex flex-col">
        <h1 class="text-2xl font-bold text-white mb-8 text-center">Admin Panel</h1>
        <nav class="flex-grow">
            <a href="index.php" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 <?php echo ($current_page == 'index.php') ? 'bg-yellow-500 text-gray-900' : 'text-gray-400 hover:bg-gray-700 hover:text-white'; ?>">
                <i class="fa-solid fa-tachometer-alt w-6"></i><span class="ml-3">Dashboard</span>
            </a>
            <a href="tournament.php" class="flex items-center px-4 py-3 mt-2 rounded-lg transition-colors duration-200 <?php echo in_array($current_page, ['tournament.php', 'manage_tournament.php']) ? 'bg-yellow-500 text-gray-900' : 'text-gray-400 hover:bg-gray-700 hover:text-white'; ?>">
                <i class="fa-solid fa-trophy w-6"></i><span class="ml-3">Tournaments</span>
            </a>
            <a href="user.php" class="flex items-center px-4 py-3 mt-2 rounded-lg transition-colors duration-200 <?php echo ($current_page == 'user.php') ? 'bg-yellow-500 text-gray-900' : 'text-gray-400 hover:bg-gray-700 hover:text-white'; ?>">
                <i class="fa-solid fa-users w-6"></i><span class="ml-3">Users</span>
            </a>
             <a href="deposit_requests.php" class="flex items-center px-4 py-3 mt-2 rounded-lg transition-colors duration-200 <?php echo ($current_page == 'deposit_requests.php') ? 'bg-yellow-500 text-gray-900' : 'text-gray-400 hover:bg-gray-700 hover:text-white'; ?>">
                <i class="fa-solid fa-arrow-down-to-bracket w-6"></i><span class="ml-3">Deposits</span>
            </a>
             <a href="withdrawal_requests.php" class="flex items-center px-4 py-3 mt-2 rounded-lg transition-colors duration-200 <?php echo ($current_page == 'withdrawal_requests.php') ? 'bg-yellow-500 text-gray-900' : 'text-gray-400 hover:bg-gray-700 hover:text-white'; ?>">
                <i class="fa-solid fa-arrow-up-from-bracket w-6"></i><span class="ml-3">Withdrawals</span>
            </a>
            <a href="setting.php" class="flex items-center px-4 py-3 mt-2 rounded-lg transition-colors duration-200 <?php echo ($current_page == 'setting.php') ? 'bg-yellow-500 text-gray-900' : 'text-gray-400 hover:bg-gray-700 hover:text-white'; ?>">
                <i class="fa-solid fa-cog w-6"></i><span class="ml-3">Settings</span>
            </a>
        </nav>
        <div class="mt-auto">
             <form method="POST" action="login.php">
                <button type="submit" name="logout" class="w-full flex items-center px-4 py-3 mt-2 rounded-lg text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-colors duration-200">
                     <i class="fa-solid fa-right-from-bracket w-6"></i><span class="ml-3">Logout</span>
                </button>
             </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-gray-800 shadow p-4 flex justify-between items-center">
             <h2 class="text-xl font-bold">Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</h2>
             <!-- Add any header items here -->
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-6">
            <!-- Page content starts here -->