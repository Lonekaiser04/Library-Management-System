<?php
// Start session at the VERY beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'auth.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>📚 Library Management System - Admin Dashboard</title>
  <style>
    /* Include all your existing CSS styles here */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #333;
    }

    /* Mobile Menu Button */
    .mobile-menu-btn {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1001;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      border-radius: 8px;
      width: 45px;
      height: 45px;
      font-size: 20px;
      cursor: pointer;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar {
      width: 280px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 30px 20px;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      z-index: 1000;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 40px;
      font-size: 24px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .sidebar a {
      color: #333;
      text-decoration: none;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-radius: 12px;
      margin-bottom: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .sidebar a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      transform: scaleY(0);
      transition: transform 0.3s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      transform: translateX(5px);
    }

    .sidebar a.active::before {
      transform: scaleY(1);
    }

    .sidebar footer {
      text-align: center;
      font-size: 13px;
      opacity: 0.7;
      padding-top: 20px;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }

    header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 20px 40px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }

    header h1 {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-size: 26px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
      flex-wrap: wrap;
    }

    .search-bar {
      position: relative;
      flex: 1;
      min-width: 250px;
    }

    .search-bar input {
      padding: 10px 40px 10px 15px;
      border: 2px solid rgba(102, 126, 234, 0.3);
      border-radius: 25px;
      width: 100%;
      outline: none;
      transition: all 0.3s ease;
    }

    .search-bar input:focus {
      border-color: #667eea;
      box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
    }

    .search-bar button {
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      padding: 8px 15px;
      border-radius: 20px;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .search-bar button:hover {
      transform: translateY(-50%) scale(1.05);
    }

    .container {
      padding: 30px;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 25px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .card h3 {
      font-size: 42px;
      margin-bottom: 10px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .card p {
      color: #666;
      font-weight: 500;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 1px;
    }

    tr:hover {
      background: rgba(102, 126, 234, 0.05);
    }

    .status {
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
    }

    .status.available {
      background: #d4edda;
      color: #155724;
    }

    .status.issued {
      background: #fff3cd;
      color: #856404;
    }

    .status.overdue {
      background: #f8d7da;
      color: #721c24;
    }

    .action-btn {
      padding: 8px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      margin: 2px;
      transition: all 0.3s ease;
      font-size: 12px;
    }

    .btn-edit {
      background: #667eea;
      color: white;
    }

    .btn-delete {
      background: #f56565;
      color: white;
    }

    .btn-issue {
      background: #48bb78;
      color: white;
    }

    .btn-return {
      background: #ed8936;
      color: white;
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    form {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      margin: 0 auto;
    }

    form label {
      display: block;
      margin-top: 18px;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
    }

    form input, form select {
      width: 100%;
      padding: 14px;
      border: 2px solid rgba(102, 126, 234, 0.3);
      border-radius: 12px;
      outline: none;
      transition: all 0.3s ease;
      font-size: 15px;
    }

    form input:focus, form select:focus {
      border-color: #667eea;
      box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
    }

    form button {
      width: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 16px;
      margin-top: 25px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    form button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .section-header h2 {
      font-size: 28px;
      color: white;
    }

    .filter-group {
      display: flex;
      gap: 15px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .filter-group select {
      padding: 10px 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.95);
      cursor: pointer;
      outline: none;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(5px);
    }

    .modal-content {
      background: white;
      margin: 5% auto;
      padding: 30px;
      border-radius: 20px;
      max-width: 600px;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
      animation: slideDown 0.3s ease;
      width: 90%;
    }

    @keyframes slideDown {
      from {
        transform: translateY(-50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 32px;
      font-weight: bold;
      cursor: pointer;
      line-height: 20px;
    }

    .close:hover {
      color: #000;
    }

    .hidden {
      display: none;
    }

    /* Profile Section Styles */
    .profile-container {
      max-width: 600px;
      margin: 0 auto;
    }

    .profile-info {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .profile-field {
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .profile-field label {
      font-weight: 600;
      color: #333;
      display: block;
      margin-bottom: 8px;
    }

    .profile-field .value {
      color: #666;
      font-size: 16px;
    }

    /* Info Box Styles */
    .info-box {
      background: #e2f0ff;
      border: 1px solid #b3d9ff;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
    }

    .info-box h4 {
      color: #0066cc;
      margin-bottom: 10px;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 1024px) {
      .sidebar {
        position: fixed;
        height: 100%;
        transform: translateX(-100%);
      }
      
      .sidebar.open {
        transform: translateX(0);
      }
      
      .mobile-menu-btn {
        display: block;
      }
      
      .main-content {
        width: 100%;
      }
      
      header {
        padding: 15px 20px;
      }
      
      .container {
        padding: 20px;
      }
      
      .cards {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
      }
      
      .card {
        padding: 20px;
      }
      
      .card h3 {
        font-size: 36px;
      }
      
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
      
      .search-bar {
        min-width: 200px;
      }
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .user-info {
        width: 100%;
        justify-content: space-between;
      }
      
      .search-bar {
        min-width: 100%;
        margin-top: 10px;
      }
      
      .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      
      .filter-group {
        width: 100%;
      }
      
      .filter-group select {
        flex: 1;
        min-width: 150px;
      }
      
      .modal-content {
        padding: 20px;
        margin: 10% auto;
      }
      
      form {
        padding: 20px;
      }
    }

    @media (max-width: 480px) {
      .cards {
        grid-template-columns: 1fr;
      }
      
      .card h3 {
        font-size: 32px;
      }
      
      .action-btn {
        display: block;
        width: 100%;
        margin: 5px 0;
      }
      
      th, td {
        padding: 10px 8px;
        font-size: 14px;
      }
      
      header h1 {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>

  <button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>

  <div class="sidebar" id="sidebar">
    <div>
      <h2>📚 Library Pro</h2>
      <a href="#" onclick="showSection('home')" class="active" id="nav-home">
        <span>🏠</span> Dashboard
      </a>
      <a href="#" onclick="showSection('books')" id="nav-books">
        <span>📘</span> Books
      </a>
      <a href="#" onclick="showSection('add-book')" id="nav-add-book">
        <span>➕</span> Add Book
      </a>
      <a href="#" onclick="showSection('students')" id="nav-students">
        <span>👥</span> Students
      </a>
      <a href="#" onclick="showSection('add-student')" id="nav-add-student">
        <span>👤</span> Add Student
      </a>
      <a href="#" onclick="showSection('transactions')" id="nav-transactions">
        <span>📊</span> Transactions
      </a>
      <a href="#" onclick="showSection('profile')" id="nav-profile">
        <span>👤</span> Profile
      </a>
      <a href="#" onclick="showSection('user-management')" id="nav-user-management">
        <span>👥</span> User Management
      </a>
    </div>
    <footer>
      <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)</p>
      <p>© 2025 | Kaiser, Auzair, Momin and Nawazish</p>
    </footer>
  </div>

  <div class="main-content">
    <header>
      <h1>Smart Library Management System - Admin Panel</h1>
      <div class="user-info">
        <div class="search-bar">
          <input type="text" id="globalSearch" placeholder="Search books, students..." onkeyup="globalSearch()">
          <button onclick="globalSearch()">🔍</button>
        </div>
        <p><strong>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> 
           (<a href="logout.php" style="color: #667eea; text-decoration: none; margin-left: 10px;">Logout</a>)</p>
      </div>
    </header>

    <div class="container">
      <!-- Dashboard Section -->
      <section id="home">
        <div class="section-header">
          <h2>📊 Admin Dashboard Overview</h2>
        </div>
        <div class="cards">
          <div class="card">
            <h3 id="totalBooks">0</h3>
            <p>Total Books</p>
          </div>
          <div class="card">
            <h3 id="availableBooks">0</h3>
            <p>Available Books</p>
          </div>
          <div class="card">
            <h3 id="issuedBooks">0</h3>
            <p>Issued Books</p>
          </div>
          <div class="card">
            <h3 id="totalStudents">0</h3>
            <p>Active Students</p>
          </div>
          <div class="card">
            <h3 id="totalUsers">0</h3>
            <p>Registered Users</p>
          </div>
          <div class="card">
            <h3 id="overdueBooks">0</h3>
            <p>Overdue Books</p>
          </div>
        </div>
      </section>

      <!-- Books Section -->
      <section id="books" class="hidden">
        <div class="section-header">
          <h2>📚 Book Management</h2>
        </div>
        <div class="filter-group">
          <select id="categoryFilter" onchange="filterBooks()">
            <option value="all">All Categories</option>
          </select>
          <select id="statusFilter" onchange="filterBooks()">
            <option value="all">All Status</option>
            <option value="available">Available</option>
            <option value="issued">Issued</option>
          </select>
        </div>
        <table id="booksTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>ISBN</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="booksTableBody"></tbody>
        </table>
      </section>

      <!-- Add Book Section -->
      <section id="add-book" class="hidden">
        <div class="section-header">
          <h2>➕ Add New Book</h2>
        </div>
        <form onsubmit="addBook(event)">
          <label>Book Title:</label>
          <input type="text" id="bookTitle" required>

          <label>Author:</label>
          <input type="text" id="bookAuthor" required>

          <label>Category:</label>
          <select id="bookCategory" required>
            <option value="Technology">Technology</option>
            <option value="Fiction">Fiction</option>
            <option value="Non-Fiction">Non-Fiction</option>
            <option value="Science">Science</option>
            <option value="History">History</option>
            <option value="Biography">Biography</option>
            <option value="Sports">Sports</option>
            <option value="Poetry">Poetry</option>
          </select>

          <label>ISBN:</label>
          <input type="text" id="bookISBN" required>

          <label>Quantity:</label>
          <input type="number" id="bookQuantity" min="1" value="1" required>

          <button type="submit">Add Book</button>
        </form>
      </section>

      <!-- Students Section -->
      <section id="students" class="hidden">
        <div class="section-header">
          <h2>👥 Student Management</h2>
        </div>
        <table id="studentsTable">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Course</th>
              <th>Semester</th>
              <th>Books Borrowed</th>
              <th>Join Date</th>
              <th>User Account</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="studentsTableBody"></tbody>
        </table>
      </section>

      <!-- Add Student Section -->
      <section id="add-student" class="hidden">
        <div class="section-header">
          <h2>👤 Add New Student</h2>
        </div>
        <form onsubmit="addStudent(event)">
          <label>Student ID:</label>
          <input type="text" id="studentId" required placeholder="e.g., STU001">
          
          <label>Full Name:</label>
          <input type="text" id="studentName" required>

          <label>Email:</label>
          <input type="email" id="studentEmail" required>

          <label>Phone:</label>
          <input type="tel" id="studentPhone" required>

          <label>Address:</label>
          <input type="text" id="studentAddress">

          <label>Course:</label>
          <input type="text" id="studentCourse" placeholder="e.g., Computer Science">

          <label>Semester:</label>
          <input type="text" id="studentSemester" placeholder="e.g., 3rd Semester">

          <button type="submit">Add Student</button>
        </form>
        
        <div class="info-box">
          <h4>📝 Note:</h4>
          <p>• A system-generated 8-digit password will be created automatically</p>
          <p>• The student will be forced to change password on first login</p>
          <p>• Student ID will be used as username for login</p>
        </div>
      </section>

      <!-- Transactions Section -->
      <section id="transactions" class="hidden">
        <div class="section-header">
          <h2>📊 Transaction History</h2>
        </div>
        <table id="transactionsTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Book</th>
              <th>Student</th>
              <th>Issue Date</th>
              <th>Due Date</th>
              <th>Return Date</th>
              <th>Status</th>
              <th>Fine</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="transactionsTableBody"></tbody>
        </table>
      </section>

      <!-- Profile Section -->
      <section id="profile" class="hidden">
        <div class="section-header">
          <h2>👤 Admin Profile</h2>
        </div>
        <div class="profile-container">
          <div class="profile-info">
            <div class="profile-field">
              <label>Full Name:</label>
              <div class="value"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
            </div>
            <div class="profile-field">
              <label>Username:</label>
              <div class="value"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
            </div>
            <div class="profile-field">
              <label>Email:</label>
              <div class="value"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
            </div>
            <div class="profile-field">
              <label>Admin ID:</label>
              <div class="value"><?php echo htmlspecialchars($_SESSION['admin_id'] ?? 'Not set'); ?></div>
            </div>
            <div class="profile-field">
              <label>Role:</label>
              <div class="value"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></div>
            </div>
          </div>
          
          <div class="profile-info">
            <h3>Change Password</h3>
            <form onsubmit="changePassword(event)">
              <label>Current Password:</label>
              <input type="password" id="currentPassword" required>
              
              <label>New Password:</label>
              <input type="password" id="newPassword" required>
              
              <label>Confirm New Password:</label>
              <input type="password" id="confirmNewPassword" required>
              
              <button type="submit">Change Password</button>
            </form>
          </div>
        </div>
      </section>

      <!-- User Management Section -->
      <section id="user-management" class="hidden">
        <div class="section-header">
          <h2>👥 User Management</h2>
        </div>
        <table id="usersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Student ID</th>
              <th>Temp Password</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersTableBody"></tbody>
        </table>
      </section>
    </div>
  </div>

  <!-- Issue Book Modal -->
  <div id="issueModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('issueModal')">&times;</span>
      <h2>Issue Book</h2>
      <form onsubmit="issueBook(event)">
        <input type="hidden" id="issueBookId">
        <label>Select Student:</label>
        <select id="issueStudentId" required></select>
        <label>Due Date:</label>
        <input type="date" id="issueDueDate" required>
        <button type="submit">Issue Book</button>
      </form>
    </div>
  </div>

  <!-- Edit Book Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('editModal')">&times;</span>
      <h2>Edit Book</h2>
      <form onsubmit="updateBook(event)">
        <input type="hidden" id="editBookId">
        <label>Book Title:</label>
        <input type="text" id="editBookTitle" required>
        <label>Author:</label>
        <input type="text" id="editBookAuthor" required>
        <label>Category:</label>
        <select id="editBookCategory" required>
          <option value="Technology">Technology</option>
          <option value="Fiction">Fiction</option>
          <option value="Non-Fiction">Non-Fiction</option>
          <option value="Science">Science</option>
          <option value="History">History</option>
          <option value="Biography">Biography</option>
          <option value="Sports">Sports</option>
          <option value="Poetry">Poetry</option>
        </select>
        <label>ISBN:</label>
        <input type="text" id="editBookISBN" required>
        <button type="submit">Update Book</button>
      </form>
    </div>
  </div>

<script>
    // Data Storage
    let books = [];
    let students = [];
    let transactions = [];
    let users = [];

async function initializeData() {
    try {
        console.log('Initializing admin dashboard data...');
        
        // Load dashboard stats first
        await loadDashboard();
        
        // Load other data with individual error handling
        try {
            await loadBooks();
        } catch (error) {
            console.warn('Could not load books:', error.message);
            books = [];
        }
        
        try {
            await loadStudents();
        } catch (error) {
            console.warn('Could not load students:', error.message);
            students = [];
        }
        
        try {
            await loadTransactions();
        } catch (error) {
            console.warn('Could not load transactions:', error.message);
            transactions = [];
        }
        
        try {
            await loadUsers();
        } catch (error) {
            console.warn('Could not load users:', error.message);
            users = [];
        }
        
        populateFilters();
        console.log('Dashboard initialized successfully');
        
    } catch (error) {
        console.error('Error initializing dashboard:', error);
        
        // Only show alert for session expiration, not for other errors
        if (error.message === 'SESSION_EXPIRED') {
            // This will be handled by fetchWithAuth
            return;
        }
        
        // For other initialization errors, log but don't disrupt the user
        console.error('Initialization error (non-critical):', error.message);
    }
}

async function fetchWithAuth(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            credentials: 'same-origin'
        });
        
        // First check if we got a response at all
        if (!response) {
            throw new Error('No response from server');
        }
        
        // Check for HTTP errors
        if (!response.ok) {
            // If it's an auth error, handle specifically
            if (response.status === 401 || response.status === 403) {
                throw new Error('SESSION_EXPIRED');
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Get response text first to check content type
        const responseText = await response.text();
        
        // Check if response is empty
        if (!responseText.trim()) {
            console.warn('Empty response from:', url);
            return null;
        }
        
        // Try to parse as JSON
        try {
            const data = JSON.parse(responseText);
            
            // Check for API errors in the JSON response
            if (data && data.error) {
                if (data.session_expired) {
                    throw new Error('SESSION_EXPIRED');
                }
                throw new Error(data.error);
            }
            
            return data;
        } catch (parseError) {
            // If it's not JSON, check if it's HTML (which means likely redirected to login)
            if (responseText.includes('<!DOCTYPE') || responseText.includes('<html') || responseText.includes('login')) {
                console.error('Received HTML response, likely redirected to login:', responseText.substring(0, 200));
                throw new Error('SESSION_EXPIRED');
            }
            
            // If it's a simple string response that's not JSON
            console.warn('Non-JSON response from', url, ':', responseText.substring(0, 100));
            return responseText;
        }
    } catch (error) {
        console.error('Fetch error for', url, ':', error);
        
        // Handle session expiration
        if (error.message === 'SESSION_EXPIRED') {
            if (confirm('Your session has expired. Would you like to login again?')) {
                window.location.href = 'login.php';
            }
            throw error; // Re-throw to stop execution
        }
        
        // For other errors, show a user-friendly message
        if (!error.message.includes('SESSION_EXPIRED')) {
            console.error('API Error:', error.message);
            // Don't alert for every error to avoid spam
        }
        
        throw error;
    }
}


    async function loadDashboard() {
        try {
            const stats = await fetchWithAuth('dashboard.php');
            document.getElementById('totalBooks').textContent = stats.totalBooks || 0;
            document.getElementById('availableBooks').textContent = stats.availableBooks || 0;
            document.getElementById('issuedBooks').textContent = stats.issuedBooks || 0;
            document.getElementById('totalStudents').textContent = stats.totalMembers || 0;
            document.getElementById('overdueBooks').textContent = stats.overdueBooks || 0;
            document.getElementById('totalUsers').textContent = stats.totalUsers || 0;
        } catch (error) {
            console.error('Error loading dashboard:', error);
            throw error;
        }
    }

    async function loadBooks() {
        try {
            books = await fetchWithAuth('books.php');
            renderBooks();
        } catch (error) {
            console.error('Error loading books:', error);
            throw error;
        }
    }

    async function loadStudents() {
        try {
            students = await fetchWithAuth('students.php');
            renderStudents();
        } catch (error) {
            console.error('Error loading students:', error);
            // Don't throw error for students if endpoint doesn't exist yet
            students = [];
            renderStudents();
        }
    }

    async function loadTransactions() {
        try {
            transactions = await fetchWithAuth('transactions.php');
            renderTransactions();
        } catch (error) {
            console.error('Error loading transactions:', error);
            throw error;
        }
    }

    async function loadUsers() {
        try {
            users = await fetchWithAuth('users.php');
            renderUsers();
        } catch (error) {
            console.error('Error loading users:', error);
            throw error;
        }
    }

    async function changePassword(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmNewPassword = document.getElementById('confirmNewPassword').value;
        
        if (newPassword !== confirmNewPassword) {
            alert('New passwords do not match!');
            return;
        }
        
        if (newPassword.length < 6) {
            alert('New password must be at least 6 characters long!');
            return;
        }
        
        try {
            const response = await fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'change_password',
                    currentPassword: currentPassword,
                    newPassword: newPassword
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Password changed successfully!');
                e.target.reset();
            } else {
                alert('Error changing password: ' + result.error);
            }
        } catch (error) {
            alert('Network error: ' + error.message);
        }
    }

    // Toggle sidebar on mobile
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('open');
    }

    // Navigation
    function showSection(sectionId) {
      document.querySelectorAll('section').forEach(s => s.classList.add('hidden'));
      document.getElementById(sectionId).classList.remove('hidden');
      
      document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
      document.getElementById('nav-' + sectionId).classList.add('active');
      
      // Close sidebar on mobile after selection
      if (window.innerWidth <= 1024) {
        toggleSidebar();
      }
    }

    async function addBook(e) {
        e.preventDefault();
        
        try {
            const bookData = {
                title: document.getElementById('bookTitle').value,
                author: document.getElementById('bookAuthor').value,
                category: document.getElementById('bookCategory').value,
                isbn: document.getElementById('bookISBN').value,
                quantity: parseInt(document.getElementById('bookQuantity').value)
            };

            const response = await fetch('books.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(bookData)
            });

            const result = await response.json();
            
            if (result.success) {
                e.target.reset();
                alert('Book added successfully!');
                await loadDashboard();
                await loadBooks();
                populateFilters();
            } else {
                alert('Error adding book: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error: ' + error.message);
        }
    }

    function renderBooks() {
      const tbody = document.getElementById('booksTableBody');
      if (!tbody) return;
      
      tbody.innerHTML = '';
      
      const categoryFilter = document.getElementById('categoryFilter')?.value || 'all';
      const statusFilter = document.getElementById('statusFilter')?.value || 'all';
      
      const filtered = books.filter(b => {
        const catMatch = categoryFilter === 'all' || b.category === categoryFilter;
        const statMatch = statusFilter === 'all' || 
                         (statusFilter === 'available' && b.available > 0) ||
                         (statusFilter === 'issued' && b.available < b.quantity);
        return catMatch && statMatch;
      });

      filtered.forEach(book => {
        const status = book.available > 0 ? 'available' : 'issued';
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${book.id}</td>
          <td>${book.title}</td>
          <td>${book.author}</td>
          <td>${book.category}</td>
          <td>${book.isbn}</td>
          <td><span class="status ${status}">${book.available}/${book.quantity} Available</span></td>
          <td>
            <button class="action-btn btn-edit" onclick="openEditModal(${book.id})">✏️ Edit</button>
            ${book.available > 0 ? `<button class="action-btn btn-issue" onclick="openIssueModal(${book.id})">📤 Issue</button>` : ''}
            <button class="action-btn btn-delete" onclick="deleteBook(${book.id})">🗑️ Delete</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    async function deleteBook(bookId) {
        if (!confirm('Are you sure you want to delete this book?')) return;
        
        try {
            const response = await fetch(`books.php?id=${bookId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Book deleted successfully!');
                await loadDashboard();
                await loadBooks();
                populateFilters();
            } else {
                alert('Cannot delete book with active transactions!');
            }
        } catch (error) {
            alert('Error deleting book!');
        }
    }

    function populateFilters() {
      const categories = [...new Set(books.map(b => b.category))];
      const catFilter = document.getElementById('categoryFilter');
      if (catFilter) {
        const current = catFilter.value;
        catFilter.innerHTML = '<option value="all">All Categories</option>';
        categories.forEach(cat => {
          catFilter.innerHTML += `<option value="${cat}">${cat}</option>`;
        });
        catFilter.value = current;
      }
    }

    function filterBooks() {
      renderBooks();
    }

    function openIssueModal(bookId) {
      document.getElementById('issueBookId').value = bookId;
      const select = document.getElementById('issueStudentId');
      select.innerHTML = '';
      students.forEach(s => {
        select.innerHTML += `<option value="${s.student_id}">${s.name} (${s.student_id})</option>`;
      });
      
      const today = new Date();
      const dueDate = new Date(today.setDate(today.getDate() + 14));
      document.getElementById('issueDueDate').value = dueDate.toISOString().split('T')[0];
      
      document.getElementById('issueModal').style.display = 'block';
    }

    async function issueBook(e) {
        e.preventDefault();
        
        const transactionData = {
            book_id: parseInt(document.getElementById('issueBookId').value),
            student_id: document.getElementById('issueStudentId').value,
            issue_date: new Date().toISOString().split('T')[0],
            due_date: document.getElementById('issueDueDate').value
        };

        try {
            const response = await fetch('transactions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transactionData)
            });

            const result = await response.json();
            
            if (result.success) {
                closeModal('issueModal');
                alert('Book issued successfully!');
                await loadDashboard();
                await loadBooks();
                await loadStudents();
                await loadTransactions();
            } else {
                alert('Error issuing book: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error issuing book:', error);
            alert('Network error: ' + error.message);
        }
    }

    function openEditModal(bookId) {
      const book = books.find(b => b.id === bookId);
      document.getElementById('editBookId').value = book.id;
      document.getElementById('editBookTitle').value = book.title;
      document.getElementById('editBookAuthor').value = book.author;
      document.getElementById('editBookCategory').value = book.category;
      document.getElementById('editBookISBN').value = book.isbn;
      document.getElementById('editModal').style.display = 'block';
    }

    async function updateBook(e) {
        e.preventDefault();
        
        const bookData = {
            id: parseInt(document.getElementById('editBookId').value),
            title: document.getElementById('editBookTitle').value,
            author: document.getElementById('editBookAuthor').value,
            category: document.getElementById('editBookCategory').value,
            isbn: document.getElementById('editBookISBN').value
        };

        const response = await fetch('books.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(bookData)
        });

        const result = await response.json();
        
        if (result.success) {
            closeModal('editModal');
            alert('Book updated successfully!');
            await loadBooks();
            await loadTransactions();
            populateFilters();
        } else {
            alert('Error updating book!');
        }
    }

async function addStudent(e) {
    e.preventDefault();
    
    const studentId = document.getElementById('studentId').value.trim();
    const studentName = document.getElementById('studentName').value.trim();
    const studentEmail = document.getElementById('studentEmail').value.trim();
    const studentPhone = document.getElementById('studentPhone').value.trim();
    
    // Basic validation
    if (!studentId || !studentName || !studentEmail || !studentPhone) {
        alert('Please fill in all required fields: Student ID, Name, Email, and Phone.');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(studentEmail)) {
        alert('Please enter a valid email address.');
        return;
    }
    
    const studentData = {
        student_id: studentId,
        full_name: studentName,
        email: studentEmail,
        phone: studentPhone,
        address: document.getElementById('studentAddress').value.trim(),
        course: document.getElementById('studentCourse').value.trim(),
        semester: document.getElementById('studentSemester').value.trim()
    };

    console.log('Adding student:', studentData);

    try {
        const response = await fetch('users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(studentData)
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
        }

        const result = await response.json();
        
        if (result.success) {
            e.target.reset();
            alert(`✅ Student account created successfully!\n\n📋 Student Credentials:\nStudent ID: ${studentData.student_id}\nTemporary Password: ${result.temp_password}\n\nPlease provide these credentials to the student. They will be required to change their password on first login.`);
            await loadUsers();
            await loadStudents();
            showSection('students');
        } else {
            alert('Error creating student: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error adding student:', error);
        alert('Error creating student account: ' + error.message);
    }
}

    function renderStudents() {
        const tbody = document.getElementById('studentsTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        if (students.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="10" style="text-align: center; padding: 20px; color: #666;">No students found</td>`;
            tbody.appendChild(tr);
            return;
        }

        students.forEach(student => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${student.student_id}</td>
                <td>${student.name}</td>
                <td>${student.email}</td>
                <td>${student.phone}</td>
                <td>${student.course || '-'}</td>
                <td>${student.semester || '-'}</td>
                <td>${student.books_borrowed || 0}</td>
                <td>${student.join_date}</td>
                <td>${student.user_id ? '✅ Linked' : '❌ Not Linked'}</td>
                <td>
                    <button class="action-btn btn-edit" onclick="viewStudentHistory('${student.student_id}')">📜 History</button>
                    <button class="action-btn btn-delete" onclick="deleteStudent('${student.student_id}')">🗑️ Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    async function deleteStudent(studentId) {
        if (!confirm('Are you sure you want to delete this student?')) return;
        
        try {
            const response = await fetch(`students.php?id=${studentId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Student deleted successfully!');
                await loadDashboard();
                await loadStudents();
            } else {
                alert('Cannot delete student with borrowed books!');
            }
        } catch (error) {
            alert('Error deleting student: ' + error.message);
        }
    }

    function viewStudentHistory(studentId) {
        const student = students.find(s => s.student_id === studentId);
        const studentTransactions = transactions.filter(t => t.student_id === studentId);
        
        alert(`Student: ${student.name}\nStudent ID: ${student.student_id}\nBooks Borrowed: ${student.books_borrowed || 0}\nTotal Transactions: ${studentTransactions.length}`);
    }

    function renderTransactions() {
        const tbody = document.getElementById('transactionsTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        const sorted = [...transactions].sort((a, b) => b.id - a.id);

        if (sorted.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="9" style="text-align: center; padding: 20px; color: #666;">No transactions found</td>`;
            tbody.appendChild(tr);
            return;
        }

        sorted.forEach(trans => {
            const bookTitle = trans.bookTitle || trans.book_title;
            const studentName = trans.studentName || trans.student_name || trans.memberName || trans.member_name;
            const studentId = trans.student_id || trans.member_id;
            const issueDate = trans.issueDate || trans.issue_date;
            const dueDate = trans.dueDate || trans.due_date;
            const returnDate = trans.returnDate || trans.return_date;
            const status = trans.status;
            const fine = trans.fine || 0;

            const today = new Date();
            const due = new Date(dueDate);
            let statusClass = status;

            if (status === 'issued' && due < today) {
                statusClass = 'overdue';
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${trans.id}</td>
                <td>${bookTitle}</td>
                <td>${studentName} (${studentId})</td>
                <td>${issueDate}</td>
                <td>${dueDate}</td>
                <td>${returnDate || '-'}</td>
                <td><span class="status ${statusClass}">${statusClass.toUpperCase()}</span></td>
                <td>₹${fine}</td>
                ${status === 'issued' ? 
                    `<td><button class="action-btn btn-return" onclick="returnBook(${trans.id})">↩️ Return</button></td>` : 
                    '<td>-</td>'
                }
            `;
            tbody.appendChild(tr);
        });
    }

    async function returnBook(transactionId) {
        if (!confirm('Mark this book as returned?')) return;

        const transaction = transactions.find(t => t.id === transactionId);
        
        if (!transaction) {
            alert('Transaction not found!');
            return;
        }

        const returnData = {
            id: transactionId
        };

        try {
            const response = await fetch('transactions.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(returnData)
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Book returned successfully!');
                await loadDashboard();
                await loadBooks();
                await loadStudents();
                await loadTransactions();
            } else {
                alert('Error returning book: ' + result.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error: ' + error.message);
        }
    }

    function renderUsers() {
        const tbody = document.getElementById('usersTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        users.forEach(user => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${user.id}</td>
                <td>${user.username}</td>
                <td>${user.full_name}</td>
                <td>${user.email}</td>
                <td><span class="status ${user.role === 'admin' ? 'available' : 'issued'}">${user.role.toUpperCase()}</span></td>
                <td>${user.student_id || '-'}</td>
                <td>${user.temp_password ? 'Yes' : 'No'}</td>
                <td>${user.created_at}</td>
                <td>
                    <button class="action-btn btn-edit" onclick="editUser(${user.id})">✏️ Edit</button>
                    ${user.role !== 'admin' ? `<button class="action-btn btn-delete" onclick="deleteUser(${user.id})">🗑️ Delete</button>` : ''}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    async function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        
        try {
            const response = await fetch(`users.php?id=${userId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('User deleted successfully!');
                await loadUsers();
                await loadDashboard();
            } else {
                alert('Error deleting user: ' + result.error);
            }
        } catch (error) {
            alert('Error deleting user!');
        }
    }

    // Global Search
    function globalSearch() {
      const query = document.getElementById('globalSearch').value.toLowerCase();
      if (!query) {
        renderBooks();
        renderStudents();
        return;
      }

      // Search in books
      const bookResults = books.filter(b => 
        b.title.toLowerCase().includes(query) ||
        b.author.toLowerCase().includes(query) ||
        b.isbn.toLowerCase().includes(query)
      );

      // Search in students
      const studentResults = students.filter(s =>
        s.name.toLowerCase().includes(query) ||
        s.email.toLowerCase().includes(query) ||
        s.student_id.toLowerCase().includes(query)
      );

      if (bookResults.length > 0) {
        showSection('books');
        const tbody = document.getElementById('booksTableBody');
        tbody.innerHTML = '';
        bookResults.forEach(book => {
          const status = book.available > 0 ? 'available' : 'issued';
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${book.id}</td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.category}</td>
            <td>${book.isbn}</td>
            <td><span class="status ${status}">${book.available}/${book.quantity} Available</span></td>
            <td>
              <button class="action-btn btn-edit" onclick="openEditModal(${book.id})">✏️ Edit</button>
              ${book.available > 0 ? `<button class="action-btn btn-issue" onclick="openIssueModal(${book.id})">📤 Issue</button>` : ''}
              <button class="action-btn btn-delete" onclick="deleteBook(${book.id})">🗑️ Delete</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } else if (studentResults.length > 0) {
        showSection('students');
        const tbody = document.getElementById('studentsTableBody');
        tbody.innerHTML = '';
        studentResults.forEach(student => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${student.student_id}</td>
            <td>${student.name}</td>
            <td>${student.email}</td>
            <td>${student.phone}</td>
            <td>${student.course || '-'}</td>
            <td>${student.semester || '-'}</td>
            <td>${student.books_borrowed || 0}</td>
            <td>${student.join_date}</td>
            <td>${student.user_id ? '✅ Linked' : '❌ Not Linked'}</td>
            <td>
              <button class="action-btn btn-edit" onclick="viewStudentHistory('${student.student_id}')">📜 History</button>
              <button class="action-btn btn-delete" onclick="deleteStudent('${student.student_id}')">🗑️ Delete</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        alert('No results found!');
      }
    }

    // Close Modal
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    // Close modal on outside click
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing dashboard...');
        initializeData();
    });
</script>
</body>
</html>