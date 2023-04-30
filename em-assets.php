<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAssets')) {
  class LuxAssets
  {
    public function __construct()
    {
      # === EMURL . '
      # === 'https://jaminhood.github.io/exchange-manager-assets/'
    }

    private function lux_general_styles()
    {
      # === App style
      wp_register_style('app-style',  'https://jaminhood.github.io/exchange-manager-assets/css/app.min.css', array(), time());
      wp_enqueue_style('app-style');
      # === iziToast style
      wp_register_style('iziToast',  'https://jaminhood.github.io/exchange-manager-assets/css/iziToast.min.css', array(), time());
      wp_enqueue_style('iziToast');
      # === Database table style
      wp_register_style('datatables',  'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/datatables.min.css', array(), time());
      wp_enqueue_style('datatables');
      # === Database table style
      wp_register_style('datatables-bootstrap',  'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css', array(), time());
      wp_enqueue_style('datatables-bootstrap');
      # === main style
      wp_register_style('style',  'https://jaminhood.github.io/exchange-manager-assets/css/style.css', array(), time());
      wp_enqueue_style('style');
    }

    public function lux_admin_styles()
    {
      $this->lux_general_styles();
      # === main style
      wp_register_style('main-style', EMURL . 'assets/css/main.css', array(), time());
      wp_enqueue_style('main-style');
    }

    public function lux_user_styles()
    {
      $this->lux_general_styles();
      # === vendors style
      wp_register_style('vendors',  'https://jaminhood.github.io/exchange-manager-assets/css/vendors.css', array(), time());
      wp_enqueue_style('vendors');
      # === reset style
      wp_register_style('user-style', EMURL . 'assets/css/user-style.css', array(), time());
      wp_enqueue_style('user-style');
    }

    private function lux_general_scripts()
    {
      # === App script
      wp_enqueue_script('app-script',  'https://jaminhood.github.io/exchange-manager-assets/js/app.min.js', array('jquery'), 1, true);
      # === iziToast script
      wp_enqueue_script('iziToast-script',  'https://jaminhood.github.io/exchange-manager-assets/js/iziToast.min.js', array('jquery'), 1, true);
      # === Database script
      wp_enqueue_script('database-script',  'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/datatables.min.js', array('jquery'), 1, true);
      # === Database bootstrap script
      wp_enqueue_script('database-bootstrap-script',  'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js', array('jquery'), 1, true);
    }

    public function lux_admin_scripts()
    {
      $this->lux_general_scripts();
      # === Main script
      wp_enqueue_script('main-script', EMURL . 'assets/js/main.js', array('jquery'), 1, true);
      wp_localize_script(
        'main-script',
        'script_links',
        [
          'ajaxurl'       => admin_url('admin-ajax.php'),
          'signUpURL'     => site_url('/lux-auth/register/'),
          'signInURL'     => site_url('/lux-auth/sign-in/'),
          'dashboardURL'  => site_url('/lux-user/dashboard/'),
          'success_url'   => site_url('/lux-user/success/'),
          'security'      => wp_create_nonce('file_upload'),
        ]
      );
    }

    public function lux_user_scripts()
    {
      $this->lux_general_scripts();
      # === Vendor script
      wp_enqueue_script('vendors-script',  'https://jaminhood.github.io/exchange-manager-assets/js/vendors.js', array('jquery'), 1, true);
      # === Main script
      wp_enqueue_script('user-script', EMURL . 'assets/js/user-script.js', array('jquery'), 1, true);
      wp_localize_script(
        'user-script',
        'script_links',
        [
          'ajaxurl'       => admin_url('admin-ajax.php'),
          'signUpURL'     => site_url('/lux-auth/register/'),
          'signInURL'     => site_url('/lux-auth/sign-in/'),
          'dashboardURL'  => site_url('/lux-user/dashboard/'),
          'success_url'   => site_url('/lux-user/success/'),
          'security'      => wp_create_nonce('file_upload'),
        ]
      );
    }
  }
}
