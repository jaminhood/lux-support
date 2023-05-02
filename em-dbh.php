<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Requesting upgrade.php file from wordpress
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if (!class_exists('LuxDBH')) {
  class LuxDBH
  {
    public function lux_create_top_news_table()
    {
      # === Global variables required for table
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_top_news';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        title TEXT NOT NULL,
        newsPicture INT NOT NULL,
        dateAdded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    public function lux_create_referral_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_referrals';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        customer_id INT NOT NULL,
        referral_code TINYTEXT NOT NULL,
        referral TINYTEXT NOT NULL,
        total_referral INT NOT NULL,
        paid INT NOT NULL DEFAULT 0,
        PRIMARY KEY (id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    public function lux_create_transaction_pin_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_transaction_pin';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        customer_id INT NOT NULL,
        transaction_pin TINYTEXT NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    public function lux_create_customers_notifications_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_notifications';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        customer_id INT NOT NULL,
        title TINYTEXT NOT NULL,
        msg TINYTEXT NOT NULL,
        seen INT NOT NULL DEFAULT 0,
        PRIMARY KEY (id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    public function lux_create_barcodes_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_barcodes';

      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        mode INT NOT NULL,
        asset_id INT NOT NULL,
        barcode INT NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('jal_db_version', $jal_db_version);
    }

    public function lux_create_device_token_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_device_token';

      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        customer_id INT NOT NULL,
        device_token TINYTEXT NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('jal_db_version', $jal_db_version);
    }

    public function lux_create_top_assets_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_top_assets';

      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        asset_type INT NOT NULL,
        asset_id INT NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('jal_db_version', $jal_db_version);
    }

    public function lux_populate_referrals()
    {
      $customers = $this->lux_get_customer_details();

      foreach ($customers as $customer) {
        $id = $customer->ID;
        $customer_data = $customer->data;
        $customer_id = $id;
        $referral_code = substr($customer_data->user_login, 0, 3) . "-" . mt_rand(1000, 9999);
        $total_referral = 0;

        $customer_info = [
          'id' => $id,
          'customer_id' => $customer_id,
          'referral_code' => $referral_code,
          'total_referral' => $total_referral,
        ];

        for ($i = 0; $i < count($customers); $i++) {
          $this->lux_set_referrals($customer_info);
        }
      }
    }

    public function lux_get_customer_details()
    {
      $args = ['role' => 'customer'];

      $wp_user_query = new WP_User_Query($args);

      $customers = $wp_user_query->get_results();

      return $customers;
    }

    public function lux_get_single_customer($id)
    {
      $customer_name = null;
      $customers = $this->lux_get_customer_details();

      foreach ($customers as $customer) {
        $customer_data = $customer->data;
        $display_name = $customer_data->display_name;
        $user_nice_name = $customer_data->user_nicename;

        if ($id == $customer->ID) {
          $customer_name = "$display_name $user_nice_name";
          return $customer_name;
        }
      }
      return $customer_name;
    }

    public function lux_get_news()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_news';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_top_assets()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_assets';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_top_asset($asset_type, $asset_id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_assets';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE asset_type=$asset_type AND asset_id=$asset_id");

      if ($result) {
        return $result[0];
      }

      return false;
    }

    public function lux_get_device_token($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_device_token';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id=$id");

      return $result;
    }

    public function lux_get_all_device_token()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_device_token';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_notifications($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_notifications';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id=$id");

      return $result;
    }

    public function lux_get_single_referral($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_referrals';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$id'");

      return $result[0];
    }

    public function lux_get_transaction_pin($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_transaction_pin';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$id'");

      return $result[0];
    }

    public function lux_get_referral_code($code)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_referrals';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE referral_code='$code'");

      if (count($result) > 0) {
        return $result[0];
      }

      return 0;
    }

    public function lux_get_referral($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_referrals';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$id'");

      return $result[0];
    }

    public function lux_get_single_news($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_news';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$id'");

      return $result[0];
    }

    public function lux_get_all_referrals()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_referrals';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_barcodes()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_barcodes';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_currency_barcode($curr, $id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_barcodes';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE mode=$curr AND asset_id=$id");

      return $result[0];
    }

    public function lux_get_barcode($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_barcodes';

      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id=$id");

      return $result[0];
    }

    public function lux_get_top_e_currency()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'hid_ex_m_e_currency_assets';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_top_crypto_currency()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'hid_ex_m_crypto_currency_assets';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_top_giftcards()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'hid_ex_m_giftcards';

      $result = $wpdb->get_results("SELECT * FROM $table_name");

      return $result;
    }

    public function lux_get_assets()
    {
      $assets = [];

      foreach ($this->lux_get_top_e_currency() as $curr) {
        $asset = array(
          'asset_type' => 0,
          'asset_id' => $curr->id,
          'name' => $curr->name,
          'short_name' => $curr->short_name,
          'icon' => wp_get_attachment_url($curr->icon),
          'buying_price' => $curr->buying_price,
          'selling_price' => $curr->selling_price,
        );
        $assets[] = $asset;
      }

      foreach ($this->lux_get_top_crypto_currency() as $curr) {
        $asset = array(
          'asset_type' => 1,
          'asset_id' => $curr->id,
          'name' => $curr->name,
          'short_name' => $curr->short_name,
          'icon' => wp_get_attachment_url($curr->icon),
          'buying_price' => $curr->buying_price,
          'selling_price' => $curr->selling_price,
        );
        $assets[] = $asset;
      }

      foreach ($this->lux_get_top_giftcards() as $curr) {
        $asset = array(
          'asset_type' => 0,
          'asset_id' => $curr->id,
          'name' => $curr->name,
          'short_name' => $curr->short_name,
          'icon' => wp_get_attachment_url($curr->icon),
          'buying_price' => $curr->buying_price,
          'selling_price' => $curr->selling_price,
        );
        $assets[] = $asset;
      }

      return $assets;
    }

    public function lux_set_top_asset($asset)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_assets';

      $wpdb->insert($table_name, $asset);
    }

    public function lux_set_news($news)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_news';

      $wpdb->insert($table_name, $news);
    }

    public function lux_set_notification($details)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_notifications';

      $wpdb->insert($table_name, $details);
    }

    public function lux_set_barcode($details)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_barcodes';

      $wpdb->insert($table_name, $details);
    }

    public function lux_set_referrals($details)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_referrals';

      $wpdb->insert($table_name, $details);
    }

    public function lux_set_transaction_pin($details)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_transaction_pin';

      $wpdb->insert($table_name, $details);

      return true;
    }

    public function lux_set_device_token(string $token)
    {
      global $wpdb;

      $id = get_current_user_id();
      $token_result = $this->lux_get_device_token($id);
      $table_name = $wpdb->prefix . 'em_device_token';

      if (empty($token_result)) {
        $wpdb->insert($table_name, ['customer_id' => $id, 'device_token' => $token]);
      } else {
        $wpdb->update($table_name, ['device_token' => $token], ['customer_id' => $id]);
      }
      return true;
    }

    public function lux_update_barcode($details,  $where)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_barcodes';

      $wpdb->update($table_name, $details, $where);
    }

    public function lux_update_news($details,  $where)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_news';

      $wpdb->update($table_name, $details, $where);
    }

    public static function lux_update_notifications($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_notifications';

      $wpdb->update($table_name, ['seen' => 1], ['customer_id' => $id]);
    }

    public function lux_update_referral($details,  $where)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_referrals';

      $wpdb->update($table_name, $details, $where);
    }

    public function lux_update_transaction_pin($details,  $customer_id, $prev)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_transaction_pin';
      $user = $this->lux_get_transaction_pin($customer_id);
      if ($user->transaction_pin == $prev) {
        $wpdb->update($table_name, $details, ['customer_id' => $customer_id]);
        return true;
      }
      return false;
    }

    public function lux_delete_top_asset($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_assets';

      $wpdb->query("DELETE FROM $table_name WHERE id='$id'");
    }

    public function lux_delete_news($id)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'em_top_news';

      $wpdb->query("DELETE FROM $table_name WHERE id='$id'");
    }

    public function lux_get_dashboard_data($user_id)
    {
      global $wpdb;

      $total_sold = 0;
      $sell_ecurrency = 0;
      $sell_crypto = 0;
      $sell_within_month = 0;
      $total_bought = 0;
      $buy_ecurrency = 0;
      $buy_crypto = 0;
      $buy_within_month = 0;
      $total_transactions = 0;
      $pending_payments = 0;
      $current_bal = 0;

      $table_name = $wpdb->prefix . 'hid_ex_m_buy_orders';
      $result_buy = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id'");
      $result_buy_within_month = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' AND time_stamp > NOW() - interval 1 month");
      $result_buy_crypto = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' AND asset_type=2");
      $result_buy_ecurrency = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' AND asset_type=1");

      if (!empty($result_buy)) {
        foreach ($result_buy as $order) {
          $total_bought += $order->fee;
          if ($order->order_status == 1) {
            $pending_payments += $order->fee;
          }
        }
      }

      if (!empty($result_buy_within_month)) {
        foreach ($result_buy_within_month as $order) {
          $buy_within_month += $order->fee;
        }
      }

      if (!empty($result_buy_ecurrency)) {
        foreach ($result_buy_ecurrency as $order) {
          $buy_ecurrency += $order->fee;
        }
      }

      if (!empty($result_buy_crypto)) {
        foreach ($result_buy_crypto as $order) {
          $buy_crypto += $order->fee;
        }
      }

      if ($total_bought > 0) {
        $ecurrency_buy_percent = round(($buy_ecurrency / $total_bought) * 100, 2);
        $crypto_buy_percent = round(($buy_crypto / $total_bought) * 100, 2);
        $buy_percent_within_month = round(($buy_within_month / $total_bought) * 100, 2);
      } else {
        $ecurrency_buy_percent = '0.00';
        $crypto_buy_percent = '0.00';
        $buy_percent_within_month = '0.00';
      }

      $table_name = $wpdb->prefix . 'hid_ex_m_sell_orders';
      $result_sell = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id'");
      $result_sell = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id'");
      $result_sell_within_month = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' AND time_stamp > NOW() - interval 1 month");
      $result_sell_crypto = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' AND asset_type=2");
      $result_sell_ecurrency = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' AND asset_type=1");

      if (!empty($result_sell)) :
        foreach ($result_sell as $order) :
          $total_sold += $order->amount_to_recieve;
          if ($order->order_status == 1) :
            $pending_payments += $order->amount_to_recieve;
          endif;
        endforeach;
      endif;

      if (!empty($result_sell_within_month)) :
        foreach ($result_sell_within_month as $order) :
          $sell_within_month += $order->amount_to_recieve;
        endforeach;
      endif;

      if (!empty($result_sell_ecurrency)) :
        foreach ($result_sell_ecurrency as $order) :
          $sell_ecurrency += $order->amount_to_recieve;
        endforeach;
      endif;

      if (!empty($result_sell_crypto)) :
        foreach ($result_sell_crypto as $order) :
          $sell_crypto += $order->amount_to_recieve;
        endforeach;
      endif;

      if ($total_sold > 0) {
        $ecurrency_sell_percent = round(($sell_ecurrency / $total_sold) * 100, 2);
        $crypto_sell_percent = round(($sell_crypto / $total_sold) * 100, 2);
        $sell_percent_within_month = round(($buy_within_month / $total_sold) * 100, 2);
      } else {
        $ecurrency_sell_percent = '0.00';
        $crypto_sell_percent = '0.00';
        $sell_percent_within_month = '0.00';
      }

      $total_transactions = count($result_sell) + count($result_buy);
      $all_orders = array_merge($result_buy, $result_sell);

      if (count($all_orders) > 1) usort($all_orders, 'date_compare');

      if (count($all_orders) > 5) $all_orders = array_slice($all_orders, -5, 5);

      $announcements = hid_ex_m_get_all_announcements();
      $notifications = $this->lux_get_notifications($user_id);

      if (count($announcements) > 3) $announcements = array_slice($announcements, 0, 3);

      if (!metadata_exists('user', $user_id, 'account_balance')) {
        add_user_meta($user_id, 'account_balance', 0);
      }

      if (!metadata_exists('user', $user_id, 'can_withdraw')) {
        add_user_meta($user_id, 'can_withdraw', 1);
      }

      $current_bal = round(get_user_meta($user_id, 'account_balance')[0], 2);

      $data = [
        'totalBought'           => $total_bought,
        'buyPercentWithinMonth' => $buy_percent_within_month,
        'buyEcurrency'          => $buy_ecurrency,
        'ecurrencyBuyPercent'   => $ecurrency_buy_percent,
        'buyCrypto'             => $buy_crypto,
        'cryptoBuyPercent'      => $crypto_buy_percent,
        'totalSold'             => $total_sold,
        'sellPercentWithinMonth' => $sell_percent_within_month,
        'sellEcurrency'          => $sell_ecurrency,
        'ecurrencySellPercent'   => $ecurrency_sell_percent,
        'sellCrypto'             => $sell_crypto,
        'cryptoSellPercent'      => $crypto_sell_percent,
        'totalTransactions'     => $total_transactions,
        'pendingPayments'       => $pending_payments,
        'announcements'         => $announcements,
        'notifications'         => $notifications,
        'orders'                => $all_orders,
        'walletBalance'         => $current_bal
      ];

      return $data;
    }

    public function lux_get_wallet_data($user_id)
    {
      global $wpdb;

      $total_transactions = 0;
      $pending_payments = 0;

      $table_name = $wpdb->prefix . 'hid_ex_m_wallet_transactions';

      $all_transactions = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id='$user_id' ORDER BY time_stamp DESC");

      if (!empty($all_transactions)) {
        $total_transactions = count($all_transactions);

        foreach ($all_transactions as $transaction) {
          if ($transaction->transaction_status == 1) {
            $pending_payments += $transaction->amount;
          }
        }
      }

      $result = array(
        'accountBalance'   => hid_ex_m_get_account_balance($user_id),
        'canWithdraw'   => hid_ex_m_get_withdrawal_status($user_id),
        'totalTransactions'    => $total_transactions,
        'pendingPayments'  => $pending_payments,
        'allTransactions'  => $all_transactions
      );

      return $result;
    }

    public function lux_create_admin_funding($data)
    {
      try {
        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $price = $data["price"];

        $input_data = array(
          'customer_id' => $data['customer_id'],
          'transaction_type' => 1,
          'amount' => $price,
          'mode'  => 0,
          'details'   => 'Funding || Admin Funding',
          'proof_of_payment' => 0,
          'sending_instructions'  => 'Not required',
          'transaction_status'    => 1
        );

        $this->lux_create_new_wallet_transaction($input_data);

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because the admin of lux trade just funded your account.<br /><br />Below are some of the funding details<br /><br />Price : $price<br /><br />Kindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Admin Funding Successful',
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because you just funded a customer by the name $name and it's pending review.<br /><br />Below are some of the funding details<br /><br />Price : $price<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the order.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! Admin Funding Successful",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_create_admin_withdrawal($data)
    {
      $current_balance = hid_ex_m_get_account_balance($data['customer_id']);
      $withdrawable_amount = $current_balance - 100;

      if ($data['price'] < $withdrawable_amount) {
        try {
          $customer = get_userdata($data["customer_id"]);
          $email = $customer->user_email;
          $name = $customer->display_name;
          $price = $data["price"];

          $input_data = array(
            'customer_id' => $data['customer_id'],
            'transaction_type' => 2,
            'amount' => $price,
            'mode'  => 0,
            'details'   => 'Withdrawal || Admin Charge',
            'proof_of_payment' => 0,
            'sending_instructions'  => 'Not required',
            'transaction_status'    => 1
          );

          $this->lux_create_new_wallet_transaction($input_data);

          $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because the admin of lux trade just made a withdrawal from your account.<br /><br />Below are some of the withdrawal details<br /><br />Price : $price<br /><br />Kindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

          wp_mail(
            $email,
            'LuxTrade Alert !!! Admin Withdrawal Successful',
            $message_body,
            'From: ' . $email . "\r\n" .
              'Reply-To: ' . get_option('business_email') . "\r\n" .
              'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
              'X-Mailer: PHP/' . phpversion()
          );

          $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

          $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because you just made a withdrawal from a customer by the name $name and it's pending review.<br /><br />Below are some of the withdrawal details<br /><br />Price : $price<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the order.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

          wp_mail(
            get_option('business_email'),
            "LuxTrade Alert !!! Admin Withdrawal Successful",
            $message_body,
            'From: ' . $email . "\r\n" .
              'Reply-To: ' . get_option('business_email') . "\r\n" .
              'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
              'X-Mailer: PHP/' . phpversion()
          );
        } catch (\Throwable $th) {

          write_log($th);
        }
      }
    }

    public function lux_create_new_buy_order($data)
    {

      global $wpdb;
      $table_name = $wpdb->prefix . 'hid_ex_m_buy_orders';

      $wpdb->insert(
        $table_name,
        $data
      );

      try {

        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $asset_type = hid_ex_m_get_asset_type($data["asset_type"]);
        $asset_name = hid_ex_m_get_asset_name($data["asset_type"], $data["asset_id"]);
        $qty = $data["quantity"];
        $fee = $data["fee"];

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because your buy order was placed successfully and is pending review.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity : $qty<br />Fee : $fee<br /><br />Kindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Buy order created Successfully',
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because a customer by the name $name just created a new buy order and is pending review.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity : $qty<br />Fee : # $fee<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the order.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! You have a new Buy Order",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_get_all_sell_orders()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'hid_ex_m_sell_orders';

      $result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time_stamp DESC");

      return $result;
    }

    public function lux_get_all_buy_orders()
    {
      global $wpdb;

      $table_name = $wpdb->prefix . 'hid_ex_m_buy_orders';

      $result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time_stamp DESC");

      return $result;
    }

    public function lux_update_buy_order($data, $where)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'hid_ex_m_buy_orders';

      $wpdb->update($table_name, $data, $where);

      try {
        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $asset_type = hid_ex_m_get_asset_type($data["asset_type"]);
        $asset_name = hid_ex_m_get_asset_name($data["asset_type"], $data["asset_id"]);
        $qty = $data["quantity"];
        $fee = $data["fee"];

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because a buy order of yours got updated by Luxtrade admin.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity : $qty<br />Fee : # $fee<br /><br />Kindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Buy Order Updated by Admin',
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because you just updated a buy order of a customer by the name $name just created a new buy order and is pending review.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity : $qty<br />Fee : # $fee<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the order.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! You Just Updated a Buy Order",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_create_new_sell_order($data)
    {

      global $wpdb;
      $table_name = $wpdb->prefix . 'hid_ex_m_sell_orders';

      $wpdb->insert(
        $table_name,
        $data
      );

      try {

        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $asset_type = hid_ex_m_get_asset_type($data["asset_type"]);
        $asset_name = hid_ex_m_get_asset_name($data["asset_type"], $data["asset_id"]);
        $qty = $data["quantity_sold"];
        $fee = $data["amount_to_recieve"];

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because your sell order was placed successfully and is pending review.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity Sold : $qty<br />Amount to Receive : $fee<br /><br />Kindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Sell order created Successfully',
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

        $message_body = "Greetings,\n\nYou're recieving this eMail Notification because a customer by the name $name just created a sell order and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity Sold : $qty\nAmount to Recieve : # $fee\n\nKindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.\n\nCheers!!!\nLuxtrade - Admin";

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because a customer by the name $name just created a new sell order and is pending review.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity Sold : $qty<br />Amount to Receive : # $fee<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the order.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! You have a new Sell Order",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_update_sell_order($data, $where)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'hid_ex_m_sell_orders';

      $wpdb->update($table_name, $data, $where);

      try {
        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $asset_type = hid_ex_m_get_asset_type($data["asset_type"]);
        $asset_name = hid_ex_m_get_asset_name($data["asset_type"], $data["asset_id"]);
        $qty = $data["quantity_sold"];
        $fee = $data["amount_to_recieve"];

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because a sell order of yours got updated by Luxtrade admin.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity : $qty<br />Fee : # $fee<br /><br />Kindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Sell Order Updated by Admin',
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because you just updated a sell order of a customer by the name $name just created a new buy order and is pending review.<br /><br />Below are some of the order details<br /><br />Asset Type : $asset_type<br />Asset : $asset_name<br />Quantity : $qty<br />Fee : # $fee<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the order.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! You Just Updated a Sell Order",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_create_new_wallet_transaction($data)
    {

      global $wpdb;
      $table_name = $wpdb->prefix . 'hid_ex_m_wallet_transactions';

      $wpdb->insert(
        $table_name,
        $data
      );


      try {

        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $mode = hid_ex_m_get_wallet_transaction_mode($data["mode"], $data["transaction_type"]);
        $amount = intval($data["amount"]) - 50;

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because your $mode of # $amount was successful and is pending review.<br /><br />Kindly return to Luxtrade and sign into your dashboard; Visit the wallets tab to know more.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          "LuxTrade Alert !!! $mode created Successfully",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because a customer by the name $name just made a $mode of # $amount and is pending review.<br /><br />Kindly return to Luxtrade and sign into WP Admin to view and update the wallet transaction.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! You have a new $mode to review",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_create_new_customer($first_name, $last_name, $email, $phone, $password, $username, $code)
    {
      $userdata = array(
        'user_pass'             => $password,
        'user_login'            => $username,
        'user_nicename'         => $last_name,
        'user_email'            => $email,
        'display_name'          => $first_name,
        'last_name'             => $last_name,
        'role'                  => 'customer'
      );

      $user_id = wp_insert_user($userdata);
      add_user_meta($user_id, 'phone_number', $phone);
      add_user_meta($user_id, 'account_balance', 0);
      add_user_meta($user_id, 'can_withdraw', 1);

      $referral_info = [
        'customer_id' => $user_id,
        'referral_code' =>  substr($username, 0, 3) . "-" . mt_rand(1000, 9999),
        'referral' => $code,
        'total_referral' => 0,
      ];

      $this->lux_set_referrals($referral_info);

      $display_name = $first_name;
      $user_nice_name = $last_name;
      $name = "$display_name $user_nice_name";

      $customer_info = [
        'id' => $user_id,
        'displayName' => $name
      ];

      $this->lux_set_bank_details($customer_info);

      try {

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $first_name!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because your registeration at Luxtrade was successful.<br /><br />Below are some of your data provided upon registeration<br /><br />First Name : $first_name<br />Last Name : $last_name<br />Email : $email<br />Phone : $phone<br />Username : $username<br />Password : ----------------<br /><br />Kindly return to Luxtrade to sign into your dashboard to start trading Crypto and other digital assets.<br /><br />Cheers!!!<br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Customer Registeration Successful',
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>You're recieving this eMail Notification because a customer by the name $name just completed their regitration.<br /><br />Kindly return to Luxtrade and sign into WP Admin to view customers.<br /><br />Cheers!!!<br /><br />Luxtrade - Admin</p></body></html>";

        wp_mail(
          get_option('business_email'),
          "LuxTrade Alert !!! Customer Registeration Successful",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );

        wp_mail(
          'princeobj5@gmail.com',
          "LuxTrade Alert !!! Customer Registeration Successful",
          $message_body,
          'From: ' . $email . "\r\n" .
            'Reply-To: ' . get_option('business_email') . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion()
        );
      } catch (\Throwable $th) {

        write_log($th);
      }
    }

    public function lux_update_referral_price()
    {
      $all_transactions = hid_ex_m_get_all_transactions();
      $transactions_id = [];

      foreach ($all_transactions as $transaction) {
        $transactions_id[] = $transaction->customer_id;
      }

      $transactions_unique_check = array_count_values($transactions_id);
      $transactions_unique_id = [];

      foreach ($transactions_unique_check as $key => $val) {
        if ($val == 1) {
          $transactions_unique_id[] = $key;
        }
      }

      foreach ($transactions_unique_id as $unique_id) {
        $referral = $this->lux_get_referral($unique_id);

        if ($referral->paid == 0) {
          $referred_by = $this->lux_get_referral_code($referral->referral);
          if ($referred_by != 0) {
            $prev_referral = $referred_by->total_referral;

            $notify = [
              'customer_id' => $referred_by->customer_id,
              'title' => 'Referral',
              'msg' => 'You have received #500 from your referral fundings'
            ];

            $this->lux_set_notification($notify);

            $details = [
              'total_referral' => $prev_referral + 1,
            ];

            $this->lux_update_referral($details, ['referral_code' => $referral->referral]);

            $details = [
              'paid' => 1,
            ];

            $this->lux_update_referral($details, ['referral_code' => $referral->referral_code]);

            $input_data = array(
              'customer_id' => $referred_by->customer_id,
              'transaction_type' => 1,
              'amount' => 500,
              'mode'  => 0,
              'details'   => 'Funding || Referral Payment',
              'proof_of_payment' => 0,
              'sending_instructions'  => 'Not required',
              'transaction_status'    => 1
            );

            // write_log($input_data);

            $this->lux_create_new_wallet_transaction($input_data);
          }
        }
      }
    }
    # === Bank Details
    public function lux_create_customer_bank_details_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_customer_bank_details';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        displayName TEXT NOT NULL,
        bankName TINYTEXT NOT NULL,
        bankAccountName TINYTEXT NOT NULL,
        bankAccountNumber TINYTEXT NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    public function lux_populate_customer_bank_details()
    {
      $customers = $this->lux_get_customer_details();

      foreach ($customers as $customer) {
        $id = $customer->ID;
        $customer_data = $customer->data;
        $display_name = $customer_data->display_name;
        $user_nice_name = $customer_data->user_nicename;

        $customer_info = [
          'id'          => $id,
          'displayName' => "$display_name $user_nice_name"
        ];

        for ($i = 0; $i < count($customers); $i++) {
          $this->lux_set_bank_details($customer_info);
        }
      }
    }

    public function lux_get_bank_details($id)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_customer_bank_details';
      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$id'");
      return $result;
    }

    public function lux_get_all_bank_details()
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_customer_bank_details';
      $result = $wpdb->get_results("SELECT * FROM $table_name");
      return $result;
    }

    public function lux_set_bank_details($details)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_customer_bank_details';
      $wpdb->insert($table_name, $details);
    }

    public function lux_update_bank_details($details,  $where)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_customer_bank_details';
      $wpdb->update($table_name, $details, $where);
    }

    public function lux_create_giftcard_categories_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_giftcard_categories';

      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        category tinytext NOT NULL,
        icon int NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('jal_db_version', $jal_db_version);
    }

    public function lux_create_giftcard_sub_categories_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';

      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        category_id tinytext NOT NULL,
        sub_category tinytext NOT NULL,
        icon tinytext NOT NULL,
        rate decimal(10,2) NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option('jal_db_version', $jal_db_version);
    }

    public function lux_create_new_giftcard_category($data)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_categories';
      $wpdb->insert($table_name, $data);
    }

    public function lux_get_giftcard_category_data($id)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_categories';
      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$id'");
      if (!empty($result)) return $result[0];
      throw new Exception("giftcard category not found", 1);
    }

    public function lux_create_new_giftcard_sub_category($data)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $wpdb->insert($table_name, $data);
    }

    public function lux_get_all_giftcard_categories()
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_categories';
      $result = $wpdb->get_results("SELECT * FROM $table_name");

      $output = [];

      foreach ($result as $data) {

        $output[] =
          [
            'id'            => $data->id,
            'category'      => $data->category,
            'icon'          => wp_get_attachment_url($data->icon),
          ];
      }

      return $output;
    }

    public function lux_get_all_giftcard_sub_categories()
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $result = $wpdb->get_results("SELECT * FROM $table_name");

      $output = [];

      foreach ($result as $data) {
        array_push(
          $output,
          [
            'id'            => $data->id,
            'category_id'   => $data->category_id,
            'category'      => $this->lux_get_giftcard_category_data($data->category_id)->category,
            'sub_category'  => $data->sub_category,
            'icon'          => wp_get_attachment_url($data->icon),
            'rate'          => $data->rate
          ]
        );
      }

      return $output;
    }

    public function lux_get_giftcard_sub_category_data_with_category($id)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE category_id='$id'");

      if (empty($result)) {
        throw new Exception("giftcard not found", 1);
      }

      $output = [];

      foreach ($result as $data) {
        $output[] = [
          'id'            => $data->id,
          'category_id'   => $data->category_id,
          'category'      => $this->lux_get_giftcard_category_data($data->category_id)->category,
          'sub_category'  => $data->sub_category,
          'icon_id'          => $data->icon,
          'icon'          => wp_get_attachment_url($data->icon),
          'rate'          => $data->rate
        ];
      }

      return $output;
    }

    public function lux_get_giftcard_sub_category_data($id)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$id'");

      if (empty($result)) {
        throw new Exception("giftcard not found", 1);
      }

      $output = [];

      foreach ($result as $data) {
        $output[] = [
          'id'            => $data->id,
          'category_id'   => $data->category_id,
          'category'      => $this->lux_get_giftcard_category_data($data->category_id)->category,
          'sub_category'  => $data->sub_category,
          'icon_id'       => $data->icon,
          'icon'          => wp_get_attachment_url($data->icon),
          'rate'          => $data->rate
        ];
      }

      return $output[0];
    }

    public function lux_delete_giftcard_category_data($id)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_categories';
      $wpdb->query("DELETE FROM $table_name WHERE id='$id'");
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $wpdb->query("DELETE FROM $table_name WHERE category_id='$id'");
    }

    public function lux_delete_giftcard_sub_category_data($id)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $wpdb->query("DELETE FROM $table_name WHERE id='$id'");
    }

    public function lux_update_giftcard_sub_category($details,  $where)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
      $wpdb->update($table_name, $details, $where);
    }

    public function lux_create_new_giftcard_order($data)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . 'hid_ex_m_giftcard_orders';

      $wpdb->insert(
        $table_name,
        $data
      );

      try {
        $customer = get_userdata($data["customer_id"]);
        $email = $customer->user_email;
        $name = $customer->display_name;
        $asset_type = "Giftcard";
        $asset = $this->lux_get_giftcard_sub_category_data_with_category($data['asset_id']);
        $asset_name = $asset['category'];
        $qty = $data["quantity"];
        $fee = $data["price"];

        $message_body = "Greetings $name,\n\nYou're recieving this eMail Notification because your Giftcard Order was placed successfully and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity : $qty\nAmount you get : $fee\n\nKindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.\n\nCheers!!!\nLuxtrade - Admin";

        wp_mail(
          $email,
          'LuxTrade Alert !!! Giftcard Order Created Successfully',
          $message_body
        );

        $name = hid_ex_m_get_customer_data_name($data["customer_id"]);
        $message_body = "Greetings,\n\nYou're recieving this eMail Notification because a customer by the name $name just made a Giftcard Order and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity : $qty\nFee : # $fee\n\nKindly return to Luxtrade and sign into WP Admin to view and update the order.\n\nCheers!!!\nLuxtrade - Admin";

        wp_mail(
          get_option('business_email'),
          'LuxTrade Alert !!! You have a new Buy Order',
          $message_body
        );
      } catch (\Throwable $th) {
        write_log($th);
      }
    }
  }
}
