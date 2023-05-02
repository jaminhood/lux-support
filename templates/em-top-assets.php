<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminTopAssetsPage')) {
  class LuxAdminTopAssetsPage
  {
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_top_assets_template()
    {
      if (isset($_GET['tab'])) {
        if ($_GET['tab'] === 'add') {
          $asset = array(
            'asset_type' => $_GET['asset_type'],
            'asset_id' => $_GET['asset_id']
          );

          $this->lux_dbh->lux_set_top_asset($asset);
          echo "<script>location.replace('admin.php?page=lux-top-assets');</script>";
          die();
        }

        if ($_GET['tab'] === 'remove') {
          $this->lux_dbh->lux_delete_top_asset($_GET['id']);
          echo "<script>location.replace('admin.php?page=lux-top-assets');</script>";
          die();
        }
      }

      $null_msg = '<div class="badge badge-danger text-uppercase badge-shadow">null</div>' ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <div class="flex-start">
              <h3 class="text-bold">Top Assets</h3>
            </div>
          </div>
        </section>
        <?php $assets = $this->lux_dbh->lux_get_assets() ?>
        <section class="exchange-manager-wrapper-body">
          <div class="container pt-1">
            <div class="row">
              <div class="col-12">
                <div class="card text-dark">
                  <div class="card-header">
                    <h5 class="text-bold">Top Assets Table</h5>
                  </div>
                  <div class="card-body p-0">
                    <?php if (!empty($assets)) {  ?>
                      <div class="table-responsive pt-0">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Short Name</th>
                              <th>Icon</th>
                              <th>Buying Price</th>
                              <th>Selling Price</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            foreach ($assets as $asset) { ?>
                              <tr>
                                <td><?php echo $asset['name'] ?? $null_msg ?></td>
                                <td class="text-uppercase text-bold"><?php echo $asset['short_name'] ?? $null_msg ?></td>
                                <td>
                                  <img src="<?php echo $asset['icon'] ?? '' ?>" alt="<?php echo $asset['name'] ?? $null_msg ?>" width="50">
                                </td>
                                <td><?php echo $asset['buying_price'] ?? $null_msg ?></td>
                                <td><?php echo $asset['selling_price'] ?? $null_msg ?></td>
                                <td>
                                  <?php
                                  $check = $this->lux_dbh->lux_get_top_asset($asset['asset_type'], $asset['asset_id']);
                                  if ($check) { ?>
                                    <a href="<?php echo admin_url("admin.php?page=lux-top-assets&tab=remove&id=" . $check->id) ?>" class="btn btn-primary l-bg-green btn-sm">Remove</a>
                                  <?php } else { ?>
                                    <a href="<?php echo admin_url("admin.php?page=lux-top-assets&tab=add&asset_type=" . $asset['asset_type'] . "&asset_id=" . $asset['asset_id']) ?>" class="btn btn-primary l-bg-green btn-sm">Add</a>
                                  <?php } ?>
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    <?php } else { ?>
                      <p class="lead text-dark pl-4 pt-2">Sorry, No assets to display.</p>
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
