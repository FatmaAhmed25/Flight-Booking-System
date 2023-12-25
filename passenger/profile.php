<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    header("Location: ../login/login.php");
    exit();
}

$passengerId = $_SESSION['user_id'];
$query = "SELECT u.*, p.* FROM User u
          LEFT JOIN Passenger p ON u.ID = p.UserID
          WHERE u.ID = $passengerId";

// echo "Company ID: $passengerId";

$result = mysqli_query($conn, $query);

if ($result) {
    $passengerData = mysqli_fetch_assoc($result);
} else {
    echo "Error executing query: " . mysqli_error($conn);
}
$passenger= $passengerData['ID'];
// echo "Company ID: $passenger";
// mysqli_close($conn);


// Fetch the list of flights associated with the company
$flightsQuery = "SELECT * FROM Passenger WHERE ID = $passenger";
$flightsResult = mysqli_query($conn, $flightsQuery);

if ($flightsResult) {
    $userData = mysqli_fetch_assoc($flightsResult);
    $flights = mysqli_fetch_all($flightsResult, MYSQLI_ASSOC);
    mysqli_free_result($flightsResult);
} else {
    echo "Error executing query: " . mysqli_error($conn);
}
// echo "Company ID: $userData[Account]";
// mysqli_close($conn);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $passengerId = $_SESSION['user_id'];
    
    if (isset($_POST['update_name'])) {
        $newName = mysqli_real_escape_string($conn, $_POST['new_name']);
        $updateNameQuery = "UPDATE User SET Name = '$newName' WHERE ID = $passengerId";
        
        if (mysqli_query($conn, $updateNameQuery)) {
            echo "Name updated successfully.";
        } else {
            echo "Error updating name: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['update_email'])) {
        $newEmail = mysqli_real_escape_string($conn, $_POST['new_email']);
        $updateEmailQuery = "UPDATE User SET Email = '$newEmail' WHERE ID = $passengerId";
        
        if (mysqli_query($conn, $updateEmailQuery)) {
            echo "Email updated successfully.";
        } else {
            echo "Error updating email: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['update_password'])) {
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $updatePasswordQuery = "UPDATE User SET Password = '$newPassword' WHERE ID = $passengerId";
        
        if (mysqli_query($conn, $updatePasswordQuery)) {
            echo "Password changed successfully.";
        } else {
            echo "Error changing password: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['update_tel'])) {
        $newTel = mysqli_real_escape_string($conn, $_POST['new_tel']);
        $updateTelQuery = "UPDATE User SET Tel = '$newTel' WHERE ID = $passengerId";
        
        if (mysqli_query($conn, $updateTelQuery)) {
            echo "Tel updated successfully.";
        } else {
            echo "Error updating tel: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['update_account'])) {
        $newAccount = mysqli_real_escape_string($conn, $_POST['new_account']);
        $updateAccountQuery = "UPDATE Passenger SET Account = '$newAccount' WHERE ID = $passenger";
        
        if (mysqli_query($conn, $updateAccountQuery)) {
            echo "Account updated successfully.";
        } else {
            echo "Error updating account: " . mysqli_error($conn);
        }
    }
    function generateUniqueFilename($originalFilename)
{
    $uniqueId = uniqid();
    $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
    return $uniqueId . "." . $fileExtension;
}
if (isset($_FILES['new_passenger_photo']) && $_FILES['new_passenger_photo']['error'] == 0) {
  $uploadDir = "../assets/";
  $dbDir = "assets/";
  $newFilename = generateUniqueFilename($_FILES['new_passenger_photo']['name']);
  $photoPath = $uploadDir . $newFilename;
  if (move_uploaded_file($_FILES['new_passenger_photo']['tmp_name'], $photoPath)) {
      $photo = $dbDir . $newFilename;
      $updatePhotoQuery = "UPDATE Passenger SET Photo = '$photo' WHERE UserID = $passengerId";
      if (!mysqli_query($conn, $updatePhotoQuery)) {
          echo "Error updating passenger photo: " . mysqli_error($conn);
      }
  } else {
      array_push($errors, "Error moving uploaded passenger photo to destination.");
  }
}

if (isset($_FILES['new_passport_photo']) && $_FILES['new_passport_photo']['error'] == 0) {
  $uploadDir = "../assets/";
  $dbDir = "assets/";
  $newFilename = generateUniqueFilename($_FILES['new_passport_photo']['name']);
  $passportImgPath = $uploadDir . $newFilename;
  if (move_uploaded_file($_FILES['new_passport_photo']['tmp_name'], $passportImgPath)) {
      $passportImg = $dbDir . $newFilename;
      $updatePassportImgQuery = "UPDATE Passenger SET PassportImg = '$passportImg' WHERE UserID = $passengerId";
      if (!mysqli_query($conn, $updatePassportImgQuery)) {
          echo "Error updating passport photo: " . mysqli_error($conn);
      }
  } else {
      array_push($errors, "Error moving uploaded passport photo to destination.");
  }
}

    

   // After processing the form submission, add:
header("Location: profile.php");
exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Setting Page UI Design</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    <style>
 @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;900&family=Nunito:wght@400;900&family=Roboto:wght@400;900&display=swap");

body {
  background: linear-gradient(to right, #3fb6a8, #7ed386);
  overflow: hidden;
  box-sizing: border-box;
}

.container {
  background: #fff;
  width: 540px;
  height: 420px;
  margin: 0 auto;
  position: relative;
  margin-top: 10%;
  box-shadow: 2px 5px 20px rgba(119, 119, 119, 0.5);
}

.logo {
  float: right;
  margin-right: 12px;
  margin-top: 12px;
  font-family: "Nunito Sans", sans-serif;
  color: #3dbb3d;
  font-weight: 900;
  font-size: 1.5rem;
  letter-spacing: 1px;
}

.CTA {
  width: 80px;
  height: 40px;
  right: -20px;
  bottom: 0;
  margin-bottom: 90px;
  position: absolute;
  z-index: 1;
  background: #7ed386;
  font-size: 1em;
  transform: rotate(-90deg);
  transition: all 0.5s ease-in-out;
  cursor: pointer;
}

.CTA h1 {
  color: #fff;
  margin-top: 10px;
  margin-left: 9px;
}

.CTA:hover {
  background: #3fb6a8;
  transform: scale(1.1);
}

.leftbox {
  float: left;
  top: -5%;
  left: 5%;
  position: absolute;
  width: 15%;
  height: 110%;
  background: #7ed386;
  box-shadow: 3px 3px 10px rgba(119, 119, 119, 0.5);
}

nav a {
  list-style: none;
  padding: 35px;
  color: #fff;
  font-size: 1.1em;
  display: block;
  transition: all 0.3s ease-in-out;
}

nav a:hover {
  color: #3fb6a8;
  transform: scale(1.2);
  cursor: pointer;
}

nav a:first-child {
  margin-top: 7px;
}

.active {
  color: #3fb6a8;
}

.rightbox {
  float: right;
  width: 60%;
  height: 100%;
}

.profile {
  position: absolute;
  width: 70%;
}

h1 {
  font-family: "Montserrat", sans-serif;
  color: #7ed386;
  font-size: 1em;
  margin-top: 40px;
  margin-bottom: 35px;
}

h2 {
  color: #777;
  font-family: "Roboto", sans-serif;
  width: 80%;
  text-transform: uppercase;
  font-size: 8px;
  letter-spacing: 1px;
  margin-left: 2px;
}

p {
  border-width: 1px;
  border-style: solid;
  border-image: linear-gradient(to right, #3fb6a8, rgba(126, 211, 134, 0.5)) 1
    0%;
  border-top: 0;
  width: 80%;
  font-family: "Montserrat", sans-serif;
  font-size: 0.7em;
  padding: 7px 0;
  color: #070707;
}

.btn {
  float: right;
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  font-size: 10px;
  border: none;
  color: #3fb6a8;
}

.btn:hover {
  text-decoration: underline;
  font-weight: 900;
}

footer {
  position: absolute;
  width: 20%;
  bottom: 0;
  right: -20px;
  text-align: right;
  font-size: 0.8em;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-family: "Roboto", sans-serif;
}

footer p {
  border: none;
  padding: 0;
}

footer a {
  color: #fff;
  text-decoration: none;
}

footer a:hover {
  color: #7d7d7d;
}
.profile-photo {
  border-radius: 50%;
  width: 300px; /* Increase the width */
  height: 150px; /* Increase the height */
  object-fit: cover;
  /* margin-left: 150px; */
}

.pp img {
  margin-top: 30px;
  width: 100px; /* Increase the width */
  border-radius: 50%;
  height: 100px;
}

.pp {
  width: 300px; /* Increase the width */
}

.pf{
  display: flex;
  padding-top: 10px;
  justify-content: space-between;
  width:450px;
}
/* .t{
  width:50px;
  padding-right: 120px;
} */




    </style>
  </head>
  <body>
    <div class="container">

        <div id="logo">

            <h1 class="logo"></h1>
            <div class="CTA"></div>

        </div>

      <div class="leftbox">
        <nav>
           <br><br>
            <a href="#" class="active">
                <i class="fa fa-user"></i>
            </a>
            <br><br>
            <a href="#">
                <i class="fa fa-camera"></i>
            </a>
            <br><br>
            <a href="your_exit_page.php">
                <i class="fa fa-sign-out"></i>
            </a>   
        </nav>
       </div>

      <div class="rightbox">

            <div class="profile"  id="personalInfoSection">
                <div class="pf"> 
                    <div class="t"><h1>Personal Info</h1></div>
                    <div class="pp">
                      <img src="../<?php echo $userData['Photo']; ?>" alt="img" class="profile-photo" />
                    </div> 
                </div>


      
        <form action="profile.php" method="post">
          <h2>Name</h2>
          <p>
          <input type="text" name="new_name" value="<?php echo $passengerData['Name']; ?>" />
          <button type="submit" class="btn" name="update_name">Update</button>
          </p>
        </form>

        <form action="profile.php"method="post">
          <h2>Email</h2>
          <p>
            <input type="text" name="new_email" value="<?php echo $passengerData['Email']; ?>" />
            <button type="submit" class="btn" name="update_email">Update</button>
          </p>
        </form>

        <form action="profile.php" method="post">
          <h2>Password</h2>
          <p>
            <input type="password" name="new_password" value="****"/>
            <button type="submit" class="btn" name="update_password">Change</button>
          </p>
        </form>

        <form action="profile.php" method="post">
          <h2>Tel</h2>
          <p>
            <input type="text" name="new_tel" value="<?php echo $passengerData['Tel']; ?>" />
            <button type="submit" class="btn" name="update_tel">Change</button>
          </p>
        </form>

        <form action="profile.php" method="post">
          <h2>Account</h2>
          <p>
            <input type="text" name="new_account" value="<?php echo $userData['Account']; ?>" />
            <button type="submit" class="btn" name="update_account">Change</button>
          </p>
        </form>

        </div>

        </div>
      </div>


  




    <!-- <footer>
    </footer> -->
    


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var personalInfoSection = document.getElementById("personalInfoSection");
            var photoUpdateSection = document.getElementById("photoUpdateSection");

            var cameraIcon = document.querySelector(".fa-camera");
            var userIcon = document.querySelector(".fa-user");

            cameraIcon.addEventListener("click", function () {
                personalInfoSection.style.display = "none";
                photoUpdateSection.style.display = "block";
            });

            userIcon.addEventListener("click", function () {
                personalInfoSection.style.display = "block";
                photoUpdateSection.style.display = "none";
            });
        });
    </script>

  </body>
</html>