<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Elevate | Modern E-Learning Platform</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --accent: #4cc9f0;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #4ade80;
      --white: #ffffff;
      --radius: 12px;
      --shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(135deg, var(--light) 0%, #e9f2ff 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: var(--dark);
      line-height: 1.6;
    }

    .container {
      width: 90%;
      max-width: 1200px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 40px;
      padding: 20px;
    }

    .hero {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 24px;
      max-width: 800px;
    }

    .logo {
      font-size: 28px;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo svg {
      width: 32px;
      height: 32px;
    }

    h1 {
      font-size: clamp(2rem, 5vw, 3.5rem);
      font-weight: 800;
      background: linear-gradient(90deg, var(--primary), var(--accent));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin-bottom: 16px;
    }

    .subtitle {
      font-size: clamp(1rem, 2vw, 1.25rem);
      max-width: 600px;
      color: #4b5563;
      margin-bottom: 20px;
    }

    .features {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin: 20px 0;
    }

    .feature {
      display: flex;
      align-items: center;
      gap: 8px;
      background-color: var(--white);
      padding: 12px 16px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    .feature svg {
      color: var(--primary);
    }

    .card {
      background-color: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      width: 100%;
      max-width: 600px;
      padding: 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
    }

    .tabs {
      display: flex;
      gap: 16px;
      border-bottom: 2px solid #e5e7eb;
      width: 100%;
    }

    .tab {
      background: transparent;
      border: none;
      padding: 12px 16px;
      font-size: 16px;
      font-weight: 600;
      color: #6b7280;
      cursor: pointer;
      transition: var(--transition);
      position: relative;
    }

    .tab.active {
      color: var(--primary);
    }

    .tab.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: var(--primary);
    }

    .tab-content {
      width: 100%;
    }

    .tab-pane {
      display: none;
    }

    .tab-pane.active {
      display: block;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-group {
      margin-bottom: 20px;
      width: 100%;
    }

    .form-control {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #e5e7eb;
      border-radius: var(--radius);
      font-size: 16px;
      transition: var(--transition);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #4b5563;
    }

    .btn {
      display: inline-block;
      background-color: var(--primary);
      color: var(--white);
      border: none;
      padding: 14px 24px;
      font-size: 16px;
      font-weight: 600;
      border-radius: var(--radius);
      cursor: pointer;
      transition: var(--transition);
      width: 100%;
    }

    .btn:hover {
      background-color: var(--secondary);
      transform: translateY(-2px);
    }

    .btn-outline {
      background-color: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background-color: var(--primary);
      color: var(--white);
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
      z-index: 1000;
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease;
    }

    .modal-content {
      background-color: var(--white);
      border-radius: var(--radius);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      padding: 30px;
      width: 90%;
      max-width: 500px;
      position: relative;
    }

    .close-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f3f4f6;
      border-radius: 50%;
      cursor: pointer;
      transition: var(--transition);
    }

    .close-btn:hover {
      background-color: #e5e7eb;
    }

    .waves {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 15vh;
      min-height: 100px;
      max-height: 150px;
      z-index: -1;
    }

    .decoration {
      position: fixed;
      z-index: -1;
      opacity: 0.4;
    }

    .decoration-1 {
      top: 10%;
      left: 10%;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, var(--accent) 0%, rgba(255,255,255,0) 70%);
      border-radius: 50%;
      filter: blur(30px);
    }

    .decoration-2 {
      bottom: 20%;
      right: 10%;
      width: 250px;
      height: 250px;
      background: radial-gradient(circle, var(--primary) 0%, rgba(255,255,255,0) 70%);
      border-radius: 50%;
      filter: blur(40px);
    }

    @media (max-width: 768px) {
      .card {
        padding: 30px 20px;
      }
      
      .features {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>
  <div class="decoration decoration-1"></div>
  <div class="decoration decoration-2"></div>

  <div class="container">
    <div class="hero">
      <div class="logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
        </svg>
        E-learning
      </div>
      
      <h1>Transform Your Learning Journey</h1>
      <p class="subtitle">Access world-class education resources, engage with expert instructors, and accelerate your growth with our innovative learning platform.</p>
      
      <div class="features">
        <div class="feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          <span>Flexible Learning</span>
        </div>
        <div class="feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span>Learn At Your Pace</span>
        </div>
        <div class="feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span>Expert Instructors</span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="tabs">
        <button class="tab active" data-tab="student">Student</button>
        <button class="tab" data-tab="faculty">Faculty</button>
        <button class="tab" data-tab="admin">Admin</button>
      </div>

      <div class="tab-content">
        <div class="tab-pane active" id="student-tab">
          <form id="student-form" method="post" action="auth/student-login.php">
            <div class="form-group">
              <label class="form-label" for="student-email">Email</label>
              <input type="email" id="student-email" name="email" class="form-control" placeholder="your.email@example.com" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="student-password">Password</label>
              <input type="password" id="student-password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">Login as Student</button>
          </form>
        </div>

        <div class="tab-pane" id="faculty-tab">
          <form id="faculty-form" method="post" action="auth/faculty-login.php">
            <div class="form-group">
              <label class="form-label" for="faculty-email">Email</label>
              <input type="email" id="faculty-email" name="email" class="form-control" placeholder="faculty.email@example.com" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="faculty-password">Password</label>
              <input type="password" id="faculty-password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">Login as Faculty</button>
          </form>
        </div>

        <div class="tab-pane" id="admin-tab">
          <form id="admin-form" method="POST" action="auth/admin-login.php">
            <div class="form-group">
              <label class="form-label" for="admin-username">Username</label>
              <input type="text" id="admin-username" name="username" class="form-control" placeholder="admin username" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="admin-password">Password</label>
              <input type="password" id="admin-password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">Login as Admin</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <svg class="waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
    <path fill="rgba(67, 97, 238, 0.1)" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,218.7C672,235,768,245,864,234.7C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
  </svg>

  <script>
    // Tab functionality
    const tabs = document.querySelectorAll('.tab');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs and panes
        tabs.forEach(t => t.classList.remove('active'));
        tabPanes.forEach(p => p.classList.remove('active'));
        
        // Add active class to current tab and corresponding pane
        tab.classList.add('active');
        const tabName = tab.dataset.tab;
        document.getElementById(`${tabName}-tab`).classList.add('active');
      });
    });

    // Form submission handling (maintain the original functionality)
    const studentForm = document.getElementById('student-form');
    const facultyForm = document.getElementById('faculty-form');
    const adminForm = document.getElementById('admin-form');

    // Optional: Add form validation and submission handling here
    // This preserves the same functionality as the original code
    // while enhancing the user experience
  </script>
</body>
</html>