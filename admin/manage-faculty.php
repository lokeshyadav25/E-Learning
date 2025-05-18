<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Fetch all faculty members from the database
$query = "SELECT * FROM faculty";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Faculty - Admin Panel</title>
    <style>
        /* Technologia-themed fresh blue & Segoe UI font */
        @import url('https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap');

        :root {
            --primary-blue: #0a84ff; /* fresh blue */
            --primary-blue-dark: #005fcc;
            --background-light: #f5f9ff;
            --text-dark: #222;
            --border-color: #d0d7de;
            --btn-radius: 6px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            margin: 0;
            padding: 20px 0;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            background: white;
            padding: 30px 40px;
            box-shadow: 0 4px 10px rgb(10 132 255 / 0.15);
            border-radius: 10px;
        }

        h1 {
            color: var(--primary-blue);
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        a.btn {
            display: inline-block;
            background-color: var(--primary-blue);
            color: white;
            padding: 12px 25px;
            border-radius: var(--btn-radius);
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }

        a.btn:hover {
            background-color: var(--primary-blue-dark);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        thead th {
            background-color: var(--primary-blue);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            user-select: none;
        }

        tbody tr {
            background: white;
            box-shadow: 0 1px 3px rgb(10 132 255 / 0.1);
            transition: transform 0.15s ease;
            border-radius: 8px;
        }

        tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgb(10 132 255 / 0.2);
        }

        tbody td {
            padding: 14px 15px;
            color: var(--text-dark);
            border-bottom: none;
            vertical-align: middle;
        }

        tbody td a {
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            margin: 0 6px;
            transition: color 0.25s ease;
        }

        tbody td a:hover {
            text-decoration: underline;
            color: var(--primary-blue-dark);
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }

            thead {
                display: none;
            }

            tbody tr {
                display: block;
                margin-bottom: 15px;
                box-shadow: none;
                border-radius: 10px;
                background: var(--background-light);
            }

            tbody td {
                display: flex;
                justify-content: space-between;
                padding: 12px 10px;
                border-bottom: 1px solid var(--border-color);
            }

            tbody td:last-child {
                border-bottom: 0;
            }

            tbody td:before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--primary-blue);
            }

            tbody td a {
                margin: 0 3px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Faculty</h1>
        <a href="add-faculty.php" class="btn">Add New Faculty</a>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($faculty = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="ID"><?php echo $faculty['id']; ?></td>
                            <td data-label="Full Name"><?php echo htmlspecialchars($faculty['full_name']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($faculty['email']); ?></td>
                            <td data-label="Actions">
                                <a href="edit-faculty.php?id=<?php echo $faculty['id']; ?>">Edit</a> |
                                <a href="delete-faculty.php?id=<?php echo $faculty['id']; ?>" onclick="return confirm('Are you sure you want to delete this faculty member?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No faculty members found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
