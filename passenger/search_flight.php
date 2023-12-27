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
      /* background-image: url('../photos/bgSearch.jpg'); */
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
            /* background-color: #543345; */
            padding: 1px;
            border-radius: 30px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            box-shadow: 2px 5px 20px rgba(119, 119, 119, 0.5);
            z-index: 1;
    }
    .booking__nav span {
        flex: 1;
        padding: 1rem 2rem;
        font-weight: 500;
        color: var(--text-light);
        text-align: center;
        border-radius: 5px;
        cursor: pointer;
      }
      .booking__nav span:nth-child(2) {
  color: var(--white);
  background-color: var(--primary-color);
}
  .booking__container form {
    margin-top: 4rem;
    display: grid;
    grid-template-columns: repeat(3, 1fr) auto;
    gap: 1rem;
  }
  .booking__container .input__content {
  width: 100%;
}
  .booking__container .form__group {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-left: 60px;
    margin-bottom: 30px;

  }
  .booking__container .form__group span {
  padding: 10px;
  font-size: 1.5rem;
  color: var(--text-dark);
  background-color: var(--extra-light);
  border-radius: 1rem;
}
    .booking__container .input__group {
      width: 100%;
      position: relative;
    }

    .booking__container label {
      position: absolute;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
      font-size: 1.2rem;
      font-weight: 500;
      color: var(--text-dark);
      pointer-events: none;
      transition: 0.3s;
    }

    .booking__container input {
      width: 100%;
      padding: 10px 0;
      font-size: 1rem;
      outline: none;
      border: none;
      border-bottom: 1px solid var(--primary-color);
      color: var(--text-dark);
    }
    .booking__container input:focus ~ label {
      font-size: 0.8rem;
      top: 0;
    }
        .booking__container .form__group p {
      margin-top: 0.5rem;
      font-size: 0.8rem;
      color: var(--text-light);
    }
    .booking__container input:focus~label,
    .booking__container input:valid~label {
      font-size: 0.8rem;
      top: 0;
    }

    .booking__container .btn {
    padding: 0.8rem; /* Adjusted padding to minimize the size */
    font-size: 1.2rem; /* Adjusted font size */
    background-color: var(--primary-color); /* Added background color */
    color: var(--white); /* Added text color */
    border: none; /* Removed border */
    border-radius: 5px; /* Added border radius for rounded corners */
    cursor: pointer;
    margin-left: 134px;
    margin-right: 201px;
    margin-bottom: 61px;
  }

  .booking__container .btn:hover {
    background-color: var(--primary-color-dark); /* Added hover effect */
  }
  .card-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    padding-top: 50px;
  }

    .card {
      box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
      transition: 0.3s;
      width: 150px;
      border-radius: 5px;
      margin: 10px;
      background-color: #fff;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 10px;

}

    .card:hover {
      box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }
    h3{
  color:var(--primary-color);
}
    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-bottom: 1px solid #ddd;
    }
    .card a {
        color: inherit;      /* changes the link color to the color of the text in the card */
        text-decoration: none;  /* removes the underline */
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
        <span><i class="ri-map-pin-line"></i></span>
        <div class="input__content">
          <div class="input__group">
            <input type="text" id="from" name="from" required/>
            <label>From</label>
          </div>
          <p>Where are you?</p>
        </div>
        </div>
        <div class="form__group">
        <span><i class="ri-map-pin-line"></i></span>
        <div class="input__content">
          <div class="input__group">
            <input type="text" id="to" name="to" required/>
            <label>To</label>
          </div>
          <p>Where are you going?</p>
        </div>
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