<?php
// Start session and check if admin is logged in
session_start();
require 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit();
}

// Check if the status is being updated
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document_id'], $_POST['status'])) {
    $document_id = $_POST['document_id'];
    $status = $_POST['status'];

    // Update the status in the database
    $stmt = $pdo->prepare("UPDATE documents SET status = :status WHERE id = :document_id");
    $stmt->execute(['status' => $status, 'document_id' => $document_id]);

    // Return a success response for AJAX requests
    if (isset($_POST['ajax'])) {
        echo json_encode(['success' => true]);
        exit();
    }
}

// Initialize variables for sorting
$sort_by = $_GET['sort_by'] ?? 'year'; // Default sorting by year
$order_by = in_array($sort_by, ['year', 'document_type']) ? $sort_by : 'year';

// Fetch documents from database
$stmt = $pdo->prepare("SELECT * FROM documents ORDER BY $order_by");
$stmt->execute();
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sort Documents</title>
  <!-- Tailwind CSS from CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Style for the search bar */
    #searchFilter {
        width: 100%; /* Make it stretch across the container */
        max-width: 400px; /* Max width of the search input */
        padding: 0.75rem 1rem; /* Padding for a nicer feel */
        border-radius: 0.375rem; /* Rounded corners */
        border: 1px solid #ddd; /* Border to match the design */
        margin-bottom: 1rem; /* Add space below the search bar */
        transition: border-color 0.3s ease; /* Smooth border color transition */
    }

    #searchFilter:focus {
        border-color: #3b82f6; /* Blue color when focused */
        outline: none; /* Remove the default outline */
    }

    #searchFilter::placeholder {
        color: #6b7280; /* Placeholder color */
    }

    /* Default (light mode) styles */
    body {
        background-color: #f8fafc; /* Light background */
        color: #333; /* Dark text */
        transition: all 0.3s ease;
    }

    /* Button Style */
a.bg-yellow-500 {
    background-color: #6b7280; /* Classy grey background */
    color: white;              /* White text */
    transition: background-color 0.3s ease; /* Smooth transition */
    padding: 0.75rem 1.5rem;   /* Added padding for a larger button */
    border-radius: 0.375rem;   /* Rounded corners */
}

a.bg-yellow-500:hover {
    background-color: #4b5563; /* Darker grey on hover */
}

    table {
        background-color: #fff; /* Table background */
        color: #333; /* Table text */
    }

    table th, table td {
        border: 1px solid #ddd; /* Light border */
    }

    button {
        background-color: #3b82f6; /* Default button color */
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #2563eb; /* Button hover effect */
    }

    /* Dark Mode Styles */
    body.dark-mode {
        background-color: #333; /* Dark background */
        color: #e5e7eb; /* Light text */
    }

    table.dark-mode {
        background-color: #2d3748; /* Dark table background */
        color: #e5e7eb; /* Light table text */
    }

    table.dark-mode th, table.dark-mode td {
        border: 1px solid #4a5568; /* Dark border */
    }

    button.dark-mode {
        background-color: #4f46e5; /* Button color in dark mode */
    }
    .dark-mode h1{
      color:#ddd;
    }
    button.dark-mode:hover {
        background-color: #4338ca; /* Dark mode button hover effect */
    }

    input, select {
        background-color: #f3f4f6; /* Light input background */
        color: #333; /* Dark text */
        border: 1px solid #ddd; /* Light input border */
        padding: 0.5rem;
        border-radius: 0.375rem;
    }

    input.dark-mode, select.dark-mode {
        background-color: #2d3748; /* Dark input background */
        color: #e5e7eb; /* Light text in inputs */
        border: 1px solid #4a5568; /* Dark input border */
    }

    input:focus, select:focus {
        border-color: #3b82f6;
        outline: none;
    }

    input.dark-mode:focus, select.dark-mode:focus {
        border-color: #2563eb;
    }

    /* Status color changes */
    .status-in_progress {
        color: #f59e0b; /* In Progress: Yellow */
    }

    .status-ready {
        color: #10b981; /* Ready: Green */
    }

    .status-rejected {
        color: #ef4444; /* Rejected: Red */
    }

    /* Dark Mode Status colors */
    body.dark-mode .status-in_progress {
        color: #f59e0b; /* In Progress: Yellow */
    }

    body.dark-mode .status-ready {
        color: #10b981; /* Ready: Green */
    }

    body.dark-mode .status-rejected {
        color: #ef4444; /* Rejected: Red */
    }

    .dark-mode #searchFilter {
        color:#333;
    }
/* Dark Mode Styles for Table */
body.dark-mode table {
    background-color: #1e293b; /* Dark blue-gray background */
    color: #cbd5e1;           /* Soft light-blue text */
}

body.dark-mode table th, 
body.dark-mode table td {
    border-color: #334155; /* Blue-gray border for subtle contrast */
}

body.dark-mode table thead {
    background-color: #0f172a; /* Deep navy-blue for the header */
}

body.dark-mode table tr:hover {
    background-color: #334155; /* Blue-gray for hover effect */
    color: #e2e8f0;           /* Lighter text on hover for readability */
}

/* Dark Mode Styles for Select Dropdown */
body.dark-mode select {
    background-color: #2d3748; /* Dark background */
    color: #e5e7eb;            /* Light text color for readability */
    border-color: #4a5568;     /* Dark border for contrast */
}

body.dark-mode select option {
    background-color: #2d3748; /* Keep the dark background for options */
    color: #e5e7eb;            /* Light text color for options */
}

/* For when the select box is focused */
body.dark-mode select:focus {
    border-color: #3b82f6; /* Blue border when focused */
    outline: none;
}


  </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto mt-10 px-4">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-semibold text-gray-800">Sort Documents</h1>
    <a href="admin-dashboard.php" class="bg-yellow-500 text-white px-6 py-2 rounded-lg shadow-lg hover:bg-yellow-400 transition">Back to Dashboard</a>
  </div>

  <div class="mb-4">
    <input type="text" id="searchFilter" class="form-control" placeholder="Search by any field (e.g., Name, Email, Year)">
  </div>

  <div class="flex justify-between mb-4">
    <div>
      <a href="?sort_by=year" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-500 transition">Sort by Year</a>
      <a href="?sort_by=document_type" class="bg-gray-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-gray-500 transition ml-3">Sort by Document Type</a>
    </div>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
    <table class="min-w-full table-auto">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Full Name</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Email</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Year</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Department</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Faculty</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Document Type</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
        </tr>
      </thead>
      <tbody id="documents-table">
        <?php foreach ($documents as $doc): ?>
          <?php
          // Assigning text color based on document's status
          $status_class = '';
          switch ($doc['STATUS']) {
            case 'in_progress':
              $status_class = 'text-yellow-600'; // Yellow text for 'In Progress'
              break;
            case 'ready':
              $status_class = 'text-green-600'; // Green text for 'Ready for Pickup'
              break;
            case 'rejected':
              $status_class = 'text-red-600'; // Red text for 'Rejected'
              break;
            default:
              $status_class = 'text-gray-600'; // Default text color
          }
          ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['full_name']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['email']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['YEAR']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['department']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['faculty']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['document_type']); ?></td>
            <td class="px-4 py-2 <?php echo $status_class; ?>"><?php echo htmlspecialchars($doc['STATUS']); ?></td>
            <td class="px-4 py-2">
              <form method="post" class="status-update-form d-inline">
                <input type="hidden" name="document_id" value="<?php echo $doc['id']; ?>">
                <select name="status" class="form-select form-select-sm w-auto border-gray-300 focus:ring-2 focus:ring-blue-500">
                  <option value="in_progress" <?php echo $doc['STATUS'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                  <option value="ready" <?php echo $doc['STATUS'] === 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                  <option value="rejected" <?php echo $doc['STATUS'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <button type="submit" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-500 transition">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// JavaScript for search filter
document.addEventListener('DOMContentLoaded', function() {
  const searchFilter = document.getElementById('searchFilter');
  const tableRows = document.querySelectorAll('#documents-table tr'); // Target the rows inside tbody

  searchFilter.addEventListener('input', function() {
    const filterText = this.value.toLowerCase();  // Get the search text and convert to lowercase
    tableRows.forEach(row => {
      const rowText = row.textContent.toLowerCase(); // Get the row text and convert to lowercase
      row.style.display = rowText.includes(filterText) ? '' : 'none';  // Show row if it matches search text
    });
  });
});
</script>
<script src="darkmode.js"></script>

</body>
</html>