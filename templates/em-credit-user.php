<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminCreditWallet')) {
  class LuxAdminCreditWallet
  {
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_credit_wallet_template()
    {
      if (isset($_POST['credit-user'])) {
        $data = array(
          'customer_id'           => $_POST['customer'],
          'price'                 => $_POST['price'],
        );

        $this->lux_dbh->lux_create_admin_funding($data);

        $notify = [
          'customer_id' => $_POST["customer"],
          'title' => 'Admin Charge',
          'msg' => 'You have been credited ' . $_POST['price'] . ' by the admin!'
        ];

        $this->lux_dbh->lux_set_notification($notify);
        $data = [
          'title' => 'Admin Charge',
          'body' => 'You have been credited ' . $_POST['price'] . ' by the admin!'
        ];
        LuxUtils::lux_push_notification($_POST['customer'], $data);
        echo "<script>location.replace('admin.php?page=lux-credit-wallet');</script>";
      } ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
          </div>
        </section>
        <section class="exchange-manager-wrapper-body">
          <div class="container pt-1">
            <?php $all_customers = hid_ex_m_get_all_customers() ?>
            <form action="<?php echo admin_url("admin.php?page=lux-credit-wallet") ?>" method="POST">
              <div class="card text-dark">
                <div class="card-header">
                  <h4>Credit Customer</h4>
                </div>
                <div class="card-body">
                  <div class="form-group row">
                    <label for="customer_name" class="col-sm-4 col-form-label">Customer Name</label>
                    <div class="col-sm-8">
                      <div class="selects-contant">
                        <select class="js-basic-single form-control" name="customer" id="customer_name">
                          <?php
                          foreach ($all_customers as $customer) {
                            $first_name = $customer->data->display_name;
                            $last_name = ucfirst($customer->data->user_nicename); ?>
                            <option value="<?php echo $customer->ID ?>"><?php echo "$first_name $last_name" ?></option>
                          <?php } ?>
                        </select>
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
                  <button class="btn btn-primary l-bg-green mr-1" name="credit-user" type="submit">
                    Credit
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
