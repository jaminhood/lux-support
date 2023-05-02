<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminHomePage')) {
  class LuxAdminHomePage
  {
    public function lux_admin_home_page_template()
    { ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <div class="flex-start">
              <h3 class="text-bold">Welcome to Luxtrade Manager.</h3>
            </div>
            <div class="card">
              <div class="card-body">
              </div>
            </div>
          </div>
        </section>
      </main>
<?php
    }
  }
}
