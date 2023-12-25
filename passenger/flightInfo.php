<?php
session_start();

require '../database/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$flight_id = $_GET['id'];
$flight = [];

$query = "SELECT * FROM Flight WHERE ID='$flight_id' AND Canceled = false";
$result = mysqli_query($conn, $query);

if ($result) {
    $flight = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
} else {
    echo "Error executing query: " . mysqli_error($conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "SELECT ID, Account FROM Passenger WHERE UserID = '$user_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $passenger = mysqli_fetch_assoc($result);
        $passenger_id = $passenger['ID'];
        $passenger_account = $passenger['Account'];

        if ($_POST['payment_method'] == 'account' && $passenger_account >= $flight['Fees']) {
            $query = "UPDATE Passenger SET Account = Account - {$flight['Fees']} WHERE ID = '$passenger_id'";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "Error executing query: " . mysqli_error($conn);
            }
        } else {
            echo "Insufficient account balance.";
        }

        if ($result) {
            echo "Flight booked successfully!";

            // Get the company ID from the flight
            $company_id = $flight['CompanyID'];
            $paymentM = $_POST['payment_method'];
            // Create the message
            $message = "The passenger with ID $passenger_id booked the flight with ID $flight_id by  $paymentM.";

            // // Get the passenger's photo
            // $passenger_photo = $passenger['Photo'];

            // Insert the message into the CompanyMessages table
            $query = "INSERT INTO CompanyMessages (CompanyID, Message, PassengerID) VALUES ('$company_id', '$message', '$passenger_id')";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "Error executing query: " . mysqli_error($conn);
            }

            // Insert a record into the PassengerFlights table
            $query = "INSERT INTO PassengerFlights (PassengerID, FlightID, Status) VALUES ('$passenger_id', '$flight_id', 'current')";
            $result = mysqli_query($conn, $query);
            if (!$result) {
                echo "Error executing query: " . mysqli_error($conn);
            }
        }
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }

    header('Location: ../homepages/passenger_homepage.php');
    exit();
}

mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz@6..12&family=Poppins:ital@1&display=swap');

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito Sans', sans-serif;
            height: 100vh;
            background: #eaeaea;
            background: linear-gradient(45deg, #eaeaea 33.33%, #c7ecee 33.33%, #c7ecee 66.66%, #eaeaea 66.66%);
            background-image: url('../photos/bg.jpg');
            background-size: cover;
            background-position: center;

        }

        .boarding-pass {
            position: relative;
            width: 550px;

            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

        }

        .boarding-pass .card {
            display: flex;
            background-color: #ffffff;
            padding: 24px;
            border-radius: 16px;
        }

        .boarding-pass .card.card-top {
            text-align: center;
            justify-content: space-between;
            border-radius: 16px 16px 0 0;
        }

        .boarding-pass .card.card-top .code {
            font-size: 30px;
            font-weight: 700;
        }

        .boarding-pass .card.card-top .city {
            font-size: 20px;
            color: #653d58;
        }

        .flight-median {
            position: relative;
            height: 50px;
            width: 100px;
            top: 12px;
            /* background-color: red; */
            border-radius: 100% 100% 0 0 / 180% 180% 0 0;
            border: 2px dashed transparent;
            border-top-color: #653d58;

        }

        .flight-median i {
            position: relative;
            top: -11px;
            size: 100px;
            color: #653d58;

        }

        i {
            color: #653d58;
        }

        .flight-median:before {

            content: '';
            position: absolute;
            height: 6px;
            width: 6px;
            top: 14px;
            left: 5px;
            background-color: #653d58;
            border-radius: 50%;
            box-shadow: 80px 0 0 #653d58;
        }

        .boarding-pass .card.card-bottom {

            flex-direction: column;
            border-radius: 0 0 16px 16px;

        }

        .boarding-pass .card.card-bottom .card-row {

            display: flex;
            justify-content: space-between;



        }

        .boarding-pass .card.card-bottom .card-row:not(:last-child) {

            margin-bottom: 32px;

        }

        .boarding-pass .card.card-bottom .label {

            font-size: 15px;
            font-weight: 600;
            color: #653d58;

        }

        .flight-median .card-item .label {
            font-size: 15px;
            font-weight: 600;
            color: #653d58;
        }

        .flight-median .card-item .content {
            font-size: 14px;
            font-weight: 900;
        }

        .boarding-pass .card.card-bottom .content {

            font-size: 14px;
            font-weight: 900;


        }

        .median {


            height: 24px;
            margin: 0 auto;
            background-image: radial-gradient(circle, transparent 72%, #ffffff 72%),
                linear-gradient(#ffffff, #ffffff),
                repeating-linear-gradient(90deg, #ffffff 0, #ffffff 2%, #ffffff 2%, #ffffff 4%),
                radial-gradient(circle, transparent 72%, #ffffff 72%);
            background-size: 24px 24px, calc(100% - 24px) 100%, 24px 24px;
            background-position: -12px 0, 12px 50%, 12px 0, calc(100% + 12px) 0;
            background-repeat: no-repeat;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            min-width: 160px;
            z-index: 1;
        }

        .dropdown-content p {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        h2 {
            padding-bottom: 30px;
            font-size: 30px;
            /* padding-right: 250px; */
            padding-left: 0;
            color: white
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 84%;
            overflow: auto;
            margin-top: 69px;
            border-radius: 20px;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            /* Adjust width */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            padding: 12px 16px;
            z-index: 1;
            border-radius: 10px;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .button {

            padding-top: 30px;
            padding-left: 410px;

        }

        button {
            background-color: #fd891e;
            color: white;
            padding: 10px;
            border-color: transparent;
            border-radius: 10px;
        }

        #confirmationModal {
            padding-left: 100px;
            padding-top: 40px;
            background-color: rgba(0, 0, 0, 0.8);
            width: 100.2%;
            height: 86.4%;
            overflow: auto;


        }

        .btn {
            padding-left: 410px;
        }

        #confirmationModal .modal-box i {
            font-size: 60px;
            color: #653d58;
            padding-left: 140px;
            padding-top: 30px;
        }

        #confirmationModal .modal-box h2 {
            margin-top: 20px;
            font-size: 25px;
            font-weight: 500;
            color: #333;
        }

        #confirmationModal .modal-box h3 {
            font-size: 16px;
            font-weight: 400;
            color: #333;
            /* text-align: center; */
        }

        .m {
            padding-left: 40px;
        }

        /* .row{
    display: flex;
} */
        .payment-methods {
            display: flex;
            justify-content: space-between;
            width: 150px;
        }

        #confirmationModal .modal-box .buttons {
            margin-top: 25px;
        }

        #confirmationModal .modal-box button {
            font-size: 14px;
            padding: 6px 12px;
            margin: 0 10px;
        }

        .take-flight-btn {

            border-radius: 8px;
            width: 90px;
        }

        .card-item i {
            font-size: 10px;
        }

        #confirmationModal .modal-box {

            /* left: 50%;
  top: 50%; */
            /* z-index: 2;
  padding: 30px 20px;
  border-radius: 24px; */
            background-color: #fff;
            width: 350px;
            height: 300px;
            border-radius: 20px;

            /* opacity: 0;
  pointer-events: none;
  transition: all 0.3s ease;
  transform: translate(-50%, -50%) scale(1.2); */
        }


        #confirmationModal.active+#overlay {
            display: block;
        }
    </style>
</head>

<body>

    <div class="boarding-pass">

        <h2>Flight Info</h2>
        <div class="card card-top">

            <div class="source">
                <div class="code">From</div>
                <!-- <?php echo ("$flightId"); ?> -->
                <div class="city">
                    <?php echo $flight["Source"]; ?>
                </div>
            </div>

            <div class="flight-median">
                <i class="fas fa-plane"></i>
                <div class="card-item">
                    <span class="label">Fees</span>
                    <p class="content">
                        <?php echo $flight["Fees"]; ?>$
                    </p>
                </div>
            </div>

            <div class="destination">
                <div class="code">To</div>
                <div class="city">
                    <?php echo $flight["destination"]; ?>
                </div>
            </div>

        </div>

        <div class="median"></div>
        <div class="card card-bottom">

            <div class="card-row">

                <div class="card-item">
                    <span class="label">ID</span>
                    <!-- <?php echo ("$flightId"); ?> -->
                    <p class="content">
                        <?php echo $flight["ID"]; ?>
                    </p>
                </div>

                <div class="card-item">
                    <span class="label">Name</span>
                    <p class="content">
                        <?php echo $flight["Name"]; ?>
                    </p>

                </div>

                <div class="card-item">
                    <span class="label">Itinerary</span>
                    <p class="content">
                        <?php echo $flight["Itinerary"]; ?>
                    </p>
                </div>

            </div>

            <div class="card-row">
                <!-- 
            <p>Date: <?php echo date('Y-m-d', strtotime($flight["StartDay"])); ?></p>
      <p>Time: <?php echo date('H:i:s', strtotime($flight["StartDay"])); ?></p> -->
                <div class="card-item">
                    <span class="label">Boarding Day</span>
                    <p class="content">
                        <?php echo date('Y-m-d', strtotime($flight["StartDay"])); ?>
                    </p>
                </div>

                <div class="card-item">
                    <span class="label">Boarding Time</span>
                    <p class="content">
                        <?php echo date('H:i', strtotime($flight["StartDay"])); ?>
                    </p>
                </div>



                <div class="card-item">
                    <span class="label">Arrival Time</span>
                    <p class="content">14:00 GST</p>
                </div>

            </div>

            <!-- <form action="flightInfoo.php?id=<?php echo $flight_id; ?>" method="post" onsubmit="return confirmBooking();">
            <button type="submit" class="take-flight-btn">Take Flight</button>
            </form>

            <script>
            function confirmBooking() {
                return confirm('Are you sure you want to book this flight?');
            }
            </script>  -->





            <!-- <div class="card-item">
                <span class="label">book</span>
                <p class="content">bb</p>
            </div>

            <div class="card-item">
                <span class="label">Seat</span>
                <p class="content">4D</p>
            </div> -->



            <div class="card-item">
                <span class="label">Remaining Passengers</span>
                <a href="#" id="myBtn"><i class="fas fa-plane"></i></a>
                <p class="content">
                    <?php echo $flight["PendingPassengers"]; ?>
                </p>
            </div>


            <div class="btn">
                <button onclick="openModal()" class="take-flight-btn">Take Flight</button>
            </div>
            <div id="confirmationModal" class="modal">
                <div class="modal-box">
                    <i class="fas fa-plane"></i>
                    <div class="m">
                        <h2>Take Flight Confirmation</h2>
                        <h3>Choose payment method:</h3>

                        <form id="takeFlightForm" method="POST" action="flightInfo.php?id=<?php echo $flight['ID']; ?>">
                            <div class="payment-methods">
                                <div>
                                    <label for="cash">Cash</label>
                                    <input type="radio" id="cash" name="payment_method" value="cash" checked>
                                </div>

                                <div>
                                    <label for="account">Account</label>
                                    <input type="radio" id="account" name="payment_method" value="account">
                                </div>
                            </div>

                            <div class="buttons">
                                <button id="confirmPaymentButton" type="submit">Confirm</button>
                                <button id="closeConfirmationModal" type="button" onclick="closeModal()">Cancel</button>
                            </div>

                    </div>
                    </form>
                </div>
            </div>

            <script>
                var modal = document.getElementById("confirmationModal");
                var span = document.getElementsByClassName("close-button")[0];

                function openModal() {
                    modal.style.display = "block";
                }

                function closeModal() {
                    modal.style.display = "none";
                }

                span.onclick = function () {
                    closeModal();
                }

                window.onclick = function (event) {
                    if (event.target == modal) {
                        closeModal();
                    }
                }
            </script>










        </div>
    </div>




    <div id="overlay"></div>

</body>

</html>













<!-- <script>

document.querySelector("#takeFlightForm").addEventListener("submit", function(e) {
    e.preventDefault(); // prevent the form from submitting

    // Get the selected payment method
    var paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

    console.log(paymentMethod);
    if (paymentMethod === "account") {
        // If the user chose to pay by account, show a confirmation message
        if (confirm("Are you sure you want to pay by account?")) {
            this.submit(); // submit the form
        }
    } else {
        // If the user chose to pay by cash, just submit the form
        this.submit();
    }
});




</script> -->






<!--script>


    // document.querySelector("#takeFlightForm").addEventListener("submit", function(e) {
    //     e.preventDefault(); // prevent the form from submitting

    //     // Get the selected payment method
    //     console.log("Form submitted");
    //     var paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

    //     console.log(paymentMethod);
    //     if (paymentMethod == "account") {
    //         console.log('hiiiiii');
    //         // If the user chose to pay by account, show a confirmation message
    //         if (confirm("Are you sure you want to pay by account?")) {
    //             this.submit(); // submit the form
    //         }
    //     } 
    //     else{
    //         //If the user chose to pay by cash, just submit the form
    //         this.submit();
    //     }
    // });


// When the user clicks the "Take Flight" button, open the confirmation modal
// document.getElementById("takeFlightButton").addEventListener("click", function(e) {//take flight
//   e.preventDefault();
//   console.log("Take Flight button clicked");
//   document.getElementById("confirmationModal").classList.add("active"); //confirm
// //   document.getElementById("overlay").classList.add("active");
// });

// // Get the <span> element that closes the confirmation modal
// var closeConfirmationModal = document.getElementById("closeConfirmationModal");

// // When the user clicks on <span> (x) or "Cancel" button, close the confirmation modal
// closeConfirmationModal.onclick = function() {
//   document.getElementById("confirmationModal").classList.remove("active");
// //   document.getElementById("overlay").classList.remove("active");
// };

// // When the user clicks anywhere outside of the confirmation modal, close it
// window.onclick = function(event) {
//   if (event.target == document.getElementById("confirmationModal")) {
//     document.getElementById("confirmationModal").classList.remove("active");
//     // document.getElementById("overlay").classList.remove("active");
//   }
// };


// // Handle the "Yes, Take Flight" button click
// document.getElementById("takeFlightConfirm").addEventListener("click", function() {
//   // Add your logic for taking the flight here
//   // For example, you can submit the form or perform an AJAX request
//   document.getElementById("confirmationModal").classList.remove("active");
// });

</script-->