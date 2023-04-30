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
                              <th></th>
                              <th>Name</th>
                              <th>Short Name</th>
                              <th>Icon</th>
                              <th>Buying Price</th>
                              <th>Selling Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $i = 0;
                            foreach ($assets as $asset) {
                              $i++; ?>
                              <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo $asset->name ?? $null_msg ?></td>
                                <td class="text-uppercase text-bold"><?php echo $asset->short_name ?? $null_msg ?></td>
                                <td>
                                  <img src="<?php echo $asset->image_url ?? '' ?>" alt="<?php echo $asset->name ?? $null_msg ?>" width="50">
                                </td>
                                <td><?php echo $asset->buying_price ?? $null_msg ?></td>
                                <td><?php echo $asset->selling_price ?? $null_msg ?></td>
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
