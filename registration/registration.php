<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errors = array();
    $email = $_POST["email"];
    $name = $_POST["name"];
    $password = $_POST["password"];
    $tel = $_POST["tel"];
    $type = $_POST["type"];

    require_once "../database/database.php";
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);

    if (empty($name) OR empty($email) OR empty($password) OR empty($tel) OR empty($type)) {
        array_push($errors,"All fields are required");
    }

    if ($rowCount > 0 ) {
        array_push($errors, "Email already exists!");
    }
    if (count($errors) > 0) {
        foreach ($errors as  $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } 
    else 
    {
        $_SESSION['common_data'] = [
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'tel' => $tel,
            'type' => $type,
        ];

        if ($type == 'company') {
            header("Location: company_registration.php");
        } else if ($type == 'passenger') {
            header("Location: passenger_registration.php");
        }
    }
}
?>

<!-- <!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="container">
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="Name:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="tel" placeholder="Telephone:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" value="passenger">
                <label class="form-check-label">
                    Passenger
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" value="company">
                <label class="form-check-label">
                    Company
                </label>
            </div>
            <br>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>

        <div class="mt-3">
            <p>Already have an account? <a href="../login/login.php">Login</a></p>
        </div>
    </div>
</body> -->
