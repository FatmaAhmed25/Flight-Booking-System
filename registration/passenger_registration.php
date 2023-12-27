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
                        header('Location: ../homepages/passenger_homepage.php');
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
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/js/all.min.js">
    <script>
        // JavaScript function to validate the account input
        function validateAccountInput() {
            // Get the value of the account input
            var accountInput = document.getElementById('account');

            // Regular expression to match a valid money amount (e.g., $100 or $100.50)
            var moneyPattern = /^\$?\d+(\.\d{1,2})?$/;

            // Check if the input value matches the money pattern
            if (!moneyPattern.test(accountInput.value)) {
                alert('Please enter a valid money amount for the account.');
                // Clear the input field
                accountInput.value = '';
                // Prevent the form from submitting
                return false;
            }

            // Allow the form to submit if the input is valid
            return true;
        }
    </script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Montserrat', sans-serif;
}

body{
    background-color: #c9d6ff;
    background: linear-gradient(to right, #e2e2e2, #c9d6ff);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    height: 100vh;
    --main-hue:208;
    --text-color:#000;
    --input-bg-hover: hsla(var(--main-hue), 50%, 50%, 14%); 
    --light-text-color:#9ca7b6;
    --input-text: #8c9aaf; 
}

.container{
    background-color: #fff;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
    position: relative;
    overflow: hidden;
    width: 768px;
    max-width: 100%;
    min-height: 480px;
}

.container p{
    font-size: 14px;
    line-height: 20px;
    letter-spacing: 0.3px;
    margin: 20px 0;
}

.container span{
    font-size: 12px;
}

.container a{
    color: #333;
    font-size: 13px;
    text-decoration: none;
    margin: 15px 0 10px;
}

.container button{
    background-color: #000543;
    color: #fff;
    font-size: 12px;
    padding: 10px 14px;
    border: 1px solid transparent;
    border-radius: 8px;
    /* font-weight: 600; */
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-top: 56px;
    cursor: pointer;
}

.container button.hidden{
    background-color: transparent;
    border-color: #fff;
}

.container form{
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 40px;
    height: 100%;
}

.container input{
    background-color: #eee;
    border: none;
    margin: 0px 0;
    padding: 14px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;

}

.form-container{
    position: absolute;
    top: 50px;
    height: 100%;
    transition: all 0.6s ease-in-out;
    left: 40px;
}

.container.active .sign-in{
    transform: translateX(100%);
}

.sign-up{
    z-index: 1;
}

.container.active .sign-up{
    transform: translateX(100%);
    opacity: 1;
    z-index: 5;
    animation: move 0.6s;
}

@keyframes move{
    0%, 49.99%{
        opacity: 0;
        z-index: 1;
    }
    50%, 100%{
        opacity: 1;
        z-index: 5;
    }
}

.social-icons{
    margin: 20px 0;
}

.social-icons a{
    border: 1px solid #ccc;
    border-radius: 20%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    margin: 0 3px;
    width: 40px;
    height: 40px;
}

.toggle-container{
    position: absolute;
    top: 0;
    left: 62%;
    width: 38%;
    height: 100%;
    overflow: hidden;
    transition: all 0.6s ease-in-out;
    border-radius: 150px 0 0 100px;
    z-index: 1000;
}

.container.active .toggle-container{
    transform: translateX(-100%);
    border-radius: 0 150px 100px 0;
}

.toggle{
    background-color:#000543;
    height: 100%;
    /* background: linear-gradient(to right, #5c6bc0, #512da8); */
    color: #fff;
    position: relative;
    left: -100%;
    height: 100%;
    width: 200%;
    transform: translateX(0);
    transition: all 0.6s ease-in-out;
}

.container.active .toggle{
    transform: translateX(50%);
}

.toggle-panel{
    position: absolute;
    width: 50%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 30px;
    text-align: center;
    top: 0;
    transform: translateX(0);
    transition: all 0.6s ease-in-out;
}

.toggle-left{
    transform: translateX(-200%);
}

.container.active .toggle-left{
    transform: translateX(0);
}

.toggle-right{
    right: 0;
    transform: translateX(0);
}

.container.active .toggle-right{
    transform: translateX(200%);
}
.radio-option {
    display: flex;
    align-items: center;
    gap: 10px;
}
.row {
    display:flex;
    justify-content: space-between;
    width: 250px;

}
.contact-heading h1{
        font-weight: 600;
        color: black;
        font-size: 3.5rem;
        line-height: 0.9;
        white-space: nowrap;
        margin-bottom: 1.2rem;
    }
    .contact-buttons{
        grid-template-columns: 1fr 1fr;
        column-gap: 1rem;
        grid-column: span 2;
    }
    .btn{
        display: inline-block;
    padding: 1.1rem 2rem;
    background-color: #000543;
    /* color: white; */
    border-radius: 40px;
    border: none;
    font-family: inherit;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: .3s;

    }
    .btn:hover{
        background-color: #c9d6ff;
        color: black;
    }
    .btn.upload{
        position: relative;
        
    }
    .btn.upload input{
        position: absolute;
        width: 100%;
        height: 100%;
        top:0;
        left:0;
        background-color: red;
        cursor: pointer;
        opacity: 0;
    }
    .btn1{
        width: 200px;
        margin-top: 72px;
    margin-left: 100px;
    }
    .input-wrap {
            position: relative;
            margin-top: 80px;
        }

        .input-wrap.w-100 {
            width: 100%;
        }

        .contact-input {
            /* width: calc(100% - 2.7rem); */
            background-color: hsla(var(--main-hue), 50%, 50%, 6.5%);
            padding: 1.5rem 1.35rem calc(0.75rem - 2px) 1.35rem;
            border: none;
            outline: none;
            font-family: inherit;
            border-radius: 20px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.95rem;
            transition: 0.3s;
            display: inline-block;
        }

        .contact-input:hover {
            background-color: var(--input-bg-hover);
        }

        .input-wrap label {
            position: absolute;
            top: 0.5rem;
            left: 1.35rem;
            transform: translateY(50%);
            color: var(--light-text-color);
            pointer-events: none;
            transition: 0.3s;
        }

        .input-wrap .icon {
            position: absolute;
            right: 1.35rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--input-text);
            font-size: 1.25rem;
            transition: 0.3s;
        }

        .input-wrap.focused .contact-input,
        .input-wrap.filled .contact-input {
            padding-top: 2.5rem;
        }

        .input-wrap.focused label,
        .input-wrap.filled label {
            top: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-color);
        }
</style>
</head>
<body>
    <main>
        <section class="container">
        <form action="passenger_registration.php" method="post" class="contact-form" enctype="multipart/form-data">

            <div class="form-container sign-up">
                <h1>Welcome, Continue Please! </h1>
                <div class="input-wrap">
                    <input class="contact-input" type="text" name="account" placeholder="Account $$$" id="account">
                    <!-- <label>Account</label> -->
                    <i class=" icon fa-solid fa-money-bill"></i>
                </div>
            <div class="contact-buttons w-100">
                            <button class="btn upload">
                                <span>
                                <i class="fa-solid fa-image"></i>   Add Profile Photo                        
                                </span>
                                <input type="file" class="contact-input" name="photo">  
                            </button>
                            <button class="btn upload">
                                <span>
                                <i class="fa-solid fa-passport"></i>   Add Passport Photo                        
                                </span>
                                <input type="file" class="contact-input" name="passportImg">  
                            </button>     
                            <div class="btn1">
                            <input type="submit" value="Register" class="btn">  
                            </div>               
                        </div>
                    </div>
    </form>
                <div class="toggle-container">
                    <div class="toggle">
                    <div class="toggle-panel toggle-right">
                    <img src="../photos/rocket.gif" alt="plane illustration" width="270px" height="230px">
                </div>

            </div>
            </div>
            </div>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/js/all.min.js">
    </script>
</body>
</html>