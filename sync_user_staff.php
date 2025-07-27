<?php
require_once('includes/db.php');

// Step 1: Fetch all users
$user_stmt = $conn->prepare("SELECT id, username, full_name, role FROM users");
$user_stmt->execute();
$user_result = $user_stmt->get_result();

$updated = 0;
$skipped = 0;

while ($user = $user_result->fetch_assoc()) {
    $username = $user['username'];
    $full_name = $user['full_name'];
    $role = $user['role'];

    // Step 2: Try to match staff by name or role
    $staff_stmt = $conn->prepare("SELECT id FROM staff WHERE staff_name = ? OR role = ? LIMIT 1");
    $staff_stmt->bind_param("ss", $full_name, $role);
    $staff_stmt->execute();
    $staff_result = $staff_stmt->get_result();

    if ($staff_result->num_rows === 1) {
        $staff = $staff_result->fetch_assoc();
        $staff_id = $staff['id'];

        // Step 3: Update staff.username
        $update_stmt = $conn->prepare("UPDATE staff SET username = ? WHERE id = ?");
        $update_stmt->bind_param("si", $username, $staff_id);
        if ($update_stmt->execute()) {
            $updated++;
        }
    } else {
        $skipped++;
    }
}

echo "✅ Staff usernames synced successfully.\n";
echo "🔄 Updated: $updated\n";
echo "⏭️ Skipped: $skipped\n";

$conn->close();
?>
