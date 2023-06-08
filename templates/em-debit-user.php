<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminDebitWallet')) {
  class LuxAdminDebitWallet
  {
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_debit_wallet_template()
    {
      if (isset($_POST['debit-user'])) {
        $data = array(
          'customer_id'           => $_POST['customer'],
          'price'                 => $_POST['price'],
        );

        $this->lux_dbh->lux_create_admin_withdrawal($data);

        $notify = [
          'customer_id' => $_POST["customer"],
          'title' => 'Admin Charge',
          'msg' => 'You have been debited ' . $_POST['price'] . ' from the admin!'
        ];

        $this->lux_dbh->lux_set_notification($notify);
        $data = [
          'title' => 'Admin Charge',
          'body' => 'You have been debited ' . $_POST['price'] . ' from the admin!'
        ];
        LuxUtils::lux_push_notification($_POST['customer'], $data);
        echo "<script>location.replace('admin.php?page=lux-debit-wallet');</script>";
      } ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
          </div>
        </section>
        <section class="exchange-manager-wrapper-body">
          <div class="container pt-1">
            <?php $all_customers = hid_ex_m_get_all_customers() ?>
            <form action="<?php echo admin_url("admin.php?page=lux-debit-wallet") ?>" method="POST">
              <div class="card text-dark">
                <div class="card-header">
                  <h4>Debit Customer</h4>
                </div>
                <div class="card-body">
                  <div class="form-group row">
                    <label for="customer_name" class="col-sm-4 col-form-label">Customer Name</label>
                    <div class="col-sm-8">
                      <div class="selects-contant">
                        <div class="select-display">
                          <p class="select-render">Select Customer</p>
                        </div>
                        <div class="select-dropdown">
                          <div class="select-search">
                            <input type="search" id="select-search" placeholder="Search Customer..">
                            <input type="hidden" name="customer" id="customer_name">
                          </div>
                          <ul class="select-list">
                            <?php
                            foreach ($all_customers as $customer) {
                              $first_name = $customer->data->display_name;
                              $last_name = ucfirst($customer->data->user_nicename); ?>
                              <li data-value="<?php echo $customer->ID ?>"><?php echo "$first_name $last_name" ?></li>
                            <?php } ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="price" class="col-sm-4 col-form-label">Charge</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" style="max-width: 25rem;" id="price" name="price" />
                    </div>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                  <button class="btn btn-primary l-bg-green mr-1" name="debit-user" type="submit">
                    Debit
                  </button>
                  <button class="btn btn-secondary" type="reset">Reset</button>
                </div>
              </div>
            </form>
          </div>
        </section>
      </main>
<?php
    }
  }
}
