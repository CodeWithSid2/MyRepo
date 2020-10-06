<?php
session_start();

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

$err_msg = "";

if (!empty($_SESSION['no-access'])) {
   $err_msg = '<div class="alert alert-danger" role="alert">Please Login to Continue..</div>';
    unset($_SESSION['no-access']);
}

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: ../main/");
  exit;
}

require_once "../config/sql_config-info.php";


$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $err_msg = '<div class="alert alert-danger" role="alert">Please enter your username.</div>';
    } else{
        $username = trim($_POST["username"]);
    }
    if(empty(trim($_POST["password"]))){
        $err_msg = '<div class="alert alert-danger" role="alert">Please enter your password.</div>';
    } else{
        $password = trim($_POST["password"]);
    }


    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, staff_name, staff_pass FROM staff WHERE staff_name = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $username);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){

                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["staff_name"] = $username;

                           header("location: ../main/");
                           exit;

                        } else{
                            $err_msg = '<div class="alert alert-danger" role="alert">The password you entered was not valid.</div>';
                        }
                    }
                } else{
                    $err_msg = '<div class="alert alert-danger" role="alert">No account found with that username.</div>';
                }
            } else{
                $err_msg = '<div class="alert alert-danger" role="alert">Oops! Something went wrong. Please try again later.</div>';
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);

    if ($username== null){
        $err_msg = '<div class="alert alert-danger" role="alert">Please enter the username.</div>';
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>
   Login
  </title>
  <link href="../assets/img/brand/favicon.png" rel="icon" type="image/png">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link href="../assets/js/plugins/nucleo/css/nucleo.css" rel="stylesheet" />
  <link href="../assets/js/plugins/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="../assets/css/argon-dashboard.css?v=1.1.0" rel="stylesheet" />
</head>

<body class="bg-default">
  <div class="main-content">
    <nav class="navbar navbar-top navbar-horizontal navbar-expand-md navbar-dark">
      <div class="container px-4">
        <a class="navbar-brand" href="/">
          <div id='logo' style="background: url(data:image/png;base64,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Ii8+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz7sVkAWAAAAA3NCSVQICAjb4U/gAAAgAElEQVR4nO19eZxUxbX/OXX3vt09K8sw7CCCCAKiCCqKcd+NPo3G/GKiERNN8oyamBi3RF+ieT6XJCYxT+NTo+K+iwsGRAHZ930fYGaYvbe7VdX5/XGHcaa7Z0DGATTz5cKn6b5V99yqc0+dtS4OPOl26EY3vmywg01AN76e6GasbnQJuhmrG12CbsbqRpdAPdgEdAoIwJAONhWHFiQdEiPyFWYsBOASHa4cbEIOJRBYmlQZHXTe+qoyFiJ4HA8rda4+tooIDzY5hwSIQFXpsXm9N9ZEDE0eXOb6yjIWUCBZWcz/zsRK6GasEASgy7dWFa+utk0ggoM5LF9VxgIABuBx1thoyG7GAgAASaBr0uPsULDI8KvreScAjVGBKQ42IYcQEKnRUQOJB/1R+wpLLAQIBFYltYMq8g8xEKiMDj5bfaUZCwAQQVcPugF0aOHgG4QA8FVnLDhkxrEbWTgU9LxufA3RzVjd6BJ0M1Y3ugRfjo7FEJEhABCRlB1pPS0GS17dqD1zZh8VqS+rece0tdfbflwdERgiIhKRJOrozOa/AAR5z+qAPIUhIgKAaOcaiMgYAOBep2/f0VnGQgREdFzu+hwADE2xTA0B8gYUiIALCQAIoCh5hCXnlDVuCIAMVYUhopR7CVNwnj0qCMgYKApDgI5bE4DgksLAtsJymYSL5llhDFkOE0lJkgjhc+rDzwyR5ekMwn64kEnHF4IUBS1T01Um2plXISkkP2+HRMQFhferKNm/NiY9ISQB2JZm6GoWbykM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) no-repeat;width:200px;height:78px"></div>
        </a>
      </div>
    </nav>

    <div class="header bg-gradient-primary py-7 py-lg-8">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
              <h1 class="text-white">Welcome!</h1>
              <p class="text-lead text-light">Please enter your credentials.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="separator separator-bottom separator-skew zindex-100">
        <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">
          <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
        </svg>
      </div>
    </div>

    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary shadow border-0">

            <div class="card-body px-lg-5 py-lg-5">
              <div class="text-center text-muted mb-4">
                <small>Sign in</small>
              </div>
              <form role="form" method="post">
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                    </div>
                    <input class="form-control" placeholder="Username" type="username" name="username">
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                    </div>
                    <input class="form-control" placeholder="Password" type="password" name="password">
                  </div>
                </div>
             <!--   <div class="custom-control custom-control-alternative custom-checkbox">
                  <input class="custom-control-input" id=" customCheckLogin" type="checkbox">
                  <label class="custom-control-label" for=" customCheckLogin">
                    <span class="text-muted">Remember me</span>
                  </label>
                </div> -->
                <div class="text-center">
                <button class="btn btn-primary my-4" type="submit">Login</button>
                </div>
              </form>
              <?php echo $err_msg;?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="py-5">
      <div class="container">
        <div class="row align-items-center justify-content-xl-between">
          <div class="col-xl-6">
            <div class="copyright text-center text-xl-left text-muted">
              ©  <script>document.write(new Date().getFullYear());</script> <a href="https://siddhesh.me" class="font-weight-bold ml-1" target="_blank">SiddheshNan</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
  <script src="../assets/js/plugins/jquery/dist/jquery.min.js"></script>
  <script src="../assets/js/plugins/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/argon-dashboard.min.js?v=1.1.0"></script>
</body>

</html>
