<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminWallet')) {
  class LuxAdminWallet
  {
    private string $get_tab = '';
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_wallet_template()
    {
      if (isset($_GET['tab'])) {
        $this->get_tab = $_GET['tab'];
      } ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <?php
            if ($this->get_tab == 'deposits') {
              $this->lux_admin_wallet_deposits();
            } elseif ($this->get_tab == 'withdrawals') {
              $this->lux_admin_wallet_withdrawal();
            } elseif ($this->get_tab == 'customers') {
              $this->lux_admin_customer_wallet_details();
            } else {
              $this->lux_admin_wallet_master_home();
            } ?>
          </div>
        </section>
      </main>
    <?php
    }

    public function lux_admin_customer_wallet_details()
    {
      if (isset($_GET['enable-withdrawal'])) {
        $customer_id = $_GET['enable-withdrawal'];
        update_user_meta($customer_id, 'can_withdraw', 1);
        $this->lux_dbh->lux_set_notification([
          'customer_id' => $customer_id,
          'title' => 'Withdrawal',
          'msg' => 'Withdrawal enabled, you can now withdraw'
        ]);

        $data = [
          'title' => 'Withdrawal',
          'body' => 'Withdrawal enabled, you can now withdraw'
        ];
        LuxUtils::lux_push_notification($customer_id, $data);

        echo "<script>location.replace('admin.php?page=lux-wallet&tab=customers');</script>";
      }
      if (isset($_GET['disable-withdrawal'])) {
        $customer_id = $_GET['disable-withdrawal'];
        update_user_meta($customer_id, 'can_withdraw', 0);
        $this->lux_dbh->lux_set_notification([
          'customer_id' => $customer_id,
          'title' => 'Withdrawal',
          'msg' => 'Withdrawal disabled, you can not withdraw'
        ]);

        $data = [
          'title' => 'Withdrawal',
          'body' => 'Withdrawal disabled, you can not withdraw'
        ];
        LuxUtils::lux_push_notification($customer_id, $data);
        echo "<script>location.replace('admin.php?page=lux-wallet&tab=customers');</script>";
      } ?>
      <div class="row">
        <div class="col-12 mb-5">
          <div class="card h-100 mb-0">
            <div class="card-header">
              <h4 class="card-title">Customers Wallet Management</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-borderless mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th>Full Name</th>
                      <th>Account Balance</th>
                      <th>Approved Deposits</th>
                      <th>Approved Withdrawals</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody class="text-muted mb-0">
                    <?php
                    $all_customers = hid_ex_m_get_all_customers();
                    if (!empty($all_customers)) {
                      foreach ($all_customers as $customer) {
                        $bal = hid_ex_m_get_account_balance($customer->ID);
                        $deposits = hid_ex_m_get_customer_approved_transactions($customer->ID, 1);
                        $withdrawals = hid_ex_m_get_customer_approved_transactions($customer->ID, 2);
                        $can_withdraw = hid_ex_m_get_withdrawal_status($customer->ID);
                        $enable_url = admin_url("admin.php?page=lux-wallet&tab=customers&enable-withdrawal=$customer->ID");
                        $disable_url = admin_url("admin.php?page=lux-wallet&tab=customers&disable-withdrawal=$customer->ID");
                        $first_name = $customer->data->display_name;
                        $last_name = ucfirst($customer->data->user_nicename);

                        echo "<tr><td>$first_name $last_name</td>";
                        echo "<td># $bal</td>";
                        echo "<td># $deposits</td>";
                        echo "<td># $withdrawals</td>";
                        if ($can_withdraw == 1) {
                          echo "<td><a class='btn l-bg-green btn-sm mx-1' href='$disable_url'>Disable Withdrawals</a></td></tr>";
                        } else {
                          echo "<td><a class='btn l-bg-green btn-sm mx-1' href='$enable_url'>Enable Withdrawals</a></td></tr>";
                        }
                      }
                    } else { ?>
                      <p>No Customers to display</p>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

    public function lux_admin_wallet_withdrawal()
    {
      if (isset($_GET['action'])) {
        $transaction_id = $_GET['id'];
        $action = $_GET['action'];
        $transaction = hid_ex_m_get_wallet_transaction_data($transaction_id);
        $customer = $transaction->customer_id;
        $old_balance = hid_ex_m_get_account_balance($customer);
        echo "<br><br>";
        if ($action == $transaction->transaction_status) {
          echo "<span><strong>Target transaction status same as old</strong></span>";
        } else if ($action == 2) {
          if ($old_balance > $transaction->amount) {
            $new_balance = $old_balance - $transaction->amount;
            update_user_meta($customer, 'account_balance', $new_balance);
            hid_ex_m_update_transaction_status($action, $transaction_id);
            echo "<span><strong>Status Updated Successfully<br>Account Balance Updated successfully</strong></span>";
            $this->lux_dbh->lux_set_notification([
              'customer_id' => $customer,
              'title' => 'Withdrawal',
              'msg' => 'Transaction approved successfully, check your wallet balance'
            ]);

            $data = [
              'title' => 'Withdrawal',
              'body' => 'Transaction approved successfully, check your wallet balance'
            ];
            LuxUtils::lux_push_notification($customer, $data);
          } else {
            echo "<span><strong>Insufficient Balance</strong></span>";
            $this->lux_dbh->lux_set_notification([
              'customer_id' => $customer,
              'title' => 'Withdrawal',
              'msg' => 'Sorry, insufficient balance'
            ]);
            $data = [
              'title' => 'Withdrawal',
              'body' => 'Sorry, insufficient balance'
            ];
            LuxUtils::lux_push_notification($customer, $data);
          }
        } else if ($transaction->transaction_status == 2) {
          $new_balance = $old_balance + $transaction->amount;
          update_user_meta($customer, 'account_balance', $new_balance);
          hid_ex_m_update_transaction_status($action, $transaction_id);
          echo "<span><strong>Status Updated Successfully</strong></span>";
        } else {
          hid_ex_m_update_transaction_status($action, $transaction_id);
          echo "<span><strong>Status Updated Successfully</strong></span>";
        }
        echo "<script>location.replace('admin.php?page=lux-wallet&tab=withdrawals');</script>";
      }

      $all_withdrawals = hid_ex_m_get_all_withdrawals(); ?>
      <div class="row">
        <div class="col-12 mb-5">
          <div class="card h-100 mb-0">
            <div class="card-header">
              <h4 class="card-title">Withdrawal Requests</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-borderless mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th width='10%'>Customer</th>
                      <th width='15%'>Time</th>
                      <th width='10%'>Amount</th>
                      <th width='20%'>Details</th>
                      <th width='25%'>Instructions</th>
                      <th width='20%'>Change Status</th>
                    </tr>
                  </thead>
                  <tbody class="text-muted mb-0">
                    <?php
                    if (!empty($all_withdrawals)) {
                      foreach ($all_withdrawals as $transaction) {
                        $customer = hid_ex_m_get_customer_data_name($transaction->customer_id);
                        $time = $transaction->time_stamp;
                        $amount = floatval($transaction->amount);
                        $details = $transaction->details;
                        $cap = 55;

                        if (strlen($details) > $cap) {
                          $details = substr($details, 0, $cap) . " ...";
                        }

                        $instruction = $transaction->sending_instructions;
                        $status = $transaction->transaction_status;
                        $decline_url = admin_url("admin.php?page=lux-wallet&tab=withdrawals&action=0&id=$transaction->id");
                        $pending_url = admin_url("admin.php?page=lux-wallet&tab=withdrawals&action=1&id=$transaction->id");
                        $completed_url = admin_url("admin.php?page=lux-wallet&tab=withdrawals&action=2&id=$transaction->id");

                        echo "<tr>";
                        echo "<td style='white-space: normal;'>$customer</td>";
                        echo "<td style='white-space: normal;'>$time</td>";
                        echo "<td style='white-space: normal;'># $amount</td>";
                        echo "<td style='white-space: normal;'>$details</td>";
                        echo "<td style='white-space: normal;'>$instruction</td>";
                        echo "<td style='white-space: nowrap;'>";
                        if ($status != 0) {
                          echo "<a class='btn l-bg-green btn-sm mx-1' href='$decline_url'>Decline</a>";
                        }
                        if ($status != 1) {
                          echo "<a class='btn l-bg-green btn-sm mx-1' href='$pending_url'>Pending</a>";
                        }
                        if ($status != 2) {
                          echo "<a class='btn l-bg-green btn-sm mx-1' href='$completed_url'>Approved</a>";
                        }
                        echo "</tr>";
                      }
                    } else { ?>
                      <p>No Withdrawals Transactions to display</p>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

    public function lux_admin_wallet_deposits()
    {
      if (isset($_GET['action'])) {
        $transaction_id = $_GET['id'];
        $action = $_GET['action'];
        $transaction = hid_ex_m_get_wallet_transaction_data($transaction_id);
        $customer = $transaction->customer_id;
        $old_balance = hid_ex_m_get_account_balance($customer);
        echo "<br><br>";
        if ($action == $transaction->transaction_status) {
          echo "<span><strong>Target transaction status same as old</strong></span>";
        } else if ($action == 2) {
          $new_balance = $old_balance + $transaction->amount;
          update_user_meta($customer, 'account_balance', $new_balance);
          hid_ex_m_update_transaction_status($action, $transaction_id);
          echo "<span><strong>Status Updated Successfully<br>Account Balance Updated successfully</strong></span>";
          $this->lux_dbh->lux_set_notification([
            'customer_id' => $customer,
            'title' => 'Deposit',
            'msg' => 'Transaction approved successfully, check your wallet balance'
          ]);
          $data = [
            'title' => 'Deposit',
            'body' => 'Transaction approved successfully, check your wallet balance'
          ];
          LuxUtils::lux_push_notification($customer, $data);
        } else if ($transaction->transaction_status == 2) {
          $new_balance = $old_balance - $transaction->amount;
          update_user_meta($customer, 'account_balance', $new_balance);
          hid_ex_m_update_transaction_status($action, $transaction_id);
          echo "<span><strong>Status Updated Successfully</strong></span>";
        } else {
          hid_ex_m_update_transaction_status($action, $transaction_id);
          echo "<span><strong>Status Updated Successfully</strong></span>";
        }
        echo "<script>location.replace('admin.php?page=lux-wallet&tab=deposits');</script>";
      }

      $all_deposits = hid_ex_m_get_all_deposits(); ?>
      <div class="row">
        <div class="col-12 mb-5">
          <div class="card h-100 mb-0">
            <div class="card-header">
              <h4 class="card-title">Deposit Requests</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-borderless mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th>Customer</th>
                      <th>Time</th>
                      <th>Amount</th>
                      <th>Details</th>
                      <th>Proof of payment</th>
                      <th>Change Status</th>
                    </tr>
                  </thead>
                  <tbody class="text-muted mb-0">
                    <?php
                    if (!empty($all_deposits)) {
                      foreach ($all_deposits as $transaction) {
                        $customer = hid_ex_m_get_customer_data_name($transaction->customer_id);
                        $time = $transaction->time_stamp;
                        $amount = $transaction->amount;
                        $details = $transaction->details;
                        $cap = 55;

                        if (strlen($details) > $cap) {
                          $details = substr($details, 0, $cap) . " ...";
                        }

                        $proof_url = wp_get_attachment_url($transaction->proof_of_payment);
                        $status = $transaction->transaction_status;
                        $decline_url = admin_url("admin.php?page=lux-wallet&tab=deposits&action=0&id=$transaction->id");
                        $pending_url = admin_url("admin.php?page=lux-wallet&tab=deposits&action=1&id=$transaction->id");
                        $completed_url = admin_url("admin.php?page=lux-wallet&tab=deposits&action=2&id=$transaction->id");

                        echo "<tr><td>$customer</td>";
                        echo "<td>$time</td>";
                        echo "<td># $amount</td>";
                        echo "<td>$details</td>";
                        echo "<td><a href='$proof_url' target='_blank'>View Proof of Payment</a></td>";
                        echo "<td>";
                        if ($status != 0) {
                          echo "<a class='btn l-bg-green btn-sm mx-1' href='$decline_url'>Decline</a>";
                        }
                        if ($status != 1) {
                          echo "<a class='btn l-bg-green btn-sm mx-1' href='$pending_url'>Pending</a>";
                        }
                        if ($status != 2) {
                          echo "<a class='btn l-bg-green btn-sm mx-1' href='$completed_url'>Approved</a>";
                        }
                        echo "</td></tr>";
                      }
                    } else { ?>
                      <p>No Deposit Transactions to display</p>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

    public function lux_admin_wallet_master_home()
    {
      if (isset($_POST['submit-bank'])) {
        hid_ex_m_update_wallet_bank($_POST['local-bank']);
      }
      $page_data = hid_ex_m_get_admin_wallet_page_data() ?>
      <div class="row">
        <div class="col-12 m-b-30">
          <div class="card text-dark h-100 mb-0 apexchart-tool-force-top">
            <div class="card-header d-flex justify-content-between">
              <div class="card-heading">
                <h4 class="card-title" style="font-weight: 700;text-transform: capitalize;">Site activity</h4>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 col-xs-6 col-lg-3">
                  <div class="row align-items-end">
                    <div class="col">
                      <p style="font-weight: 700;text-transform: capitalize;">Customers</p>
                      <h3 class="tex-dark mb-0" style="font-weight: 700;text-transform: capitalize;"><?php echo $page_data['total_customers'] ?></h3>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-xs-6 col-lg-3">
                  <div class="row align-items-end">
                    <div class="col">
                      <p style="font-weight: 700;text-transform: capitalize;">Pending Transactions</p>
                      <h3 class="tex-dark mb-0" style="font-weight: 700;text-transform: capitalize;"><?php echo $page_data['total_pending'] ?></h3>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-xs-6 col-lg-3">
                  <div class="row align-items-end">
                    <div class="col">
                      <p style="font-weight: 700;text-transform: capitalize;">Declined Transactions</p>
                      <h3 class="tex-dark mb-0" style="font-weight: 700;text-transform: capitalize;"><?php echo $page_data['total_declined'] ?></h3>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-xs-6 col-lg-3">
                  <div class="row align-items-end">
                    <div class="col">
                      <p style="font-weight: 700;text-transform: capitalize;">Completed Transactions</p>
                      <h3 class="tex-dark mb-0" style="font-weight: 700;text-transform: capitalize;"><?php echo $page_data['percentage_completed'] ?>%</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <div class="row">
                <div class="col-6">
                  <div class="withdrawal-deposit-btn">
                    <a class="button l-bg-green" style="border: none; font-weight: 700;" href="<?php echo admin_url("admin.php?page=lux-wallet&tab=withdrawals"); ?>">View all Withdrawals</a>
                  </div>
                </div>
                <div class="col-6 d-flex justify-content-end">
                  <div class="withdrawal-deposit-btn">
                    <a class="button l-bg-green" style="border: none; font-weight: 700;" href="<?php echo admin_url("admin.php?page=lux-wallet&tab=deposits"); ?>">View all Deposits</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-xxl-12 m-b-30">
          <div class="row">
            <div class="col-12 m-b-30">
              <div class="card text-dark h-100 mb-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <?php
                      $all_banks = hid_ex_m_get_all_banks();
                      if (empty($all_banks)) { ?>
                        <h3 class="text-center">There are no local bank accounts to choose from.<br>Create Local Bank Accounts <a href="<?php echo admin_url('admin.php?page=e-currency-management&tab=local-banks'); ?>">Here</a></h3>
                      <?php } else { ?>
                        <div class="select-bank text-center">
                          <h3 class="text-center">Select the Local Bank account to be used for funding wallets</h3>
                          <form action="" method="POST">
                            <select name="local-bank" id="local-bank">
                              <?php
                              foreach ($all_banks as $bank) {
                                $bank_id = $bank->id;
                                $bank_display_name = $bank->display_name;
                                $selected = "";
                                if (get_option('wallet_local_bank')) {
                                  $prev = get_option('wallet_local_bank');
                                  if ($prev == $bank_id) {
                                    $selected = "selected";
                                  }
                                }
                                echo "<option value=$bank_id $selected>$bank_display_name</option>";
                              }
                              ?>
                            </select>
                            <input class="button l-bg-green" style="border: none; font-weight: 700;" type="submit" value="Submit" name="submit-bank" id="submit-bank">
                          </form>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  }
}
