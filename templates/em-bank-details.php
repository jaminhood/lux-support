<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminBankInfoPage')) {
  class LuxAdminBankInfoPage
  {
    private int $get_id = 0;
    private string $get_tab = '';
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_bank_info_template()
    {
      if (isset($_GET['tab'])) {
        $this->get_tab = $_GET['tab'];
      }

      if (isset($_GET['id'])) {
        $this->get_id = $_GET['id'];
      }

      if (isset($_POST['update_form'])) {
        $details = [
          'bankName' => $_POST['bankName'],
          'bankAccountNumber' => $_POST['acctNumber'],
          'bankAccountName' => $_POST['acctName']
        ];

        $where = ['id' => $_POST['id']];
        $this->lux_dbh->lux_update_bank_details($details, $where);
        echo "<script>location.replace('admin.php?page=lux-bank-details&success=update&user=" . $_POST['customerName'] . "');</script>";
        die();
      }  ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <div class="flex-start">
              <h3 class="text-bold">Bank Details.</h3>
            </div>
          </div>
        </section>
        <section class="exchange-manager-wrapper-body">
          <?php
          if ($this->get_tab === 'update') {
            $this->lux_update_component($this->get_id);
          } else {
            $this->lux_customers_component();
          }
          ?>
        </section>
      </main>
      <?php
    }

    public function lux_update_component($id)
    {
      $customer = $this->lux_dbh->lux_get_bank_details($id);
      foreach ($customer as $data) { ?>
        <form action="<?php echo admin_url("admin.php?page=lux-bank-details") ?>" method="POST">
          <div class="card text-dark">
            <div class="card-header">
              <h4><?php echo $data->displayName ?> Bank Details settings</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="bankName">Bank Name</label>
                <input name="id" type="hidden" value="<?php echo $id ?>">
                <input name="customerName" type="hidden" value="<?php echo $data->displayName ?>">
                <input type="text" class="form-control" id="bankName" name="bankName" value="<?php echo $data->bankName ?>" />
              </div>
              <div class="form-group">
                <label for="acctName">Account Name</label>
                <input type="text" class="form-control" id="acctName" name="acctName" value="<?php echo $data->bankAccountName ?>" />
              </div>
              <div class="form-group">
                <label for="acctNumber">Account Number</label>
                <input type="number" class="form-control" id="acctNumber" name="acctNumber" value="<?php echo $data->bankAccountNumber ?>" />
              </div>
            </div>
            <div class="card-footer text-right">
              <button class="btn btn-primary mr-1" name="update_form" type="submit">
                Update
              </button>
              <button class="btn btn-secondary" type="reset">Reset</button>
            </div>
          </div>
        </form>
      <?php
      }
    }

    public function lux_customers_component()
    {
      $null_msg = '<div class="badge badge-danger text-uppercase badge-shadow">not set</div>';
      $customers = $this->lux_dbh->lux_get_all_bank_details() ?>
      <div class="container pt-1">
        <div class="row">
          <div class="col-12">
            <div class="card text-dark">
              <div class="card-header">
                <h5 class="text-bold">Customer Bank Details Table.</h5>
              </div>
              <div class="card-body">
                <?php if (!empty($customers)) {  ?>
                  <div class="table-responsive">
                    <table class="table table-striped" id="database-table">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Fullname</th>
                          <th>Bank Name</th>
                          <th>Account Name</th>
                          <th>Account Number</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $i = 0;
                        foreach ($customers as $customer) {
                          $i++;
                          $updateUrl = admin_url("admin.php?page=lux-bank-details&tab=update&id=$customer->id");

                          $name = $customer->displayName;
                          $bank_name = ucfirst($customer->bankName);
                          $account_number = $customer->bankAccountNumber;
                          $account_name = $customer->bankAccountName;

                          if ($bank_name === '') $bank_name = $null_msg;
                          if ($account_number === '') $account_number = $null_msg;
                          if ($account_name === '') $account_name = $null_msg; ?>
                          <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $name ?></td>
                            <td><?php echo $bank_name ?></td>
                            <td><?php echo $account_name ?></td>
                            <td><?php echo $account_number ?></td>
                            <td>
                              <a href="<?php echo $updateUrl ?>" class="btn btn-primary l-bg-green btn-action mr-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } else { ?>
                  <p class="lead text-dark pl-4 pt-2">Sorry, No customer details to display.</p>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  }
}
