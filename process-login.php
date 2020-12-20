<?php
    // This section processes submissions from the login form // Check if the form has been submitted:
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //connect to database
        try {
            require ('mysqli_connect.php');
        // Validate the email address
        // Check for an email address:
        $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL);
       
        if ((empty($email)) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $errors[] = 'You forgot to enter your email address';
            $errors[] = ' or the e-mail format is incorrect.';
        }

        // Validate the password 
        $password = filter_var( $_POST['password'], FILTER_SANITIZE_STRING); 
        if (empty($password)) {
            $errors[] = 'You forgot to enter your password.'; 
        }
        if (empty($errors)) { 
            // If everything's OK.
            // Retrieve the user_id, psword, first_name and user_level for that // email/password combination
            #1
            $query = "SELECT userid, password, first_name, user_level FROM users WHERE email = ?";
            if($stmt = $conn->prepare($query)){
                echo "pociecho";
                $stmt->bind_param('s', $email);
                $stmt->execute();
                if($stmt->bind_result($userid, $pass, $first_name, $user_level)){
                    $res = 1;
                }
                //$stmt->store_result();
                $stmt->fetch();
    
                if($res==1){
                    if(password_verify($password, $pass)){
                        session_start();
                        $_SESSION['admin'] = 1;
                    }
                    header('Location: ' . 'index.php');
                    // Make the browser load either the members or the admin page
                } else { // No password match was made. #4 
                    $errors[] = 'Podane e-mail lub hasło są nieprawidłowe. ';
                }
            }
            }
        if (!empty($errors)) {
            $errorstring = "Wystąpił błąd <br />:<br>"; 
            foreach ($errors as $msg) { // Print each error.
                $errorstring .= " $msg<br>\n";
            }
            $errorstring .= "Prosimy spróbować później.<br>";
            echo "<div class='alert alert-warning' role='alert'>";
            echo "<p class=' text-center' style='color:red'>$errorstring</p></div>";

        }// End of if (!empty($errors)) IF.
        $stmt->free_result();
        $stmt->close();
    }
 catch(Exception $e) {// We finally handle any problems here
    // print "An Exception occurred. Message: " . $e->getMessage(); 
    print "System jest w tej chwili zajęty, prosimy spróbować później.";
   }
catch(Error $e){
    //print "An Error occurred. Message: " . $e->getMessage();
    print "System jest w tej chwili zajęty, prosimy spróbować później."; 
}
} // no else to allow user to enter values
?>