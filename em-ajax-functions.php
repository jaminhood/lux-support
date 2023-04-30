<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

function em_get_giftcard_assets()
{
  if (isset($_REQUEST)) {
    try {
      wp_send_json_success(hid_ex_m_get_all_giftcards());
    } catch (\Throwable $th) {
      write_log($th);
    }
  }
  die();
}

add_action('wp_ajax_em_get_giftcard_assets', 'em_get_giftcard_assets');
add_action('wp_ajax_nopriv_em_get_giftcard_assets', 'em_get_giftcard_assets');

function em_user_email_pass_check()
{
  if (isset($_REQUEST)) {
    $username = $_REQUEST['username'];
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $data = 0;

    $user = get_user_by('login', $username);

    if (!$user) $user = get_user_by('email', $email);

    if ($user && wp_check_password($password, $user->data->user_pass, $user->ID)) {
      $data = 1;
    }
    wp_send_json_success($data);
  }
  die();
}

add_action('wp_ajax_em_user_email_pass_check', 'em_user_email_pass_check');
add_action('wp_ajax_nopriv_em_user_email_pass_check', 'em_user_email_pass_check');

function em_complete_user_registration()
{
  $lux_dbh = new LuxDBH;

  if (isset($_REQUEST)) {

    $code = $_REQUEST['data']['referral_code'];

    $lux_dbh->lux_create_new_customer(
      $_REQUEST['data']['first_name'],
      $_REQUEST['data']['last_name'],
      $_REQUEST['data']['email'],
      $_REQUEST['data']['phone_number'],
      $_REQUEST['data']['password'],
      $_REQUEST['data']['username'],
      $code
    );

    // write_log("User Created");

    wp_send_json_success();
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_em_complete_user_registration', 'em_complete_user_registration');
add_action('wp_ajax_nopriv_em_complete_user_registration', 'em_complete_user_registration');

function lux_submit_buy_order()
{
  $lux_dbh = new LuxDBH;

  if (isset($_REQUEST)) {
    $output = 0;
    try {
      $input_data = array(
        'customer_id' => get_current_user_id(),
        'asset_type'    => $_REQUEST['chosen_asset_type'],
        'asset_id'      => $_REQUEST['chosen_asset_id'],
        'quantity' => (float)($_REQUEST['entered_quantity']),
        'fee' => $_REQUEST['amount_to_recieve'],
        'proof_of_payment'  => 0,
        'sending_instructions' => $_REQUEST['sending'],
        'order_status'  => 1
      );

      $lux_dbh->lux_create_new_buy_order($input_data);

      $input_data = array(
        'customer_id' => get_current_user_id(),
        'transaction_type' => 2,
        'amount' => $_REQUEST['amount_to_recieve'],
        'mode'  => $_REQUEST['chosen_asset_type'],
        'details'   => 'Buy Order',
        'proof_of_payment' => 0,
        'sending_instructions'  => $_REQUEST['sending'],
        'transaction_status'    => 1
      );

      // write_log($input_data);

      $lux_dbh->lux_create_new_wallet_transaction($input_data);

      $output = 1;
    } catch (\Throwable $th) {
      $output = 0;
      write_log($th);
    }

    wp_send_json_success($output);
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_submit_buy_order', 'lux_submit_buy_order');
add_action('wp_ajax_nopriv_lux_submit_buy_order', 'lux_submit_buy_order');

function lux_debit_wallet()
{
  $lux_dbh = new LuxDBH;

  if (isset($_REQUEST)) {
    $data = 0;

    // Check if user's account balance is sufficient
    $current_balance = hid_ex_m_get_account_balance(get_current_user_id());
    $withdrawable_amount = $current_balance - 100;

    // write_log("Current balance = $current_balance");
    // write_log("Requested amount =".$_REQUEST['amount_']);

    if ($_REQUEST['amount_'] < $withdrawable_amount) {
      try {
        $input_data = array(
          'customer_id' => get_current_user_id(),
          'transaction_type' => 2,
          'amount' => intval($_REQUEST['amount_']) + 5,
          'mode'  => $_REQUEST['mode_w'],
          'details'   => $_REQUEST['details'],
          'proof_of_payment' => 0,
          'sending_instructions'  => $_REQUEST['info'],
          'transaction_status'    => 1
        );

        // write_log($input_data);

        $lux_dbh->lux_create_new_wallet_transaction($input_data);
        $data = 1;
      } catch (\Throwable $th) {
        $data = -1;
      }
    }

    wp_send_json_success($data);
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_debit_wallet', 'lux_debit_wallet');
add_action('wp_ajax_nopriv_lux_debit_wallet', 'lux_debit_wallet');

function lux_set_notifications_function()
{
  if (isset($_REQUEST)) {
    $data = 0;
    try {
      $lux_dbh = new LuxDBH;

      $notify = [
        'customer_id' => get_current_user_id(),
        'title' => $_REQUEST['title'],
        'msg' => $_REQUEST['msg']
      ];

      $lux_dbh->lux_set_notification($notify);

      $data = [
        'title' => $_REQUEST['title'],
        'body' => $_REQUEST['msg']
      ];
      LuxUtils::lux_push_notification(get_current_user_id(), $data);

      $data = 1;
    } catch (\Throwable $th) {
      $data = -1;
    }

    wp_send_json_success($data);
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_set_notifications_function', 'lux_set_notifications_function');
add_action('wp_ajax_nopriv_lux_set_notifications_function', 'lux_set_notifications_function');

// The function that handles ajax request from the frontend
function lux_get_e_assets_with_barcode()
{
  $lux_dbh = new LuxDBH;
  // _REQUEST is the PHP superglobal bringing in all the data sent via ajax
  if (isset($_REQUEST)) {
    try {
      $result = hid_ex_m_get_all_e_currency_assets();
      if ($result != 0) {
        foreach ($result as $asset) {
          $barcode = $lux_dbh->lux_get_currency_barcode(1, $asset->id);
          $asset->barcode = wp_get_attachment_url($barcode->barcode);
        }
      }
      wp_send_json_success($result);
    } catch (\Throwable $th) {
      write_log($th);
    }
    // wp_send_json_success( $data = $output );
  }
  // Killing the Ajax function
  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_get_e_assets_with_barcode', 'lux_get_e_assets_with_barcode');
add_action('wp_ajax_nopriv_lux_get_e_assets_with_barcode', 'lux_get_e_assets_with_barcode');

function lux_get_crypto_assets_with_barcode()
{
  $lux_dbh = new LuxDBH;
  // _REQUEST is the PHP superglobal bringing in all the data sent via ajax
  if (isset($_REQUEST)) {
    //write_log("Got Here Now");
    try {
      $result = hid_ex_m_get_all_crypto_currency_assets();
      if ($result != 0) {
        foreach ($result as $asset) {
          $barcode = $lux_dbh->lux_get_currency_barcode(2, $asset->id);
          $asset->barcode = wp_get_attachment_url($barcode->barcode);
        }
      }
      wp_send_json_success($result);
    } catch (\Throwable $th) {
      write_log($th);
    }
    //return hid_ex_m_get_all_crypto_currency_assets_with_local_bank();
  }
  // Killing the Ajax function
  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_get_crypto_assets_with_barcode', 'lux_get_crypto_assets_with_barcode');
add_action('wp_ajax_nopriv_lux_get_crypto_assets_with_barcode', 'lux_get_crypto_assets_with_barcode');

function lux_credit_wallet()
{
  $lux_dbh = new LuxDBH;

  if (isset($_REQUEST)) {

    check_ajax_referer('file_upload', 'security');

    $data = 0;

    try {

      // check_ajax_referer('file_upload', 'security');

      $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

      if (in_array($_FILES['file']['type'], $arr_img_ext)) {

        $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

        // write_log($upload);

        $type = '';
        if (!empty($upload['type'])) {

          $type = $upload['type'];
        } else {
          $mime = wp_check_filetype($upload['file']);
          if ($mime) {
            $type = $mime['type'];
          }
        }

        $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);

        $image_id = wp_insert_attachment($attachment, $upload['file']);

        wp_update_attachment_metadata($data, wp_generate_attachment_metadata($data, $upload['file']));
      }

      $input_data = array(
        'customer_id' => get_current_user_id(),
        'transaction_type' => 1,
        'amount' => $_REQUEST['amount'],
        'mode'  => $_REQUEST['mode'],
        'details'   => $_REQUEST['details'],
        'proof_of_payment' => $image_id,
        'sending_instructions'  => 'Not required',
        'transaction_status'    => 1
      );

      // write_log($input_data);

      $lux_dbh->lux_create_new_wallet_transaction($input_data);

      $data = 1;
    } catch (\Throwable $th) {
      $data = -1;
    }

    wp_send_json_success($data);
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_credit_wallet', 'lux_credit_wallet');
add_action('wp_ajax_nopriv_lux_credit_wallet', 'lux_credit_wallet');

function lux_submit_sell_order()
{
  $lux_dbh = new LuxDBH;


  if (isset($_REQUEST)) {
    check_ajax_referer('file_upload', 'security');
    $output = 0;
    $data = 0;

    try {

      $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

      if (in_array($_FILES['file']['type'], $arr_img_ext)) {

        $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

        $type = '';
        if (!empty($upload['type'])) {

          $type = $upload['type'];
        } else {
          $mime = wp_check_filetype($upload['file']);
          if ($mime) {
            $type = $mime['type'];
          }
        }

        $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);

        $data = wp_insert_attachment($attachment, $upload['file']);

        wp_update_attachment_metadata($data, wp_generate_attachment_metadata($data, $upload['file']));
      }


      $input_data = array(

        'customer_id' => get_current_user_id(),
        'asset_type'    => $_REQUEST['chosen_asset_type'],
        'asset_id'      => $_REQUEST['chosen_asset_id'],
        'quantity_sold' => (float)($_REQUEST['entered_quantity']),
        'amount_to_recieve' => $_REQUEST['amount_to_recieve'],
        'proof_of_payment'  => $data,
        'order_status'  => 1

      );

      $lux_dbh->lux_create_new_sell_order($input_data);

      // write_log($input_data);

      $output = 1;
    } catch (\Throwable $th) {
      $output = 0;
      write_log($th);
    }

    wp_send_json_success($output);
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_lux_submit_sell_order', 'lux_submit_sell_order');
add_action('wp_ajax_nopriv_lux_submit_sell_order', 'lux_submit_sell_order');
