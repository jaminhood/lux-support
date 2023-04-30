<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

switch (strtolower(get_query_var('auth_page'))) {
  case 'sign-in':
    $data = ["title" => "User Login"];
    em_page_header($data);
    em_auth_page(strtolower(get_query_var('auth_page')));
    break;
  case 'register':
    $data = ["title" => "User Registration"];
    em_page_header($data);
    em_auth_page(strtolower(get_query_var('auth_page')));
    break;
  case 'password':
    $data = ["title" => "Forgot Password"];
    em_page_header($data);
    em_auth_page(strtolower(get_query_var('auth_page')));
    break;
  default:
    $data = ["title" => "Error"];
    em_page_header($data);
    em_auth_page(strtolower(get_query_var('auth_page')));
    break;
}

function em_auth_page($page)
{ ?>
  <!--=== begin app-wrap === -->
  <div class="app-wrap">
    <!--=== begin pre-loader === -->
    <div class="loader">
      <div class="h-100 d-flex justify-content-center">
        <div class="align-self-center">
          <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="loader">
        </div>
      </div>
    </div>
    <!--=== end pre-loader === -->
    <!--=== start page content === -->
    <div class="app-contant">
      <div class="bg-white">
        <div class="container-fluid p-0">
          <div class="row no-gutters">
            <div class="col-sm-6 col-lg-5 col-xxl-3  align-self-center order-2 order-sm-1">
              <div class="d-flex align-items-center h-100-vh">
                <?php
                switch ($page) {
                  case 'sign-in':
                    em_login_template();
                    break;
                  case 'register':
                    em_register_template();
                    break;
                  case 'password':
                    em_forgot_password_template();
                    break;
                  default:
                    em_error_template();
                    break;
                }
                ?>
              </div>
            </div>
            <div class="col-sm-6 col-xxl-9 col-lg-7 l-bg-green o-hidden order-1 order-sm-2">
              <div class="row align-items-center h-100">
                <div class="col-7 mx-auto ">
                  <img class="img-fluid w-100" src="<?php echo EMURL . "assets/imgs/logo-white.png" ?>" alt="Luxtrade App">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--=== end page content === -->
  </div>
<?php em_page_footer();
}

function em_login_template()
{ ?>
  <div class="login p-50">
    <h1 class="mb-2">Luxtrade</h1>
    <p>Welcome back, please login to your account.</p>
    <form action="" class="mt-3 mt-sm-5 loginTemplate">
      <div class="row">
        <div class="col-12">
          <div class="form-group">
            <label for="username" class="control-label">Username Or Email*</label>
            <input type="text" class="form-control" id="username" name="username" />
            <p class="text-danger text-bold username-error-msg"></p>
            <p class="text-success text-bold username-success-msg"></p>
          </div>
        </div>
        <div class="col-12">
          <div class="form-group">
            <label for="password" class="control-label">Password*</label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" />
              <div class="input-group-prepend" style="cursor: pointer" id="password-visible">
                <span class="input-group-text">
                  <i class="fas fa-eye"></i>
                </span>
              </div>
            </div>
            <p class="text-danger text-bold password-error-msg"></p>
            <p class="text-success text-bold password-success-msg"></p>
          </div>
        </div>
        <div class="col-12">
          <div class="d-block d-sm-flex  align-items-center">
            <div class="form-check">
              <input class="form-check-input rememberMe" value="lsRememberMe" type="checkbox" id="gridCheck">
              <label class="form-check-label" for="gridCheck">
                Remember Me
              </label>
            </div>
            <a href="<?php echo site_url('/lux-auth/password/') ?>" class="ml-auto">Forgot Password ?</a>
          </div>
        </div>
        <div class="col-12 mt-3">
          <button type="submit" class="btn btn-primary text-uppercase" id="signInBtn">Sign In</button>
        </div>
        <div class="col-12  mt-3">
          <p>Don't have an account ?<a href="<?php echo site_url('/lux-auth/register/') ?>"> Sign Up</a></p>
        </div>
      </div>
    </form>
  </div>
<?php
}

function em_register_template()
{ ?>
  <div class="register p-5">
    <h1 class="mb-2">LuxTrade</h1>
    <p>Welcome, Please create your account.</p>
    <form action="" class="mt-2 mt-sm-5" method="POST" id="customer-registration">
      <div class="row">
        <div class="col-12 col-sm-6">
          <div class="form-group">
            <label class="control-label">First Name*</label>
            <input type="text" class="form-control" placeholder="First Name" id="f-name" />
            <p class="text-danger firstname-error-msg"></p>
            <p class="text-success firstname-success-msg"></p>
          </div>
        </div>
        <div class="col-12 col-sm-6">
          <div class="form-group">
            <label class="control-label">Last Name*</label>
            <input type="text" class="form-control" placeholder="Last Name" id="l-name" />
            <p class="text-danger lastname-error-msg"></p>
            <p class="text-success lastname-success-msg"></p>
          </div>
        </div>
        <div class="col-12 col-sm-6">
          <div class="form-group">
            <label class="control-label">Phone Number*</label>
            <input type="text" class="form-control" placeholder="Tel" id="phone-number" />
            <p class="text-danger phone-error-msg"></p>
            <p class="text-success phone-success-msg"></p>
          </div>
        </div>
        <div class="col-12 col-sm-6">
          <div class="form-group">
            <label class="control-label">Email*</label>
            <input type="email" class="form-control" placeholder="Email" id="email" />
            <p class="text-danger email-error-msg"></p>
            <p class="text-success email-success-msg"></p>
          </div>
        </div>
        <div class="col-12 col-sm-6">
          <div class="form-group">
            <label class="control-label">Username*</label>
            <input type="text" class="form-control" placeholder="Username" id="username" />
            <p class="text-danger username-error-msg"></p>
            <p class="text-success username-success-msg"></p>
          </div>
        </div>
        <div class="col-12 col-sm-6">
          <div class="form-group">
            <label class="control-label">Password*</label>
            <input type="password" class="form-control" placeholder="Password" id="password" />
            <p class="text-danger password-error-msg"></p>
            <p class="text-success password-success-msg"></p>
          </div>
        </div>
        <div class="col-12">
          <div class="form-group">
            <label class="control-label">Referral Code (Optional)</label>
            <input type="code" class="form-control" placeholder="code" id="code" />
            <p class="text-danger code-error-msg"></p>
            <p class="text-success code-success-msg"></p>
          </div>
        </div>
        <p class="text-danger register-error-msg"></p>
        <p class="text-success register-success-msg"></p>
        <div class="col-12 mt-3">
          <button type="submit" class="btn btn-primary text-uppercase" id="register">Sign Up</button>
        </div>
        <div class="col-12  mt-3">
          <p>Already have an account ?<a href="<?php echo site_url('/lux-auth/sign-in/') ?>"> Sign In</a></p>
        </div>
      </div>
    </form>
  </div>
<?php
}

function em_forgot_password_template()
{
  if (isset($_POST['submit']) && (email_exists($_POST['email']))) {
    hid_ex_m_password_reset_eMail(email_exists($_POST['email']), $_POST['email']);
  }
  $updated_user_id = -1;
  if (isset($_POST['update-customer-password']) && ($_POST['password'] == $_POST['password2']) && isset($_POST['user_id'])) {
    $userdata = array(
      'ID'              => $_POST['user_id'],
      'user_pass'       => $_POST['password'],
    );
    $updated_user_id = wp_update_user($userdata);
  } ?>
  <div class="register p-5">
    <h1 class="mb-2">LuxTrade</h1>
    <?php if (!(isset($_GET['customer']) && isset($_GET['token']))) { ?>
      <?php if (isset($_POST['submit']) && (email_exists($_POST['email']))) { ?>
        <p class="text-bold">Check Your Inbox</p>
        <p class="py-2">Back to <a href="<?php echo site_url('/lux-auth/sign-in/') ?>">Sign In</a> page</p>
      <?php } else { ?>
        <p>Please enter your registered E-Mail address below to start the password recovery process. If the E-Mail entered is registered with us, you will get a password reset Mail</p>
        <form action="" class="mt-2 mt-sm-5" method="POST" id="hid_ex_m_customer_password_reset">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label class="control-label">Email*</label>
                <input class="form-control" placeholder="Email" type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2, 4}$" />
                <?php if (isset($_POST['submit']) && !(email_exists($_POST['email']))) { ?>
                  <small style="color:red;">The E-Mail address you entered is not registered</small>
                <?php } ?>
              </div>
            </div>
            <div class="col-12 mt-3">
              <button type="submit" id="btn-login-submit" name="submit" class="btn btn-primary text-uppercase">Reset Password</button>
            </div>
            <div class="col-12  mt-3">
              <p>Back to <a href="<?php echo site_url('/lux-auth/sign-in/') ?>">Sign In</a> page</p>
            </div>
          </div>
        </form>
      <?php } ?>
    <?php } ?>

    <?php if (isset($_GET['customer']) && isset($_GET['token']) && ($updated_user_id == -1)) { ?>
      <?php
      $token = $_GET['token'];
      $customer = $_GET['customer'];
      $the_user_id = -1;
      $the_user = get_user_by('login', $customer);
      $sign_in_url = site_url('/authentication/sign-in/');
      if (!($the_user)) {
        echo "<script>location.replace('$sign_in_url');</script>";
      } else {
        $the_user_id = $the_user->ID;
      }

      if (hid_ex_m_check_token($the_user_id, $token) == 0) {
        echo "<script>location.replace('$sign_in_url');</script>";
      } ?>

      <p class="text-bold">Your E-Mail have been confirmed successfully. Now enter your desired password and submit the form below</p>
      <form action="" class="mt-2 mt-sm-5" method="POST" id="hid_ex_m_customer_password_reset">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label class="control-label">Password</label>
              <input type="hidden" name="user_id" value="<?php echo $the_user_id ?>">
              <input class="form-control" type="password" name="password" id="password" />
            </div>
            <div class="form-group">
              <label class="control-label">Confirm Password</label>
              <input class="form-control" type="password" name="password2" id="password2" />
              <?php if (isset($_POST['update-customer-password']) && ($_POST['password'] != $_POST['password2'])) { ?>
                <small style="color:red;">Your Passwords Don't match</small>
              <?php } ?>
            </div>
          </div>
          <div class="col-12 mt-3">
            <button id="btn-login-submit" type="submit" name="update-customer-password">Reset Password</button>
          </div>
          <div class="col-12  mt-3">
            <p>Back to <a href="<?php echo site_url('/lux-auth/sign-in/') ?>">Sign In</a> page</p>
          </div>
        </div>
      </form>
    <?php } ?>

    <?php if ($updated_user_id != -1) { ?>
      <p class="text-bold">Password Updated Successfully.</p>
      <?php
      $sign_in_url = site_url('/authentication/sign-in/');
      $command = "setInterval(() => location.replace('$sign_in_url'), 2000)";
      echo "<script>$command</script>";
      ?>
    <?php } ?>
  </div>
<?php
}

function em_error_template()
{
  require_once(EMPATH . "templates/user/em-error.php");
}
