//#653d58


<?php
session_start();

require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    header("Location: ../login/login.php"); 
    exit();
}

$passengerId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];
$query = "SELECT u.*, p.* FROM User u
          LEFT JOIN Passenger p ON u.ID = p.UserID
          WHERE u.ID = $passengerId";

$result = mysqli_query($conn, $query);

if ($result) {
    $mergedData = mysqli_fetch_assoc($result);
} else {
    echo "Error executing query: " . mysqli_error($conn);
}
$passenger = $mergedData['ID'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have a function to handle database updates, update the user information here
    $updatedName = $_POST['name'];
    $updatedEmail = $_POST['email'];
    $updatedPassword = $_POST['password'];
    $updatedTel = $_POST['tel'];
    $updatedAccount = $_POST['account'];

    // Update the user information in the database
    // You should replace the following lines with your actual update logic
    $updateQuery = "UPDATE User SET Name='$updatedName', Email='$updatedEmail', Password='$updatedPassword', Tel='$updatedTel', Account='$updatedAccount' WHERE ID=$passengerId";
    $updateResult = mysqli_query($conn, $updateQuery);

    if (!$updateResult) {
        echo "Error updating user information: " . mysqli_error($conn);
    }
}
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fontawesome/css/all.css">
    <title>Profile settings</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Poppins:wght@100;400&display=swap');
 body{
        overflow-x: hidden;
    }
    .container{
        background: #fff;
        width: 540px;
        height: 450px;
        margin: 0 auto;
        position: relative;
        margin-top: 10%;
        box-shadow: 2px 5px 20px rgba(119,0,0,.5);
    }

    .leftbox{
        float: left;
        top:-5%;
        left: 5%;
        position: absolute;
        width: 15%;
        height: 110%;
        background: #2ed573;
        box-shadow: 3px 3px 10px rgba(119,119,119,.5);
        border: .1em solid #fff;
    }
    nav a{
        list-style: none;
        padding: 35px;
        color: #fff;
        font-size: 1.1em;
        display: block;
        transition: all .3s ease-in-out;
    }
    nav a:hover{
        color:#10ac84;
        cursor: pointer;
        transform: scale(1.2);
    }
    nav a:first-child{
        margin-top: 7px;
    }
    nav a.active{
        color: #10ac84;
    }
    .rightbox{
        width: 60%;
        margin-left: 27%;
    }
    .tabShow{
        transition: all .5s ease-in;
        width: 80%;
    }
    h1{
        font-family: "Montserrat", sans-serif;
        color: #7ed386;
        font-size: 1.2rem;
        margin-top: 40px;
        margin-bottom: 35px;

    }
    h2{
        color: #777;
        font-family: "Roboto", sans-serif;
        text-transform: uppercase;
        font-size: 8px;
        letter-spacing: 1px;
        margin-left: 2px;
        margin-top: 10px;

    }
    .input, p{
        border:0;
        border-bottom:1px solid #3fb6a8;
        width: 80%;
        font-family: 'montserrat',sans-serif;
        font-size: .7em;
        padding: 7px 0;
        outline: none;
    }
    span{
        font-size: .7em;
        color: #777;
    }
    .btn{
        font-family: "roboto", sans-serif;
        text-transform: uppercase;
        font-size: 15px;
        border: 0;
        color: #fff;
        background: #7ed386;
        padding:7px 15px ;
        box-shadow: 0px 2px 4px 0px rgba(0,0,0,.2);
        cursor: pointer;
        margin-top: 15px;
    }
    .tabShow{
        display: none;
    }
    img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 50%;
  display: block;
  box-shadow: 1px 3px 12px rgba(0, 0, 0, 0.18);
}
    </style>
</head>
<body>
    <div class="container">
        <div class="leftbox">
            <nav>
                <a onclick="tabs(0)" class="tab active">
                <i class="fa fa-user"></i>
            </a>
            <a onclick="tabs(1)" class="tab">
                <i class="fa fa-camera"></i>
            </a><a onclick="tabs(2)" class="tab">
                <i class="fa fa-exit"></i>
            </a>
            </nav>
        </div>
        <div class="rightbox">
            <div class="profile tabShow">
                <h1>Personal Info</h1>
                <h2>NAME</h2>
                <input type="text" name="name" id="" placeholder="<?php echo $mergedData['Name'];?>">
            <h2>Email</h2>
                <input type="Email" name="email" id="" placeholder="<?php echo $mergedData['Email'];?>">
            <h2>PASSWORD</h2>
                <input type="text" name="password" id="" placeholder="<?php echo $mergedData['Password'];?>">
            <h2>TEL</h2>
                <input type="text" name="tel" id="" placeholder="<?php echo $mergedData['Tel'];?>">
            <h2>ACCOUNT</h2>
                <input type="text" name="account" id="" placeholder="<?php echo $mergedData['Account'];?>">
            <button class="btn">Update</button>
            </div>
            <div class="camera tabShow">
                <h1>Photo Info</h1>
                <h2>Profile</h2>
                <img src="../<?php echo $mergedData['Photo']; ?>">
                <input type="file" class="custom-file-input" name="photo" width="30px" height="30px">
            <h2>Passport</h2>
            <img src="../<?php echo $mergedData['PassportImg']; ?>" alt="Passport Image">
            <input type="file" class="custom-file-input" name="passport">
    
            <button class="btn">Update</button>
            </div>
        </div>
    </div>
    <script>
        const tabBtn=document.querySelectorAll(".tab");
        const tab =document.querySelectorAll(".tabShow");
        function tabs(panelIndex){
            if (panelIndex === 2) { // Check if "Exit" tab is clicked
            // Perform logout action, e.g., redirect to logout page
            window.location.href = 'passenger_homepage.php';
            return;
        }
            tab.forEach(function(node){
                node.style.display = "none";
            });
            tab[panelIndex].style.display="block";
            }tabs(0);


        </script>
</body>
</html>