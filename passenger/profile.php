<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    header("Location: ../login/login.php");
    exit();
}

$emailUpdated = true;
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
  
      // Check if email already exists
      $checkEmailQuery = "SELECT * FROM User WHERE Email = '$newEmail'";
      $result = mysqli_query($conn, $checkEmailQuery);
  
      if (mysqli_num_rows($result) > 0) {
          echo "<script>alert('There is an existing account with this email.');</script>";
      } else {
          $updateEmailQuery = "UPDATE User SET Email = '$newEmail' WHERE ID = $passengerId";
          $emailUpdated = false;
  
          if (mysqli_query($conn, $updateEmailQuery)) {
              echo "<script>alert('Email updated successfully.');</script>";
          } else {
              echo "<script>alert('Error updating email: " . mysqli_error($conn) . "');</script>";
          }
      }
  }
  if (!$emailUpdated) {
    echo "<script>alert('The email was not updated.');</script>";
}
  
  

    if (isset($_POST['update_password'])) {
        $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
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
  background-image: url('../photos/bg.jpg');
    background-size: cover;
    background-position: center;

}

.container {
  background: #fff;
 width: 660px;
  height: 500px;
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



.leftbox {
  float: left;
  top: -5%;
  left: 5%;
  position: absolute;
  width: 15%;
  height: 110%;
  background: #653d58;;
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
  color: #fd891e;
  transform: scale(1.2);
  cursor: pointer;
}

nav a:first-child {
  margin-top: 7px;
}



.rightbox {
  /* float: right; */
  width: 60%;
  height: 100%;
  padding-left: 170px;
}

.profile {
  position: absolute;
  width: 70%;
}

h1 {
  font-family: "Montserrat", sans-serif;
  color: #653d58;
  font-size: 1.01em;
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
  border-image: linear-gradient(to right, #653d58, rgba(126, 211, 134, 0.5)) 1
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
  color: #653d58;
}

.btn:hover {
  text-decoration: underline;
  font-weight: 900;
}


.profile-photo {
  border-radius: 50%;
  width: 300px; 
  height: 150px; 
  object-fit: cover;
  
}

.pp img {
  margin-top: 30px;
  width: 100px; 
  border-radius: 50%;
  height: 100px;
}

/* .pp {
  width: 300px; 
  
} */

.pf{
  display: flex;
  /* padding-top: 20px; */
  /* justify-content: space-between; */
  
  width:450px;
}
.t{
  padding-top: 40px;
  padding-right: 30px;
}
.rightbox input{
  border-color:white ;
}
.profile2 h2{
font-size: 10px;
}
.profile2 .pp img{
  margin-top: 10px;
  border-radius: 20%;
  width: 110px;
  height:110px;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}
.rightbox .profile2 {
 padding-top: 50px;
  
}

.profile2{
  position: absolute;
  width: 70%;
}






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
            <a href="#" class="active">
                <i class="fa fa-camera"></i>
            </a>
            <br><br>
            <a href="../homepages/passenger_homepage.php" class="active">
                <i class="fa fa-sign-out"></i>
            </a>   
        </nav>
       </div>

      <div class="rightbox">

            <div class="profile"  id="personalInfoSection" >
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
   





        <div class="profile2" id="photoUpdateSection" style="display: none;">
    
        <form action="profile.php" method="post" enctype="multipart/form-data">
          
            <h2>Passenger Photo</h2>
            <div class="pp"><img src="../<?php echo $userData['Photo']; ?>" alt="Passenger Photo" class="profile-photo" /> </div>
              <p>
                  <input type="file" name="new_passenger_photo" />
                  <button type="submit" class="btn" name="update_passenger_photo">Update</button>
              </p>
        </form>

        <form action="profile.php" method="post" enctype="multipart/form-data">
            <h2>Passport Photo</h2>
            <div class="pp"><img src="../<?php echo $userData['PassportImg']; ?>" alt="Passenger Photo" class="profile-photo" /> </div> 
            <p>
                <input type="file" name="new_passport_photo" />
                <button type="submit" class="btn" name="update_passport_photo">Update</button>
            </p>
           
        </form>
  </div>
  

        





        </div>
      </div>


  




    


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var personalInfoSection = document.getElementById("personalInfoSection");
            var photoUpdateSection = document.getElementById("photoUpdateSection");

            var cameraIcon = document.querySelector(".fa-camera");
            var userIcon = document.querySelector(".fa-user");

            cameraIcon.addEventListener("click", function () {
                personalInfoSection.style.display = "none";
                photoUpdateSection.style.display = "block";
                cameraIcon.style.color="#fd891e";
                userIcon.style.color="white";
            });

            userIcon.addEventListener("click", function () {
                personalInfoSection.style.display = "block";
                photoUpdateSection.style.display = "none";
                userIcon.style.color="#fd891e";
                cameraIcon.style.color="white";
            });
        });
    </script>

  </body>
</html>