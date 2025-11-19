<?php
// /turnament-pro/admin/manage_tournament.php
require_once 'common/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: tournament.php");
    exit();
}

$tournament_id = $_GET['id'];
$message = '';
$error = '';

// Handle updating Room ID/Pass
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {
    $room_id = $_POST['room_id'];
    $room_password = $_POST['room_password'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tournaments SET room_id = ?, room_password = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $room_id, $room_password, $status, $tournament_id);
    if ($stmt->execute()) {
        $message = "Tournament details updated successfully.";
    } else {
        $error = "Failed to update details.";
    }
    $stmt->close();
}

// Handle declaring winner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['declare_winner'])) {
    $winner_id = $_POST['winner_id'];
    
    // Fetch tournament prize and commission
    $stmt_tourney = $conn->prepare("SELECT prize_pool, commission_percentage, title FROM tournaments WHERE id = ? AND status != 'Completed'");
    $stmt_tourney->bind_param("i", $tournament_id);
    $stmt_tourney->execute();
    $tourney = $stmt_tourney->get_result()->fetch_assoc();
    $stmt_tourney->close();

    if ($tourney) {
        $prize = $tourney['prize_pool'];
        $commission = $tourney['commission_percentage'];
        $winnings = $prize - ($prize * ($commission / 100));

        $conn->begin_transaction();
        try {
            // Update winner's wallet
            $stmt_wallet = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt_wallet->bind_param("di", $winnings, $winner_id);
            $stmt_wallet->execute();
            
            // Record transaction
            $desc = "Prize money for " . $tourney['title'];
            $stmt_trans = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'credit', ?)");
            $stmt_trans->bind_param("ids", $winner_id, $winnings, $desc);
            $stmt_trans->execute();

            // Update tournament status
            $stmt_status = $conn->prepare("UPDATE tournaments SET status = 'Completed', winner_id = ? WHERE id = ?");
            $stmt_status->bind_param("ii", $winner_id, $tournament_id);
            $stmt_status->execute();
            
            $conn->commit();
            $message = "Winner declared and prize distributed successfully!";
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $error = "Transaction failed: " . $exception->getMessage();
        }
    } else {
        $error = "Tournament already completed or does not exist.";
    }
}


// Fetch tournament details
$stmt = $conn->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$tournament = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tournament) {
    header("Location: tournament.php"); exit();
}

// Fetch participants
$participants_stmt = $conn->prepare("SELECT u.id, u.username FROM users u JOIN participants p ON u.id = p.user_id WHERE p.tournament_id = ?");
$participants_stmt->bind_param("i", $tournament_id);
$participants_stmt->execute();
$participants = $participants_stmt->get_result();
$participants_stmt->close();
?>

<a href="tournament.php" class="text-yellow-400 hover:text-yellow-300 mb-6 inline-block"><i class="fa-solid fa-arrow-left mr-2"></i>Back to Tournaments</a>
<h1 class="text-3xl font-bold text-white mb-6">Manage: <?php echo htmlspecialchars($tournament['title']); ?></h1>

<?php if ($message): ?>
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4 text-sm"><p><?php echo htmlspecialchars($message); ?></p></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm"><p><?php echo htmlspecialchars($error); ?></p></div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Details & Winner Declaration -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Update Form -->
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Tournament Details</h2>
            <form method="POST" action="manage_tournament.php?id=<?php echo $tournament_id; ?>">
                <input type="hidden" name="update_room" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Room ID</label>
                        <input type="text" name="room_id" value="<?php echo htmlspecialchars($tournament['room_id'] ?? ''); ?>" class="w-full bg-gray-700 rounded py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Room Password</label>
                        <input type="text" name="room_password" value="<?php echo htmlspecialchars($tournament['room_password'] ?? ''); ?>" class="w-full bg-gray-700 rounded py-2 px-3">
                    </div>
                     <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Status</label>
                        <select name="status" class="w-full bg-gray-700 rounded py-2 px-3">
                            <option value="Upcoming" <?php if($tournament['status'] == 'Upcoming') echo 'selected'; ?>>Upcoming</option>
                            <option value="Running" <?php if($tournament['status'] == 'Running') echo 'selected'; ?>>Running</option>
                            <option value="Completed" <?php if($tournament['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-400">Update Details</button>
            </form>
        </div>
        
        <!-- Declare Winner Form -->
        <?php if ($tournament['status'] !== 'Completed'): ?>
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Declare Winner</h2>
            <form method="POST" action="manage_tournament.php?id=<?php echo $tournament_id; ?>">
                <input type="hidden" name="declare_winner" value="1">
                <label class="block text-gray-400 text-sm font-bold mb-2">Select Winner</label>
                <select name="winner_id" class="w-full bg-gray-700 rounded py-2 px-3 mb-4" required>
                    <option value="">-- Select a participant --</option>
                    <?php 
                        $participants->data_seek(0); // Reset pointer
                        while($p = $participants->fetch_assoc()): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['username']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="w-full bg-green-500 text-gray-900 font-bold py-2 px-4 rounded-lg hover:bg-green-400">Declare & Distribute Prize</button>
            </form>
        </div>
        <?php else: ?>
         <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
             <i class="fa-solid fa-check-circle text-green-400 fa-3x"></i>
             <h2 class="text-xl font-bold mt-4">Tournament Completed</h2>
             <?php
                if($tournament['winner_id']){
                    $winner_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                    $winner_stmt->bind_param("i", $tournament['winner_id']);
                    $winner_stmt->execute();
                    $winner = $winner_stmt->get_result()->fetch_assoc();
                    echo "<p class='mt-2 text-gray-300'>Winner: <strong class='text-yellow-400'>" . htmlspecialchars($winner['username']) . "</strong></p>";
                }
             ?>
         </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Participants List -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">Participants (<?php echo $participants->num_rows; ?>)</h2>
        <ul class="space-y-2 max-h-96 overflow-y-auto">
            <?php if ($participants->num_rows > 0): 
                $participants->data_seek(0); // Reset pointer
                while($p = $participants->fetch_assoc()): ?>
                <li class="bg-gray-700 p-3 rounded-lg"><?php echo htmlspecialchars($p['username']); ?></li>
            <?php endwhile; else: ?>
                <li class="text-gray-500">No participants have joined yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php
$conn->close();
require_once 'common/bottom.php';
?>