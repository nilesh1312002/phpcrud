<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crud_records";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Sorry, we failed to connect: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD Operation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">iNotes</a>
    </div>
</nav>

<div class="container my-4">
    <h2>Add a Note</h2>
    <form action="" method="POST">
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

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        $sno = intval($_POST['snoEdit']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $sql = "UPDATE crud_tb SET title='$title', description='$description' WHERE sno=$sno";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: /Crud/crud-op.php?update=1");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } else {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $sql = "INSERT INTO crud_tb (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: /Crud/crud-op.php?success=1");
            exit();
        } else {
            echo "Error inserting record: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success">Record inserted successfully.</div>';
}
if (isset($_GET['update']) && $_GET['update'] == 1) {
    echo '<div class="alert alert-success">Record updated successfully.</div>';
}
?>

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
                        <button class='delete btn btn-sm btn-danger' data-sno='" . $row['sno'] . "'>Delete</button>
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
            <form action="" method="POST">
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

<script>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
