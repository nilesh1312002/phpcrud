<?php

// $servername = "localhost";
// $username = "root";
// $password ="";

// $conn = mysqli_connect($servername, $username, $password);

// if(!$conn){
//     die("sorry we failed to connect: ". mysqli_connect_error());
// }

// $dbname="crud_records";

// $sql = "CREATE DATABASE $dbname";

// if(mysqli_query($conn,$sql)){
// echo "Database was created successfully";
// }
// else{
//     echo "Database was not created successfully throughs error". mysqli_error($conn);
// }

// mysqli_close($conn);



$servername = "localhost";
$username = "root";
$password = "";
$dbname="crud_records";

$conn = mysqli_connect($servername, $username, $password,$dbname);

if(!$conn){
    die("sorry we failed to connect".mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD OPERATTION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
            <a class="navbar-brand" href="#">iNotes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active text-light" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                    </a>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled text-light" aria-disabled="true">Disabled</a>
                </li>
                </ul>
                <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
            </div>
        </nav>

        <?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if form fields are set
    if (isset($_POST['title']) && isset($_POST['description'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $sql = "INSERT INTO crud_tb (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            // Redirect after successful insertion
            header("Location: /Crud/crud-op.php?success=1");
            exit();
        } else {
            echo "The record was not inserted successfully: " . mysqli_error($conn);
        }
    } else {
        echo "Connection failed: " . mysqli_error($conn);
    }
}

// Display success message if redirected with a success parameter
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> Your note has been inserted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

    <div class="container my-4">
        <h2>Add a Note</h2>
<form action="/Crud/crud-op.php" method="post">
<input type="hidden" id="editSno" name="snoEdit">
<div class="mb-3">
    <label for="editTitle" class="form-label">Note Title</label>
    <input type="text" class="form-control" id="editTitle" name="title" required>
</div>
<div class="mb-3">
    <label for="editDescription" class="form-label">Note Description</label>
    <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
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
                    <td><button class='edit btn btn-sm btn-primary' sno = ".$row['sno'].">Edit</button>
                    <button class='delete btn btn-sm btn-primary' id=d ".$row['sno'].">Delete</button></td>
                </tr>";
                $sno++;
            }
            ?>
        </tbody>
</div>


<!-- Edit -->

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit']) && !empty($_POST['snoEdit'])) {
        // Update operation
        $sno = intval($_POST['snoEdit']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $sql = "UPDATE crud_tb SET title = '$title', description = '$description' WHERE sno = $sno";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Redirect after successful update
            header("Location: /Crud/crud-op.php?update=1");
            exit();
        } else {
            echo "The record was not updated successfully: " . mysqli_error($conn);
        }
    }
}


if (isset($_GET['update']) && $_GET['update'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> Your note has been updated successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}


?>





<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit this Note</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="crud-op.php">
                    <input type="hidden" id="editSno" name="snoEdit">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Note Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
















<script
    src="https://code.jquery.com/jquery-3.7.1.js"
    integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous">
    </script>
    <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.edit').forEach((button) => {
        button.addEventListener('click', (e) => {
            const tr = e.target.closest('tr');
            const sno = tr.querySelector('th').innerText.trim();
            const title = tr.querySelectorAll('td')[0].innerText.trim();
            const description = tr.querySelectorAll('td')[1].innerText.trim();

            // Populate modal fields
            document.getElementById('editSno').value = sno; // Set snoEdit for update
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;

            // Show modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
    });
});



    </script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
crossorigin="anonymous"></script>
</body>
</html>