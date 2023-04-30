<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminReferralPage')) {
  class LuxAdminReferralPage
  {
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_referral_template()
    { ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <div class="flex-start">
              <h3 class="text-bold">Referrals</h3>
            </div>
          </div>
        </section>
        <section class="exchange-manager-wrapper-body">
          <?php $customers = $this->lux_dbh->lux_get_all_referrals() ?>
          <div class="container pt-1">
            <div class="row">
              <div class="col-12">
                <div class="card text-dark">
                  <div class="card-header">
                    <h5 class="text-bold">Customers Referral Table.</h5>
                  </div>
                  <div class="card-body p-0">
                    <?php if (!empty($customers)) {  ?>
                      <div class="table-responsive py-3">
                        <table class="table table-striped" id="database-table">
                          <thead>
                            <tr>
                              <th>id</th>
                              <th>Customer's Name</th>
                              <th>Referral Code</th>
                              <th>Referral</th>
                              <th>Total Referrals</th>
                              <th>Earnings</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $i = 0;
                            foreach ($customers as $customer) {
                              $i++;
                              $id = $customer->customer_id;
                              $customer_name = $this->lux_dbh->lux_get_single_customer($id);
                              $referral_code = ($customer->referral_code);
                              $referral = $customer->referral;
                              if ($referral == '') {
                                $referral = '<div class="badge badge-danger l-bg-green text-capitalize badge-shadow">null</div>';
                              }
                              $total_referrals = $customer->total_referral;
                              $earnings = 500 * $customer->total_referral; ?>
                              <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo $customer_name ?></td>
                                <td><?php echo $referral_code ?></td>
                                <td><?php echo $referral ?></td>
                                <td><?php echo $total_referrals ?></td>
                                <td><?php echo "#" . $earnings ?></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    <?php } else { ?>
                      <p class="lead text-dark pl-4 pt-2">Sorry, No customer details to display.</p>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
<?php }
  }
}
