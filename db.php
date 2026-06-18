<?php
/**
 * Centralized Database Connection & Query Helpers
 *
 * All new code should use this file instead of creating direct connections.
 * Provides prepared statement wrappers to prevent SQL injection.
 */

/**
 * Get a singleton mysqli database connection.
 *
 * @return mysqli
 */
function getDbConnection()
{
    static $conn = null;

    if ($conn === null) {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $name = getenv('DB_NAME') ?: 'loginsystem';

        $conn = new mysqli($host, $user, $pass, $name);

        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $conn->set_charset("utf8mb4");
    }

    return $conn;
}

/**
 * Execute a prepared statement and return the result.
 *
 * @param string      $sql    SQL query with ? placeholders
 * @param array       $params Parameter values
 * @param string|null $types  Type string (s=string, i=integer, d=double, b=blob)
 *                            If null, auto-detects from param types
 * @return mysqli_result|bool
 */
function dbQuery($sql, $params = [], $types = null)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    if (!empty($params)) {
        if ($types === null) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false && $stmt->errno) {
        die("Query execution failed: " . $stmt->error);
    }

    return $result ?: true;
}

/**
 * Fetch all rows from a SELECT query as associative arrays.
 *
 * @param string      $sql
 * @param array       $params
 * @param string|null $types
 * @return array
 */
function dbFetchAll($sql, $params = [], $types = null)
{
    $result = dbQuery($sql, $params, $types);

    if ($result === true) {
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Fetch a single row from a SELECT query.
 *
 * @param string      $sql
 * @param array       $params
 * @param string|null $types
 * @return array|null
 */
function dbFetchOne($sql, $params = [], $types = null)
{
    $result = dbQuery($sql, $params, $types);

    if ($result === true) {
        return null;
    }

    return $result->fetch_assoc();
}

/**
 * Execute an INSERT/UPDATE/DELETE and return affected row count.
 *
 * @param string      $sql
 * @param array       $params
 * @param string|null $types
 * @return int
 */
function dbExecute($sql, $params = [], $types = null)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    if (!empty($params)) {
        if ($types === null) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->affected_rows;
}

/**
 * Get the last auto-increment ID after an INSERT.
 *
 * @return int
 */
function dbGetInsertId()
{
    return getDbConnection()->insert_id;
}

/**
 * Count total rows matching a query.
 *
 * @param string      $sql    Should be a SELECT COUNT(*) query
 * @param array       $params
 * @param string|null $types
 * @return int
 */
function dbCountRows($sql, $params = [], $types = null)
{
    $row = dbFetchOne($sql, $params, $types);
    if ($row === null) {
        return 0;
    }
    return (int) reset($row);
}

/**
 * Escape a string for safe output in HTML.
 *
 * @param string|null $str
 * @return string
 */
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Set a flash message in the session.
 *
 * @param string $type    Message type: 'success', 'error', 'warning', 'info'
 * @param string $message The message text
 */
function setFlashMessage($type, $message)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear any flash message from the session.
 *
 * @return array|null ['type' => string, 'message' => string] or null
 */
function getFlashMessage()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }

    return null;
}
