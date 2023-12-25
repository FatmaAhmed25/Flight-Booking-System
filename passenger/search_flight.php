<?php
session_start();

require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    header("Location: ../login/login.php"); 
    exit();
}

$flights = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from = $_POST['from'];
    $to = $_POST['to'];

    $query = "SELECT * FROM Flight WHERE source='$from' AND destination='$to' AND Canceled = false";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $flights = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }
}

mysqli_close($conn);


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" />
  <title>Search</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap");

    :root {
      --primary-color: #3d5cb8;
      --primary-color-dark: #334c99;
      --text-dark: #0f172a;
      --text-light: #64748b;
      --extra-light: #f1f5f9;
      --white: #ffffff;
      --max-width: 1200px;
    }

    body {
      margin: 0;
      padding: 0;
      background-image: url('../photos/bgSearch.jpg');
      background-size: cover;
      background-position: center;
      font-family: 'Poppins', sans-serif;
    }

    .blurred-background {
      position: relative;
      height: 100vh;
      justify-content: center;
      align-items: center;
    }

    .blur-overlay {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      pointer-events: none;
    }

    .booking__container {
      background-color: var(--extra-light);
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      z-index: 1;
      text-align: center;
    }

    .booking__container form {
      display: flex;
      justify-content: space-around;
      align-items: center;
      flex-wrap: wrap;
    }

    .booking__container .form__group {
      flex: 1;
      display: flex;
      flex-direction: column;
      margin: 10px;
      position: relative;
    }

    .booking__container .input__group {
      position: relative;
      margin-bottom: 20px;
    }

    .booking__container label {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      font-size: 1rem;
      font-weight: 500;
      color: var(--text-dark);
      pointer-events: none;
      transition: 0.3s;
      background-color: var(--extra-light);
      padding: 0 5px;
    }

    .booking__container input {
      width: 100%;
      padding: 15px;
      font-size: 1rem;
      outline: none;
      border: none;
      border-radius: 10px;
      background-color: var(--white);
      color: var(--text-dark);
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .booking__container input:focus~label,
    .booking__container input:valid~label {
      font-size: 0.8rem;
      top: 0;
    }

    .booking__container .btn {
      padding: 15px;
      font-size: 1rem;
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.3s ease;
    }

    .booking__container .btn:hover {
      background-color: var(--primary-color-dark);
    }

    .card-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 20px;
    }

    .card {
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
      width: 300px;
      border-radius: 15px;
      margin: 20px;
      overflow: hidden;
    }

    .card:hover {
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
    }

    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-bottom: 1px solid #ddd;
    }

    .card-content {
      padding: 20px;
    }

    h3 {
      color: var(--primary-color);
    }

    p {
      margin: 0;
      color: var(--text-dark);
      font-size: 0.9rem;
    }

    a {
      text-decoration: none;
      color: var(--primary-color);
      font-weight: bold;
      transition: color 0.3s ease;
    }

    a:hover {
      color: var(--primary-color-dark);
    }
  </style>
</head>

<body>
  <div class="blurred-background">
    <div class="blur-overlay"></div>

    <section class="section__container booking__container">
      <form action="search_flight.php" method="post">
        <div class="form__group">
          <div class="input__group">
            <input type="text" id="from" name="from" required/>
            <label>From</label>
          </div>
          <p>Where are you?</p>
        </div>

        <div class="form__group">
          <div class="input__group">
            <input type="text" id="to" name="to" required/>
            <label>To</label>
          </div>
          <p>Where are you going?</p>
        </div>
        <button type="submit" class="btn"><i class="ri-search-line"></i> Search</button>
      </form>
    </section>

    <section class="card-container">
      <?php if (!empty($flights)): ?>
        <h3>Available Flights:</h3>
        <?php foreach ($flights as $flight): ?>
          <div class="card">
            <div class="card-content">
              <h4><?php echo $flight["Name"]; ?></h4>
              <p>Date: <?php echo date('Y-m-d', strtotime($flight["StartDay"])); ?></p>
              <p>Time: <?php echo date('H:i', strtotime($flight["StartDay"])); ?></p>
              <a href="../passenger/flightInfo.php?id=<?php echo $flight['ID']; ?>">View Details</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
</body>

</html>
