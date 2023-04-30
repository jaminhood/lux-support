<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminBarcodePage')) {
  class LuxAdminBarcodePage
  {
    private int $get_id = 0;
    private string $get_tab = '';
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_barcode_template()
    {
      if (isset($_GET['tab'])) {
        $this->get_tab = $_GET['tab'];
      }

      if (isset($_GET['id'])) {
        $this->get_id = $_GET['id'];
      }

      if (isset($_POST['add_form'])) {
        $data = [
          'mode' => $_POST['asset-type'],
          'asset_id' => $_POST['asset'],
          'barcode' => $_POST['barcode-id'],
        ];

        $this->lux_dbh->lux_set_barcode($data);
        echo "<script>location.replace('admin.php?page=lux-barcode');</script>";
        die();
      }

      if (isset($_POST['update_form'])) {
        $data = [
          'mode' => $_POST['asset-type'],
          'asset_id' => $_POST['asset'],
          'barcode' => $_POST['barcode-id'],
        ];
        $where = ['id' => $_POST['id']];
        $this->lux_dbh->lux_update_barcode($data, $where);
        echo "<script>location.replace('admin.php?page=lux-barcode');</script>";
        die();
      }  ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-body">
          <?php
          if ($this->get_tab === 'add-new') {
            $this->lux_add_barcode();
          } else if ($this->get_tab === 'update') {
            $this->lux_update_barcode();
          } else {
            $this->lux_barcodes();
          }
          ?>
        </section>
      </main>
    <?php
    }

    public function lux_update_barcode()
    {
      $barcode = $this->lux_dbh->lux_get_barcode($_GET['id']) ?>
      <div class="row">
        <div class="col-12">
          <form action="<?php echo admin_url("admin.php?page=lux-barcode") ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $barcode->id ?>">
            <div class="card">
              <div class="card-header">
                <h3>Update Barcode</h3>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="asset-type" class="col-sm-2 col-form-label">Select Asset Type</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="asset-type" name="asset-type">
                      <option value='<?php echo $barcode->mode ?>'>
                        <?php
                        if ($barcode->mode == 1) {
                          echo 'E-currency';
                        } elseif ($barcode->mode == 2) {
                          echo 'Crypto-currency';
                        }
                        ?>
                      </option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="asset" class="col-sm-2 col-form-label">Select Asset</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="asset" name="asset">
                      <?php
                      if ($barcode->mode == 1) {
                        $currency_data = hid_ex_m_get_e_currency_data($barcode->asset_id) ?>
                        <option value=<?php echo $currency_data->id ?>><?php echo $currency_data->name . ' | ' . $currency_data->short_name ?> </option>
                      <?php } elseif ($barcode->mode == 2) {
                        $currency_data = hid_ex_m_get_crypto_currency_data($barcode->asset_id) ?>
                        <option value=<?php echo $currency_data->id ?>><?php echo $currency_data->name . ' | ' . $currency_data->short_name ?> </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="barcode" class="col-sm-2 col-form-label">Barcode</label>
                  <div class="col-sm-10">
                    <div class="custom-file" style="max-width: 25rem;">
                      <input type="file" name="barcode" id="barcode" class="custom-file-input">
                      <label for=" barcode" id="barcode-label" class="custom-file-label"><?php echo wp_get_attachment_url($barcode->barcode) ?></label>
                      <input type="hidden" id="barcode-id" name="barcode-id" value="<?php echo $barcode->barcode ?>">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" name="update_form" class="btn l-bg-green">Add barcode</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    public function lux_add_barcode()
    { ?>
      <div class="row">
        <div class="col-12">
          <form action="<?php echo admin_url("admin.php?page=lux-barcode") ?>" method="POST">
            <div class="card">
              <div class="card-header">
                <h3>Add Barcode</h3>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="asset-type" class="col-sm-2 col-form-label">Select Asset Type</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="asset-type" name="asset-type">
                      <option selected value="">Select Asset Type</option>
                      <option value="1">E-currency</option>
                      <option value="2">Crypto-currency</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="asset" class="col-sm-2 col-form-label">Select Asset</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="asset" name="asset">
                      <option selected value="">Select Asset</option>
                      <option value="1">Bitcoin</option>
                      <option value="2">Etherum</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="barcode" class="col-sm-2 col-form-label">Barcode</label>
                  <div class="col-sm-10">
                    <div class="custom-file" style="max-width: 25rem;">
                      <input type="file" name="barcode" id="barcode" class="custom-file-input">
                      <label for="barcode" id="barcode-label" class="custom-file-label">Choose File</label>
                      <input type="hidden" id="barcode-id" name="barcode-id">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" name="add_form" class="btn l-bg-green">Add barcode</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    public function lux_barcodes()
    {
      $add_url = admin_url("admin.php?page=lux-barcode&tab=add-new") ?>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-6">
                  <div class="flex-start">
                    <h3>Barcodes</h3>
                  </div>
                </div>
                <div class="col-6 text-right">
                  <a href="<?php echo $add_url ?>" class="btn btn-danger text-bold l-bg-green">Add barcode</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-borderless mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th></th>
                      <th>Currency</th>
                      <th>Asset</th>
                      <th>Barcode</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody class="text-muted mb-0">
                    <?php
                    $all_barcodes = $this->lux_dbh->lux_get_barcodes();
                    if (!empty($all_barcodes)) {
                      $i = 0;
                      foreach ($all_barcodes as $barcode) {
                        $i++;
                        $currency = '';
                        $currency_data = '';
                        $asset = '';
                        if ($barcode->mode == 1) {
                          $currency = 'E-currency';
                          $currency_data = hid_ex_m_get_e_currency_data($barcode->asset_id);
                          $asset = $currency_data->name . ' | ' . $currency_data->short_name;
                        } elseif ($barcode->mode == 2) {
                          $currency = 'Crypto currency';
                          $currency_data = hid_ex_m_get_crypto_currency_data($barcode->asset_id);
                          $asset = $currency_data->name . ' | ' . $currency_data->short_name;
                        }


                        $barcode_img = wp_get_attachment_url($barcode->barcode);
                        $update_url = admin_url("admin.php?page=lux-barcode&tab=update&id=$barcode->id");

                        echo "<tr><td>$i</td>";
                        echo "<td>$currency</td>";
                        echo "<td>$asset</td>";
                        echo "<td><img src='$barcode_img' width='50px'></td>";
                        echo "<td>";
                        echo "<a href='$update_url' class='btn btn-primary l-bg-green btn-action mr-1' title='Update'><i class='fas fa-pencil-alt'></i></a>";
                        echo "</td></tr>";
                      }
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  }
}
