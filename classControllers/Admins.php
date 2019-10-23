<?php

class Admins
{
  public function adminLogin()
  {

    //global $con;
    global $database;
    $errors = [];
    $email = $database->escape_string( $_POST["email"]);
    $password = $database->escape_string($_POST["password"]);
    if (empty($email)) {
      array_push($errors, "Email is required");
    }

    if (empty($password)) {
      array_push($errors, "Password is required");
    }

    // check if there are errors
    // if (count($errors)) {
    //   echo '<p>Registration Failed with the following Errors</p>';
    //   foreach ($errors as $error) {
    //     echo $error . '<br />';
    //   }
    // }

    if(count($errors) === 0){
      // there are no errors
      $query = 'SELECT * FROM admins WHERE email="' . $email . '" AND password="' . $password . '" ';

      $res = $database->query($query);
      $count = $database->affected_rows();

      if ($count > 0) {
        // user exist
        // get user details and create session

        $row = mysqli_fetch_assoc($res);

        $fullname = $row["firstname"] . ' ' . $row["lastname"];
        $role = $row["role"];

        $_SESSION["fullname"] = $fullname;
        $_SESSION["role"] = $role;

        echo '<script>window.location.href = "dashboard.php"</script>';
      }
    }
  }

  public function allAdmins()
  {
    global $con;
    global $database;
    $display = '';
    $query = 'SELECT * FROM admins';
    $res = $database->query($query);
    $count = $database->affected_rows();
    if ($count > 0) {
      // inters exist
      $sn = 1;
      while ($row = mysqli_fetch_assoc($res)) {
        $display .= '
                    <tr>
                        <td>' . $sn . '</td>
                        <td>' . $row["firstname"] . '</td>
                        <td>' . $row["lastname"] . '</td>
                        <td>' . $row["email"] . '</td>
                        <td>' . $row["role"] . '</td>
                        <td>' . $row["timestamp"] . '</td>
                    </tr>';
        $sn++;
      }
    } else {
      // there are no interns
      $display = "0";
    }

    return $display;
  }

  public function newAdmin($firstname, $lastname, $email, $role,$password)
  {
    global $database;
    //global $con;


    // check for existing email
    $query = "SELECT * FROM admins WHERE email = '" . $email . "' ";
    $res = $database->query($query);
    $count = $database->affected_rows();
    if ($count > 0) {
      // email exists
      $res = $resp = '<div class="alert alert-danger" role="alert">
                                    The Email you entered is already associated with an account
                                </div>';
    } else {
      // email is available, good, proceed to register

      $query = "INSERT INTO admins (firstname, lastname, email, password, role, timestamp) VALUES ('" . $firstname . "', '" . $lastname . "', '" . $email . "', '" . $password . "', '" . $role . "', now())";
      $res = $database->query($query);
      $count = $database->affected_rows();

      if ($count > 0) {
        // success
        $subject = 'WElCOME TO HNG INTERNSHIP';
        $body = "<html>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
        </head>
        <body style='margin: 0; padding: 0;'>
          <table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr>
              <td style='padding: 10px 0 30px 0;'>
                <table align='center' border='0' cellpadding='0' cellspacing='0' width='600' style='border: 1px solid #cccccc; border-collapse: collapse;'>
                  <tr>
                    <td align='center' bgcolor='#fff' style='padding: 40px 0 30px 0; color: #153643; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;'>
                      <img src='https://res.cloudinary.com/phiileo/image/upload/v1571147073/brand-logo_tx0mdt.png' alt='Creating Email Magic' width='300' height='150' style='display: block;' />
                    </td>
                  </tr>
                  <tr>
                    <td bgcolor='#ffffff' style='padding: 40px 30px 40px 30px;'>
                      <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                          <td style='color: #153643; font-family: Arial, sans-serif; font-size: 24px;'>
                            <b>Dear,  $firstname $lastname .</b>
                          </td>
                        </tr>
                        <tr>
                          <td style='padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;'>
                                            You have been registered as an admin on HNG INTERSHIP site, kindly find your registration details below and login. We recommend you change your password after you login.<br> Thank you.
                          </td>
                                        </tr>";
        sendNewAdminMail($email, $password, $subject, $body);

        $resp = '<div class="alert alert-success" role="alert">
                                Admin Created Successfully
                            </div>';
      } else {
        // failed
        $resp = '<div class="alert alert-danger" role="alert">
                                An error occurred. Please try again
                            </div>';
      }
    }

    return $resp;
  }
}

if (isset($_GET["login"])) {
  $admin = new Admins();
  $admin->adminLogin();
}
