<?php
// Only process POST requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form fields and remove whitespace.
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r", "\n"), array(" ", " "), $name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $cont_subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Check that data was sent to the mailer.
    if (empty($name) || empty($message) || empty($cont_subject) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Oops! There was a problem with your submission. Please complete the form and try again.";
        exit;
    }

    // Connect to the MySQL database on RDS
    $host = 'database-3.chaocgea2ln5.us-east-1.rds.amazonaws.com'; // เปลี่ยนเป็น endpoint ของ RDS
    $dbName = 'test.db'; // ชื่อฐานข้อมูล
    $user = 'admin';
    $pass = 'Natta123$';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement to insert data
        $stmt = $pdo->prepare("INSERT INTO contact_form (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $cont_subject);
        $stmt->bindParam(':message', $message);
        $stmt->execute();

        // Set a 200 (okay) response code.
        http_response_code(200);
        echo "Thank You! Your message has been recorded.";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
?>
