<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
  header("Location: ../login/login.php");
  exit();
}
$flights = [];
$userId = $_SESSION['user_id'];
$query = "SELECT u.*, c.* FROM User u
          LEFT JOIN Company c ON u.ID = c.UserID
          WHERE u.ID = $userId";

// echo "Company ID: $passengerId";

$result = mysqli_query($conn, $query);

if ($result) {
  $companyData = mysqli_fetch_assoc($result);
} else {
  echo "Error executing query: " . mysqli_error($conn);
}
$companyId = $companyData['ID'];
$flightsQuery = "SELECT * FROM Flight WHERE CompanyID = $companyId";
$flightsResult = mysqli_query($conn, $flightsQuery);

if ($flightsResult) {
  $flights = mysqli_fetch_all($flightsResult, MYSQLI_ASSOC);
  mysqli_free_result($flightsResult);
} else {
  echo "Error executing query: " . mysqli_error($conn);
}

// Fetch the list of flights associated with the company
$companyQuery = "SELECT * FROM Company WHERE ID = $companyId";
$cResult = mysqli_query($conn, $companyQuery);

if ($cResult) {
  $userData = mysqli_fetch_assoc($cResult);
  $company = mysqli_fetch_all($cResult, MYSQLI_ASSOC);
  mysqli_free_result($cResult);
} else {
  echo "Error executing query: " . mysqli_error($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


  if (isset($_POST['update_name'])) {
    $newName = mysqli_real_escape_string($conn, $_POST['new_name']);
    $updateNameQuery = "UPDATE User SET Name = '$newName' WHERE ID = $userId";

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
      $updateEmailQuery = "UPDATE User SET Email = '$newEmail' WHERE ID = $userId";

      if (mysqli_query($conn, $updateEmailQuery)) {
        echo "<script>alert('Email updated successfully.');</script>";
      } else {
        echo "<script>alert('Error updating email: " . mysqli_error($conn) . "');</script>";
      }
    }
  }



  if (isset($_POST['update_password'])) {
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $updatePasswordQuery = "UPDATE User SET Password = '$newPassword' WHERE ID = $userId";

    if (mysqli_query($conn, $updatePasswordQuery)) {
      echo "Password changed successfully.";
    } else {
      echo "Error changing password: " . mysqli_error($conn);
    }
  }

  if (isset($_POST['update_tel'])) {
    $newTel = mysqli_real_escape_string($conn, $_POST['new_tel']);
    $updateTelQuery = "UPDATE User SET Tel = '$newTel' WHERE ID = $userId";

    if (mysqli_query($conn, $updateTelQuery)) {
      echo "Tel updated successfully.";
    } else {
      echo "Error updating tel: " . mysqli_error($conn);
    }
  }

  if (isset($_POST['update_account'])) {
    $newAccount = mysqli_real_escape_string($conn, $_POST['new_account']);
    $updateAccountQuery = "UPDATE Company SET Account = '$newAccount' WHERE ID = $companyId";

    if (mysqli_query($conn, $updateAccountQuery)) {
      echo "Account updated successfully.";
    } else {
      echo "Error updating account: " . mysqli_error($conn);
    }
  }
  if (isset($_POST['update_location'])) {
    $newLocation = mysqli_real_escape_string($conn, $_POST['new_location']);
    $updateAccountQuery = "UPDATE Company SET Location = 'newLocation' WHERE ID = $companyId";

    if (mysqli_query($conn, $updateAccountQuery)) {
      echo "Account updated successfully.";
    } else {
      echo "Error updating account: " . mysqli_error($conn);
    }
  }
  if (isset($_POST['update_address'])) {
    $newAddress = mysqli_real_escape_string($conn, $_POST['new_address']);
    $updateAddressQuery = "UPDATE Company SET Address = 'newAddress' WHERE ID = $companyId";

    if (mysqli_query($conn, $updateAddressQuery)) {
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
  if (isset($_FILES['new_company_photo']) && $_FILES['new_company_photo']['error'] == 0) {
    $uploadDir = "../assets/";
    $dbDir = "assets/";
    $newFilename = generateUniqueFilename($_FILES['new_company_photo']['name']);
    $logoPath = $uploadDir . $newFilename;
    if (move_uploaded_file($_FILES['new_company_photo']['tmp_name'], $logoPath)) {
      $logo = $dbDir . $newFilename;
      $updateLogoQuery = "UPDATE Company SET Logo = '$logo' WHERE UserID = $userId";
      if (!mysqli_query($conn, $updateLogoQuery)) {
        echo "Error updating passenger photo: " . mysqli_error($conn);
      }
    } else {
      array_push($errors, "Error moving uploaded passenger photo to destination.");
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
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
      background: #653d58;
      ;
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
      border-image: linear-gradient(to right, #653d58, rgba(126, 211, 134, 0.5)) 1 0%;
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

    .pf {
      display: flex;
      /* padding-top: 20px; */
      /* justify-content: space-between; */

      width: 450px;
    }

    .t {
      padding-top: 40px;
      padding-right: 30px;
    }

    .rightbox input {
      border-color: white;
    }

    .profile2 h2 {
      font-size: 10px;
    }

    .profile2 .pp img {
      margin-top: 10px;
      border-radius: 20%;
      width: 110px;
      height: 110px;
      border-bottom-left-radius: 0;
      border-bottom-right-radius: 0;
    }

    .rightbox .profile2 {
      padding-top: 50px;

    }

    .profile2 {
      position: absolute;
      width: 70%;

    }

    .flight-card {
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      width: 200px;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
      flex-basis: calc(25% - 10px);
    }

    .flights-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }

    .flight-card h3 {
      color: #653d58;
    }

    .flight-card p {
      color: #653d58;
    }

    .profile4 {
      padding-top: 50px;
    }

    .profile4 h2 {
      font-size: 13px;
    }

    .profile3 {
      padding-top: 60px;
    }

    textarea {
      width: 260px;
      height: 140px;
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
        <br>
        <a href="#">
          <i class="fa fa-user"></i>
        </a>
        <a href="#">
          <i class="fa fa-lock"></i>
        </a>
        <br>
        <a href="#">
          <i class="fa fa-plane"></i>
        </a>
        <br>
        <a href="#" class="active">
          <i class="fa fa-camera"></i>
        </a>
        <br>
        <a href="../homepages/company_homepage.php" class="active">
          <i class="fa fa-sign-out"></i>
        </a>
      </nav>
    </div>

    <div class="rightbox">

      <div class="profile" id="personalInfoSection">
        <div class="pf">
          <div class="t">
            <h1>Personal Info</h1>
          </div>
          <div class="pp">
            <img src="../<?php echo $userData['Logo']; ?>" alt="img" class="profile-photo" />
          </div>
        </div>



        <form action="profile.php" method="post">
          <h2>Name</h2>
          <p>
            <input type="text" name="new_name" value="<?php echo $companyData['Name']; ?>" />
            <button type="submit" class="btn" name="update_name">Update</button>
          </p>
        </form>

        <form action="profile.php" method="post">
          <h2>Email</h2>
          <p>
            <input type="text" name="new_email" value="<?php echo $companyData['Email']; ?>" />
            <button type="submit" class="btn" name="update_email">Update</button>
          </p>
        </form>

        <form action="profile.php" method="post">
          <h2>Password</h2>
          <p>
            <input type="password" name="new_password" value="****" />
            <button type="submit" class="btn" name="update_password">Change</button>
          </p>
        </form>

        <form action="profile.php" method="post">
          <h2>Tel</h2>
          <p>
            <input type="text" name="new_tel" value="<?php echo $companyData['Tel']; ?>" />
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

          <h2>Company Logo</h2>
          <div class="pp"><img src="../<?php echo $companyData['Logo']; ?>" alt="Company Photo" class="profile-photo" />
          </div>
          <p>
            <input type="file" name="new_company_photo" />
            <button type="submit" class="btn" name="update_passenger_photo">Update</button>
          </p>
        </form>

      </div>


      <div class="profile3" id=secondProfileSection style="display: none;">
        <form action="profile.php" method="post">
          <h2>Adrress</h2>
          <p>
            <input type="text" name="new_address" value="<?php echo $userData['Address']; ?>" />
            <button type="submit" class="btn" name="update_address">Update</button>
          </p>
        </form>
        <form action="profile.php" method="post">
          <h2>Location</h2>
          <p>
            <input type="text" name="new_location" value="<?php echo $userData['Location']; ?>" />
            <button type="submit" class="btn" name="update_location">Update</button>
          </p>
        </form>
        <form action="profile.php" method="post">
          <h2>Bio</h2>
          <p>
            <textarea type="text" name="new_bio"><?php echo $userData['Bio']; ?></textarea>
            <button type="submit" class="btn" name="update_bio">Update</button>
          </p>
        </form>



      </div>


      <div class="profile4" id="flightsSection" style="display: none;">
        <div class="profile">

          <h2>My Flights</h2>
          <div class="flights-container">
            <?php foreach ($flights as $flight): ?>
              <div class="flight-card">
                <h3>
                  <?php echo $flight['Name']; ?>
                </h3>
                <p>Source:
                  <?php echo $flight['Source']; ?>
                </p>
                <p>Destination:
                  <?php echo $flight['destination']; ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>

        </div>
      </div>



    </div>

  </div>
  </div>


  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var personalInfoSection = document.getElementById("personalInfoSection");
      var photoUpdateSection = document.getElementById("photoUpdateSection");
      var secondProfileSection = document.getElementById("secondProfileSection");
      var flightsSection = document.getElementById("flightsSection");
      var cameraIcon = document.querySelector(".fa-camera");
      var userIcon = document.querySelector(".fa-user");
      var lockIcon = document.querySelector(".fa-lock");
      var flightIcon = document.querySelector(".fa-plane");
      cameraIcon.addEventListener("click", function () {
        personalInfoSection.style.display = "none";
        secondProfileSection.style.display = "none";
        flightsSection.style.display = "none";
        photoUpdateSection.style.display = "block";
        cameraIcon.style.color = "#fd891e";
        userIcon.style.color = "white";
        lockIcon.style.color = "white";
        flightIcon.style.color = "white";

      });

      userIcon.addEventListener("click", function () {
        personalInfoSection.style.display = "block";
        photoUpdateSection.style.display = "none";
        flightsSection.style.display = "none";
        secondProfileSection.style.display = "none";
        cameraIcon.style.color = "white";
        userIcon.style.color = "#fd891e";
        lockIcon.style.color = "white";
        flightIcon.style.color = "white";

      });
      lockIcon.addEventListener("click", function () {
        personalInfoSection.style.display = "none";
        photoUpdateSection.style.display = "none";
        flightsSection.style.display = "none";
        secondProfileSection.style.display = "block";
        cameraIcon.style.color = "#white";
        userIcon.style.color = "white";
        flightIcon.style.color = "white";
        lockIcon.style.color = "#fd891e";

      });
      flightIcon.addEventListener("click", function () {
        personalInfoSection.style.display = "none";
        photoUpdateSection.style.display = "none";
        flightsSection.style.display = "block";
        secondProfileSection.style.display = "none";
        cameraIcon.style.color = "white";
        userIcon.style.color = "white";
        lockIcon.style.color = "white";
        flightIcon.style.color = "#fd891e";


      });
    });
  </script>


</body>

</html>