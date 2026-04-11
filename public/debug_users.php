<?php
/**
 * Debug script to check users in the database
 * DELETE THIS FILE AFTER DEBUGGING
 */

require_once __DIR__ . '/../app/config/database.php';

echo "<h2>Database Debug - Users</h2>";

try {
    $db = Database::connect();
    echo "<p style='color:green'>✅ Database connected successfully</p>";
    echo "<p>Server: " . $_SERVER['SERVER_NAME'] . "</p>";
    
    // Check if table exists
    $tables = mysqli_query($db, "SHOW TABLES");
    echo "<h3>Tables in database:</h3><ul>";
    while ($table = mysqli_fetch_array($tables)) {
        echo "<li>" . $table[0] . "</li>";
    }
    echo "</ul>";
    
    // Check users
    $result = mysqli_query($db, "SELECT id_user, username, password, role, status_aktif FROM tb_user");
    
    if (!$result) {
        echo "<p style='color:red'>❌ Query error: " . mysqli_error($db) . "</p>";
    } else {
        $count = mysqli_num_rows($result);
        echo "<h3>Users found: $count</h3>";
        
        if ($count > 0) {
            echo "<table border='1' cellpadding='8' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Username</th><th>Password</th><th>Role</th><th>Status Aktif</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id_user'] . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                echo "<td>" . $row['role'] . "</td>";
                echo "<td>" . $row['status_aktif'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:red'>❌ No users in database!</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
