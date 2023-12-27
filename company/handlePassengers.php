<?php
session_start();

require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../login/login.php");
    exit();
}

$flightId = $_GET['flight_id'];

$registeredPassengers = [];
$pendingPassengers = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_passenger'])) {
    $booking_id = $_POST['booking_id']; // Get the booking id from the form

    // Fetch the passenger's payment method and flight fees
    $query = "SELECT PaymentMethod, Fees FROM PassengerFlights JOIN Flight ON PassengerFlights.FlightID = Flight.ID WHERE BookingID = '$booking_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $paymentMethod = $row["PaymentMethod"];
    $fees = $row["Fees"];

    // If the payment method is 'account', deduct the flight fees from the passenger's account
    if ($paymentMethod == 'account') {
        $query = "UPDATE Passenger SET Account = Account - '$fees' WHERE ID IN (SELECT PassengerID FROM PassengerFlights WHERE BookingID = '$booking_id')";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "Error executing query: " . mysqli_error($conn);
        }
    }

    // Update the companyStatus field to 'registered' for this specific booking
    $query = "UPDATE PassengerFlights SET companystatus = 'registered' WHERE BookingID = '$booking_id'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error executing query: " . mysqli_error($conn);
    }
}


// Fetch the registered passengers
$query = "SELECT * FROM PassengerFlights JOIN Passenger ON PassengerFlights.PassengerID = Passenger.ID JOIN User ON Passenger.UserID = User.ID WHERE FlightID = '$flightId' AND companyStatus = 'registered'";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $registeredPassengers[] = $row;
    }
    mysqli_free_result($result);
}

// Fetch the pending passengers
$query = "SELECT * FROM PassengerFlights JOIN Passenger ON PassengerFlights.PassengerID = Passenger.ID JOIN User ON Passenger.UserID = User.ID WHERE FlightID = '$flightId' AND companystatus = 'pending'";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pendingPassengers[] = $row;
    }
    mysqli_free_result($result);
}
?>


<!DOCTYPE html>
<html>

<head>
    <style>
<span style="font-family: verdana, geneva, sans-serif;">/*  import google fonts */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");
*{
  margin: 0;
  padding: 0;
  border: none;
  outline: none;
  text-decoration: none;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
body{
  background: rgb(219, 219, 219);
}
.header{
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 60px;
  padding: 20px;
  background: #fff;
}
.logo{
  display: flex;
  align-items: center;
}
.logo a{
  color: #000;
  font-size: 18px;
  font-weight: 600;
  margin: 2rem 8rem 2rem 2rem;
}
.search_box{
  display: flex;
  align-items: center;
}
.search_box input{
  padding: 9px;
  width: 250px;
  background: rgb(228, 228, 228);
  border-top-left-radius: 5px;
  border-bottom-left-radius: 5px;
}
.search_box i{
  padding: 0.66rem;
  cursor: pointer;
  color: #fff;
  background: #000;
  border-top-right-radius: 5px;
  border-bottom-right-radius: 5px;
}
.header-icons{
  display: flex;
  align-items: center;
}
.header-icons i{
  margin-right: 2rem;
  cursor: pointer;
}
.header-icons .account{
  width: 130px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.header-icons .account img{
  width: 35px;
  height: 35px;
  cursor: pointer;
  border-radius: 50%;
}
.container{
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
}

/* Side menubar section */
nav{
  background: #fff;
}
.side_navbar{
  padding: 1px;
  display: flex;
  flex-direction: column;
}
.side_navbar span{
  color: gray;
  margin: 1rem 3rem;
  font-size: 12px;
}
.side_navbar a{
  width: 100%;
  padding: 0.8rem 3rem;
  font-weight: 500;
  font-size: 15px;
  color: rgb(100, 100, 100);
}
.links{
  margin-top: 5rem;
  display: flex;
  flex-direction: column;
}
.links a{
  font-size: 13px;
}
.side_navbar a:hover{
  background: rgb(235, 235, 235);
}
.side_navbar .active{
  border-left: 2px solid rgb(100, 100, 100);
}

/* Main Body Section */
.main-body{
  width: 70%;
  padding: 1rem;
}
.promo_card{
  width: 100%;
  color: #fff;
  margin-top: 10px;
  border-radius: 8px;
  padding: 0.5rem 1rem 1rem 3rem;
  background: rgb(37, 37, 37);
}
.promo_card h1, .promo_card span, button{
  margin: 10px;
}
.promo_card button{
  display: block;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
}
.history_lists{
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.row{
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 1rem 0;
}
table{
  background: #fff;
  padding: 1rem;
  text-align: left;
  border-radius: 10px;
}
table td, th{
  padding: 0.2rem 0.8rem;
}
table th{
  font-size: 15px;
}
table td{
  font-size: 13px;
  color: rgb(100, 100, 100);
}



/* Sidebar Section */
.sidebar{
  width: 15%;
  padding: 2rem 1rem;
  background: #fff;
}
.sidebar h4{
  margin-bottom: 1.5rem;
}
.sidebar .balance{
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}
.balance .icon{
  color: #fff;
  font-size: 20px;
  border-radius: 6px;
  margin-right: 1rem;
  padding: 1rem;
  background: rgb(37, 37, 37);
}
.balance .info h5{
  font-size: 16px;
}
.balance .info span{
  font-size: 14px;
  color: rgb(100, 100, 100);
}
.balance .info i{
  margin-right: 2px;
}
    </style>
</head>

<span style="font-family: verdana, geneva, sans-serif;"><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
</head>
<body>
  <header class="header">
    <div class="logo">
      <a href="#">Admin</a>
     
    </div>
  <div class="container">
    <nav>
      <div class="side_navbar">
        <span>Main Menu</span>
        <a href="#" class="active">Dashboard</a>
      </div>
    </nav>

    <div class="main-body">
      <h2>Dashboard</h2>
      <div class="promo_card">
        <h1>Welcome To Admin Dashboard!</h1>
      </div>

      <div class="history_lists">
        <div class="list1">
          <div class="row">
            <h4>Pending Passengers</h4>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($pendingPassengers as $passenger): ?>
                <tr>
                    <td><?php echo $passenger['PassengerID']; ?></td>
                    <!-- <td><?php echo $passenger['Name']; ?></td>
                    <td><?php echo $passenger['Amount']; ?></td> -->
                    <td>
                        <form method="POST" action="handlePassengers.php?flight_id=<?php echo $flightId; ?>">
                        <input type="hidden" name="booking_id" value="<?php echo $passenger['BookingID']; ?>">
                            <button class="accept-btn" type="submit" name="accept_passenger">Accept</button>
                            <button class="reject-btn" type="submit" name="reject_passenger">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
          </table>
        </div>

        <div class="list2">
          <div class="row">
            <h4>Registered Passenger</h4>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
              </tr>
            </thead>
            <tbody>
                    <?php foreach ($registeredPassengers as $passenger): ?>
                        <tr>
                            <td><?php echo $passenger['ID']; ?></td>
                            <td><?php echo $passenger['Name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
          </table>
        </div>
    </div>
  </div>
</body>
</html>
</span>