<?php
session_start();

require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../login/login.php"); 
    exit();
}
$registeredPassengers = [];
$flight = []; // Initialize $flight as an empty array
$flightId='';
$pendingPassengers=[];
$passengerId='';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['flight_id'])) {
    $flightId = $_GET['flight_id'];

    $query = "SELECT * FROM Flight WHERE ID='$flightId'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $flight = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    } else {
        echo "No flight found with ID: " . $flightId;
    }

    // Fetch the registered passengers
    $query = "SELECT * FROM PassengerFlights JOIN Passenger ON PassengerFlights.PassengerID = Passenger.ID JOIN User ON Passenger.UserID = User.ID WHERE FlightID = '$flightId' AND Status = 'completed'";
    $result = mysqli_query($conn, $query);
    $registeredPassengers = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $registeredPassengers[] = $row;
        }
        mysqli_free_result($result);
    }

    // Fetch the pending passengers
    $query = "SELECT * FROM PassengerFlights JOIN Passenger ON PassengerFlights.PassengerID = Passenger.ID JOIN User ON Passenger.UserID = User.ID WHERE FlightID = '$flightId' AND Status = 'pending'";
    $result = mysqli_query($conn, $query);
    $pendingPassengers = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pendingPassengers[] = $row;
        }
        mysqli_free_result($result);
    }
   


}
//    foreach ($pendingPassengers as $passenger): 
//          echo $passenger['Name'];
//      endforeach; 

// $x=$registeredPassengers[0];

// foreach ($registeredPassengers as $passenger) {
//     // Fetch the Passenger ID using the User ID
//     $query1 = "SELECT ID FROM Passenger WHERE UserID = '".$passenger['ID']."'";
//     $result = mysqli_query($conn, $query1);
//     $passengerId = mysqli_fetch_assoc($result)['ID'];

//     echo("$passengerId   ");}
//     $query = "UPDATE Passenger SET Account = Account + '".$flight['Fees']."' WHERE ID = '$passengerId'";
//     mysqli_query($conn, $query);
//     $x=$flight['Fees'];
//     echo("$x   ");

//    $query2 = "SELECT Account FROM Passenger  WHERE ID = '$passengerId'";
//    $result2 = mysqli_query($conn, $query2);
//    $pass = mysqli_fetch_assoc($result2)['Account'];
//    echo("$pass");
    

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_flight'])) {
    $flightId = $_POST['cancel_flight'];

    $query = "SELECT * FROM Flight WHERE ID='$flightId'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $flight = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    } else {
        echo "No flight found with ID: " . $flightId;
    }
    // Update the flight status
    $query = "UPDATE Flight SET Completed = false WHERE ID = '$flightId'";
    mysqli_query($conn, $query);

    // Fetch the flight fees
    $query = "SELECT Fees FROM Flight WHERE ID = '$flightId'";
    $result = mysqli_query($conn, $query);
    $fees = mysqli_fetch_assoc($result)["Fees"];
    mysqli_free_result($result);

    // Fetch the registered passengers
    $query = "SELECT PassengerID FROM PassengerFlights WHERE FlightID = '$flightId' AND Status = 'completed'";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        // Refund the passenger
        $passengerId = $row["PassengerID"];
        $query = "UPDATE Passenger SET Account = Account + '$fees' WHERE ID = '$passengerId'";
        mysqli_query($conn, $query);
    }
    $query = "DELETE FROM PassengerFlights WHERE FlightID = '$flightId'";
    mysqli_query($conn, $query);
    mysqli_free_result($result);
    // Update the flight sta

}

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_flight'])) {
        $flightId = $_POST['cancel_flight'];
    
        $query = "SELECT * FROM Flight WHERE ID='$flightId'";
        $result = mysqli_query($conn, $query);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $flight = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        } else {
            echo "No flight found with ID: " . $flightId;
        }
        // Update the flight status
        $query = "UPDATE Flight SET Completed = false WHERE ID = '$flightId'";
        mysqli_query($conn, $query);
    
        // Fetch the flight fees
        $query = "SELECT Fees FROM Flight WHERE ID = '$flightId'";
        $result = mysqli_query($conn, $query);
        $fees = mysqli_fetch_assoc($result)["Fees"];
        mysqli_free_result($result);
    
        // Fetch the registered passengers
        $query = "SELECT PassengerID FROM PassengerFlights WHERE FlightID = '$flightId' AND Status = 'completed'";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            // Refund the passenger
            $passengerId = $row["PassengerID"];
            $query = "UPDATE Passenger SET Account = Account + '$fees' WHERE ID = '$passengerId'";
            mysqli_query($conn, $query);
        }
        $query = "DELETE FROM PassengerFlights WHERE FlightID = '$flightId'";
        mysqli_query($conn, $query);
        mysqli_free_result($result);
        // Update the flight status
        $query = "UPDATE Flight SET Canceled = true WHERE ID = '$flightId'";
        mysqli_query($conn, $query);
    
         // Redirect to another page
         header('Location: ../homepages/company_homepage.php');
         exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz@6..12&family=Poppins:ital@1&display=swap');
*{
    padding: 0;
    margin:0;
    box-sizing: border-box;
}
body {
    font-family: 'Nunito Sans' ,sans-serif;
    height:100vh;
    background:#eaeaea;
    background:linear-gradient(45deg,#eaeaea 33.33%,#c7ecee 33.33% , #c7ecee 66.66%, #eaeaea 66.66%);
    background-image: url('../photos/bg.jpg');
    background-size: cover;
    background-position: center;

}
.boarding-pass {
   position:relative;
   width:550px;
   
   top:50%;
   left:50%;
   transform:translate(-50%,-50%);
  
}
.boarding-pass .card {
   display:flex;
   background-color: #ffffff;
   padding:24px;
   border-radius:16px;
}
.boarding-pass .card.card-top {
   text-align:center;
   justify-content:space-between ;
   border-radius:16px 16px 0 0;
}
.boarding-pass .card.card-top .code {
    font-size:30px;
    font-weight: 700;
}
.boarding-pass .card.card-top .city {
    font-size:20px;
    color:#653d58;
}
.flight-median {
    position:relative;
    height:50px;
    width:100px;
    top:12px;
    /* background-color: red; */
    border-radius: 100% 100% 0 0 / 180% 180% 0 0;
    border:2px dashed transparent;
    border-top-color:#653d58;

}

.flight-median i {
    position:relative;
    top:-11px;
    size:100px;
    color:#653d58;

}
i{
    color:#653d58;
}
.flight-median:before {

    content:'';
    position:absolute;
    height:6px;
    width:6px;
    top:14px;
    left:5px;
    background-color: #653d58;
    border-radius: 50%;
    box-shadow:80px 0 0 #653d58;
}

.boarding-pass .card.card-bottom {

    flex-direction: column;
    border-radius:0 0 16px 16px;

}

.boarding-pass .card.card-bottom .card-row{

        display:flex;
        justify-content:space-between;



}
.boarding-pass .card.card-bottom .card-row:not(:last-child){

        margin-bottom:32px;

}
.boarding-pass .card.card-bottom .label{

        font-size: 15px;
        font-weight: 600;
        color:#653d58;

}
.flight-median .card-item .label {
    font-size: 15px;
        font-weight: 600;
        color:#653d58;
}
.flight-median .card-item .content {
    font-size: 14px;
    font-weight:900;
}
.boarding-pass .card.card-bottom .content{

    font-size: 14px;
    font-weight:900;


}
.median {
    
   
    height:24px;
    margin: 0 auto;
    background-image: radial-gradient(circle,transparent 72%, #ffffff 72%),
    linear-gradient(#ffffff,#ffffff),
    repeating-linear-gradient(90deg, #ffffff 0,#ffffff 2% ,#ffffff 2% , #ffffff 4% ),
    radial-gradient(circle,transparent 72%, #ffffff 72%);
    background-size: 24px 24px, calc(100% - 24px) 100%, 24px 24px;
    background-position: -12px 0 ,12px 50% , 12px 0 ,calc(100% + 12px) 0;
    background-repeat: no-repeat;
}
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    min-width: 160px;
    z-index: 1;
}

.dropdown-content p {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown:hover .dropdown-content {
    display: block;
}
h2{
    padding-bottom: 30px;
    font-size: 30px;
    /* padding-right: 250px; */
    padding-left: 0;
    color:white
}
.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 84%; 
    overflow: auto; 
    margin-top: 69px;
    border-radius: 20px;
    background-color: rgb(0,0,0); 
    background-color: rgba(0,0,0,0.4); 
}

.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%; /* Adjust width */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    padding: 12px 16px;
    z-index: 1;
    border-radius: 10px;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.button {

    margin-top: 30px;
    /* padding-top: 30px;
    padding-left: 410px; */
   
}
button{
    background-color: #fd891e;
    color:white;
    padding: 10px;
    border-color:transparent;
    border-radius: 10px;
}

.btn{
    padding-left:410px ;
} 



/* .row{
    display: flex;
} */


.card-item i{
    font-size: 10px;
}




    </style>
</head>
<body>
<div class="boarding-pass">

<h2>Flight Info</h2>
   <div class="card card-top">

    <div class="source">
        <div class="code">From</div>
        <!-- <?php echo("$flightId");?> -->
        <div class="city"><?php echo $flight["Source"]; ?></div>
     </div>

    <div class="flight-median">
      <i class="fas fa-plane"></i>
    </div>

    <div class="destination">
        <div class="code">To</div>
        <div class="city"><?php echo $flight["destination"]; ?></div>
    </div>  
</div>

<div class="median"></div>   
<div class="card card-bottom">
<div class="card-row">

    <div class="card-item">
        <span class="label">ID</span>
        <!-- <?php echo("$flightId");?> -->
        <p class="content"><?php echo $flight["ID"]; ?></p>
    </div>

    <div class="card-item">
        <span class="label">Name</span>
        <p class="content"><?php echo $flight["Name"]; ?></p>

    </div>
    <div class="card-item">
        <span class="label">Itinerary</span>
        <p class="content"><?php echo $flight["Itinerary"]; ?></p>
    </div>
</div>

<div class="card-row">
<div class="card-row">
<div class="card-item">
<span class="label">Registered Passengers- <?php echo count($registeredPassengers); ?> </span>
<!-- Trigger/Open The Modal -->
<a href="#" id="myBtn"><i class="fas fa-plane"></i></a>

<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">×</span>
        <?php foreach ($registeredPassengers as $passenger): ?>
            <!-- <?php echo  $passenger["Email"]; ?> -->
            <p><?php echo $passenger['Name']; ?></p>
        <?php endforeach; ?>
    </div>
</div>
</div>
</div>



    <div class="card-item">
<span class="label">Pending Passengers- <?php echo count($pendingPassengers); ?></span>
<div class="dropdown">
<span class="dropbtn"><a href="#" id="myBtn"><i class="fas fa-plane"></i></a>
<div class="dropdown-content">
    <?php foreach ($pendingPassengers as $passenger): ?>
        <p><?php echo $passenger['Name']; ?></p>
    <?php endforeach; ?>
</div>
</div>
</div>

<!-- Cancel Flight Form -->
<div class="button">
<form method="POST" action="" id="cancelFlightForm">
    <input type="hidden" name="cancel_flight" value="<?php echo $flight['ID']; ?>">
    <button type="submit" id="cancelFlightButton">Cancel Flight</button>
</form>
</div>


<script>
document.getElementById('cancelFlightButton').addEventListener('click', function(e) {
    if (!confirm('Are you sure you want to cancel this flight?')) {
        e.preventDefault();
    }
});
</script>



<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function(e) {
e.preventDefault();
modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}
</script>
</body>
</html>   

</body>
</html>