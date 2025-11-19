<?php
// /turnament-pro/admin/tournament.php
require_once 'common/header.php';

$message = '';
$error = '';

// Handle creating a new tournament
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tournament'])) {
    $title = $_POST['title'];
    $game_name = $_POST['game_name'];
    $entry_fee = $_POST['entry_fee'];
    $prize_pool = $_POST['prize_pool'];
    $match_time = $_POST['match_time'];
    $commission = $_POST['commission_percentage'];

    if (!empty($title) && !empty($game_name) && is_numeric($entry_fee) && is_numeric($prize_pool) && !empty($match_time)) {
        $stmt = $conn->prepare("INSERT INTO tournaments (title, game_name, entry_fee, prize_pool, match_time, commission_percentage) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddsi", $title, $game_name, $entry_fee, $prize_pool, $match_time, $commission);
        if ($stmt->execute()) {
            $message = "Tournament created successfully!";
        } else {
            $error = "Failed to create tournament.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all required fields correctly.";
    }
}

// Handle deleting a tournament
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tournament'])) {
    $tournament_id = $_POST['tournament_id'];
    // You should also delete participants to avoid orphaned rows
    $conn->query("DELETE FROM participants WHERE tournament_id = $tournament_id");
    $stmt = $conn->prepare("DELETE FROM tournaments WHERE id = ?");
    $stmt->bind_param("i", $tournament_id);
    if ($stmt->execute()) {
        $message = "Tournament deleted successfully.";
    } else {
        $error = "Failed to delete tournament.";
    }
    $stmt->close();
}


// Fetch all tournaments
$tournaments_result = $conn->query("SELECT * FROM tournaments ORDER BY created_at DESC");
?>

<h1 class="text-3xl font-bold text-white mb-6">Manage Tournaments</h1>

<?php if ($message): ?>
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4 text-sm">
    <p><?php echo htmlspecialchars($message); ?></p>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
    <p><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>


<!-- Add Tournament Form -->
<div class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
    <h2 class="text-xl font-bold mb-4">Add New Tournament</h2>
    <form method="POST" action="tournament.php" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="add_tournament" value="1">
        <div>
            <label class="block text-gray-400 text-sm font-bold mb-2">Title</label>
            <input type="text" name="title" class="w-full bg-gray-700 rounded py-2 px-3" required>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-bold mb-2">Game Name</label>
            <input type="text" name="game_name" class="w-full bg-gray-700 rounded py-2 px-3" required>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-bold mb-2">Entry Fee (₹)</label>
            <input type="number" step="0.01" name="entry_fee" class="w-full bg-gray-700 rounded py-2 px-3" required>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-bold mb-2">Prize Pool (₹)</label>
            <input type="number" step="0.01" name="prize_pool" class="w-full bg-gray-700 rounded py-2 px-3" required>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-bold mb-2">Match Time</label>
            <input type="datetime-local" name="match_time" class="w-full bg-gray-700 rounded py-2 px-3" required>
        </div>
        <div>
            <label class="block text-gray-400 text-sm font-bold mb-2">Commission (%)</label>
            <input type="number" name="commission_percentage" class="w-full bg-gray-700 rounded py-2 px-3" value="0" required>
        </div>
        <div class="md:col-span-2">
            <button type="submit" class="w-full bg-yellow-500 text-gray-900 font-bold py-2 px-4 rounded-lg hover:bg-yellow-400 transition-transform transform hover:scale-105">Create Tournament</button>
        </div>
    </form>
</div>

<!-- List of Tournaments -->
<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <table class="w-full text-sm text-left text-gray-300">
        <thead class="text-xs text-gray-400 uppercase bg-gray-700">
            <tr>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">Fee</th>
                <th scope="col" class="px-6 py-3">Prize</th>
                <th scope="col" class="px-6 py-3">Time</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($tournaments_result->num_rows > 0): ?>
            <?php while($row = $tournaments_result->fetch_assoc()): ?>
            <tr class="border-b border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium whitespace-nowrap"><?php echo htmlspecialchars($row['title']); ?></th>
                <td class="px-6 py-4"><?php echo format_currency($row['entry_fee']); ?></td>
                <td class="px-6 py-4"><?php echo format_currency($row['prize_pool']); ?></td>
                <td class="px-6 py-4"><?php echo date("d M, h:i A", strtotime($row['match_time'])); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['status']); ?></td>
                <td class="px-6 py-4 flex space-x-2">
                    <a href="manage_tournament.php?id=<?php echo $row['id']; ?>" class="font-medium text-blue-400 hover:underline">Manage</a>
                    <form method="POST" action="tournament.php" onsubmit="return confirm('Are you sure you want to delete this tournament?');">
                        <input type="hidden" name="tournament_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_tournament" class="font-medium text-red-400 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center py-4">No tournaments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
require_once 'common/bottom.php';
?>