<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crud_records";

// Establish connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['delete'])) {
    $sno = intval($_GET['delete']); // Sanitize input
    $sql = "DELETE FROM crud_tb WHERE sno = $sno";
    mysqli_query($conn, $sql);

    // Redirect to the same page without the query parameters
    echo "<script>
        window.location.href = '" . strtok($_SERVER['REQUEST_URI'], '?') . "';
    </script>";
    exit();
}

// Handle Insert or Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit']) && $_POST['snoEdit'] !== '') {
        // Update record
        $sno = intval($_POST['snoEdit']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $sql = "UPDATE crud_tb SET title='$title', description='$description' WHERE sno=$sno";
        mysqli_query($conn, $sql);
        header("Location: ?update=1");
        exit();
    } else {
        // Insert new record
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $sql = "INSERT INTO crud_tb (title, description) VALUES ('$title', '$description')";
        mysqli_query($conn, $sql);
        header("Location: ?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD Operation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">CRUD Application</a>
    </div>
</nav>

<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Your note has been inserted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
} elseif (isset($_GET['update'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> Your note has been updated successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
} elseif (isset($_GET['delete'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Your note has been deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
}
?>


<div class="container my-4">
    <h2>Add a Note</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Note Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Note Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<div class="container">
    <table class="table" id="myTable">
        <thead>
        <tr>
            <th>S.No</th>
            <th>Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM crud_tb ORDER BY sno DESC";
        $result = mysqli_query($conn, $sql);
        $sno = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>"
                . "<th>" . $sno . "</th>"
                . "<td>" . htmlspecialchars($row['title']) . "</td>"
                . "<td>" . htmlspecialchars($row['description']) . "</td>"
                . "<td>"
                . "<button class='edit btn btn-sm btn-primary' data-sno='" . $row['sno'] . "'>Edit</button> "
                . "<a href='?delete=" . $row['sno'] . "' class='delete btn btn-sm btn-danger'>Delete</a>"
                . "</td>"
                . "</tr>";
            $sno++;
        }
        ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="snoEdit" name="snoEdit">
                    <div class="mb-3">
                        <label for="titleEdit" class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="titleEdit" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionEdit" class="form-label">Note Description</label>
                        <textarea class="form-control" id="descriptionEdit" name="description" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        const table = $('#myTable').DataTable();

        $('#myTable tbody').on('click', '.edit', function () {
            const row = $(this).closest('tr');
            const sno = $(this).data('sno');
            const title = row.find('td').eq(0).text();
            const description = row.find('td').eq(1).text();

            $('#snoEdit').val(sno);
            $('#titleEdit').val(title);
            $('#descriptionEdit').val(description);

            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        });

        $('#myTable tbody').on('click', '.delete', function (e) {
            if (!confirm('Are you sure you want to delete this note?')) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>
