<?php
session_start();
require 'db_connect.php';

// Ensure the student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: student-login.php");
    exit();
}

// Fetch the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch the document requests made by the logged-in user
$stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = :user_id ORDER BY request_date DESC");
$stmt->execute(['user_id' => $user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Status</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  
  <style>
  /* General Styling */
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background: var(--background-color, #f5f5f5);
    color: var(--text-color, #333);
    transition: background-color 0.3s ease, color 0.3s ease;
    overflow-x: hidden;
    overflow-y: auto;
}

.container {
    max-width: 1150px;
    margin: 30px auto;
    padding: 40px;
    background: var(--container-bg, #ffffff);
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color, #e1e1e1);
    transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Title Styling */
h1 {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-color, #333);
    text-align: center;
    margin-bottom: 30px;
    font-family: 'Merriweather', serif;
    letter-spacing: 0.05em;
}

h1:after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background: var(--accent-color, #7f8c8d);
    margin: 15px auto;
    border-radius: 3px;
}

/* Table Styling */
.table {
    width: 100%;
    border-radius: 12px;
    background: var(--table-bg, #ffffff);
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.table thead {
    background: var(--header-bg, #f8f9fa);
    color: var(--header-text-color, #666);
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    border-bottom: 2px solid var(--border-color, #ddd);
}

.table tbody tr {
    background: var(--row-bg, #ffffff);
    border-bottom: 1px solid var(--border-color, #f1f1f1);
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.table tbody tr:nth-child(even) {
    background: var(--row-alt-bg, #f9f9f9);
}

.table tbody tr:hover {
    background: var(--row-hover-bg, #f1f1f1);
    transform: scale(1.01);
}

.table tbody td {
    padding: 18px;
    font-size: 1rem;
    color: var(--text-color, #333);
    vertical-align: middle;
}

/* General Badge Styling */
.badge {
    display: inline-block;
    padding: 0.5em 1.2em;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: capitalize;
    text-align: center;
    transition: background-color 0.2s ease, color 0.2s ease;
}

/* Status: Requested */
.badge.bg-secondary {
    background: #6c757d; /* Neutral gray */
    color: #ffffff !important; /* White text */
}

/* Status: In Progress */
.badge.bg-warning {
    background: #ffa726; /* Orange for "In Progress" */
    color: #ffffff; /* White text */
}

/* Status: Ready for Pickup */
.badge.bg-success {
    background: #28a745; /* Green for "Ready for Pickup" */
    color: #ffffff; /* White text */
}

/* Status: Rejected */
.badge.bg-danger {
    background: #dc3545; /* Red for "Rejected" */
    color: #ffffff; /* White text */
}

/* Button Styling */
.btn {
    padding: 12px 25px;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 500;
    text-transform: capitalize;
    border: 2px solid transparent;
    background: var(--btn-bg, #7f8c8d); /* Default background */
    color: #fff; /* White text */
    transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
}

/* Button Hover Effect */
.btn:hover {
    background: var(--btn-hover-bg, #6c757d); /* Darker background for hover */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}

/* Alerts */
.alert {
    padding: 20px;
    border-radius: 10px;
    font-size: 1rem;
    background: var(--alert-bg, #f8f9fa);
    color: var(--alert-text-color, #666);
    border: 1px solid var(--alert-border-color, #e0e0e0);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

/* Dark Mode Variables */
body.dark-mode {
    --background-color: #121212;
    --text-color: #121212;
    --container-bg: #1e1e1e;
    --border-color: #2a2a2a;
    --primary-color: #ffffff;
    --accent-color: #4caf50;
    --table-bg: #1c1c1c;
    --header-bg: #242424;
    --header-text-color: #bbb;
    --row-bg: #1f1f1f; /* Darker background for rows */
    --row-alt-bg: #262626; /* Darker alternate rows */
    --row-hover-bg: #2d2d2d; /* Slightly lighter hover effect */
    --badge-secondary-bg: #333333;
    --badge-text-color: #bbb;
    --badge-warning-bg: #f57c00;
    --badge-success-bg: #388e3c;
    --badge-danger-bg: #d32f2f;
    --alert-bg: #212121;
    --alert-border-color: #424242;
 
}

  </style>
</head>
<body>
<div class="container mt-5">
  <h1 class="text-center mb-4 display-5 fw-bold text-gradient">My Document Requests</h1>
  
  <!-- Search Filter -->
  <div class="mb-4">
    <input type="text" id="searchFilter" class="form-control" placeholder="Search by any field (e.g., Document Type, Year, Status)">
  </div>
  
  <?php if (count($documents) > 0): ?>
   <div class="table-responsive">
      <table id="documents-table" class="table table-striped table-hover align-middle shadow-sm rounded">
        <thead class="table">
          <tr>
            <th>Document Type</th>
            <th>Year</th>
            <th>Department</th>
            <th>Faculty</th>
            <th>Preferred Language</th>
            <th>Copies</th>
            <th>Request Date</th>
            <th>Due Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($documents as $doc): ?>
            <tr>
              <td class="fw-bold"><?php echo htmlspecialchars($doc['document_type']); ?></td>
              <td><?php echo htmlspecialchars($doc['year']); ?></td>
              <td><?php echo htmlspecialchars($doc['department']); ?></td>
              <td><?php echo htmlspecialchars($doc['faculty']); ?></td>
              <td><?php echo htmlspecialchars($doc['preferred_language']); ?></td>
              <td><?php echo htmlspecialchars($doc['copies']); ?></td>
              <td><?php echo htmlspecialchars(date("M d, Y", strtotime($doc['request_date']))); ?></td>
              <td><?php echo htmlspecialchars(date("M d, Y", strtotime($doc['due_date']))); ?></td>
              <td>
                <?php
                switch ($doc['status']) {
                    case 'requested':
                        echo '<span class="badge bg-secondary"><i class="bi bi-clock"></i> Requested</span>';
                        break;
                    case 'in_progress':
                        echo '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> In Progress</span>';
                        break;
                    case 'ready':
                        echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Ready for Pickup</span>';
                        break;
                    case 'rejected':
                        echo '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>';
                        break;
                }
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">No document requests found.</div>
  <?php endif; ?>
  
  <div class="mt-4 text-center">
    <a href="student-dashboard.php" class="btn btn-outline-primary btn-lg shadow-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>

<!-- JavaScript to filter table rows -->
<script>
  const searchFilter = document.getElementById('searchFilter');
  const tableRows = document.querySelectorAll('#documents-table tbody tr');

  searchFilter.addEventListener('input', function() {
    const filterText = this.value.toLowerCase();
    tableRows.forEach(row => {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(filterText) ? '' : 'none';
    });
  });
</script>

<!-- Include Bootstrap JS and icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"></script>
<script src="script.js"></script>
<script src="darkmode.js"></script>
</body>
</html>