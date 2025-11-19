<?php
// /turnament-pro/admin/login.php
require_once __DIR__ . '/../common/config.php';

$error = '';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Invalid credentials.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Admin Login - Turnament Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-full select-none">
    <div class="bg-gray-800 p-8 rounded-lg shadow-2xl w-full max-w-sm mx-4">
        <h1 class="text-3xl font-bold text-center text-white mb-2">Turnament Pro</h1>
        <p class="text-center text-gray-400 mb-6">Admin Panel</p>

        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="login" value="1">
            <div class="mb-4">
                <label for="username" class="block text-gray-400 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" id="username" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-400 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-gray-900 font-bold py-2 px-4 rounded-lg hover:bg-yellow-400 transition-transform transform hover:scale-105">Login</button>
        </form>
    </div>
</body>
</html>