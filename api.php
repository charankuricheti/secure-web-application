<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

function respond(int $status, array $body): never {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir) && !mkdir($dataDir, 0700, true) && !is_dir($dataDir)) {
        throw new RuntimeException('Storage could not be initialized.');
    }

    $db = new PDO('sqlite:' . $dataDir . '/contacts.sqlite', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $db->exec('CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->query('SELECT id, name, email FROM contacts ORDER BY id DESC LIMIT 100');
        respond(200, ['contacts' => $stmt->fetchAll()]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: GET, POST');
        respond(405, ['error' => 'Method not allowed.']);
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) respond(400, ['error' => 'Invalid JSON request.']);

    $name = trim((string)($input['name'] ?? ''));
    $email = trim((string)($input['email'] ?? ''));

    if ($name === '' || mb_strlen($name) > 80) {
        respond(422, ['error' => 'Name is required and must be 80 characters or fewer.']);
    }
    if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(422, ['error' => 'Enter a valid email address.']);
    }

    // Prepared statements keep user data separate from SQL instructions.
    $stmt = $db->prepare('INSERT INTO contacts (name, email) VALUES (:name, :email)');
    $stmt->execute([':name' => $name, ':email' => $email]);
    respond(201, ['message' => 'Contact saved.']);
} catch (Throwable $e) {
    // Do not expose database errors, file paths, or secrets to the browser.
    error_log('Contact API failure: ' . $e->getMessage());
    respond(500, ['error' => 'A server error occurred. Please try again.']);
}
