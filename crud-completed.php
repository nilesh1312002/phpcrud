<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crud_records";

$delete = false;
// Establish connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $sno = intval($_GET['delete']); // Sanitize input
    $sql = "DELETE FROM crud_tb WHERE sno = $sno";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $delete = true;
    }
}

// Handle Insert or Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit']) && $_POST['snoEdit'] !== '') {
        // Update record
        $sno = intval($_POST['snoEdit']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $sql = "UPDATE crud_tb SET title='$title', description='$description' WHERE sno=$sno";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("Location: crud-op.php?update=1");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } else {
        // Insert new record
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $sql = "INSERT INTO crud_tb (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("Location: crud-op.php?success=1");
            exit();
        } else {
            echo "Error inserting record: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD Operation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">iNotes</a>
    </div>
</nav>

<?php
// Display Alerts
if ($delete) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Your note has been deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
}
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Your note has been inserted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
}
if (isset($_GET['update']) && $_GET['update'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> Your note has been updated successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

<div class="container my-4">
    <h2>Add a Note</h2>
    <form action="crud-op.php" method="POST">
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
            <th scope="col">S.NO</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM crud_tb ORDER BY sno DESC";
        $result = mysqli_query($conn, $sql);
        $sno = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <th scope='row'>" . $sno . "</th>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['description']) . "</td>
                <td>
                    <button class='edit btn btn-sm btn-primary' data-sno='" . $row['sno'] . "'>Edit</button>
                    <a href='crud-op.php?delete=" . $row['sno'] . "' class='delete btn btn-sm btn-danger'>Delete</a>
                </td>
            </tr>";
            $sno++;
        }
        ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="crud-op.php" method="POST">
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

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let table = new DataTable('#myTable');

    document.querySelectorAll('.edit').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const sno = e.target.dataset.sno;
            const row = e.target.closest('tr');
            const title = row.querySelectorAll('td')[0].textContent;
            const description = row.querySelectorAll('td')[1].textContent;

            document.getElementById('snoEdit').value = sno;
            document.getElementById('titleEdit').value = title;
            document.getElementById('descriptionEdit').value = description;

            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete').forEach((button) => {
        button.addEventListener('click', (e) => {
            // Locate the parent row of the button
            let tr = e.target.closest('tr');

            // Extract details from the row
            let sno = tr.querySelector('th')?.innerText.trim(); // Ensure `th` exists
            if (!sno) {
                console.error("Unable to extract sno from the row");
                return;
            }

            let title = tr.querySelectorAll('td')[0]?.innerText.trim();
            let description = tr.querySelectorAll('td')[1]?.innerText.trim();

            console.log("Details to delete:", { sno, title, description });

            // Confirm deletion with user
            if (confirm(`Are you sure you want to delete this entry? (S.No: ${sno}, Title: ${title})`)) {
                // Proceed with deletion by redirecting
                window.location.href = `/Crud/crud-op.php?delete=${sno}`;
            } else {
                // Prevent any further action (delete action is canceled)
                e.preventDefault();
                console.log("Deletion canceled by user.");
            }
        });
    });
});
</script>


</body>
</html>
