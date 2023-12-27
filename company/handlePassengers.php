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
    header("Location: handlepassengers.php");
    exit();

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
function getUserCountByCompanyStatus($conn, $flightId, $status)
{
    $query = "SELECT COUNT(*) AS count FROM PassengerFlights WHERE FlightID = '$flightId' AND companystatus = '$status'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    } else {
        return 0; // Return 0 if there is an error or no results
    }
}

// Get the counts
$registeredCount = getUserCountByCompanyStatus($conn, $flightId, 'registered');
$pendingCount = getUserCountByCompanyStatus($conn, $flightId, 'pending');
?>


<!DOCTYPE html>
<html>

<head>
    <style>
<span style="font-family: verdana, geneva, sans-serif;">/*  import google fonts */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

:root{
  --main-color: #DD2F6E;
  --color-dark: #1D2231;
  --text-grey: #8390A2;

}
*{
  margin: 0;
  padding: 0;
  text-decoration: none;
  box-sizing: border-box;
  list-style-type:none ;
  font-family: "Poppins", sans-serif;
}
.sidebar{
  width: 320px;
  position: fixed;
  left: 0;
  top: 0;
  height: 100%;
}
.main-content{
  margin-left:220px ;
}
.sidebar-brand{
  height: 90px;
  padding: 1rem 0rem 1rem 2rem;
  color: #89CFF0;

}
.sidebar-brand span{
  display: inline-block;
  padding-right: 1rem;
}
.sidebar-menu li{
  width: 100%;
  margin-bottom: 1.3rem;
  padding-left: 20px;
}
.sidebar-menu a{
  display: block;
  color: #89CFF0;
  font-size: 1.1rem;
}
.sidebar-menu a span:first-child{
  font-size: 1.5rem;
  padding-right: 1rem;
}
.sidebar-menu a.active{
  background: #fff;
  padding-top: 1rem;
  padding-bottom: 1rem;
  color: #1D2231;
  padding-left: 20px;
  border-radius:30px 0px 0px 30px ;
}
/* body{
  background: rgb(219, 219, 219);
} */

header{
  display: flex;
  justify-content: space-between;
  padding: 1rem 1.6rem;
  box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
}
.sidebar-menu{
  margin-top: 1rem;
}
header h2{
  color: #222;

}
header label span{
  font-size: 1.7rem;
  padding-right: 1rem;
}

main{
  margin-top: 85px;
  padding: 2rem 1.5rem;
  /* background: #f1f5f9; */
  min-height: calc(100vh - 85px);
}

.cards {
  display: flex; /* Use flexbox to arrange the divs side by side */
  justify-content: space-between; /* Add space between the two divs */
  margin-top: -90px;
}

.card-single {
  width: 48%; /* Adjust the width as needed */
  display: flex;
  justify-content: space-between;
  background: #fff;
  padding: 20px; /* Add padding as needed */
  border-radius: 8px; /* Add border-radius for a rounded appearance */
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add box-shadow for a subtle effect */
}

.card-single h1 {
  font-size: 35px; /* Adjust font size as needed */
}

.card-single span {
  font-size: 14px; /* Adjust font size as needed */
  color: var(--text-grey); /* Use variable for consistent color */
}
.card-single span:first-child{
  color: var(--text-grey);
}
.card-single span:last-child{
  font-size: 2rem;
}

table{
  border-collapse: collapse;
}

.recent-grid{
  margin-top: 3.5rem;
  display: grid;
  /* grid-gap: 2rem; */
  grid-template-columns: 50% auto;

}
.card{
  background: #fff;
  border-radius: 5px;
}
.card-header, .card-body{
  padding: 1rem;
}
.card-header{
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #f0f0f0;
}
.card-header button{
  background:var(--main-color);
  border-radius: 10px;
  color: #fff;
  font-size: .8rem;
  padding: .5rem 1rem;
  border: 1px solid var(--main-color);
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
  display: flex;
  justify-content: space-between;
}

/* Side menubar section */
nav{
    background: rgba(0, 0, 0, 0.5);
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
  width: 48%;
  padding: 1rem;
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
  background:black;
}


.accept-btn{
    background-color: black;
            color: white;
            padding: 5px;
            border-color: transparent;
            border-radius: 10px;
}

    </style>
    </span>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <div class="sidebar">
    <div class="sidebar-brand">
      <h1><span class="las la-plane"></span>Admin</h1>
    </div>
  <div class="sidebar-menu">
    <ul>
      <li>
        <a href="" class="active"><span class="las la-igloo"></span><span>Dashboard</span></a>

      </li>
      <li>
      <a href="../login/logout.php"><span class="las la-sign-out-alt"></span><span>Logout</span></a>

      </li>
    </ul>
  </div>
</div>
  <div class="main-content">
  <header>
    <h2>
      <label for=""></label>
    </h2>
    Admin Dashboard
  </header>
  <main>
    <div class="cards">
      <div class="card-single">
        <div>
        <h1>
          <?php echo $pendingCount; ?>
        </h1>
        <span>Pending Passengers</span>
      </div>
    <div>
        <span class="las la-users"></span>
      </div>
</div>
    <div class="card-single">
      <div>
        <h1><?php echo $registeredCount; ?></h1>
        <span>Registered Passengers</span>
      </div>
      <div>
        <span class="las la-users"></span>
      </div>
    </div>
</div>
<div class="recent-grid">
  <div class="project">
    <div class="card">
      <div class="card-header">
        <h2>Pending Passengers</h2>
      </div>
      <div class="card-body">
      <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Booking ID</th>
                <th>Flight ID</th>
                <th>Payment Method</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($pendingPassengers as $passenger): ?>
                <tr>
                    <td><?php echo $passenger['PassengerID']; ?></td>
                    <td><?php echo $passenger['BookingID']; ?></td>
                    <td><?php echo $passenger['FlightID']; ?></td>
                    <td><?php echo $passenger['PaymentMethod']; ?></td>
                    <td>
                        <form method="POST" action="handlePassengers.php?flight_id=<?php echo $flightId; ?>">
                        <input type="hidden" name="booking_id" value="<?php echo $passenger['BookingID']; ?>">
                            <button class="accept-btn" type="submit" name="accept_passenger">Accept</button>
                            <!-- <button class="reject-btn" type="submit" name="reject_passenger">Reject</button> -->
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
          </table>
      </div>
    </div>
  </div>
  <div class="customers">
    <div class="card">
      <div class="card-header">
        <h2>Registered Passengers</h2>
      </div>
      <div class="card-body">
        <div class="customer">
          <div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                
                <th>Booking ID</th>
                <th>Flight ID</th>
                <th>Payment Method</th>
              </tr>
            </thead>
            <tbody>
                    <?php foreach ($registeredPassengers as $passenger): ?>
                        <tr>
                       
                       
                        <td><?php echo $passenger['PassengerID']; ?></td>
                           
                        <td><?php echo $passenger['Name']; ?></td>
                            
                            <td><?php echo $passenger['BookingID']; ?></td>
                            <td><?php echo $passenger['FlightID']; ?></td>
                            <td><?php echo $passenger['PaymentMethod']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  </main>
    <!-- <div class="main-body">
      <h2>Dashboard</h2>
      <div class="promo_card">
        <h2>Welcome To Admin Dashboard!</h2>
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
                <th>Booking ID</th>
                <th>Flight ID</th>
                <th>Payment Method</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($pendingPassengers as $passenger): ?>
                <tr>
                    <td><?php echo $passenger['PassengerID']; ?></td>
                    <td><?php echo $passenger['BookingID']; ?></td>
                    <td><?php echo $passenger['FlightID']; ?></td>
                    <td><?php echo $passenger['PaymentMethod']; ?></td>
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
  </div> -->
</body>
</html>