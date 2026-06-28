<?php
session_start();
require 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: student-login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id']; // Assuming `user_id` is stored in session
$stmt = $pdo->prepare("SELECT full_name, student_id, email FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$full_name = $user['full_name'] ?? 'Student Name';
$student_id = $user['student_id'] ?? 'Student ID';
$email = $user['email'] ?? 'No email found';

// Document categories based on `status`
$statuses = ['requested', 'in_progress', 'ready', 'rejected'];
$documents = [];

// Fetch documents for each status
foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = :user_id AND status = :status ORDER BY request_date DESC");
    $stmt->execute(['user_id' => $user_id, 'status' => $status]);
    $documents[$status] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="logo">
      <img src="assets/profile.jpg" alt="User Image">
          <h4><?php echo htmlspecialchars($full_name); ?></h4>
          <p>ID: <?php echo htmlspecialchars($student_id); ?></p>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-home"></i> Home</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-user"></i> Profile</a></li>
        <li class="nav-item"><a href="#" id="dark-mode-toggle" class="nav-link"><i class="fas fa-adjust"></i> Dark Mode</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-language"></i> Language</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-cog"></i> Settings</a></li>
  
      </ul>
      <div class="mt-auto">
             <li class="nav-item"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </div>
    </nav>

    <!-- Content -->
    <main class="content">
    <h2>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card transition">
            <div class="card-body">
              <i class="far fa-folder-open"></i>
              <h5>Request a Document</h5>
              <p>Ask for your school certificate, transcript, or other documents.</p>
               <a href="form.php" class="btn btn-primary">Request a Document</a>

            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card transition">
            <div class="card-body">
              <i class="far fa-bookmark"></i>
              <h5>Check Document Status</h5>
              <p>Stay updated on the status of your document requests.</p>
              <a href="check_document.php" class="btn btn-primary">Check Status</a>
            </div>
          </div>
        </div>
      </div>
      <h2>My Documents</h2>
      <div class="mt-5">
        <ul class="nav nav-tabs">
          <?php foreach ($statuses as $status): ?>
          <li class="nav-item">
            <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#<?php echo $status; ?>">
              <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
            </button>
          </li>
          <?php endforeach; ?>
        </ul>

        <div class="tab-content mt-4">
          <?php foreach ($statuses as $status): ?>
          <div class="tab-pane fade <?= $status === 'requested' ? 'show active' : '' ?>" id="<?php echo $status; ?>">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Document Type</th>
                  <th>Request Date</th>
                  <th>Due Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
  <?php foreach ($documents[$status] as $doc): ?>
  <tr>
    <td><?= htmlspecialchars($doc['document_type']) ?></td>
    <td><?= htmlspecialchars($doc['request_date']) ?></td>
    <td><?= htmlspecialchars($doc['due_date']) ?></td>
    <td>
      <?php
      // Assign color class based on the status
      $status_class = '';
      switch ($doc['STATUS']) {
        case 'in_progress':
          $status_class = 'bg-warning text-dark'; // Bootstrap "warning" for in-progress status
          break;
        case 'ready':
          $status_class = 'bg-success text-white'; // Bootstrap "success" for ready status
          break;
        case 'rejected':
          $status_class = 'bg-danger text-white'; // Bootstrap "danger" for rejected status
          break;
        default:
          $status_class = 'bg-secondary text-white'; // Default color for unknown status
          break;
      }
      ?>
      <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($doc['status'] ?? 'requested'); ?></span>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (empty($documents[$status])): ?>
  <tr>
    <td colspan="4" class="text-center">No documents found.</td>
  </tr>
  <?php endif; ?>
</tbody>

            </table>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 
 <script src="script.js"></script> 

</body>
</html>