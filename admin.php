<?php
    session_start();

    //database info & array
    include_once("dbinfo.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, "unit_7");

    //check for successful connection 
    if (!$conn) {
        die("Connection Failed: " .mysqli_connect_error());
    } else {
        echo "Connection Successful!";
    }

    //retrieve the user_id & session_id
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $session_id = $_SESSION["session_id"];

        //check if there is a match of user_id & session_id
        $sql="SELECT * FROM login_sessions 
            WHERE user_id='$user_id' 
            AND session_id='$session_id'";

        if ($result=mysqli_query($conn,$sql)) {

            if(mysqli_num_rows($result) > 0) {
                //update time in login_sessions table
                $date = strtotime(date('Y-m-d H:i:s'));
                $sql = "UPDATE login_sessions 
                        SET last_access_time='$date'
                        WHERE session_id='$session_id'";
                mysqli_query($conn, $sql);

                echo "<br/>Welcome user $user_id from session $session_id!";
            } else {
                //no matching record...redirect
                header("Location:http://localhost:8888/form.html");
            } 
            
        } else {
            echo "Query Failed.";
        }
    }

    //close the database connection
    mysqli_close($conn);