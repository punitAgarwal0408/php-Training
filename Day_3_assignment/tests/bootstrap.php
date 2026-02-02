<?php
/**
 * Test runner bootstrap.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

// Load application bootstrap
require dirname(__DIR__) . '/config/bootstrap.php';

// Ensure tests run with CLI server context
$_SERVER['PHP_SELF'] = '/';

// Create in-memory test schema if using SQLite in-memory connection
use Cake\Datasource\ConnectionManager;

try {
    $conn = ConnectionManager::get('test');
    $driver = $conn->config()['driver'] ?? '';

    if (strpos($driver, 'Sqlite') !== false || strpos($conn->config()['database'], ':memory:') !== false) {
        // Drop tables if they exist
        $conn->execute("DROP TABLE IF EXISTS registrations;");
        $conn->execute("DROP TABLE IF EXISTS training_sessions;");
        $conn->execute("DROP TABLE IF EXISTS users;");

        // Create users table
        $conn->execute("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created DATETIME NULL,
            modified DATETIME NULL
        );");

        // Create training_sessions table (with instructor_id and foreign key)
        $conn->execute("CREATE TABLE training_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            instructor_id INTEGER NOT NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME NOT NULL,
            max_participants INTEGER NULL,
            status VARCHAR(20) NULL,
            created DATETIME NULL,
            modified DATETIME NULL,
            FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE
        );");

        // Create registrations table
        $conn->execute("CREATE TABLE registrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            training_session_id INTEGER NOT NULL,
            created DATETIME NULL,
            modified DATETIME NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (training_session_id) REFERENCES training_sessions(id) ON DELETE CASCADE
        );");
    }
} catch (\Exception $e) {
    // Ignore - Connection may not be available at this stage for non-SQLite test setups
}

