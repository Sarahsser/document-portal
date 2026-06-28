<?php
session_start();
require 'db_connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit();
}

// Fetch logged-in admin's details
$admin_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = :admin_id");
$stmt->execute(['admin_id' => $admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Set default values if admin details are not found
$full_name = $admin['full_name'] ?? 'Admin Name';

// Fetch recent documents
$recent_treated_stmt = $pdo->prepare("
    SELECT * FROM documents 
    WHERE status IN ('ready', 'rejected') 
    ORDER BY created_at DESC 
    LIMIT 10
");
$recent_treated_stmt->execute();
$recent_treated_documents = $recent_treated_stmt->fetchAll(PDO::FETCH_ASSOC);

$recent_pending_stmt = $pdo->prepare("
    SELECT * FROM documents 
    WHERE status IN ('requested', 'in_progress') 
    ORDER BY created_at DESC 
    LIMIT 10
");
$recent_pending_stmt->execute();
$recent_pending_documents = $recent_pending_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="dashboard-styles.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
 /* Dark Mode Styles for Recent Documents Table */
 body.dark-mode .table {
    background-color: #000; /* Black background for the table */
    color: #fff; /* White text for table content */
    border-color: #555; /* Subtle border color */
  }

  body.dark-mode .table th {
    background-color: #222; /* Darker header background */
    color: #fff; /* White text for header */
  }

  body.dark-mode .table td {
    background-color: #222; /* Dark gray row background */
    color: #fff; /* White text */
  }

</style>

</head>
<body>

<div class="d-flex">
  <!-- Sidebar -->
  <nav class="sidebar">
    <div class="logo">
      <img src="assets/profile.jpg" alt="Profile">
      <h4><?php echo htmlspecialchars($full_name); ?></h4>
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

  <!-- Main Content -->
  <main class="content">
    <h2>Welcome to the Admin Dashboard !</h2>
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users"></i>
            <h5>Manage Users</h5>
            <p>View, edit, and manage all registered users in the system.</p>
            <a href="#" class="btn btn-primary">Get Started</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-file-alt"></i>
            <h5>Manage Documents</h5>
            <p>Oversee document approvals and track all student requests.</p>
            <a href="sort_document.php" class="btn btn-primary">Get Started</a>
          </div>
        </div>
      </div>
    </div>

    <h2>Recent Documents :</h2>
    <div class="mt-5">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#treated-documents">Recently Treated</button>
        </li>
        <li class="nav-item">
          <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#pending-documents">Pending Treatment</button>
        </li>
      </ul>

      <div class="tab-content mt-4">
        <!-- Recently Treated Documents -->
         <div class="tab-pane fade show active" id="treated-documents">
          <?php if (!empty($recent_treated_documents)): ?>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Document ID</th>
                  <th>Student Name</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Request Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_treated_documents as $doc): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($doc['id']); ?></td>
                    <td><?php echo htmlspecialchars($doc['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                    <td><?php echo htmlspecialchars($doc['STATUS']); ?></td>
                    <td><?php echo htmlspecialchars($doc['request_date']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No recently treated documents found.</p>
          <?php endif; ?>
         </div>
         <!-- Pending Documents -->
        <div class="tab-content mt-4">
          <div class="tab-pane fade" id="pending-documents">
          <?php if (!empty($recent_pending_documents)): ?>
            <table class="table table-bordered">
            
              <thead>
                <tr>
                  <th>Document ID</th>
                  <th>Student Name</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Request Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_pending_documents as $doc): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($doc['id']); ?></td>
                    <td><?php echo htmlspecialchars($doc['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                    <td><?php echo htmlspecialchars($doc['STATUS' ?? 'requested']); ?></td>
                    <td><?php echo htmlspecialchars($doc['request_date']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No pending documents found.</p>
          <?php endif; ?>
        </div>
       </div>
  
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script> 
</body>
</html>