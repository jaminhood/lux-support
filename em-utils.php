<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxUtils')) {
  class LuxUtils
  {
    public function lux_admin_menu()
    {
      $lux_admin_referral_page = new LuxAdminReferralPage;
      $lux_admin_top_assets_page = new LuxAdminTopAssetsPage;
      $lux_admin_top_news_page = new LuxAdminTopNewsPage;
      $lux_admin_bank_info_page = new LuxAdminBankInfoPage;
      $lux_admin_home_page = new LuxAdminHomePage;
      $lux_admin_wallet = new LuxAdminWallet;
      $lux_admin_buy_assets = new LuxAdminBuyAssets;
      $lux_admin_sell_assets = new LuxAdminSellAssets;
      $lux_admin_barcode = new LuxAdminBarcodePage;
      $lux_admin_giftcards = new LuxAdminGiftcards;
      $lux_admin_debit_user = new LuxAdminDebitWallet;
      $lux_admin_credit_user = new LuxAdminCreditWallet;
      $lux_admin_announcement = new LuxAdminAnnouncement;
      # === adding plugin in menu
      add_menu_page(
        'Lux Management', //page title
        'Lux Management', //menu title
        'manage_options', //capabilities
        'lux', //menu slug
        [$lux_admin_home_page, 'lux_admin_home_page_template'], //function
        'dashicons-admin-site', // Icon
        11, // Position
      );

      add_submenu_page(
        'lux', //parent page slug
        'Debit User', //page title
        'Debit User', //menu titel
        'manage_options', //manage optios
        'lux-debit-wallet', //slug
        [$lux_admin_debit_user, 'lux_admin_debit_wallet_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Credit User', //page title
        'Credit User', //menu titel
        'manage_options', //manage optios
        'lux-credit-wallet', //slug
        [$lux_admin_credit_user, 'lux_admin_credit_wallet_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Giftcards', //page title
        'Giftcards', //menu titel
        'manage_options', //manage optios
        'lux-giftcards', //slug
        [$lux_admin_giftcards, 'lux_admin_giftcards_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Barcodes', //page title
        'Barcodes', //menu titel
        'manage_options', //manage optios
        'lux-barcode', //slug
        [$lux_admin_barcode, 'lux_admin_barcode_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Wallet', //page title
        'Wallet', //menu titel
        'manage_options', //manage optios
        'lux-wallet', //slug
        [$lux_admin_wallet, 'lux_admin_wallet_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Buy Orders', //page title
        'Buy Orders', //menu titel
        'manage_options', //manage optios
        'lux-buy', //slug
        [$lux_admin_buy_assets, 'lux_admin_buy_assets_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Sell Orders', //page title
        'Sell Orders', //menu titel
        'manage_options', //manage optios
        'lux-sell', //slug
        [$lux_admin_sell_assets, 'lux_admin_sell_assets_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Bank Details', //page title
        'Bank Details', //menu titel
        'manage_options', //manage optios
        'lux-bank-details', //slug
        [$lux_admin_bank_info_page, 'lux_admin_bank_info_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Referrals', //page title
        'Referrals', //menu titel
        'manage_options', //manage optios
        'lux-referrals', //slug
        [$lux_admin_referral_page, 'lux_admin_referral_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Top Assets', //page title
        'Top Assets', //menu titel
        'manage_options', //manage optios
        'lux-top-assets', //slug
        [$lux_admin_top_assets_page, 'lux_admin_top_assets_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Top News', //page title
        'Top News', //menu titel
        'manage_options', //manage optios
        'lux-top-news', //slug
        [$lux_admin_top_news_page, 'lux_admin_top_news_template'] //function
      );

      add_submenu_page(
        'lux', //parent page slug
        'Create Announcement', //page title
        'Create Announcement', //menu titel
        'manage_options', //manage optios
        'lux-announcement', //slug
        [$lux_admin_announcement, 'lux_admin_announcement_template'] //function
      );
    }

    public static function lux_get_order_status($status)
    {

      if ($status == 0) {
        return "Declined";
      } elseif ($status == 1) {
        return "Pending";
      } elseif ($status == 2) {
        return "Confirmed";
      } else {
        return "Completed";
      }
    }

    public static function lux_push_notification($id, array $data)
    {
      $lux_dbh = new LuxDBH;
      $token = $lux_dbh->lux_get_device_token($id);
      $device_token = '';
      if (!empty($token)) {
        $device_token = $lux_dbh->lux_get_device_token($id)[0]->device_token;
      }
      $api_key = 'AAAAFjFU1q0:APA91bFsTU-nPXkB4i0QLIUOZLsWEuT10H80KI6sjbBAW9-mmhKThl9hJd99Fg3_vvUYER3FZNAvxv9wlPxZRZ7FKB_6a4ncZ6uYaAncmOTkCohv8Ep_CJRN7guhRWkxIW8TvhTLg0So';
      $url = 'https://fcm.googleapis.com/fcm/send';
      $fields = json_encode([
        'to' => $device_token,
        'notification' => $data
      ]);

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

      $headers = array();
      $headers[] = 'Authorization: key = ' . $api_key;
      $headers[] = 'Content-Type: application/json';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);
      if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
      }
      curl_close($ch);
    }

    public function lux_rewrite_rules()
    {
      add_rewrite_rule(
        'lux-auth/([a-zA-Z0-9-]+)[/]?$',
        'index.php?auth_page=$matches[1]',
        'top'
      );

      add_rewrite_rule(
        'lux-user/([a-zA-Z0-9-]+)[/]?$',
        'index.php?user_page=$matches[1]',
        'top'
      );
    }

    public function lux_query_vars($query_vars)
    {
      $query_vars[] = 'auth_page';
      $query_vars[] = 'user_page';
      return $query_vars;
    }

    public function lux_template_include($template)
    {
      if (get_query_var('auth_page') != false && get_query_var('auth_page') != '') {
        if (is_user_logged_in()) {
          wp_redirect(site_url('/lux-user/dashboard/'));
        } else {
          return EMPATH . 'templates/user/em-auth-page.php';
        }
      }

      if (get_query_var('user_page') != false && get_query_var('user_page') != '') {
        if (!is_user_logged_in()) {
          wp_redirect(site_url('/lux-auth/sign-in/'));
        } else {
          return EMPATH . 'templates/user/em-user-page.php';
        }
      }

      return $template;
    }

    public function lux_guest_rates()
    {  ?>
      <div class="container-fluid guestRate">
        <div class="row">
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">Rates Calculator</h4>
                </div>
              </div>
              <div class="card-body">
                <form method="POST" id="ratesCalculator">
                  <fieldset class="form-group">
                    <div class="row">
                      <div class="col-form-label col-sm-4 pt-3">Asset Type</div>
                      <div class="col-sm-8">
                        <div class="form-check py-2">
                          <input class="form-check-input" type="radio" name="asset_type" id="eCurrency" value="E-Currency">
                          <label class="form-check-label" for="eCurrency">
                            E-currency
                          </label>
                        </div>
                        <div class="form-check py-2">
                          <input class="form-check-input" type="radio" name="asset_type" id="cryptoCurrency" value="Crypto Currency">
                          <label class="form-check-label" for="cryptoCurrency">
                            Crypto-Currency
                          </label>
                        </div>
                        <div class="form-check py-2">
                          <input class="form-check-input" type="radio" name="asset_type" id="giftcard" value="Giftcard">
                          <label class="form-check-label" for="giftcard">
                            Giftcard
                          </label>
                        </div>
                        <p class="font-12 py-2 text-muted">What type of asset would you like to purchase?</p>
                      </div>
                    </div>
                  </fieldset>
                  <div class="form-group row select-wrapper">
                    <label for="asset" class="col-sm-4 col-form-label">Select Asset</label>
                    <div class="col-sm-8">
                      <div class="selects-contant">
                        <select class="js-basic-single form-control" name="asset" id="selectAsset">
                          <option value="select">Select Asset</option>
                        </select>
                      </div>
                      <p class="font-12 py-2 text-muted">Select the asset to calculate</p>
                    </div>
                  </div>
                  <div class="form-group row qtyWrapper">
                    <label for="qty" class="col-sm-4 col-form-label">Quantity</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" id="item-quantity">
                      <p class="font-12 py-2 text-muted">Enter quantity to calculate.</p>
                    </div>
                  </div>
                  <div class="row border-top">
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="buyingPrice" class="col-sm-12 col-form-label">Buying Price</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-buying" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="sellingPrice" class="col-sm-12 col-form-label">Selling Price</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-selling" disabled>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="buyingPerQuantity" class="col-sm-12 col-form-label">Buying Per Quantity</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-buying-q" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="sellingPerQuantity" class="col-sm-12 col-form-label">Selling Per Quantity</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-selling-q" disabled>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">E-currency Rates</h4>
                </div>
              </div>
              <div class="card-body p-0">
                <?php
                $eAssets = hid_ex_m_get_all_e_currency_assets();
                if (empty($eAssets)) {
                  echo "<p class='py-3 px-4'>You haven't made any Transactions</p>";
                } else {
                ?>
                  <div class="datatable-wrapper table-responsive">
                    <table id="datatable-user" class="display compact table table-striped table-bordered">
                      <thead>
                        <tr>
                          <td>Icon</td>
                          <td>Name | Allias</td>
                          <td>Buying Price</td>
                          <td>Selling Price</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($eAssets as $asset) { ?>
                          <tr>
                            <td class="p-3" style="width: 3rem;aspect-ratio: 1;overflow: hidden;"><img src="<?php echo wp_get_attachment_url($asset->icon) ?>" style="width: 100%;object-fit: cover;" alt="..."></td>
                            <td><?php echo $asset->name . ' | ' . $asset->short_name ?></td>
                            <td><?php echo $asset->buying_price ?></td>
                            <td><?php echo $asset->selling_price ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">Crypto Currency Rates</h4>
                </div>
              </div>
              <div class="card-body p-0">
                <?php
                $cryptoAssets = hid_ex_m_get_all_crypto_currency_assets();
                if (empty($cryptoAssets)) {
                  echo "<p class='py-3 px-4'>You haven't made any Transactions</p>";
                } else {
                ?>
                  <div class="datatable-wrapper table-responsive">
                    <table id="datatable-crypto" class="display compact table table-striped table-bordered">
                      <thead>
                        <tr>
                          <td>Icon</td>
                          <td>Name | Allias</td>
                          <td>Buying Price</td>
                          <td>Selling Price</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($cryptoAssets as $asset) { ?>
                          <tr>
                            <td class="p-3" style="width: 3rem;aspect-ratio: 1;overflow: hidden;"><img src="<?php echo wp_get_attachment_url($asset->icon) ?>" style="width: 100%;object-fit: cover;" alt="..."></td>
                            <td><?php echo $asset->name . ' | ' . $asset->short_name ?></td>
                            <td><?php echo $asset->buying_price ?></td>
                            <td><?php echo $asset->selling_price ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">Giftcards</h4>
                </div>
              </div>
              <div class="card-body p-0">
                <?php
                $giftcards = hid_ex_m_get_all_giftcards();
                if (empty($giftcards)) {
                  echo "<p class='py-3 px-4'>No Giftcard to display</p>";
                } else {
                ?>
                  <div class="datatable-wrapper table-responsive">
                    <table id="datatable-giftcard" class="display compact table table-striped table-bordered">
                      <thead>
                        <tr>
                          <td>Icon</td>
                          <td>Name | Allias</td>
                          <td>Buying Price</td>
                          <td>Selling Price</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($giftcards as $asset) { ?>
                          <tr>
                            <td class="p-3" style="width: 3rem;aspect-ratio: 1;overflow: hidden;"><img src="<?php echo wp_get_attachment_url($asset->icon) ?>" style="width: 100%;object-fit: cover;" alt="..."></td>
                            <td><?php echo $asset->name . ' | ' . $asset->short_name ?></td>
                            <td><?php echo $asset->buying_price ?></td>
                            <td><?php echo $asset->selling_price ?? '--' ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php }

    public function lux_top_news_slide()
    {
      $lux_dbh = new LuxDBH;
      $all_news = $lux_dbh->lux_get_news();
      if (!empty($all_news)) { ?>
        <div class="container-fluid py-5">
          <div class="row">
            <div class="col-12 col-xs-12 col-lg-12 mb-1">
              <h4 class="title">Top News</h4>
            </div>
          </div>
          <div class="owl-wrapper">
            <div class="owl-carousel owl-theme" data-nav-dots="false" data-items="3" data-xl-items="3" data-lg-items="3" data-md-items="2" data-sm-items="1" data-xs-items="1" data-xx-items="1">
              <?php foreach ($all_news as $news) { ?>
                <div class="card p-1 rounded">
                  <img src="<?php echo wp_get_attachment_url($news->newsPicture) ?>" alt="<?php echo $news->title ?>" class="card-img-top" style="max-height: 300px;min-height: 300px;object-fit: cover;">
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php }
    }
    public function lux_download_button()
    { ?>
      <div class="row">
        <div class="col-12 text-right menu-download-container">
          <button id="home-download-button" class="btn btn-success"><span style="font-family: poppins;">Download</span> <i class="fas fa-download"></i></button>
          <div id="home-download-modal">
            <ul class="download-list">
              <li class="download-item">
                <a href="https://apps.apple.com/ng/app/lux-trade/id6443710711"><i class="fab fa-app-store-ios"></i> <span>App Store</span></a>
              </li>
              <li class="download-item">
                <a href="https://play.google.com/store/apps/details?id=com.lux.luxTrade"><i class="fab fa-google-play"></i> <span>Play Store</span></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
<?php
    }
  }
}
