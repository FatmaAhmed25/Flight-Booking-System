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
$passenger='';
if ($result) {
    $mergedData = mysqli_fetch_assoc($result);
    $passenger=$mergedData['ID'];
    mysqli_free_result($result);
    $bookedFlightsQuery = "SELECT COUNT(*) AS bookedFlightsCount FROM passengerflights WHERE passengerID = $passengerId AND status = 'booked'";
    $bookedFlightsResult = mysqli_query($conn, $bookedFlightsQuery);

    if ($bookedFlightsResult) {
        $bookedFlightsData = mysqli_fetch_assoc($bookedFlightsResult);
        $bookedFlightsCount = $bookedFlightsData['bookedFlightsCount'];
    } else {
        echo "Error executing booked flights query: " . mysqli_error($conn);
    }
} else {
    echo "Error executing query: " . mysqli_error($conn);
}
$completedFlightsQuery = "SELECT f.Name AS Name, f.Fees As Fees FROM passengerflights pf JOIN flight f ON pf.flightID WHERE pf.passengerID = $passengerId AND pf.status = 'completed'";
$completedFlightsResult = mysqli_query($conn, $completedFlightsQuery);

if (!$completedFlightsResult) {
    echo "Error executing completed flights query: " . mysqli_error($conn);
}

$completedFlightsCount = mysqli_num_rows($completedFlightsResult);
$pendingFlightsQuery = "SELECT Name, Fees FROM passengerflights 
                       JOIN flight ON passengerflights.flightID = flight.ID
                       WHERE passengerID = $passenger AND status = 'current'";

$pendingFlightsResult = mysqli_query($conn, $pendingFlightsQuery);

if (!$pendingFlightsResult) {
    echo "Error executing pending flights query: " . mysqli_error($conn);
}

$pendingFlightsCount = mysqli_num_rows($pendingFlightsResult);
$passenger = $mergedData['ID'];
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Passenger Dashboard</title>
  <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
    />
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
  color:white;
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
    background: #0091ff; /* Your existing background color */
  color: #fff;
  border: none;
  padding: 10px 25px;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 20px;
  /* Add the following line to set the background color */
  background-color: #C63826; /* Add your desired color value */
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
    }

    .flight-card {
    width: calc(33.33% - 20px); /* Adjust width based on your layout preferences */
    margin-bottom: 20px;
    box-sizing: border-box;
    box-shadow: 1px 3px 12px rgba(0, 0, 0, 0.18);
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease-in-out;
}
.flight-card:hover {
    transform: scale(1.05); /* Adjust the scale factor based on your preference */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
    .flight-card img {
        width: 100%;
        height: 120px; /* Set your desired image height */
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
            background: rgba(0, 0, 0, 0.5); /* Set the background color with transparency */
            padding: 10px;
            position: fixed;
            width: 100%;
            display: flex;
            justify-content: space-between;
            z-index: 1000; /* Set a higher z-index to ensure it's above other content */
        }

        .firstnav nav a {
            color: white;
            text-decoration: none;
            padding: 10px;
            /* transition: background 0.3s ease; */
        }

        .firstnav nav a:hover {
            background: rgba(255, 255, 255, 0.2); /* Set the hover background color with transparency */
        }

        .passenger-name {
            margin-left: 9px;
            margin-top: 7px;
            font-size: 1.2rem;
            font-weight: 500;
            color:white
        }

       .nav-links {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .nav-links a {
            margin-left: 20px;
        }
        nav ul li a.active {
        font-weight: bold;
        color: #0091ff; /* Change this to your desired color */
    }
    /* .about{
      justify-content: space-between;
    }
    span
    {
      padding-right: 40px;
    } */

 
  </style>
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

            completedLink.parentElement.classList.add('active');
            pendingLink.parentElement.classList.remove('active');
        } else {
            completedFlights.style.display = "none";
            pendingFlights.style.display = "block";

            completedLink.parentElement.classList.remove('active');
            pendingLink.parentElement.classList.add('active');
        }
    }

</script>

</head>
<body>
<?php if (isset($mergedData)) : ?>

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
      <h2><?php echo $mergedData['Name']; ?></h2>
      <p><?php echo $mergedData['Email']; ?></p>
      <p><?php echo $mergedData['Tel']; ?></p>


      <ul class="about">
        <!-- <li><span><?php echo $bookedFlightsCount; ?></span>Completed Flight</li> -->
        <li><span><?php echo $mergedData['Account']; ?>$</span>Account</li>
        <li><span><?php echo $mergedData['ID']; ?></span>passengerID</li>
      </ul>

    </div>
    <div class="right__col">
    <nav>
        <ul>
            <li><a href="#" onclick="toggleFlights('completed', event)" class="active">List Completed Flights</a></li>
            <li><a href="#" onclick="toggleFlights('pending',event)" class="active">List Pending Flights</a></li>
        </ul>
        <button><a href="../passenger/search_flight.php">Search for a flight</a></button>
    </nav>
    <div class="card flight-list" id="completed-flights" style="display: none;">
        <h3>Completed Flights</h3>
        <ul id="completed-flight-list">
            <?php if ($completedFlightsCount > 0) : ?>
                <?php while ($flight = mysqli_fetch_assoc($completedFlightsResult)) : ?>
                    <li class="flight-card">
                        <div class="flight-info">
                            <h3><?php echo $flight['Name']; ?></h3>
                            <p>Fees: $<?php echo $flight['Fees']; ?></p>
                        </div>
                    </li>
                <?php endwhile; ?>
            <?php else : ?>
                <li>No completed flights found</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="card flight-list" id="pending-flights" style="display: none;">
        <h3>Pending Flights</h3>
        <ul id="pending-flight-list">
            <?php if ($pendingFlightsCount > 0) : ?>
                <?php while ($flight = mysqli_fetch_assoc($pendingFlightsResult)) : ?>
                    <li class="flight-card">
                        <div class="flight-info">
                            <h3><?php echo $flight['Name']; ?></h3>
                            <p>Fees: $<?php echo $flight['Fees']; ?></p>
                        </div>
                    </li>
                <?php endwhile; ?>
            <?php else : ?>
                <li>No pending flights found</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
</div>
    <?php endif; ?>

</body>
</html>

   