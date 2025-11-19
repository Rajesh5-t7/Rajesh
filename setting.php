<?php
// /turnament-pro/admin/setting.php
require_once 'common/header.php';

$message = '';
$error = '';

// Function to update a setting
function update_setting($conn, $key, $value) {
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param("sss", $key, $value, $value);
    return $stmt->execute();
}

// Handle UPI settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_upi_settings'])) {
    $admin_upi_id = $_POST['admin_upi_id'];
    
    // Update UPI ID
    update_setting($conn, 'admin_upi_id', $admin_upi_id);
    
    // Handle QR image upload
    if (isset($_FILES['admin_qr_image']) && $_FILES['admin_qr_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["admin_qr_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if(getimagesize($_FILES["admin_qr_image"]["tmp_name"])) {
             if (move_uploaded_file($_FILES["admin_qr_image"]["tmp_name"], $target_file)) {
                update_setting($conn, 'admin_qr_image', $target_file);
                $message = "Settings updated successfully.";
             } else {
                $error = "Sorry, there was an error uploading your file.";
             }
        } else {
            $error = "File is not an image.";
        }
    } else {
         $message = "UPI ID updated successfully.";
    }
}


// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password && !empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $_SESSION['admin_id']);
        if ($stmt->execute()) {
            $message = "Admin password updated successfully.";
        } else {
            $error = "Failed to update password.";
        }
    } else {
        $error = "Passwords do not match or are empty.";
    }
}

// Get current settings
$admin_upi_id = get_setting($conn, 'admin_upi_id');
$admin_qr_image = get_setting($conn, 'admin_qr_image');

?>
<h1 class="text-3xl font-bold text-white mb-6">Settings</h1>

<?php if ($message): ?>
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4 text-sm"><p><?php echo htmlspecialchars($message); ?></p></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm"><p><?php echo htmlspecialchars($error); ?></p></div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- UPI Settings -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">UPI Payment Settings</h2>
        <form method="POST" action="setting.php" enctype="multipart/form-data">
            <input type="hidden" name="update_upi_settings" value="1">
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">Admin UPI ID</label>
                <input type="text" name="admin_upi_id" value="<?php echo htmlspecialchars($admin_upi_id); ?>" class="w-full bg-gray-700 rounded py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">Upload New QR Code Image</label>
                <input type="file" name="admin_qr_image" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-500 file:text-gray-900 hover:file:bg-yellow-400">
                <?php if ($admin_qr_image): ?>
                    <p class="text-xs text-gray-500 mt-2">Current QR:</p>
                    <img src="<?php echo htmlspecialchars($admin_qr_image); ?>" alt="Current QR" class="w-24 h-24 mt-1 rounded bg-white p-1">
                <?php endif; ?>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-400">Save UPI Settings</button>
        </form>
    </div>

    <!-- Password Settings -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">Change Admin Password</h2>
        <form method="POST" action="setting.php">
             <input type="hidden" name="update_password" value="1">
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">New Password</label>
                <input type="password" name="new_password" class="w-full bg-gray-700 rounded py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" class="w-full bg-gray-700 rounded py-2 px-3" required>
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-gray-900 font-bold py-2 px-4 rounded-lg hover:bg-yellow-400">Update Password</button>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once 'common/bottom.php';
?>