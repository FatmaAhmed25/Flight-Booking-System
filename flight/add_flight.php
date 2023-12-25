<?php
session_start();
include '../database/database.php';


$commonData = isset($_SESSION['common_data']) ? $_SESSION['common_data'] : [];


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    // Redirect to login or show an error message
    header("Location: ../login/login.php");
    exit();
}
$userID = $_SESSION['user_id']; // Retrieve company ID from the session

// Query to retrieve CompanyID based on UserID
$companyIDQuery = "SELECT ID FROM Company WHERE UserID = ?";
$stmtCompanyID = mysqli_prepare($conn, $companyIDQuery);
mysqli_stmt_bind_param($stmtCompanyID, "d", $userID);
mysqli_stmt_execute($stmtCompanyID);
mysqli_stmt_bind_result($stmtCompanyID, $companyID);
mysqli_stmt_fetch($stmtCompanyID);
mysqli_stmt_close($stmtCompanyID);

$companyId = $companyID; 
//echo "Company ID: $companyId";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();
    $name = $_POST["name"];
    $itinerary = $_POST["itinerary"];
    $fees = $_POST["fees"];
    $passengers = $_POST["passengers"];
    $startDay = $_POST["startday"];
    $endDay = $_POST["endday"];
    $source=$_POST["source"];
    $destination=$_POST["destination"];
  


    // Validate form data (you may want to add more specific validations based on your requirements)
    if (empty($name)  || empty($source)|| empty($destination) || empty($itinerary) || empty($fees) || empty($passengers) ||empty($startDay) || empty($endDay)) {
        array_push($errors, "All fields are required");
    }

    // Additional validation logic goes here...

    $flightData = array_merge($commonData, [
        'name' => $name,
        'itinerary' => $itinerary,
        'fees' => $fees,
        'passengers' => $passengers,
        'startday' => $startDay,
        'endday' => $endDay,
        'source'=>$source,
        'destination'=>$destination
        


    ]);
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
    
      
        // $companyId=2;
        // Insert the flight into the database
        $insertFlightQuery = "INSERT INTO Flight (Name, Source, Destination, Itinerary, Fees, RegisteredPassengers, PendingPassengers, StartDay, EndDay, Completed, Canceled, CompanyID) VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, 0, false, ?)";
        $stmtFlight = mysqli_prepare($conn, $insertFlightQuery);
        
        if ($stmtFlight) {
            // Adjust the parameter types and order based on your actual database schema
            mysqli_stmt_bind_param($stmtFlight, "ssssddssd", $flightData['name'], $flightData['source'], $flightData['destination'], $flightData['itinerary'], $flightData['fees'], $flightData['passengers'], $flightData['startday'], $flightData['endday'], $companyId);
            if (mysqli_stmt_execute($stmtFlight)) {
                echo "<div class='alert alert-success'>Flight added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error executing the flight insert statement: " . mysqli_error($conn) . "</div>";
            }

            mysqli_stmt_close($stmtFlight);
        } else {
            echo "<div class='alert alert-danger'>Error preparing the flight insert statement: " . mysqli_error($conn) . "</div>";
        }

        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flight</title>
    <link href="https://fonts.googleapis.com/css?family=Pacifico|Paytone+One" rel="stylesheet">
    <link rel="stylesheet" href="../datetimepicker-master/build/jquery.datetimepicker.min.css">
    <script src="../datetimepicker-master/jquery.js"></script>
    <script src="../datetimepicker-master/build/jquery.datetimepicker.full.js"></script>
    <style>
    

@import url('https://fonts.googleapis.com/css2?family=Poppins:ital@1&display=swap');
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins',sans-serif;

}
/* body{
    display: flex;
    height: 80vh;
    justify-content: center;
    align-items: center;
    padding:10px;
    background-image: url('../photos/bg5.png');
    background-size: cover;
        background-position: center;

}
.container{
max-width:700px;
width:100%;
padding:25px 30px;
border-radius: 5px;
box-shadow: 5px 5px 5px 3px #131313;
background-color: rgba(70, 0, 0, 0.3);
margin-top: 250px;
} */
body {
  background: linear-gradient(to right, #6D4796, #A773E0);
  overflow: hidden;
  box-sizing: border-box;
  background-image: url('../photos/bg.jpg');
  background-size: cover;
  background-position: center;

}

.container {
  background: #fff;
  width: 890px;
  height: 480px;
  margin: 0 auto;
  position: relative;
  margin-top: 10%;
  border-radius: 20px;
  box-shadow: 2px 5px 20px rgba(119, 119, 119, 0.5);
}
.user-details
{
    padding-top: 50px;
    padding-left: 50px;
    padding-right: 50px;
}
.container form .user-details{
    display: flex;
    flex-wrap:wrap;
    justify-content: space-between;
    margin:20px 0 12px 0;
}
form .user-details .input-box {
margin-bottom:15px;
width:300px;
/* padding-left: 20px; */
/* width:calc(100%/2 - 20px); */
}
.user-details .input-box .details{
    display:block;
   font-weight:700;
   font-size:17px;
   margin-bottom: 5px;

}
.user-details .input-box input{
    height:40px;
    width:100%;
    outline:none;
    border-radius: 5px;
    border:1px solid #ccc;
    padding-left: 15px;
    font-size:16px;
    transition:all 0.3s ease;


}

.user-details .input-box input:focus,
.user-details .input-box input:valid,
{
   border-color:#9b596b;





}
.input-group {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .input-group input {
        width: calc(80% );
    }

    .input-group select {
        width: 48%;
    }

    #flightsButton {
    margin-left: 50px;
    width: 100px; 
    background-color: #653d58;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em; 
}
 #flightsButton:hover {
    background-color: #fd891e;
} 
span{
       color:#653d58;
       font-size: 0.5em;
       font-weight: 400;
    }

.button {
    padding-left: 700px;
}  

</style>

</head>

<body>

<div class="container">
<form action="add_flight.php" method="post">

            <div class="user-details">

              

                <div class="input-box">
                    <span class="details">Flight Name</span>
                    <input type="text" id="name" name="name"required>
                </div>
                <div class="input-box">
                    <span class="details">Itinerary</span>
                    <input type ="text" id="itinerary" name="itinerary" required>
                </div>
                <div class="input-box">
                    <span class="details">Fees</span>
                    <input type="text" id="fees" name="fees" required>
                </div>
                <div class="input-box">
                    <span class="details">No of Passengers</span>
                    <input type="number" id="passengers" name="passengers" required>
                </div>
                <div class="input-box">
                    <span class="details">Source</span>
                    <input type="text" name="source">
                </div>
                <div class="input-box">
                    <span class="details">Destination</span>
                    <input type="text" name="destination">
                </div>
                <div class="input-box">
                       
                            <span class="details">Departure</span>
                            <input id="pickDAteAndTime1" name="startday" placeholder="YYYY-MM-DD HH:MM:SS" required>
                        </div>
                        <div class="input-box" >
                        <span class="details">Arrival</span>
                            <input id="pickDAteAndTime2" name="endday" placeholder="YYYY-MM-DD HH:MM:SS" required>
                        </div>
                        </div>

                  <div class="button">
                  <input type="submit" name="submit" id="flightsButton">
                  </div>
                    <!-- </div> -->

            </div>
        </form>
    </div>
    <script>
            $("#pickDAteAndTime1").datetimepicker({
                format: 'Y-m-d H:i:i',
                formatTime: 'H:i:i',
                formatDate: 'Y-m-d',
                step: 30
            });
            $("#pickDAteAndTime2").datetimepicker({
                format: 'Y-m-d H:i:i',
                formatTime: 'H:i:i',
                formatDate: 'Y-m-d',
                step: 30
            });
        </script>

</body>

</html>
