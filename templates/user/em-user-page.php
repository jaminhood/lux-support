<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

global $user;
global $page_data;
$user = wp_get_current_user();
$page_data = [];
$lux_dbh = new LuxDBH;

$current_page = strtolower(get_query_var('user_page'));

switch ($current_page) {
  case 'dashboard':
    $data = ["title" => "Dashboard"];
    $page_data = $lux_dbh->lux_get_dashboard_data($user->ID);
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'wallet':
    $data = ["title" => "Wallet"];
    $page_data = $lux_dbh->lux_get_wallet_data($user->ID);
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'buy':
    $data = ["title" => "Buy Asset"];
    $page_data = $lux_dbh->lux_get_wallet_data($user->ID);
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'sell':
    $data = ["title" => "Sell Asset"];
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'referral':
    $data = ["title" => "Referral"];
    $page_data = $lux_dbh->lux_get_single_referral($user->ID);
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'rate':
    $data = ["title" => "Today's Rate"];
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'announcement':
    $data = ["title" => "Announcement"];
    $page_data = hid_ex_m_get_all_announcements();
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'statement':
    $data = ["title" => "Statement"];
    $page_data = hid_ex_m_get_user_history_data($user->ID);
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'settings':
    $data = ["title" => "Settings"];
    $page_data = [
      'userId'   => $user->ID,
      'username' => $user->user_login,
      'userFirstName' => $user->display_name,
      'userLastName' => $user->last_name,
      'userEmail' => $user->user_email,
      'userPhone' => get_user_meta($user->ID, 'phone_number')[0]
    ];
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'support':
    $data = ["title" => "Support"];
    $page_data = [
      "user" => [
        'ID'   => $user->ID,
        'display_name' => $user->display_name,
      ],
      "data" => hid_ex_m_get_customer_support_tickets($user->ID)
    ];
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'success':
    $data = ["title" => "Order Successful"];
    em_page_header($data);
    em_page_template($current_page);
    break;
  case 'logout':
    wp_logout($user->ID);
    wp_redirect(site_url('/lux-auth/sign-in/'));
    break;
  default:
    $data = ["title" => "Error"];
    em_page_header($data);
    em_error_template();
    break;
}
em_page_footer();

function em_page_template($page)
{
  global $user;
  global $page_data; ?>
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
    <?php em_main_header($user) ?>
    <div class="app-container">
      <?php em_sidebar(strtolower(get_query_var('user_page'))) ?>
      <!-- begin app-main -->
      <div class="app-main" id="main">
        <!-- begin container-fluid -->
        <div class="container-fluid py-md-5">
          <?php
          switch (strtolower($page)) {
            case 'dashboard':
              em_dashboard_template($page_data, $user->ID);
              break;
            case 'wallet':
              em_wallet_template($page_data);
              break;
            case 'buy':
              em_buy_asset_template($page_data);
              break;
            case 'sell':
              em_sell_asset_template();
              break;
            case 'referral':
              em_referral_template($page_data);
              break;
            case 'rate':
              em_rates_template();
              break;
            case 'announcement':
              em_announcement_template($page_data);
              break;
            case 'statement':
              em_statement_template($page_data);
              break;
            case 'settings':
              em_settings_template($page_data);
              break;
            case 'support':
              em_support_template($page_data);
              break;
            case 'success':
              em_success_template();
              break;
            default:
              em_error_template();
              break;
          }
          ?>
        </div>
        <!-- end container-fluid -->
      </div>
      <!-- end app-main -->
    </div>
  </div>
<?php }

function em_dashboard_template($page_data, $user_id)
{ ?>
  <!-- begin row -->
  <div class="row">
    <div class="col-md-12 m-b-30">
      <!-- begin page title -->
      <div class="d-block d-flex flex-nowrap align-items-center">
        <div class="page-title mr-4 pr-4">
          <h1>Overview</h1>
        </div>
        <div class="ml-auto d-flex align-items-center secondary-menu text-center">
          <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
        </div>
      </div>
      <!-- end page title -->
    </div>
  </div>
  <!-- Notification -->
  <?php
  $announcement = $page_data['announcements'];
  if (!empty($announcement)) { ?>
    <div class="row">
      <div class="col-md-12">
        <div class="alert border-0 alert-primary l-bg-green m-b-30 alert-dismissible fade show border-radius-none text-light" role="alert">
          <?php foreach ($announcement as $announce) {
            if ($announce->id === $announcement[count($announcement) - 1]->id) {
              $bodyText = $announce->body;

              if (strlen($announce->body) > 70) {
                $bodyText = substr($announce->body, 0, 70) . " ...";
              }

              $headline = str_replace('\\', '', $announce->headline);
              $body = str_replace('\\', '', $bodyText) ?>
              <p class="text-light"><strong class="text-bold"><?php echo $headline ?>!</strong> - <?php echo $body ?></p>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ti ti-close"></i>
              </button>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php }
  $notifications = $page_data['notifications'];
  if (!empty($notifications)) {
    foreach ($notifications as $notify) {
      if ($notify->seen == 0) { ?>
        <div class="row">
          <div class="col-md-12">
            <div class="alert border-0 alert-primary l-bg-green m-b-30 alert-dismissible fade show border-radius-none text-light" role="alert">
              <?php
              $msg_text = $notify->msg;
              $title = str_replace('\\', '', $notify->title);
              $msg = str_replace('\\', '', $msg_text);
              LuxDBH::lux_update_notifications($user_id);
              ?>
              <p class="text-light"><strong class="text-bold"><?php echo $title ?>!</strong> - <?php echo $msg ?></p>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ti ti-close"></i>
              </button>
            </div>
          </div>
        </div>
  <?php
      }
    }
  } ?>
  <!-- end row -->
  <div class="row">
    <div class="col-xxl-12 m-b-30">
      <div class="card card-statistics h-100 mb-0 apexchart-tool-force-top">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Account Summary</h4>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-6 col-xs-6 col-lg-6 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Total Sales</p>
                  <h4 class="tex-dark mb-0">#<?php echo $page_data['totalSold'] ?></h4>
                </div>
              </div>
            </div>
            <div class="col-6 col-xs-6 col-lg-6 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Pending Payments</p>
                  <h4 class="tex-dark mb-0"><?php echo $page_data['pendingPayments'] ?></h4>
                </div>
              </div>
            </div>
            <div class="col-6 col-xs-6 col-lg-6 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Total Transactions</p>
                  <h4 class="tex-dark mb-0"><?php echo $page_data['totalTransactions'] ?></h4>
                </div>
              </div>
            </div>
            <div class="col-6 col-xs-6 col-lg-6 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Wallet Balance</p>
                  <h4 class="tex-dark mb-0">#<?php echo $page_data['walletBalance'] ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-6 m-b-30">
      <div class="card card-statistics h-100 mb-0">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Purchase Analysis</h4>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-xs-6">
              <h2>#<?php echo $page_data['totalBought'] ?></h2>
            </div>
            <div class="col-xs-6">
              <div class="apexchart-wrapper">
                <div id="analytics2" class="chart-fit"></div>
              </div>
            </div>
          </div>
          <div class="border-top my-4"></div>
          <h4 class="card-title">Purchase by Asset</h4>
          <div class="row">
            <div class="col-12 col-md-6">
              <span>E-currency: <b>#<?php echo $page_data['buyEcurrency'] ?></b></span>
              <div class="progress my-3" style="height: 4px">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $page_data['ecurrencyBuyPercent'] ?>%" aria-valuenow="<?php echo $page_data['ecurrencyBuyPercent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <span>Crypto Currency: <b>#<?php echo $page_data['buyCrypto'] ?></b></span>
              <div class="progress my-3" style="height: 4px">
                <div class="progress-bar l-bg-green" role="progressbar" style="width: <?php echo $page_data['cryptoBuyPercent'] ?>%" aria-valuenow="<?php echo $page_data['cryptoBuyPercent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-6 m-b-30">
      <div class="card card-statistics h-100 mb-0">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Total sales</h4>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-xs-12">
              <h2>#<?php echo $page_data['totalSold'] ?></h2>
            </div>
          </div>
          <div class="border-top my-4"></div>
          <h4 class="card-title">Sales by Asset</h4>
          <div class="row">
            <div class="col-12 col-md-6">
              <span>E-currency: <b>#<?php echo $page_data['sellEcurrency'] ?></b></span>
              <div class="progress my-3" style="height: 4px">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $page_data['ecurrencySellPercent'] ?>%" aria-valuenow="<?php echo $page_data['ecurrencySellPercent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <span>Crypto Currency: <b>#<?php echo $page_data['sellCrypto'] ?></b></span>
              <div class="progress my-3" style="height: 4px">
                <div class="progress-bar l-bg-green" role="progressbar" style="width: <?php echo $page_data['cryptoSellPercent'] ?>%" aria-valuenow="<?php echo $page_data['cryptoSellPercent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-statistics">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Statement</h4>
          </div>
        </div>
        <div class="card-body p-0">
          <?php
          if (empty($page_data['orders'])) {
            echo "<p class='py-3 px-4'>You haven't made any orders</p>";
          } else {
          ?>
            <div class="datatable-wrapper table-responsive m-0">
              <table id="datatable-user" class="display compact table table-striped table-bordered p-0 m-0">
                <thead>
                  <tr>
                    <th></th>
                    <th>Type</th>
                    <th>Order</th>
                    <th>Asset</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 0;
                  foreach ($page_data['orders'] as $order) {
                    $i++;
                    $asset_type = "E-Currency";
                    if ($order->asset_type == 2) $asset_type = "Crypto Currency";

                    $order_type = "<span class='badge l-bg-green'>Buy</span>";

                    if (isset($order->amount_to_recieve))
                      $order_type = "<span class='badge badge-danger'>Sell</span>";

                    $order_status = "<span class='badge badge-secondary'>Pending</span>";

                    if ($order->order_status == 0) {
                      $order_status = "<span class='badge badge-danger'>Declined</span>";
                    } elseif ($order->order_status == 2) {
                      $order_status = "<span class='badge badge-success'>Completed</span>";
                    }
                  ?>
                    <tr>
                      <td class="text-right"><?php echo $i ?></td>
                      <td><?php echo $asset_type ?></td>
                      <td><?php echo $order_type ?></td>
                      <td><?php echo hid_ex_m_get_asset_short_name($order->asset_type, $order->asset_id) ?></td>
                      <td><?php echo $order_status ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
<?php
}

function em_wallet_template($page_data)
{ ?>
  <!-- begin row -->
  <div class="row">
    <div class="col-md-12 m-b-30">
      <!-- begin page title -->
      <div class="d-block d-lg-flex flex-nowrap align-items-center">
        <div class="page-title mr-4 pr-4">
          <h1>Wallet</h1>
        </div>
        <div class="ml-auto d-flex align-items-center secondary-menu text-center">
          <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
        </div>
      </div>
      <!-- end page title -->
    </div>
  </div>
  <!-- end row -->
  <div class="row">
    <div class="col-xxl-12 m-b-30">
      <div class="card card-statistics h-100 mb-0 apexchart-tool-force-top">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">My Wallet</h4>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-xs-6 col-lg-4 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Pending Wallet Payments</p>
                  <h4 class="tex-dark mb-0"><?php echo $page_data['pendingPayments'] ?></h4>
                </div>
              </div>
            </div>
            <div class="col-12 col-xs-6 col-lg-4 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Total Wallet Transactions</p>
                  <h4 class="tex-dark mb-0"><?php echo $page_data['totalTransactions'] ?></h4>
                </div>
              </div>
            </div>
            <div class="col-12 col-xs-12 col-lg-4 pb-5">
              <div class="row mb-2 pb-3 align-items-end">
                <div class="col">
                  <p>Total Wallet Balance</p>
                  <h4 class="tex-dark mb-0">#<?php echo $page_data['accountBalance'] ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-xs-12 col-lg-6 m-b-30">
      <div class="card card-statistics h-100 mb-0">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Fund Wallet</h4>
          </div>
        </div>
        <div class="card-body text-left">
          <button class="btn btn-primary" data-toggle="modal" data-target="#fundModal">Click Now</button>
        </div>
      </div>
      <div class="modal fade" id="fundModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content p-3">
            <form method="POST" id="fundForm">
              <div class="modal-header">
                <h5 class="modal-title" id="verticalCenterTitle">Fund Wallet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <fieldset class="form-group">
                  <div class="row">
                    <div class="col-form-label col-sm-3 pt-3">Funding Mode</div>
                    <div class="col-sm-9">
                      <div class="form-check py-2">
                        <input class="form-check-input" type="radio" name="mode" id="localBank" value="0">
                        <label class="form-check-label" for="localBank">
                          Local Bank Transfer
                        </label>
                      </div>
                      <div class="form-check py-2">
                        <input class="form-check-input" type="radio" name="mode" id="eCurrency" value="1">
                        <label class="form-check-label" for="eCurrency">
                          E-currency
                        </label>
                      </div>
                      <div class="form-check py-2">
                        <input class="form-check-input" type="radio" name="mode" id="cryptoCurrency" value="2">
                        <label class="form-check-label" for="cryptoCurrency">
                          Crypto-Currency
                        </label>
                      </div>
                      <p class="font-12 py-2 text-muted">How do you want to fund your account.</p>
                    </div>
                  </div>
                </fieldset>
                <div class="form-group row select-wrapper">
                  <label for="asset" class="col-sm-3 col-form-label">Select Asset</label>
                  <div class="col-sm-9">
                    <div class="selects-contant">
                      <select class="form-control" name="selectAsset" id="selected-asset">
                        <option value="select">Select Asset</option>
                      </select>
                    </div>
                    <p class="font-12 py-2 text-muted">Select the asset to purchase</p>
                  </div>
                </div>
                <div class="form-group row qty-wrapper">
                  <label for="qty" class="col-sm-3 col-form-label">Quantity</label>
                  <div class="col-sm-9">
                    <input type="number" class="form-control" id="qty">
                    <p class="font-12 py-2 text-muted">What quantity would you like to purchase?</p>
                  </div>
                </div>
                <div class="form-group row disabled">
                  <label for="amount" class="col-sm-3 col-form-label">Amount</label>
                  <div class="col-sm-9">
                    <input type="number" class="form-control" id="amount-output" placeholder="100" disabled>
                    <p class="font-12 py-2 text-muted rate-wrapper">Exchange Rate - <span class="form-exchange-rate text-bold">0.00</span></p>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="sendingInstructions" class="col-sm-3 col-form-label">Sending Instructions</label>
                  <div class="col-sm-9">
                    <div class="bg-dark p-2 rounded" style="min-height: 5rem;" id="sendingInstructions"></div>
                    <p class="font-12 py-2 text-muted">Follow these instructions to purchase your asset.</p>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="paymentProof" class="col-sm-3 col-form-label">Proof of Payment</label>
                  <div class="col-sm-9">
                    <div class="border-dotted">
                      <div class="custom-file">
                        <input type="file" name="proofImg" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                      </div>
                      <p class="font-12 py-2 text-muted">Upload a screenshot of proof of payment</p>
                    </div>
                  </div>
                </div>
                <p class="text-danger fund-error-msg"></p>
                <p class="text-success fund-success-msg"></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger w-100" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success l-bg-green w-100">Fund Wallet</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xs-12 col-lg-6 m-b-30">
      <div class="card card-statistics h-100 mb-0">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Request Withdrawal</h4>
          </div>
        </div>
        <div class="card-body text-left">
          <?php
          if ($page_data['canWithdraw'] == 0) {
            echo '<p>You cannot withdraw at the moment</p>';
          } elseif (($page_data['canWithdraw'] == 1)) {
            echo '<button class="btn btn-primary" data-toggle="modal" data-target="#withdrawalModal">Click Now</button>';
          }
          ?>
        </div>
      </div>
      <div class="modal fade" id="withdrawalModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content p-3">
            <form method="POST" id="withdrawalModal">
              <div class="modal-header">
                <h5 class="modal-title" id="verticalCenterTitle">Request Withdrawal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <fieldset class="form-group">
                  <div class="row">
                    <div class="col-form-label col-sm-3 pt-3">Withdrawal Mode</div>
                    <div class="col-sm-9">
                      <div class="form-check">
                        <input type="hidden" name="balance" id="account_balance" value="<?php echo $page_data['accountBalance'] ?>">
                        <input class="form-check-input" name="mode_w" type="radio" id="localBank" value="0" checked>
                        <label class="form-check-label" for="localBank">
                          Local Bank Transfer
                        </label>
                      </div>
                      <p class="font-12 text-muted">Only Local bank transfer are available at this time.</p>
                    </div>
                  </div>
                </fieldset>
                <div class="form-group row">
                  <label for="amount" class="col-sm-3 col-form-label">Amount</label>
                  <div class="col-sm-9">
                    <input type="number" name="amount" class="form-control" id="amount">
                    <p class="font-14 text-muted">How much do you wish to withdraw?</p>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="sendingInstructions" class="col-sm-3 col-form-label">Sending Instructions</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="sendingInstructions" name="sendingInstructions"></textarea>
                    <p class="font-14 text-muted">Where should we send your asset?</p>
                  </div>
                </div>
                <p class="font-14 text-muted"><strong>NOTE:</strong> You would be debitted 5 naira for all withdrawal.</p>
                <p class="font-14 text-danger withdraw-error-msg"></p>
                <p class="font-14 text-success withdraw-success-msg"></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn w-100 btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn w-100 btn-success l-bg-green">Withdraw from Wallet</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12 m-b-30">
      <div class="card card-statistics">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Wallet Statement</h4>
          </div>
        </div>
        <div class="card-body p-0">
          <?php
          if (empty($page_data['allTransactions'])) {
            echo "<p class='py-3 px-4'>You haven't made any Wallet Transactions</p>";
          } else {
          ?>
            <div class="datatable-wrapper table-responsive">
              <table id="datatable-user" class="display compact table table-striped table-bordered">
                <thead>
                  <tr>
                    <td></td>
                    <td>Type</td>
                    <td>Amount</td>
                    <td>Balance</td>
                    <td>Mode</td>
                    <td>Time</td>
                    <td>Details</td>
                    <td>Status</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $buildString = "";
                  $accountBalance = $page_data['accountBalance'];
                  $i = 0;

                  foreach ($page_data['allTransactions'] as $transaction) {
                    $i++;

                    $type = "";
                    $amount = $transaction->amount;
                    $mode = "";
                    $time = $transaction->time_stamp;
                    $details = $transaction->details;
                    $status = 0;

                    if ($transaction->transaction_type == 1) {
                      $type = "Credit";
                    } else {
                      $type = "Withdrawal";
                    }

                    if ($transaction->mode == 0) {
                      $mode = "Local Bank";
                    } elseif ($transaction->mode == 1) {
                      $mode = "E-Currency";
                    } elseif ($transaction->mode == 2) {
                      $mode = "Crypto Currency";
                    }

                    if ($transaction->transaction_status == 0) {
                      $status = "Declined";
                    } elseif ($transaction->transaction_status == 1) {
                      $status = "Pending";
                    } elseif ($transaction->transaction_status == 2) {
                      $status = "Completed";
                    }

                    $buildString .= "<tr><td>$i</td>";
                    $buildString .= "<td>$type</td>";

                    $buildString .= "<td>$amount</td>";

                    $buildString .= "<td>$accountBalance</td>";

                    $buildString .= "<td>$mode</td>";

                    $buildString .= "<td>$time</td>";

                    $buildString .= "<td>$details</td>";

                    $buildString .= "<td>$status</td></tr>";

                    if ($transaction->transaction_type == 1 && $transaction->transaction_status == 2) {
                      $accountBalance -= $transaction->amount;
                    } else if ($transaction->transaction_type == 2 && $transaction->transaction_status == 2) {
                      $accountBalance += $transaction->amount;
                    }
                  }
                  echo $buildString; ?>
                </tbody>
              </table>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
<?php
}

function em_buy_asset_template($page_data)
{
  // $lux_dbh = new LuxDBH;
  // $lux_dbh->lux_set_device_token('dGlVrCiNSMewyuUjNNRsYF:APA91bFXrWKyLWIE5Q89utI5QOSwAL-1qOjDCnacvY7uUm9hjajGwiM-1YtJ9qs4jRglq9_frtvHKnhfRfpd9J779wro60g2Gfn4fplLOAnbRfi54DJ6g6aw83lODaXApvdEKO6UltOs');
?>
  <!-- begin row -->
  <div class="row">
    <div class="col-md-12 m-b-30">
      <!-- begin page title -->
      <div class="d-block d-lg-flex flex-nowrap align-items-center">
        <div class="page-title mr-4 pr-4">
          <h1>Buy Asset</h1>
        </div>
        <div class="ml-auto d-flex align-items-center secondary-menu text-center">
          <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
        </div>
      </div>
      <!-- end page title -->
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-xs-12 col-lg-12 m-b-30">
      <form method="POST" id="buyAsset">
        <div class="card card-statistics h-100 mb-0">
          <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="card-heading">
              <h4 class="card-title">Place a buy</h4>
            </div>
          </div>
          <div class="card-body">
            <input type="hidden" name="wallet_link" id="wallet_link" value="<?php echo site_url('/lux-user/wallet/') ?>">
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
                <p class="font-12 py-2 text-muted">Select the asset to be purchased.</p>
              </div>
            </div>
            <div class="form-group row qtyWrapper">
              <label for="quantity" class="col-sm-4 col-form-label">Quantity</label>
              <div class="col-sm-8">
                <input name="quantity" type="number" class="form-control" id="quantity">
                <p class="font-12 py-2 text-muted">What quantity would you like to purchase?</p>
              </div>
            </div>
            <div class="form-group row disabled">
              <label for="fee" class="col-sm-4 col-form-label">Fee</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="fee" disabled>
                <p class="font-12 py-2 text-muted">Exchange Rate - <span class="form-exchange-rate text-bold">0.00</span></p>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-4"></div>
              <div class="col-sm-8">
                <h5 class="card-title">Balance: #<span id="wallet-balance"><?php echo $page_data['accountBalance'] ?></span></h5>
              </div>
            </div>
            <div class="form-group row">
              <label for="receivingInstructions" class="col-sm-4 col-form-label">Receiving Instruction</label>
              <div class="col-sm-8">
                <textarea class="form-control" id="receivingInstructions" name="receiver"></textarea>
                <p class="font-12 py-2 text-muted">Where should we send your asset?</p>
              </div>
            </div>
            <p class="text-danger buying-error-msg py-2 font-italic"></p>
            <p class="text-success buying-success-msg py-2 font-italic"></p>
            <div class="form-group">
              <button type="submit" class="btn btn-success w-100">Buy Now</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php
}

function em_sell_asset_template()
{ ?>
  <!-- begin row -->
  <div class="row">
    <div class="col-md-12 m-b-30">
      <!-- begin page title -->
      <div class="d-block d-lg-flex flex-nowrap align-items-center">
        <div class="page-title mr-4 pr-4">
          <h1>Sell Asset</h1>
        </div>
        <div class="ml-auto d-flex align-items-center secondary-menu text-center">
          <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
        </div>
      </div>
      <!-- end page title -->
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-xs-12 col-lg-12 m-b-30">
      <div class="card card-statistics h-100 mb-0">
        <div class="card-header d-flex justify-content-between">
          <div class="card-heading">
            <h4 class="card-title">Place a sell</h4>
          </div>
        </div>
        <div class="card-body">
          <form method="POST" id="sellAsset">
            <fieldset class="form-group">
              <div class="row">
                <div class="col-form-label col-sm-4 pt-3">Asset Type</div>
                <div class="col-sm-8">
                  <div class="form-check py-2">
                    <input class="form-check-input" type="radio" name="asset_type" id="eCurrency" value="1">
                    <label class="form-check-label" for="eCurrency">
                      E-currency
                    </label>
                  </div>
                  <div class="form-check py-2">
                    <input class="form-check-input" type="radio" name="asset_type" id="cryptoCurrency" value="2">
                    <label class="form-check-label" for="cryptoCurrency">
                      Crypto-Currency
                    </label>
                  </div>
                  <p class="font-12 text-muted py-2">What type of asset would you like to purchase?</p>
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
                <p class="font-12 py-2 text-muted">Select the asset to be purchased.</p>
              </div>
            </div>
            <div class="form-group row qtyWrapper">
              <label for="quantity" class="col-sm-4 col-form-label">Quantity</label>
              <div class="col-sm-8">
                <input name="quantity" type="number" class="form-control" id="quantity">
                <p class="font-12 py-2 text-muted">What quantity would you like to purchase?</p>
              </div>
            </div>
            <div class="form-group row disabled">
              <label for="fee" class="col-sm-4 col-form-label">Fee</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="fee" disabled>
                <p class="font-12 py-2 text-muted">Exchange Rate - <span class="form-exchange-rate text-bold">0.00</span></p>
              </div>
            </div>
            <div class="form-group row barcode-wrapper">
              <label for="download-barcode" class="col-sm-4 col-form-label">Barcode</label>
              <div class="col-sm-8">
                <img id="barcode-img" src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="" style="width: 200px">
                <p class="font-12 py-2 text-muted">
                  <a href="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" id="download-barcode" download>Download Barcode</a>
                </p>
              </div>
            </div>
            <div class="form-group row">
              <label for="sendingInstructions" class="col-sm-4 col-form-label">Sending Instruction</label>
              <div class="col-sm-8">
                <div class="bg-dark p-2 rounded" style="min-height: 3rem;text-align: justify;" id="sendingInstructions"></div>
                <p class="font-12 py-2 text-muted">Follow this instructions to send in your asset</p>
              </div>
            </div>
            <div class="form-group row">
              <label for="paymentProof" class="col-sm-4 col-form-label">Proof of Payment</label>
              <div class="col-sm-8">
                <div class="border-dotted">
                  <div class="custom-file">
                    <input type="file" name="proofImg" class="custom-file-input proofImg" id="customFile">
                    <label class="custom-file-label image-label" for="customFile">Choose file</label>
                  </div>
                  <p class="font-12 py-2 text-muted">Upload a screenshot of proof of payment</p>
                </div>
              </div>
            </div>
            <p class="text-danger selling-error-msg py-2 font-italic"></p>
            <p class="text-success selling-success-msg py-2 font-italic"></p>
            <div class="form-group">
              <button type="submit" class="btn btn-success w-100">Sell Now</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php
}

function em_success_template()
{ ?>
  <!-- begin row -->
  <div class="row">
    <div class="col-md-12 m-b-30">
      <!-- begin page title -->
      <div class="d-block d-lg-flex flex-nowrap align-items-center">
        <div class="page-title mr-4 pr-4">
          <h1>Order Successful.</h1>
        </div>
        <div class="ml-auto d-flex align-items-center secondary-menu text-center">
          <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
        </div>
      </div>
      <!-- end page title -->
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-xs-12 col-lg-12 m-b-30">
      <div class="card card-statistics h-100 mb-0">
        <div class="card-header d-flex justify-content-between">
          <div class="card-body py-5">
            <h2 class="text-center mb-3">Thank you for your order.</h2>
            <p class="text-center">Your order has been placed and its currently pending, you would be notified once your order is confirmed.</p>
            <p class="text-center py-3"><a href="<?php echo site_url('/lux-user/dashboard/') ?>" class="btn btn-primary">Back to dashboard</a></p>
          </div>
        </div>
      </div>
    </div>
  <?php
}

function em_referral_template($page_data)
{ ?>
    <!-- begin row -->
    <div class="row">
      <div class="col-md-12 m-b-30">
        <!-- begin page title -->
        <div class="d-block d-lg-flex flex-nowrap align-items-center">
          <div class="page-title mr-4 pr-4">
            <h1>Referral</h1>
          </div>
          <div class="ml-auto d-flex align-items-center secondary-menu text-center">
            <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
          </div>
        </div>
        <!-- end page title -->
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-xs-12 col-lg-12 m-b-30">
        <div class="card card-statistics h-100 mb-0">
          <div class="card-header d-flex justify-content-between">
            <div class="card-heading">
              <h4 class="card-title">Referral Code</h4>
            </div>
          </div>
          <div class="card-body">
            <p><?php echo $page_data->referral_code ?></p>
          </div>
          <div class="card-footer">
            <p>
              Note: any user you referred must perform a transaction, you would be credited #500 on your referral's first transaction
            </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-xs-12 col-md-6 m-b-30">
        <div class="card card-statistics h-100 mb-0">
          <div class="card-header d-flex justify-content-between">
            <div class="card-heading">
              <h4 class="card-title">Earning</h4>
            </div>
          </div>
          <div class="card-body">
            <p>#<?php echo 500 * $page_data->total_referral ?></p>
          </div>
        </div>
      </div>
      <div class="col-12 col-xs-12 col-md-6 m-b-30">
        <div class="card card-statistics h-100 mb-0">
          <div class="card-header d-flex justify-content-between">
            <div class="card-heading">
              <h4 class="card-title">Total Referrals</h4>
            </div>
          </div>
          <div class="card-body">
            <p><?php echo $page_data->total_referral ?></p>
          </div>
        </div>
      </div>
    </div>
  <?php
}

function em_rates_template()
{ ?>
    <!-- begin row -->
    <div class="row">
      <div class="col-md-12 m-b-30">
        <!-- begin page title -->
        <div class="d-block d-lg-flex flex-nowrap align-items-center">
          <div class="page-title mr-4 pr-4">
            <h1>Today's Rate</h1>
          </div>
          <div class="ml-auto d-flex align-items-center secondary-menu text-center">
            <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
          </div>
        </div>
        <!-- end page title -->
      </div>
    </div>
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
            $e_assets = hid_ex_m_get_all_e_currency_assets();
            if (empty($e_assets)) {
              echo "<p class='py-3 px-4'>Sorry, No Rates to display at the moment</p>";
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
                    foreach ($e_assets as $asset) { ?>
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
            $crypto_assets = hid_ex_m_get_all_crypto_currency_assets();
            if (empty($crypto_assets)) {
              echo "<p class='py-3 px-4'>Sorry, No Rates to display at the moment</p>";
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
                    foreach ($crypto_assets as $asset) { ?>
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
  <?php
}

function em_announcement_template($page_data)
{ ?>
    <div class="row">
      <div class="col-md-12 m-b-30">
        <!-- begin page title -->
        <div class="d-block d-lg-flex flex-nowrap align-items-center">
          <div class="page-title mr-4 pr-4">
            <h1>Announcement</h1>
          </div>
          <div class="ml-auto d-flex align-items-center secondary-menu text-center">
            <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
          </div>
        </div>
        <!-- end page title -->
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-xs-12 col-lg-12 m-b-30">
        <div class="card card-statistics h-100 mb-0">
          <div class="card-header d-flex justify-content-between">
            <div class="card-heading">
              <h4 class="card-title">Announcements</h4>
            </div>
          </div>
          <div class="card-body">
            <?php
            if (empty($page_data)) {
              echo "<p>No Announcements at this moment</p>";
            } else {
              foreach ($page_data as $announcement) {
                $headline = str_replace('\\', '', $announcement->headline);
                $body = str_replace('\\', '', $announcement->body);
                $dateTime = new DateTime(str_replace('\\', '', $announcement->time_stamp));
                $time = $dateTime->format('n - j - Y, H:i');
            ?>
                <div class="media p-2 py-3 announcement-card">
                  <div class="media-body">
                    <h4><?php echo $headline ?></h4>
                    <p><span class="badge badge-success"><?php echo $time ?></span></p>
                    <div class="pt-3">
                      <p><?php echo $body ?></p>
                      <p class="pt-2 font-13 font-italic">Announcement Published by <span class="text-primary">Admin</span></p>
                    </div>
                  </div>
                </div>
            <?php }
            } ?>
          </div>
        </div>
      </div>
    </div>
  <?php
}

function em_statement_template($page_data)
{ ?>
    <!-- begin row -->
    <div class="row">
      <div class="col-md-12 m-b-30">
        <!-- begin page title -->
        <div class="d-block d-lg-flex flex-nowrap align-items-center">
          <div class="page-title mr-4 pr-4">
            <h1>Transaction Statements</h1>
          </div>
          <div class="ml-auto d-flex align-items-center secondary-menu text-center">
            <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
          </div>
        </div>
        <!-- end page title -->
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-xs-12 col-lg-12 m-b-30">
        <div class="card card-statistics h-100 mb-0">
          <div class="card-header d-flex justify-content-between">
            <div class="card-heading">
              <h4 class="card-title">Statement</h4>
            </div>
          </div>
          <div class="card-body p-0">
            <?php
            if (empty($page_data)) {
              echo "<p class='py-3 px-4'>You haven't made any Transactions yet</p>";
            } else {
            ?>
              <div class="datatable-wrapper table-responsive">
                <table id="datatable-user" class="display compact table table-striped table-bordered">
                  <thead>
                    <tr>
                      <td></td>
                      <td>Asset Type</td>
                      <td>Asset</td>
                      <td>Order</td>
                      <td>Quantity</td>
                      <td>Fee</td>
                      <td>Rate</td>
                      <td>Status</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 0;
                    foreach ($page_data as $order) {
                      $i++;
                      $asset_type = "eCurr.";

                      if ($order->asset_type == 2) {
                        $asset_type = "Crypto";
                      }

                      $order_type = "<span class='badge l-bg-green'>Buy</span>";
                      if (isset($order->amount_to_recieve)) {
                        $order_type = "<span class='badge badge-danger'>Sell</span>";
                      }

                      $order_quantity = 0;
                      $rate = 0;
                      $money = 0;

                      if (isset($order->amount_to_recieve)) {
                        $order_quantity = $order->quantity_sold;
                        $rate = $order->amount_to_recieve / $order_quantity;
                        $money = $order->amount_to_recieve;
                      } elseif (isset($order->fee)) {
                        $order_quantity = $order->quantity;
                        $rate = $order->fee / $order_quantity;
                        $money = $order->fee;
                      }

                      $asset_display_name = hid_ex_m_get_asset_full_name($order->asset_type, $order->asset_id);

                      $rate = round($rate, 2);
                      $money = round($money, 2);
                      $order_quantity = round($order_quantity, 2);

                      $order_status = "<span class='badge badge-secondary'>Pending</span>";
                      if ($order->order_status == 0) {
                        $order_status = "<span class='badge badge-danger'>Declined</span>";
                      } elseif ($order->order_status == 2) {
                        $order_status = "<span class='badge badge-success'>Completed</span>";
                      }

                      $build_string = "<tr>";
                      $build_string .= "<td class='text-right'>$i.</td>";
                      $build_string .= "<td>$asset_type</td>";
                      $build_string .= "<td>$asset_display_name</td>";
                      $build_string .= "<td style='letter-spacing: .1rem;text-transform: uppercase;'>$order_type</td>";
                      $build_string .= "<td>$order_quantity</td>";
                      $build_string .= "<td>$money</td>";
                      $build_string .= "<td>$rate</td>";
                      $build_string .= "<td>$order_status</td>";
                      $build_string .= "</tr>";

                      echo $build_string;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php
}

function em_settings_template($page_data)
{
  $user = $page_data ?>
    <!-- begin row -->
    <div class="row">
      <div class="col-md-12 m-b-30">
        <!-- begin page title -->
        <div class="d-block d-lg-flex flex-nowrap align-items-center">
          <div class="page-title mr-4 pr-4">
            <h1>Account Settings</h1>
          </div>
          <div class="ml-auto d-flex align-items-center secondary-menu text-center">
            <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
          </div>
        </div>
        <!-- end page title -->
      </div>
    </div>
    <!--mail-Compose-contant-start-->
    <div class="row account-contant">
      <div class="col-12">
        <div class="card card-statistics">
          <div class="card-body p-0">
            <div class="row no-gutters">
              <div class="col-xl-12 col-md-12 col-12 border-t border-right">
                <div class="page-account-form">
                  <div class="form-titel border-bottom p-3">
                    <h5 class="mb-0 py-2">Edit Your Personal Settings</h5>
                  </div>
                  <div class="p-4">
                    <form method="POST" id="userProfileForm">
                      <div class="form-row">
                        <div class="form-group col-md-12">
                          <label for="firstName">First Name</label>
                          <input type="hidden" name="user-id" value="<?php echo $user['userId'] ?>">
                          <input type="text" name="first-name" class="form-control" id="firstName" value="<?php echo $user['userFirstName'] ?>">
                        </div>
                        <div class="form-group col-md-12">
                          <label for="lastName">Last Name</label>
                          <input type="text" name="last-name" class="form-control" id="lastName" value="<?php echo $user['userLastName'] ?>">
                        </div>
                        <div class="form-group col-md-12">
                          <label for="phoneNumber">Phone Number</label>
                          <input type="text" class="form-control" name="phone-number" id="phoneNumber" value="<?php echo $user['userPhone'] ?>">
                        </div>
                        <div class="form-group col-md-12">
                          <label for="email">Email</label>
                          <input type="email" class="form-control" name="email" id="email" value="<?php echo $user['userEmail'] ?>">
                        </div>
                      </div>
                      <div class="form-row border-top pt-2">
                        <div class="form-group col-md-12 disabled">
                          <label for="username">Username</label>
                          <input type="text" class="form-control" name="username" id="username" value="<?php echo $user['username'] ?>" disabled>
                        </div>
                        <div class="form-group col-md-12">
                          <label for="password">Password</label>
                          <input type="password" name="password" class="form-control" id="password">
                        </div>
                        <div class="form-group col-md-12">
                          <label for="retype-password">Retype Password</label>
                          <input type="password" class="form-control" id="retypePassword">
                        </div>
                      </div>
                      <p class="text-danger update-error-msg"></p>
                      <p class="text-success update-success-msg"></p>
                      <button type="submit" class="btn btn-primary w-100">Update Information</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--mail-Compose-contant-end-->
  <?php
}

function em_support_template($page_data)
{ ?>
    <!-- begin row -->
    <div class="row">
      <div class="col-md-12 m-b-30">
        <!-- begin page title -->
        <div class="d-block d-lg-flex flex-nowrap align-items-center">
          <div class="page-title mr-4 pr-4">
            <h1>Support</h1>
          </div>
          <div class="ml-auto d-flex align-items-center secondary-menu text-center">
            <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 70px;">
          </div>
        </div>
        <!-- end page title -->
      </div>
    </div>
    <!--mail-Compose-contant-start-->
    <div class="row">
      <div class="col-12">
        <div class="card card-statistics">
          <div class="card-body p-0">
            <div class="row no-gutters">
              <div class="col-xl-6 col-md-6 col-12 border-t border-right">
                <div class="page-account-form">
                  <div class="form-titel border-bottom p-3">
                    <h5 class="mb-0 py-2">Open a Support Ticket</h5>
                  </div>
                  <div class="p-4">
                    <form method="post" action="" id="ticket-form">
                      <input type="hidden" name="customer" value="<?php echo $page_data["user"]["ID"] ?>">
                      <input type="hidden" name="customer-name" value="<?php echo $page_data["user"]["display_name"] ?>">

                      <div class="form-row">
                        <div class="form-group col-md-12">
                          <label for="title">Title</label>
                          <input type="text" class="form-control" id="title" name="ticket-title">
                        </div>
                        <div class="form-group col-md-12">
                          <label for="details">Details</label>
                          <textarea class="form-control" name="ticket-details" id="details"></textarea>
                        </div>
                        <p class="text-danger form-error-msg"></p>
                        <p class="text-success form-success-msg"></p>
                        <div class="form-group col-md-12">
                          <button type="submit" class="btn btn-primary w-100">Open Ticket</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="border-top border-right">
                  <div class="page-account-form">
                    <div class="form-titel border-bottom p-3">
                      <h5 class="mb-0 py-2">Existing Tickets</h5>
                    </div>
                    <div class="p-4">
                      <?php
                      if (empty($page_data['data'])) {
                        echo "<p>You don't have any opened tickets at the moment</p>";
                      } else {
                        foreach ($page_data['data'] as $ticket) {
                          $ticketTitle = str_replace('\\', '', $ticket->title);
                          if (strlen($ticketTitle) > 30) {
                            $ticketTitle = substr($ticketTitle, 0, 30) . " ...";
                          }
                          $ticketDetails = str_replace('\\', '', $ticket->details);
                          if (strlen($ticketDetails) > 50) {
                            $ticketDetails = substr($ticketDetails, 0, 50) . " ...";
                          } ?>
                          <div class="media p-2 py-3 announcement-card" style="max-width:100%;overflow:hidden">
                            <div class="media-body">
                              <h4 style="max-width:100%;text-align:justify;"><?php echo $ticketTitle ?></h4>
                              <div class="pt-3">
                                <p style="max-width:100%;text-align:justify;"><?php echo $ticketDetails ?></p>
                                <p class="msg-open">
                                  <span ticket='<?php echo $ticket->id ?>' ticketTitle='<?php echo $ticketTitle ?>' class="open-chat"> Open Ticket &#8594;</span> | Requested by <?php echo $ticket->requester ?>
                                </p>
                              </div>
                            </div>
                          </div>
                      <?php }
                      } ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-6 pb-xl-0 pb-5 border-top border-right chat-div">
                <div class="app-chat-msg">
                  <div class="d-flex align-items-center justify-content-between p-3 px-4 border-bottom">
                    <div class="app-chat-msg-title">
                      <h4 class="mb-0 chat-title">Chat Box</h4>
                    </div>
                  </div>
                  <div class="scrollbar scroll_dark app-chat-msg-chat p-4">
                    <div class="chat-box">
                      <div class="d-flex justify-content-center align-items-center" style="height: 70vh;">
                        <img src="<?php echo EMURL . "assets/imgs/logo-edited.png" ?>" alt="logo" style="max-width: 50%;">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="app-chat-type">
                  <form action="" id="send-chat">
                    <p class="text-primary attachment-text text-center"></p>
                    <div class="input-group mb-0 ">
                      <div class="input-group-prepend d-sm-flex">
                        <input type="file" id="attachmentImg" style="opacity:0;visibility:hidden;position:absolute;z-index:-99;" accept="image/png, image/jpg, image/jpeg">
                        <span class="input-group-text attachment">
                          <i class="fa fa-file-upload">
                          </i>
                        </span>
                      </div>
                      <input class="form-control chat-text" placeholder="Type here..." type="text">
                      <input type="hidden" id="sender" value="<?php echo $page_data["user"]["display_name"] ?>">
                      <div class="input-group-prepend">
                        <span class="input-group-text send-btn">
                          <i class="fa fa-paper-plane"></i>
                        </span>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--mail-Compose-contant-end-->
  <?php
}

function em_error_template()
{ ?>
    <div class="container p-5 h-100-vh">
      <?php require_once(EMPATH . "/templates/user/em-error.php"); ?>
    </div>
  <?php
}
