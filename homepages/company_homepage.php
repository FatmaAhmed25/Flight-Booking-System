<?php
session_start();

require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../login/login.php");
    exit();
}

$companyId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

$query = "SELECT u.*, c.* FROM User u
          LEFT JOIN Company c ON u.ID = c.UserID
          WHERE u.ID = $companyId";
$result = mysqli_query($conn, $query);

if ($result) {
    $userData = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
} else {
    echo "Error executing query: " . mysqli_error($conn);
}

$company= $userData['ID'];
// Fetch the list of flights associated with the company
$flightsQuery = "SELECT * FROM Flight WHERE CompanyID = $company AND Canceled = false";
$flightsResult = mysqli_query($conn, $flightsQuery);


if ($flightsResult) {
    $flights = mysqli_fetch_all($flightsResult, MYSQLI_ASSOC);
    mysqli_free_result($flightsResult);
} else {
    echo "Error executing query: " . mysqli_error($conn);
}



$query2 = "SELECT cm.Message, p.Photo FROM 
CompanyMessages cm JOIN Passenger p 
ON cm.PassengerID = p.ID WHERE cm.CompanyID = '$company'";
$result2 = mysqli_query($conn, $query2);

$messages = [];
while ($row = mysqli_fetch_assoc($result2)) {
    $messages[] = $row;
}


    
// Check if the markAllAsRead POST parameter is set
if (isset($_POST['markAllAsRead'])) {
  $query = "UPDATE CompanyMessages SET IsRead = TRUE WHERE CompanyID = '$company'";
  $result = mysqli_query($conn, $query);

  if (!$result) {
      echo "Error executing query: " . mysqli_error($conn);
  }
}
$query = "SELECT COUNT(*) as UnreadMessageCount FROM CompanyMessages WHERE IsRead = FALSE AND CompanyID = '$company'";
$result = mysqli_query($conn, $query);
$unreadMessageCount = mysqli_fetch_assoc($result)['UnreadMessageCount'];
// echo("$$unreadMessageCount");
mysqli_close($conn);
?>




<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>

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

.company-name {
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

.icon {
	cursor: pointer;
	margin-right: 2px;
	/* line-height: 60px; */
 
 
}
.icon span {
	background: #f00;
	padding: 7px;
	border-radius: 60%;
	color: #fff;
  width:10px;
	vertical-align: top;
	margin-left: -21px;
  font-size:10px
}
.icon img {
	display: inline-block;
	width: 40px;
	margin-top: 20px;
}
.icon:hover {
	opacity: .7;
}

.logo {
	flex: 1;
	margin-left: 50px;
	color: #eee;
	font-size: 20px;
	font-family: monospace;
}

.notifi-box {
	width: 310px;
	height: 0px;
	opacity: 0;
  overflow-y: scroll;
	position: absolute;
	top: 63px;
	right: 35px;
  border-radius: 8px;
  background-color: white;
	transition: 1s opacity, 250ms height;
	box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.notifi-box h2 {
	font-size: 14px;
	padding: 10px;
	border-bottom: 1px solid #eee;
	color: #999;
}
.notifi-box h2 span {
	color: #f00;
}
.notifi-item {
	display: flex;
	border-bottom: 1px solid #eee;
	padding: 15px 5px;
	margin-bottom: 15px;
	cursor: pointer;
}
.notifi-item:hover {
	background-color: #eee;
}
.notifi-item img {
	display: block;
	width: 50px;
	margin-right: 10px;
	border-radius: 50%;
}
.notifi-item .text h4 {
	color: #777;
	font-size: 16px;
	margin-top: 10px;
}
.notifi-item .text p {
	color: #aaa;
	font-size: 12px;
}
.photo {
  border-radius: 50%;
  width: 100px;
  height: 50px;
  object-fit: cover;'
  padding-top: 30PX;
  
}
i{
  color:white;
  font-size: 30px;
  padding-top: 10px;
}
#notif-count{
  font-size
}
.fas fa-envelope
{
  padding-left: 20px;
}
</style>


</head>
<body>
<?php if (isset($userData)) : ?>


  

    <div class="firstnav">
    <nav>
        <div class="company-name">
            <?php echo $userData['Username']; ?>
        </div>

        
    <div class="nav-links">
    <div class="icon" onclick="toggleNotifi(); markAllAsRead();">
       <i class="fas fa-envelope"> <span id="notif-count"><?php echo $unreadMessageCount; ?></span></i>  
    </div>
    <div class="notifi-box" id="box">
          <h2>Messages <span id="notif-count-header"><?php echo count($messages) ?></span></h2>
          <?php foreach ($messages as $message) : ?>
              <div class="notifi-item">
                  <img src="../<?php echo $message['Photo']; ?>" alt="img" class="photo">
                  <div class="text">
                      <!-- <h4>Message</h4> -->
                      <p><?php echo $message['Message']; ?></p>
                  </div> 
              </div>
          <?php endforeach; ?>
     </div>
            <a href="../company/profile.php">Profile</a>
            <a href="../login/logout.php">Logout</a>
        </div>

      


     
    </nav>
</div>
<div class="header__wrapper">
      <header></header>
      <div class="cols__container">
        <div class="left__col">
          <div class="img__container">
          <img src="../<?php echo $userData['Logo']; ?>">
            <span></span>
          </div>
          <h2><?php echo $userData['Name']; ?></h2>
          <p><?php echo $userData['Email']; ?></p>

          <ul class="about">
            <li><span><?php echo count($flights); ?></span>Flights</li>
            <li><span><?php echo $userData['Account']; ?>$</span>Account</li>
            <li><span><?php echo $userData['ID']; ?></span>companyID</li>
          </ul>

          <div class="content">
            <p>
            <?php echo $userData['Bio']; ?>
            </p>

            <!-- <ul>
              <li><i class="fab fa-twitter"></i></li>
              <i class="fab fa-pinterest"></i>
              <i class="fab fa-facebook"></i>
              <i class="fab fa-dribbble"></i>
            </ul> -->
          </div>
        </div>
        <div class="right__col">
          <nav>
            <ul>
              <!-- <li><a href="../flight/add_flight.php" class="btn btn-primary">Add Flight</a></li> -->
              <li><a href="">List Flights</a></li>
             
            </ul>
            <button><a href="../flight/add_flight.php" >Add Flight</a></button>
          </nav>
          <div class="card flight-list">
          <ul id="flight-list">
    <?php
    $photoPaths = ['../photos/plane1.png', '../photos/plane2.png', '../photos/plane3.png', '../photos/plane4.png','../photos/plane5.png', '../photos/plane6.png'];
    $photoIndex = 0;

    foreach ($flights as $flight) : ?>
        <li class="flight-card">
            <a href="../company/flightInfo.php?flight_id=<?php echo $flight['ID']; ?>">
                <img src="<?php echo $photoPaths[$photoIndex % count($photoPaths)]; ?>" alt="Random Photo">
                <div class="flight-info">
                    <h3><?php echo $flight['Name']; ?></h3>
                    <!-- Add other flight information as needed -->
                </div>
            </a>
        </li>

        <?php
        // Increment photo index for the next flight
        $photoIndex++;
        endforeach;
    ?>
</ul>
        
        </div>
      </div>
    </div>
        <?php endif; ?>


    

<script>
  var box  = document.getElementById('box');
  var down = false;

  function toggleNotifi(){
      if (down) {
          box.style.height  = '0px';
          box.style.opacity = 0;
          down = false;
      } else {
          box.style.height  = '510px';
          box.style.opacity = 1;
          down = true;
      }
  }
</script>
<script>
function markAllAsRead() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "", true);  // POST to the current page
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          document.getElementById('notif-count').textContent = '0';
          // document.getElementById('notif-count-header').textContent = '0';
       
      }
  };
  xhr.send("markAllAsRead=true");
}


</script>
</script>
    

</body>
</html>
