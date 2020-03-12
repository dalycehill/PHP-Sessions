<?php
    session_start();

    //retrieve values
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    //database info & array
    include_once("dbinfo.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, "unit_7");
    
    //check for successful connection 
    if (!$conn) {
        die("Connection Failed: " .mysqli_connect_error());
    } else {
        echo "Connection Successful!";
    }

    //check if there is a match of username & password
    $sql="SELECT * FROM users 
             WHERE username='$username' 
             AND password='$password'";

    if ($result=mysqli_query($conn,$sql)) {

        if(mysqli_num_rows($result) > 0) {

            //get the user id
            $sql = "SELECT user_id FROM users 
                    WHERE username='$username' 
                    AND password = '$password' LIMIT 1";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                $user_id = $row["user_id"];
            }

            //set the session
            $_SESSION["user_id"] = $user_id;
            $session_id = session_id();
            $_SESSION["session_id"] = $session_id;
            echo "<br/>user_id: $user_id found. sess_id: $session_id set.";

            //see if theres a matching session in login sessions
            $sql="SELECT * FROM login_sessions 
             WHERE user_id='$user_id' 
             AND session_id='$session_id'";

            if ($result=mysqli_query($conn,$sql)) {

                //if not
                if(mysqli_num_rows($result) <= 0) {
                    //insert user_id, session_id, and current time to login_sessions table
                    $date = strtotime(date('Y-m-d H:i:s'));
                    $sql = "INSERT INTO login_sessions (user_id, session_id, last_access_time)
                            VALUES ('$user_id', '$session_id', '$date')";
                    mysqli_query($conn, $sql);
                }

                //redirect
                header("Location:http://localhost:8888/admin.php");
                
            } else {
                echo "Query Failed.";
            }
        
        } else {
            //no matching record...redirect
            header("Location:http://localhost:8888/form.html");
        } 

    } else {
        echo "Query Failed.";
    }

    mysqli_close($conn);