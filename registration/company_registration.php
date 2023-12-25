<?php

session_start();
include '../database/database.php';
$commonData = isset($_SESSION['common_data']) ? $_SESSION['common_data'] : [];

$logo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $account = $_POST["account"];
    $bio = $_POST["bio"];
    $address = $_POST["address"];
    $location = $_POST["location"];
    $username = $_POST["username"];

    $errors = array();

    function generateUniqueFilename($originalFilename)
    {
        $uniqueId = uniqid();
        $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        return $uniqueId . "." . $fileExtension;
    }

    if (isset($_FILES["logo"])) {
        if ($_FILES["logo"]["error"] == 0) {
            $uploadDir = "../assets/";
            $dbDir = "assets/";
            $newFilename = generateUniqueFilename($_FILES["logo"]["name"]);
            $logoPath = $uploadDir . $newFilename;
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $logoPath)) {
                $logo =  $dbDir . $newFilename;
            } else {
                array_push($errors, "Error moving uploaded file to destination.");
            }
        } else {
            array_push($errors, "File upload error: " . $_FILES["logo"]["error"]);
        }
    }

    $companyData = array_merge($commonData, [
        'bio' => $bio,
        'address' => $address,
        'location' => $location,
        'username' => $username,
        'logo' => $logo,
    ]);

    if (count($errors) > 0) {
        foreach ($errors as  $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else 
    {

        $insertUserQuery = "INSERT INTO User (Name, Email, Password, Tel, Type) VALUES (?, ?, ?, ?, 'company')";
        $stmtUser = mysqli_prepare($conn, $insertUserQuery);

        if ($stmtUser) {
            mysqli_stmt_bind_param($stmtUser, "ssss", $companyData['name'], $companyData['email'], $companyData['password'], $companyData['tel']);

            if (mysqli_stmt_execute($stmtUser)) {
                // Retrieve the last inserted user ID
                $userID = mysqli_insert_id($conn);

                $insertCompanyQuery = "INSERT INTO Company (UserID, Bio, Address, Location, Username, Logo, Account) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtCompany = mysqli_prepare($conn, $insertCompanyQuery);

                if ($stmtCompany) {
                    mysqli_stmt_bind_param($stmtCompany, "dsssssd", $userID, $companyData['bio'], $companyData['address'], $companyData['location'], $companyData['username'], $companyData['logo'], $account);

                    if (mysqli_stmt_execute($stmtCompany)) {
                        echo "Company registration successful!";
                    } else {
                        echo "Error executing the Company insert statement: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmtCompany);
                } else {
                    echo "Error preparing the Company insert statement: " . mysqli_error($conn);
                }
            } else {
                echo "Error executing the User insert statement: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmtUser);
        } else {
            echo "Error preparing the User insert statement: " . mysqli_error($conn);
        }

        mysqli_close($conn);
        unset($_SESSION['common_data']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="container">
        <h2>Company Registration</h2>
        <form action="company_registration.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="account">Account:</label>
                <input type="text" class="form-control" name="account" required>
            </div>

            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea class="form-control" name="bio" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" name="address" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" class="form-control" name="location" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="form-group">
                <label for="logo">Logo:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="logo" required>
                </div>
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
    </div>
</body>

</html>