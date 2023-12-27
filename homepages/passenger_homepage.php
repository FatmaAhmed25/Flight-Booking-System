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
$passenger = '';
if ($result) {
  $mergedData = mysqli_fetch_assoc($result);
  $passenger = $mergedData['ID'];
  mysqli_free_result($result);

  // Fetch completed flights
  $completedFlightsQuery = "SELECT pf.*, f.* FROM PassengerFlights pf
LEFT JOIN Flight f ON pf.FlightID = f.ID
WHERE pf.PassengerID = $passenger AND pf.Status = 'completed'";
  $completedFlightsResult = mysqli_query($conn, $completedFlightsQuery);
  $completedFlights = mysqli_fetch_all($completedFlightsResult, MYSQLI_ASSOC);

  // Fetch current flights
  $currentFlightsQuery = "SELECT pf.*, f.* FROM PassengerFlights pf
LEFT JOIN Flight f ON pf.FlightID = f.ID
WHERE pf.PassengerID = $passenger AND pf.Status = 'current'";
  $currentFlightsResult = mysqli_query($conn, $currentFlightsQuery);
  $currentFlights = mysqli_fetch_all($currentFlightsResult, MYSQLI_ASSOC);


} else {
  echo "Error executing query: " . mysqli_error($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $bookingId = $_POST['booking_id'];

  // Update the flight status to 'completed'
  $query = "UPDATE PassengerFlights SET Status = 'completed' WHERE BookingID = $bookingId";
  if (mysqli_query($conn, $query)) {
    echo "Flight status updated successfully.";
  } else {
    echo "Error updating flight status: " . mysqli_error($conn);
  }

  header("Location: passenger_homepage.php");
exit();
}


?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Passenger Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap");

    body {
      width: 100%;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      min-height: 100vh;
      font-family: "Poppins", sans-serif;
    }

    ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
    }

    a {
      text-decoration: none;
      color: white;
    }

    .header__wrapper header {
      width: 100%;
      background: url("../photos/bg.jpg") no-repeat 50% 20% / cover;
      min-height: calc(100px + 15vw);
    }

    .header__wrapper .cols__container .left__col {
      padding: 25px 20px;
      text-align: center;
      max-width: 350px;
      position: relative;
      margin: 0 auto;
    }

    .header__wrapper .cols__container .left__col .img__container {
      position: absolute;
      top: -60px;
      left: 50%;
      transform: translatex(-50%);
    }

    .header__wrapper .cols__container .left__col .img__container img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      display: block;
      box-shadow: 1px 3px 12px rgba(0, 0, 0, 0.18);
    }

    .header__wrapper .cols__container .left__col .img__container span {
      position: absolute;
      background: #2afa6a;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      bottom: 3px;
      right: 11px;
      border: 2px solid #fff;
    }

    .header__wrapper .cols__container .left__col h2 {
      margin-top: 60px;
      font-weight: 600;
      font-size: 22px;
      margin-bottom: 5px;
    }

    .header__wrapper .cols__container .left__col p {
      font-size: 0.9rem;
      color: #818181;
      margin: 0;
    }

    .header__wrapper .cols__container .left__col .about {
      justify-content: space-between;
      position: relative;
      margin: 35px 0;
    }

    .header__wrapper .cols__container .left__col .about li {
      display: flex;
      flex-direction: column;
      color: #818181;
      font-size: 0.9rem;
      text-align: center;

    }

    .header__wrapper .cols__container .left__col .about li span {
      color: #1d1d1d;
      font-weight: 600;

    }

    .header__wrapper .cols__container .left__col .about:after {
      position: absolute;
      content: "";
      bottom: -16px;
      display: block;
      background: #cccccc;
      height: 1px;
      width: 100%;
    }

    .header__wrapper .cols__container .content p {
      font-size: 1rem;
      color: #1d1d1d;
      line-height: 1.8em;
    }

    .header__wrapper .cols__container .content ul {
      gap: 30px;
      justify-content: center;
      align-items: center;
      margin-top: 25px;
    }

    .header__wrapper .cols__container .content ul li {
      display: flex;
    }

    .header__wrapper .cols__container .content ul i {
      font-size: 1.3rem;
    }

    .header__wrapper .cols__container .right__col nav {
      display: flex;
      align-items: center;
      padding: 30px 0;
      justify-content: space-between;
      flex-direction: column;
    }

    .header__wrapper .cols__container .right__col nav ul {
      display: flex;
      gap: 20px;
      flex-direction: column;
    }

    .header__wrapper .cols__container .right__col nav ul li a {
      text-transform: uppercase;
      color: #818181;
    }

    .header__wrapper .cols__container .right__col nav ul li:nth-child(1) a {
      color: #1d1d1d;
      font-weight: 600;
    }

    .header__wrapper .cols__container .right__col nav button {
      background: #0091ff;
  
      color: #fff;
      border: none;
      padding: 10px 25px;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 20px;
      background-color: #C63826;
     
    }

    .header__wrapper .cols__container .right__col nav button:hover {
      opacity: 0.8;
    }

    .header__wrapper .cols__container .right__col .photos {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
      gap: 20px;
    }

    .header__wrapper .cols__container .right__col .photos img {
      max-width: 100%;
      display: block;
      height: 100%;
      object-fit: cover;
    }

    /* Responsiveness */

    @media (min-width: 868px) {
      .header__wrapper .cols__container {
        max-width: 1200px;
        margin: 0 auto;
        width: 90%;
        justify-content: space-between;
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 50px;
      }

      .header__wrapper .cols__container .left__col {
        padding: 25px 0px;
      }

      .header__wrapper .cols__container .right__col nav ul {
        flex-direction: row;
        gap: 30px;
      }

      .header__wrapper .cols__container .right__col .photos {
        height: 365px;
        overflow: auto;
        padding: 0 0 30px;
      }
    }


    @media (min-width: 1017px) {
      .header__wrapper .cols__container .left__col {
        margin: 0;
        margin-right: auto;
      }

      .header__wrapper .cols__container .right__col nav {
        flex-direction: row;
      }

      .header__wrapper .cols__container .right__col nav button {
        margin-top: 0;
      }
    }

    #flight-list {
      list-style-type: none;
      padding: 0;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding-right: 120px;
    }

    .flight-card {
      width: calc(33.33% - 20px);
      margin-bottom: 20px;
      box-sizing: border-box;
      box-shadow: 1px 3px 12px rgba(0, 0, 0, 0.18);
      border-radius: 8px;
      overflow: hidden;
      transition: transform 0.3s ease-in-out;
    }

    .flight-card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .flight-card img {
      width: 100%;
      height: 120px;
      object-fit: cover;
    }

    .flight-card .flight-info {
      padding: 10px;
      text-align: center;
    }

    .flight-card h3 {
      margin: 0;
      font-size: 16px;
    }

    .flight-card a {
      text-decoration: none;
      color: #333;
    }

    .firstnav nav {
      background: rgba(0, 0, 0, 0.5);
      padding: 10px;
      position: fixed;
      width: 100%;
      display: flex;
      justify-content: space-between;
      z-index: 1000;

    }

    .firstnav nav a {
      color: white;
      text-decoration: none;
      padding: 10px;
    }

    .firstnav nav a:hover {
      background: rgba(255, 255, 255, 0.2);

    }

    .passenger-name {
      margin-left: 9px;
      margin-top: 7px;
      font-size: 1.2rem;
      font-weight: 500;
      color: white
    }

    .nav-links {
      display: flex;
      align-items: center;
      margin-right: 20px;
    }

    .nav-links a {
      margin-left: 20px;
    }

    /* nav ul li a.active {
      font-weight: bold;
      color: #0091ff;
      /* Change this to your desired color */
    } */

    /* .about{
      justify-content: space-between;
    }
    span
    {
      padding-right: 40px;
    } */
  </style>
 

</head>

<body>
  <?php if (isset($mergedData)): ?>

    <div class="firstnav">
      <nav>
        <div class="passenger-name">
          <?php echo $mergedData['Name']; ?>
        </div>
        <div class="nav-links">
          <a href="../passenger/profile.php">Profile</a>
          <a href="../login/logout.php">Logout</a>
        </div>
      </nav>
    </div>
    <div class="header__wrapper">
      <header></header>
      <div class="cols__container">
        <div class="left__col">
          <div class="img__container">
            <img src="../<?php echo $mergedData['Photo']; ?>">
            <span></span>
          </div>
          <h2>
            <?php echo $mergedData['Name']; ?>
          </h2>
          <p>
            <?php echo $mergedData['Email']; ?>
          </p>
          <p>
            <?php echo $mergedData['Tel']; ?>
          </p>


          <ul class="about">
            <li><span>
                <?php echo count($completedFlights) ?>
              </span>Completed Flights</li>
            <li><span>
                <?php echo $mergedData['Account']; ?>$
              </span>Account</li>
            <li><span>
                <?php echo $mergedData['ID']; ?>
              </span>passengerID</li>
          </ul>

        </div>
        <div class="right__col">
          <nav>
            <ul>
              <li><a href="#" onclick="toggleFlights('completed', event)" >List Completed Flights</a></li>
              <li><a href="#" onclick="toggleFlights('pending',event)">List Current Flights</a></li>
            </ul>
            <button><a href="../passenger/search_flight.php">Search for a flight</a></button>
          </nav>
          <!-- Completed Flights -->
          <div class="card flight-list" id="completed-flights" style="display: none;">
            <ul id="completed-flight-list">
              <?php if (count($completedFlights) > 0): ?>
                <?php foreach ($completedFlights as $flight): ?>
                  <li class="flight-card">

                    <div class="flight-info">
                      <h3><?php echo $flight['Name']; ?></h3>
                      <p>Fees: <?php echo isset($flight['Fees']) ? $flight['Fees'] : 'N/A'; ?></p>
                      <p>Date: <?php echo date('Y-m-d', strtotime($flight["StartDay"])); ?> </p>
                      <p>Time:<?php echo date('H:i', strtotime($flight["StartDay"])); ?> </p>
                        
                     </div>
                    
                  </li>
                <?php endforeach; ?>
               <?php else: ?>
                <li>No completed flights found</li>
              <?php endif; ?>
            </ul>
          </div>

          <!-- Current Flights -->
          <div class="card flight-list" id="pending-flights" style="display: none;">
            <ul id="pending-flight-list">
              <?php if (count($currentFlights) > 0): ?>
                <?php foreach ($currentFlights as $flight): ?>
                  <li class="flight-card">
                    <div class="flight-info">
                    <h3><?php echo $flight['Name']; ?></h3>
                      <p>Fees: <?php echo isset($flight['Fees']) ? $flight['Fees'] : 'N/A'; ?></p>
                      <p>Date: <?php echo date('Y-m-d', strtotime($flight["StartDay"])); ?> </p>
                      <p>Time:<?php echo date('H:i', strtotime($flight["StartDay"])); ?> </p>

                      <form action="passenger_homepage.php" method="post">
                    <input type="hidden" name="booking_id" value="<?php echo $flight['BookingID']; ?>">
                    <input type="submit" value="Mark as Completed">
                  </form>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li>No current flights found</li>
              <?php endif; ?>
            </ul>
          </div>

        </div>
      </div>
    <?php endif; ?>

    <script>
    function toggleFlights(type, e) {
      e.preventDefault(); // Prevent the default behavior of the link

      var completedFlights = document.getElementById("completed-flights");
      var pendingFlights = document.getElementById("pending-flights");

      var completedLink = document.querySelector('a[href="#completed-flights"]');
      var pendingLink = document.querySelector('a[href="#pending-flights"]');

      if (type === 'completed') {
        completedFlights.style.display = "block";
        pendingFlights.style.display = "none";

        // completedLink.parentElement.classList.add('active');
        completedLink.style.color="black";
        pendingLink.style.color="grey";

        // pendingLink.parentElement.classList.remove('active');
      } else {
        completedFlights.style.display = "none";
        pendingFlights.style.display = "block";
        pendingLink.style.color="black";
        completedLink.style.color="grey";

        

        // completedLink.parentElement.classList.remove('active');
        // pendingLink.parentElement.classList.add('active');
      }
    }

  </script>

</body>

</html>