<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Pagination & Search parameters
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Prepare search condition safely
$search_sql = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $search_sql = " WHERE c.title LIKE '%$search_escaped%' ";
}

// Count total courses for pagination
$count_query = "SELECT COUNT(*) as total FROM courses c $search_sql";
$count_result = $conn->query($count_query);
$total_courses = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_courses / $limit);

// Fetch courses with faculty
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
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 1100px; }
        .table td, .table th { vertical-align: middle; }
        .description-cell { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>
<div class="container bg-white p-4 rounded shadow-sm">
    <h1 class="mb-4">Manage Courses</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="add-course.php" class="btn btn-primary">Add New Course</a>

        <form class="d-flex" method="GET" action="">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control me-2" placeholder="Search by title..." />
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </form>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-striped table-hover">
            <thead class="table-primary">
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
                        <a href="edit-course.php?id=<?php echo urlencode($course['id']); ?>" class="btn btn-sm btn-warning me-1">Edit</a>

                        <!-- Delete with POST and JS confirmation -->
                        <form method="POST" action="delete-course.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($course['id']); ?>" />
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Course pagination">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                <?php endif; ?>

                <?php
                // Show up to 5 pages centered around current page
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
