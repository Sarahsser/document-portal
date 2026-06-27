<?php 
session_start();
require_once 'db_connect.php'; // Include your PDO database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST request
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $student_id = $_POST['student_id'];
    $year = $_POST['year'];
    $department = $_POST['department'];
    $document_type = $_POST['doc_type'];
    $copies = $_POST['copies'];
    $preferred_language = $_POST['language'];
    $faculty = $_POST['faculty'];
    $request_date = $_POST['current_date'];
    $due_date = $_POST['due_date'];

    try {
        // Retrieve the logged-in user's details
        $stmt = $pdo->prepare("SELECT full_name, email, student_id FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validate that the submitted details match the logged-in user
        if (!$user || $user['full_name'] !== $full_name || $user['email'] !== $email || $user['student_id'] !== $student_id) {
            echo json_encode(['status' => 'error', 'message' => 'Submitted details do not match the logged-in user\'s records.']);
            exit();
        }

        // SQL query to insert data into the documents table
        $sql = "INSERT INTO documents (
                    user_id, full_name, dob, email, student_id, year, department, document_type, copies,
                    preferred_language, faculty, request_date, due_date
                ) VALUES (
                    :user_id, :full_name, :dob, :email, :student_id, :year, :department, :document_type, :copies,
                    :preferred_language, :faculty, :request_date, :due_date
                )";

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':full_name' => $full_name,
            ':dob' => $dob,
            ':email' => $email,
            ':student_id' => $student_id,
            ':year' => $year,
            ':department' => $department,
            ':document_type' => $document_type,
            ':copies' => $copies,
            ':preferred_language' => $preferred_language,
            ':faculty' => $faculty,
            ':request_date' => $request_date,
            ':due_date' => $due_date
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Document request submitted successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>