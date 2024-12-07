<?php
session_start();
//INSERT INTO note1 (sno, title, description, tstamp) VALUES ('3', 'Buy Vegitables', 'Go to the market and buy the Vegitables', current_timestamp());




// Connection to MySQL Server
// $servername = "localhost";
// $username = "root";
// $password = "";

// Create a connection
// $conn = mysqli_connect($servername, $username, $password);

// Check the connection
// if (!$conn) {
//     die("sorry we failed to connect: " . mysqli_connect_error());
// }

// $dbname= "notes";
// SQL query to create a database
// $sql = "CREATE DATABASE $dbname" ;

// Execute the query and check if successful
// if (mysqli_query($conn, $sql)) {
//     echo "Database was created successfully";
// } else {
//     echo "Error creating database: " . mysqli_error($conn);
// }

// Close the connection
// mysqli_close($conn);

//-----------------------------------------------------------------------------------------------------------------------------------------------

// Connection to the Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notes";

//create a connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("sorry we failed to connect". mysqli_connect_error());
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit']) && !empty($_POST['snoEdit'])) {
        // Update record logic
        $snoEdit = $_POST['snoEdit'];
        $title = $_POST['title'];
        $description = $_POST['description'];

        // Ensure proper escaping of user inputs to prevent SQL injection
        $title = mysqli_real_escape_string($conn, $title);
        $description = mysqli_real_escape_string($conn, $description);

        $sql = "UPDATE note1 SET title = '$title', description = '$description' WHERE sno = $snoEdit";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['flash'] = "Your record was updated successfully.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Update failed: " . mysqli_error($conn);
        }




    } else {
        // Insert new record logic
        $title = $_POST['title'];
        $description = $_POST['description'];

        $title = mysqli_real_escape_string($conn, $title);
        $description = mysqli_real_escape_string($conn, $description);

        $sql = "INSERT INTO note1 (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);


        
        if ($result) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?insert=success");
            exit();
        } else {
            echo "The record was not inserted successfully: " . mysqli_error($conn);
        }
    }
}

?>

<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project 1 - PHP CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    
    </head>
    <body>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content p-4">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit this Note</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
    <form id="editForm" method="POST" action="index.php">
    <input type="hidden" id="editId" name="snoEdit">
    <div class="mb-3">
        <label for="editTitle" class="form-label">Note Title</label>
        <input type="text" class="form-control" id="title" name="title">
    </div>
    <div class="mb-3">
        <label for="editDescription" class="form-label">Note Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <hr class="mt-4">
    </div>
    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Note</button>
    </div>
    </form>
    </div>
    </div>
</div>


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
        if (isset($_SESSION['flash'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> ' . $_SESSION['flash'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            unset($_SESSION['flash']); // Clear the message after displaying
        }
        ?>


        <?php
if (isset($_GET['insert']) && $_GET['insert'] == 'success'){
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Your note has been inserted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
}
?>

        <div class="container my-4">
            <h2>Add a Note</h2>
            <form action="/Crud/index.php" method="post">
                <input type="hidden" name="snoEdit" id="snoEdit">
                <div class="mb-3">
                <label for="Title" class="form-label">Note Title</label>
                <input type="text" class="form-control" id="title" name="title" required aria-describedby="emailHelp">
                </div>

                <div class="formgroup">
                <label for="desc">Note Description</label>
                <div class="form-floating">
                    <textarea class="form-control" required placeholder="Leave a comment here" id="description" name="description" style="height: 100px"></textarea>
                    
                </div>
            </div>
                <button type="submit" class="btn btn-primary my-3">Add Note</button>
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
            $sql = "SELECT * FROM note1";
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
    </table>
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
            let tr = e.target.closest('tr');

            let sno = tr.querySelector('th').innerText.trim();
            let title = tr.querySelectorAll('td')[0]?.innerText.trim();
            let description = tr.querySelectorAll('td')[1]?.innerText.trim();

            // Populate the modal inputs
            document.getElementById('editId').value = sno; // Assign sno to hidden field
            document.getElementById('edittitle').value = title;
            document.getElementById('editdescription').value = description;

            let editModal = new bootstrap.Modal(document.getElementById('editModal'));
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