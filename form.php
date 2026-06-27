<?php
session_start(); 
require 'db_connect.php';

// Ensure the email is properly set
if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = 'No email found'; // Default if not set
}
$email = $_SESSION['email'];

// Check if the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: student-login.php");
    exit();
}

// Fetch logged-in user's details
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$stmt = $pdo->prepare("SELECT full_name, student_id, email FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Set default values if user details are not found
$full_name = $user['full_name'] ?? 'Student Name';
$student_id = $user['student_id'] ?? 'Student ID';
$email = $user['email'] ?? $email; // Update email if found in DB
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Request Form</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="form.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
</head>

<body>
  <div class="main-container d-flex flex-column align-items-center justify-content-center vh-100">
    <div id="formContainer" class="login-container">
      <h1 class="mb-4">Document Request Form</h1>
      <form id="requestForm" method="POST">
        <!-- Personal Details Section -->
        <div class="form-section">
          <div class="details personal">
            <div class="fields row g-3">
              <div class="col-12">
                <h6><i class="fas fa-user me-2"></i>Personal Details:</h6>
              </div>
              <div class="col-md-6">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" id="fullName" value="<?php echo htmlspecialchars($full_name); ?>" readonly>
              </div>
              <div class="col-md-6">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob" id="dob" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
              </div>
              <div class="col-md-6">
                <label for="studentId" class="form-label">Student ID</label>
                <input type="text" class="form-control" name="student_id" id="studentId" value="<?php echo htmlspecialchars($student_id); ?>" readonly>
              </div>
              <div class="col-md-6">
                <label for="year" class="form-label">Year</label>
                <select name="year" id="year" class="form-select" required>
                  <option disabled selected>Select year</option>
                  <option>L1</option>
                  <option>L2</option>
                  <option>L3</option>
                  <option>M1</option>
                  <option>M2</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <select name="department" id="department" class="form-select" required>
                  <option disabled selected>Select department</option>
                  <option>Informatique</option>
                  <option>Physique</option>
                  <option>Biologie</option>
                  <option>ST</option>
                  <option>Others</option>
                </select>
              </div>
              <div class="col-12">
                <h6><i class="fas fa-file"></i></i> Document details:</h6>
              </div>
              <div class="col-md-6">
                <label for="docType" class="form-label">Type of Document</label>
                <select name="doc_type" id="docType" class="form-select" required>
                  <option disabled selected>Select type</option>
                  <option>Certificat de Scolarité</option>
                  <option>Attestation de Bonne Conduite</option>
                  <option>Relevé de Notes</option>
                  <option>Diplôme</option>
                  <option>Others</option>
                </select>
              </div>
              <div class="col-md-6">
  <label for="copies" class="form-label">Number of Copies</label>
  <select name="copies" id="copies" class="form-select" required>
    <option value="" disabled selected>Select number of copies</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
  </select>
  

              <div class="col-md-6">
                <label for="language" class="form-label">Preferred Language</label>
                <select name="language" id="language" class="form-select" required>
                  <option disabled selected>Select language</option>
                  <option>French</option>
                  <option>Arabic</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="faculty" class="form-label">Faculty</label>
                <select name="faculty" id="faculty" class="form-select" required>
                  <option disabled selected>Select faculty</option>
                  <option>INIM</option>
                  <option>INGM</option>
                  <option>INH</option>
                  <option>Others</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="currentDate" class="form-label">Current Date</label>
                <input type="date" class="form-control" name="current_date" id="currentDate" required>
              </div>
              <div class="col-md-6">
                <label for="dueDate" class="form-label">Due Date</label>
                <input type="date" class="form-control" name="due_date" id="dueDate" required>
              </div>
            </div>
            <button type="submit" class="login-btn w-100 mt-4">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-light text-secondary">
        <h5 class="modal-title">Request Submitted</h5>
      </div>
      <div class="modal-body text-center">
        <i class="fas fa-check-circle fa-3x text-success mb-3 "></i>
        <p class="mb-0">Your document request has been submitted successfully!</p>
      </div>
      <div class="modal-footer d-flex justify-content-between bg-light">
        <button type="button" class="btn btn-primary" id="redirectButton">Go back to  Dashboard</button>
      </div>
    </div>
  </div>
</div>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    const form = document.getElementById('requestForm');
    const modal = new bootstrap.Modal(document.getElementById('successModal'));

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(form);

      fetch('request_document.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            modal.show();
          } else {
            alert(data.message);
          }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('redirectButton').addEventListener('click', () => {
      window.location.href = 'student-dashboard.php';
    });
  </script>
  <script src="darkmode.js"></script>
  <script src="form.js"></script>

</body>

</html>