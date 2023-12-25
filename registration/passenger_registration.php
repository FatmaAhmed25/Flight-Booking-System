<?php

session_start();
include '../database/database.php';
$commonData = isset($_SESSION['common_data']) ? $_SESSION['common_data'] : [];

// unset($_SESSION['common_data']);

$photo = $passportImg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $account = $_POST["account"];
    $errors = array();

    function generateUniqueFilename($originalFilename)
    {
        $uniqueId = uniqid();
        $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        return $uniqueId . "." . $fileExtension;
    }

    if (isset($_FILES["photo"])) {
        if ($_FILES["photo"]["error"] == 0) {
            $uploadDir = "../assets/";
            $dbDir = "assets/";
            $newFilename = generateUniqueFilename($_FILES["photo"]["name"]);
            $photoPath = $uploadDir . $newFilename;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath)) {
                $photo = $dbDir . $newFilename;
            } else {
                array_push($errors, "Error moving uploaded file to destination.");
            }
        } else {
            array_push($errors, "File upload error: " . $_FILES["photo"]["error"]);
        }
    }

    if (isset($_FILES["passportImg"]) && $_FILES["passportImg"]["error"] == 0) {
        $uploadDir = "../assets/";
        $dbDir = "assets/";
        $newFilename = generateUniqueFilename($_FILES["passportImg"]["name"]);
        $passportImgPath = $uploadDir . $newFilename;
        if (move_uploaded_file($_FILES["passportImg"]["tmp_name"], $passportImgPath)) {
            $passportImg = $dbDir . $newFilename;
        } else {
            array_push($errors, "Error uploading the Passport Image.");
        }
    }

    $passengerData = array_merge($commonData, [
        'photo' => $photo,
        'passportImg' => $passportImg,
    ]);


    if (count($errors) > 0) {
        foreach ($errors as  $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } 
    else {

        $insertUserQuery = "INSERT INTO User (Name, Email, Password, Tel, Type) VALUES (?, ?, ?, ?, 'passenger')";
        $stmtUser = mysqli_prepare($conn, $insertUserQuery);

        if ($stmtUser) {
            mysqli_stmt_bind_param($stmtUser, "ssss", $passengerData['name'], $passengerData['email'], $passengerData['password'], $passengerData['tel']);

            if (mysqli_stmt_execute($stmtUser)) {
                // Retrieve the last inserted user ID
                $userID = mysqli_insert_id($conn);

                $insertPassengerQuery = "INSERT INTO Passenger (UserID, Photo, PassportImg, Account) VALUES (?, ?, ?, ?)";
                $stmtPassenger = mysqli_prepare($conn, $insertPassengerQuery);

                if ($stmtPassenger) {
                    mysqli_stmt_bind_param($stmtPassenger, "dssd", $userID, $passengerData['photo'], $passengerData['passportImg'], $account);

                    if (mysqli_stmt_execute($stmtPassenger)) {
                        echo "Passenger registration successful!";
                    } else {
                        echo "Error executing the Passenger insert statement: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmtPassenger);
                } else {
                    echo "Error preparing the Passenger insert statement: " . mysqli_error($conn);
                }
            } else {
                echo "Error executing the User insert statement: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmtUser);
        } else {
            echo "Error preparing the User insert statement: " . mysqli_error($conn);
        }

        unset($_SESSION['common_data']);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="container">
        <h2>Passenger Registration</h2>
        <form action="passenger_registration.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="photo">Photo:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="photo">
                </div>
            </div>

            <div class="form-group">
                <label for="passportImg">Passport Image:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="passportImg">
                </div>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="account" placeholder="Account: $$$$">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
    </div>
</body>

</html>