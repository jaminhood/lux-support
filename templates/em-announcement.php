<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminAnnouncement')) {
  class LuxAdminAnnouncement
  {
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_announcement_template()
    {
      if (isset($_POST['create-announcement'])) {

        $data = array(
          'headline'    => $_POST['headline'],
          'body'        => $_POST['body']
        );

        hid_ex_m_create_new_announcement($data);

        wp_mail(
          get_option('business_email'),
          'LuxTrade Alert - New Announcement Published',
          "This is to notify you that a new alert was just published on your platform.\nDo well to check it out"
        );

        $data = [
          'title' => $_POST['headline'],
          'body' => $_POST['body']
        ];

        foreach ($this->lux_dbh->lux_get_all_device_token() as $device_token) {
          LuxUtils::lux_push_notification($device_token->customer_id, $data);
        }

        echo "<script>location.replace('admin.php?page=announcements');</script>";
      } ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
          </div>
        </section>
        <section class="exchange-manager-wrapper-body">
          <div class="container pt-1">
            <form action="<?php echo admin_url("admin.php?page=lux-announcement") ?>" method="POST">
              <div class="card text-dark">
                <div class="card-header">
                  <h4>Make A New Announcement</h4>
                </div>
                <div class="card-body">
                  <div class="form-group row">
                    <label for="headline" class="col-sm-4 col-form-label">Headline</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="headline" name="headline" />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="body" class="col-sm-4 col-form-label">Body</label>
                    <div class="col-sm-8">
                      <textarea id="body" name="body" class="form-control border-dark"></textarea>
                    </div>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                  <button class="btn btn-primary l-bg-green mr-1" name="create-announcement" type="submit">
                    Publish Announcement
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
