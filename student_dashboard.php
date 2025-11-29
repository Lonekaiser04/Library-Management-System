<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

if (!isLoggedIn() || !isStudent()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>📚 Library Management System - Student Dashboard</title>
  <style>
    /* Include all your existing CSS styles here, same as admin_dashboard.php */
    /* You can copy the exact same CSS from admin_dashboard.php */
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
      <a href="#" onclick="showSection('my-books')" id="nav-my-books">
        <span>📚</span> My Books
      </a>
      <a href="#" onclick="showSection('my-transactions')" id="nav-my-transactions">
        <span>📊</span> My Transactions
      </a>
      <a href="#" onclick="showSection('available-books')" id="nav-available-books">
        <span>🔍</span> Available Books
      </a>
      <a href="#" onclick="showSection('profile')" id="nav-profile">
        <span>👤</span> Profile
      </a>
    </div>
    <footer>
      <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Student)</p>
      <p>© 2025 | Kaiser, Auzair, Momin and Nawazish</p>
    </footer>
  </div>

  <div class="main-content">
    <header>
      <h1>Smart Library Management System - Student Portal</h1>
      <div class="user-info">
        <div class="search-bar">
          <input type="text" id="globalSearch" placeholder="Search available books..." onkeyup="globalSearch()">
          <button onclick="globalSearch()">🔍</button>
        </div>
        <p><strong>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> 
           (<a href="logout.php" style="color: #667eea; text-decoration: none; margin-left: 10px;">Logout</a>)</p>
      </div>
    </header>

    <div class="container">
      <!-- Student Dashboard Section -->
      <section id="home">
        <div class="section-header">
          <h2>📊 Student Dashboard</h2>
        </div>
        <div class="cards">
          <div class="card">
            <h3 id="myBorrowedBooks">0</h3>
            <p>Books Borrowed</p>
          </div>
          <div class="card">
            <h3 id="myOverdueBooks">0</h3>
            <p>Overdue Books</p>
          </div>
          <div class="card">
            <h3 id="totalAvailableBooks">0</h3>
            <p>Available Books</p>
          </div>
          <div class="card">
            <h3 id="myTotalTransactions">0</h3>
            <p>Total Transactions</p>
          </div>
        </div>

        <div class="section-header" style="margin-top: 40px;">
          <h2>📚 Currently Borrowed Books</h2>
        </div>
        <table id="currentBooksTable">
          <thead>
            <tr>
              <th>Book Title</th>
              <th>Author</th>
              <th>Issue Date</th>
              <th>Due Date</th>
              <th>Status</th>
              <th>Fine</th>
            </tr>
          </thead>
          <tbody id="currentBooksTableBody"></tbody>
        </table>
      </section>

      <!-- My Books Section -->
      <section id="my-books" class="hidden">
        <div class="section-header">
          <h2>📚 My Borrowed Books</h2>
        </div>
        <table id="myBooksTable">
          <thead>
            <tr>
              <th>Book Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>Issue Date</th>
              <th>Due Date</th>
              <th>Status</th>
              <th>Fine</th>
            </tr>
          </thead>
          <tbody id="myBooksTableBody"></tbody>
        </table>
      </section>

      <!-- My Transactions Section -->
      <section id="my-transactions" class="hidden">
        <div class="section-header">
          <h2>📊 My Transaction History</h2>
        </div>
        <table id="myTransactionsTable">
          <thead>
            <tr>
              <th>Book Title</th>
              <th>Issue Date</th>
              <th>Due Date</th>
              <th>Return Date</th>
              <th>Status</th>
              <th>Fine</th>
            </tr>
          </thead>
          <tbody id="myTransactionsTableBody"></tbody>
        </table>
      </section>

      <!-- Available Books Section -->
      <section id="available-books" class="hidden">
        <div class="section-header">
          <h2>🔍 Available Books</h2>
        </div>
        <div class="filter-group">
          <select id="categoryFilter" onchange="filterAvailableBooks()">
            <option value="all">All Categories</option>
          </select>
          <input type="text" id="searchBooks" placeholder="Search by title or author..." onkeyup="filterAvailableBooks()">
        </div>
        <table id="availableBooksTable">
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>ISBN</th>
              <th>Available</th>
            </tr>
          </thead>
          <tbody id="availableBooksTableBody"></tbody>
        </table>
      </section>

      <!-- Profile Section -->
      <section id="profile" class="hidden">
        <div class="section-header">
          <h2>👤 Student Profile</h2>
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
              <label>Student ID:</label>
              <div class="value"><?php echo htmlspecialchars($_SESSION['student_id'] ?? 'Not linked'); ?></div>
            </div>
            <div class="profile-field">
              <label>Role:</label>
              <div class="value"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></div>
            </div>
          </div>
          
          <div class="profile-info">
            <h3>Update Profile</h3>
            <form onsubmit="updateProfile(event)">
              <label>Email:</label>
              <input type="email" id="updateEmail" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
              
              <label>Full Name:</label>
              <input type="text" id="updateFullName" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" required>
              
              <button type="submit">Update Profile</button>
            </form>
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
    </div>
  </div>

  <script>
async function fetchWithAuth(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            if (response.status === 401 || response.status === 403) {
                throw new Error('SESSION_EXPIRED');
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        
        if (!responseText.trim()) {
            return null;
        }
        
        try {
            const data = JSON.parse(responseText);
            if (data && data.error) {
                if (data.session_expired) {
                    throw new Error('SESSION_EXPIRED');
                }
                throw new Error(data.error);
            }
            return data;
        } catch (parseError) {
            if (responseText.includes('<!DOCTYPE') || responseText.includes('<html') || responseText.includes('login')) {
                throw new Error('SESSION_EXPIRED');
            }
            return responseText;
        }
    } catch (error) {
        if (error.message === 'SESSION_EXPIRED') {
            if (confirm('Your session has expired. Would you like to login again?')) {
                window.location.href = 'login.php';
            }
            throw error;
        }
        console.error('API Error:', error.message);
        throw error;
    }
}

// Data Storage
let myBooks = [];
let myTransactions = [];
let availableBooks = [];
let studentStats = {};

async function initializeData() {
    try {
        console.log('Initializing student dashboard...');
        await loadStudentDashboard();
        await loadMyBooks();
        await loadMyTransactions();
        await loadAvailableBooks();
        populateCategoryFilters();
        console.log('Student dashboard initialized successfully');
    } catch (error) {
        console.error('Error initializing data:', error);
        if (!error.message.includes('SESSION_EXPIRED')) {
            // Show user-friendly error message
            const errorSection = document.getElementById('home');
            if (errorSection) {
                errorSection.innerHTML += `
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px 0;">
                        <strong>Error loading data:</strong> ${error.message}. Please refresh the page.
                    </div>
                `;
            }
        }
    }
}

async function loadStudentDashboard() {
    try {
        const data = await fetchWithAuth('studentdashboard.php');
        console.log('Dashboard data:', data); // Debug log
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        studentStats = data.stats || {};
        document.getElementById('myBorrowedBooks').textContent = studentStats.borrowedBooks || 0;
        document.getElementById('myOverdueBooks').textContent = studentStats.overdueBooks || 0;
        document.getElementById('totalAvailableBooks').textContent = studentStats.totalAvailableBooks || 0;
        document.getElementById('myTotalTransactions').textContent = studentStats.totalTransactions || 0;
        
        renderCurrentBooks(data.currentBooks || []);
    } catch (error) {
        console.error('Error loading dashboard:', error);
        // Show specific error message
        const errorSection = document.getElementById('home');
        if (errorSection) {
            errorSection.innerHTML += `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <strong>Error loading dashboard data:</strong> ${error.message}
                </div>
            `;
        }
        throw error;
    }
}

async function loadMyBooks() {
    try {
        myBooks = await fetchWithAuth('student_books.php');
        renderMyBooks();
    } catch (error) {
        console.error('Error loading my books:', error);
        myBooks = [];
        renderMyBooks();
    }
}

async function loadMyTransactions() {
    try {
        myTransactions = await fetchWithAuth('student_transactions.php');
        renderMyTransactions();
    } catch (error) {
        console.error('Error loading transactions:', error);
        myTransactions = [];
        renderMyTransactions();
    }
}

async function loadAvailableBooks() {
    try {
        availableBooks = await fetchWithAuth('student_books.php?available=1');
        renderAvailableBooks();
    } catch (error) {
        console.error('Error loading available books:', error);
        availableBooks = [];
        renderAvailableBooks();
    }
}

function renderCurrentBooks(books) {
    const tbody = document.getElementById('currentBooksTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (books.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="6" style="text-align: center; padding: 20px; color: #666;">No books currently borrowed</td>`;
        tbody.appendChild(tr);
        return;
    }

    books.forEach(book => {
        const today = new Date();
        const dueDate = new Date(book.due_date);
        const isOverdue = dueDate < today;
        const status = isOverdue ? 'overdue' : 'issued';
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${book.title || 'Unknown'}</td>
            <td>${book.author || 'Unknown'}</td>
            <td>${book.issue_date || 'Unknown'}</td>
            <td>${book.due_date || 'Unknown'}</td>
            <td><span class="status ${status}">${status.toUpperCase()}</span></td>
            <td>₹${book.fine || 0}</td>
        `;
        tbody.appendChild(tr);
    });
}

function renderMyBooks() {
    const tbody = document.getElementById('myBooksTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (myBooks.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="7" style="text-align: center; padding: 20px; color: #666;">No books found</td>`;
        tbody.appendChild(tr);
        return;
    }

    myBooks.forEach(book => {
        const today = new Date();
        const dueDate = new Date(book.due_date);
        const isOverdue = dueDate < today && book.status === 'issued';
        const status = isOverdue ? 'overdue' : (book.status || 'unknown');
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${book.title || 'Unknown'}</td>
            <td>${book.author || 'Unknown'}</td>
            <td>${book.category || 'Unknown'}</td>
            <td>${book.issue_date || 'Unknown'}</td>
            <td>${book.due_date || 'Unknown'}</td>
            <td><span class="status ${status}">${status.toUpperCase()}</span></td>
            <td>₹${book.fine || 0}</td>
        `;
        tbody.appendChild(tr);
    });
}

function renderMyTransactions() {
    const tbody = document.getElementById('myTransactionsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (myTransactions.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="6" style="text-align: center; padding: 20px; color: #666;">No transactions found</td>`;
        tbody.appendChild(tr);
        return;
    }

    const sorted = [...myTransactions].sort((a, b) => new Date(b.issue_date) - new Date(a.issue_date));

    sorted.forEach(trans => {
        const status = trans.status || 'unknown';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${trans.title || 'Unknown'}</td>
            <td>${trans.issue_date || 'Unknown'}</td>
            <td>${trans.due_date || 'Unknown'}</td>
            <td>${trans.return_date || '-'}</td>
            <td><span class="status ${status}">${status.toUpperCase()}</span></td>
            <td>₹${trans.fine || 0}</td>
        `;
        tbody.appendChild(tr);
    });
}

function renderAvailableBooks() {
    const tbody = document.getElementById('availableBooksTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (availableBooks.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="5" style="text-align: center; padding: 20px; color: #666;">No available books found</td>`;
        tbody.appendChild(tr);
        return;
    }

    availableBooks.forEach(book => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${book.title || 'Unknown'}</td>
            <td>${book.author || 'Unknown'}</td>
            <td>${book.category || 'Unknown'}</td>
            <td>${book.isbn || 'Unknown'}</td>
            <td><span class="status available">${book.available || 0} Available</span></td>
        `;
        tbody.appendChild(tr);
    });
}

function populateCategoryFilters() {
    const categories = [...new Set(availableBooks.map(b => b.category))];
    const catFilter = document.getElementById('categoryFilter');
    if (catFilter) {
        const current = catFilter.value;
        catFilter.innerHTML = '<option value="all">All Categories</option>';
        categories.forEach(cat => {
            if (cat) {
                catFilter.innerHTML += `<option value="${cat}">${cat}</option>`;
            }
        });
        catFilter.value = current;
    }
}

function filterAvailableBooks() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const searchQuery = document.getElementById('searchBooks').value.toLowerCase();
    
    const filtered = availableBooks.filter(book => {
        const catMatch = categoryFilter === 'all' || book.category === categoryFilter;
        const searchMatch = book.title.toLowerCase().includes(searchQuery) || 
                          book.author.toLowerCase().includes(searchQuery);
        return catMatch && searchMatch;
    });
    
    const tbody = document.getElementById('availableBooksTableBody');
    tbody.innerHTML = '';

    if (filtered.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="5" style="text-align: center; padding: 20px; color: #666;">No books match your search</td>`;
        tbody.appendChild(tr);
        return;
    }

    filtered.forEach(book => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.category}</td>
            <td>${book.isbn}</td>
            <td><span class="status available">${book.available} Available</span></td>
        `;
        tbody.appendChild(tr);
    });
}

async function updateProfile(e) {
    e.preventDefault();
    
    const email = document.getElementById('updateEmail').value;
    const fullName = document.getElementById('updateFullName').value;
    
    try {
        const result = await fetchWithAuth('profile.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'update_profile',
                email: email,
                full_name: fullName
            })
        });
        
        if (result.success) {
            alert('Profile updated successfully!');
            // Update session data in UI
            document.querySelector('.sidebar footer p:first-child').textContent = 
                `Welcome, ${fullName} (Student)`;
            document.querySelector('.user-info p strong').textContent = 
                `Welcome, ${fullName}`;
        } else {
            alert('Error updating profile: ' + result.error);
        }
    } catch (error) {
        if (!error.message.includes('SESSION_EXPIRED')) {
            alert('Network error: ' + error.message);
        }
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
        const result = await fetchWithAuth('profile.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'change_password',
                currentPassword: currentPassword,
                newPassword: newPassword
            })
        });
        
        if (result.success) {
            alert('Password changed successfully!');
            e.target.reset();
        } else {
            alert('Error changing password: ' + result.error);
        }
    } catch (error) {
        if (!error.message.includes('SESSION_EXPIRED')) {
            alert('Network error: ' + error.message);
        }
    }
}

function globalSearch() {
    const query = document.getElementById('globalSearch').value.toLowerCase();
    if (!query) {
        renderAvailableBooks();
        return;
    }

    const filtered = availableBooks.filter(book => 
        book.title.toLowerCase().includes(query) ||
        book.author.toLowerCase().includes(query) ||
        book.category.toLowerCase().includes(query)
    );

    if (filtered.length > 0) {
        showSection('available-books');
        const tbody = document.getElementById('availableBooksTableBody');
        tbody.innerHTML = '';
        filtered.forEach(book => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${book.title}</td>
                <td>${book.author}</td>
                <td>${book.category}</td>
                <td>${book.isbn}</td>
                <td><span class="status available">${book.available} Available</span></td>
            `;
            tbody.appendChild(tr);
        });
    } else {
        alert('No books found matching your search!');
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

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing student dashboard...');
    initializeData();
});
  </script>
</body>
</html>