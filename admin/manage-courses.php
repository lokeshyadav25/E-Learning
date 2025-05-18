<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$search_sql = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $search_sql = " WHERE c.title LIKE '%$search_escaped%' ";
}

$count_query = "SELECT COUNT(*) as total FROM courses c $search_sql";
$count_result = $conn->query($count_query);
$total_courses = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_courses / $limit);

$query = "
    SELECT c.id, c.title, c.description, c.created_at, c.price, f.full_name 
    FROM courses c
    LEFT JOIN faculty f ON c.faculty_id = f.id
    $search_sql
    ORDER BY c.created_at DESC
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Courses - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        .container {
            max-width: 1150px;
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 0 20px hsla(209, 66.70%, 51.80%, 0.86);
        }
        h1 {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .table thead th {
            background-color: #e9ecef;
            font-weight: 600;
        }
        .description-cell {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            border-radius: 6px;
        }
        .pagination .page-link {
            color: #495057;
        }
        .pagination .page-item.active .page-link {
            background-color:rgb(62, 127, 192);
            color: #fff;
            border-color:rgb(61, 133, 206);
        }
        .form-control {
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Courses</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="add-course.php" class="btn btn-dark px-4">Add New Course</a>

        <form class="d-flex" method="GET" action="">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control me-2" placeholder="Search by title..." />
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </form>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Faculty</th>
                    <th>Price (₹)</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($course = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['id']); ?></td>
                    <td><?php echo htmlspecialchars($course['title'] ?? ''); ?></td>
                    <td class="description-cell" title="<?php echo htmlspecialchars($course['description'] ?? ''); ?>">
                        <?php 
                            $desc = $course['description'] ?? '';
                            echo htmlspecialchars(mb_strlen($desc) > 60 ? mb_substr($desc, 0, 60) . '...' : $desc);
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($course['full_name'] ?? 'Unknown'); ?></td>
                    <td><?php echo number_format((float)$course['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($course['created_at'] ? date('d M Y', strtotime($course['created_at'])) : ''); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit-course.php?id=<?php echo urlencode($course['id']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="delete-course.php" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($course['id']); ?>" />
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <nav aria-label="Pagination">
            <ul class="pagination justify-content-center mt-4">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?php if ($i === $page) echo 'active'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next</a></li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php else: ?>
        <p class="text-center mt-4">No courses found.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
