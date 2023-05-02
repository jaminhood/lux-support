<?php

/**
 * Plugin Name: Lux Support
 * Plugin URI: https://github.com/jaminhood/lux-support
 * Version: 1.0.0
 * Description: This plugin provides backend api support for Lux Trade mobile applications and also create a dashboard as well for trading on the website
 * Author: JaminHood
 * Author URI: https://github.com/jaminhood
 * License: GPU
 * Text Domain: em
 */

# === To deny anyone access to this file directly
if (!defined("ABSPATH")) {
  die("Direct access forbidden");
}
# === Plugin path
define("EMPATH", plugin_dir_path(__FILE__));
# === Plugin url
define("EMURL", plugin_dir_url(__FILE__));

if (!class_exists('Lux')) {
  class Lux
  {
    public function __construct()
    {
      # === Requesting files from external scripts
      require_once(EMPATH . "em-dbh.php");
      require_once(EMPATH . "em-utils.php");
      require_once(EMPATH . "em-assets.php");
      require_once(EMPATH . "em-assets.php");
      require_once(EMPATH . "em-ajax-functions.php");
      require_once(EMPATH . "templates/em-home.php");
      require_once(EMPATH . "templates/em-bank-details.php");
      require_once(EMPATH . "templates/em-referrals.php");
      require_once(EMPATH . "templates/em-top-assets.php");
      require_once(EMPATH . "templates/em-top-news.php");
      require_once(EMPATH . "templates/em-wallet.php");
      require_once(EMPATH . "templates/em-buy-assets.php");
      require_once(EMPATH . "templates/em-sell-assets.php");
      require_once(EMPATH . "templates/em-barcode.php");
      require_once(EMPATH . "templates/em-giftcards.php");
      require_once(EMPATH . "templates/em-debit-user.php");
      require_once(EMPATH . "templates/em-credit-user.php");
      require_once(EMPATH . "templates/em-announcement.php");
      require_once(EMPATH . "rest/em-rest.php");
      require_once(EMPATH . "templates/user/em-user-requirements.php");
      $this->lux_init();
    }
    // https://app.getpostman.com/join-team?invite_code=8defe78d10d216b61c3e8c2914e6b22a
    public function lux_init()
    {
      $lux_assets = new LuxAssets;
      $lux_utils = new LuxUtils;
      register_activation_hook(__FILE__, [$this, 'lux_activate']);
      register_deactivation_hook(__FILE__, [$this, 'lux_deactivate']);
      # ===
      add_action('admin_enqueue_scripts', [$lux_assets, 'lux_admin_styles']);
      add_action('admin_enqueue_scripts', [$lux_assets, 'lux_admin_scripts']);
      add_action('wp_enqueue_scripts', [$lux_assets, 'lux_user_styles']);
      add_action('wp_enqueue_scripts', [$lux_assets, 'lux_user_scripts']);
      # ===
      add_action('admin_menu', [$lux_utils, 'lux_admin_menu']);
      add_action('init', [$lux_utils, 'lux_rewrite_rules']);
      add_filter('query_vars', [$lux_utils, 'lux_query_vars']);
      add_action('template_include', [$lux_utils, 'lux_template_include']);
      add_shortcode('em_guest_rate', [$lux_utils, 'lux_guest_rates']);
      add_shortcode('em_top_news', [$lux_utils, 'lux_top_news_slide']);
      add_shortcode('em_download_buttons', [$lux_utils, 'lux_download_button']);
      # ===
      $lux_dbh = new LuxDBH;
      add_action('init', [$lux_dbh, 'lux_update_referral_price']);
      # ===
      # ===
    }

    public function lux_activate()
    {
      $lux_dbh = new LuxDBH;
      $lux_dbh->lux_create_customer_bank_details_table();
      $lux_dbh->lux_create_top_news_table();
      $lux_dbh->lux_create_referral_table();
      $lux_dbh->lux_create_giftcard_categories_table();
      $lux_dbh->lux_create_giftcard_sub_categories_table();
      $lux_dbh->lux_create_customers_notifications_table();
      $lux_dbh->lux_create_barcodes_table();
      $lux_dbh->lux_create_transaction_pin_table();
      $lux_dbh->lux_create_device_token_table();
      $lux_dbh->lux_create_top_assets_table();
      $lux_dbh->lux_populate_customer_bank_details();
      $lux_dbh->lux_populate_referrals();
    }

    public function lux_deactivate()
    {
      flush_rewrite_rules(true);
      global $wpdb;

      $tables = [
        'em_top_news',
        'em_customer_bank_details',
        'em_referrals',
        'em_giftcard_categories',
        'em_giftcard_sub_categories',
        'em_transaction_pin',
        'em_device_token',
        'em_top_assets'
      ];

      foreach ($tables as $table) {
        $table_name = $wpdb->prefix . $table;

        $wpdb->query("DROP TABLE IF EXISTS " . $table_name);
      }
    }
  }
}

new Lux;
