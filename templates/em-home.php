<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminHomePage')) {
  class LuxAdminHomePage
  {
    public function lux_admin_home_page_template()
    {
      $lux_dbh = new LuxDBH ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <div class="flex-start">
              <h3 class="text-bold">Welcome to Luxtrade Manager.</h3>
            </div>
            <div class="card">
              <div class="card-body">
                <?php
                // $lux_dbh->lux_set_device_token('cJqrLmeyTG6wd6RJArFhQa:APA91bG9s9CjIPWmf58C73UHI2J-9Tx8W2ioxKq7oUQqE3XUUwGn_fqJP4Xroq_t1pNsQSwSNpCNkAhC1akR5L62pL9OkY5vRGSpUXkyc7TI5HORSbhP9OaoIUt0b-YGF6k4K9WKk5-L');
                // print_r($lux_dbh->lux_get_device_token());
                // echo 'Home';

                $data = [
                  'title' => 'Lux Trade',
                  'body' => 'Hello, From PHP Script'
                ];

                // LuxUtils::lux_push_notification($data);
                ?>
              </div>
            </div>
          </div>
        </section>
      </main>
<?php
    }
  }
}
