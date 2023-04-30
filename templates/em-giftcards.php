<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminGiftcards')) {
  class LuxAdminGiftcards
  {
    private int $get_id = 0;
    private string $get_tab = '';
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_giftcards_template()
    {
      if (isset($_GET['tab'])) {
        $this->get_tab = $_GET['tab'];
      }

      if (isset($_GET['id'])) {
        $this->get_id = $_GET['id'];
      }

      if ($this->get_tab === 'delete-category') {
        $this->lux_dbh->lux_delete_giftcard_category_data($this->get_id);
        echo "<script>location.replace('admin.php?page=lux-giftcards');</script>";
        die();
      }

      if ($this->get_tab === 'delete-sub-category') {
        $this->lux_dbh->lux_delete_giftcard_sub_category_data($this->get_id);
        echo "<script>location.replace('admin.php?page=lux-giftcards');</script>";
        die();
      }

      if (isset($_POST['create-category'])) {
        $data = [
          'category' => $_POST['category'],
          'icon' => $_POST['icon-id'],
        ];
        $this->lux_dbh->lux_create_new_giftcard_category($data);
        echo "<script>location.replace('admin.php?page=lux-giftcards');</script>";
        die();
      }

      if (isset($_POST['create-sub-category'])) {
        $data = [
          'category_id' => $_POST['category'],
          'sub_category' => $_POST['sub-category'],
          'icon' => $_POST['icon-id'],
          'rate' => $_POST['rate'],
        ];
        $this->lux_dbh->lux_create_new_giftcard_sub_category($data);
        echo "<script>location.replace('admin.php?page=lux-giftcards');</script>";
        die();
      }

      if (isset($_POST['update-sub-category'])) {
        $data = [
          'category_id' => $_POST['category'],
          'sub_category' => $_POST['sub-category'],
          'icon' => $_POST['icon-id'],
          'rate' => $_POST['rate'],
        ];
        $where = ['id' => $_POST['id']];
        $this->lux_dbh->lux_update_giftcard_sub_category($data, $where);
        echo "<script>location.replace('admin.php?page=lux-giftcards');</script>";
        die();
      } ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <?php
            if ($this->get_tab === 'create-category') {
              $this->lux_admin_create_category();
            } elseif ($this->get_tab === 'create-sub-category') {
              $this->lux_admin_create_sub_category();
            } elseif ($this->get_tab === 'update-sub-category') {
              $this->lux_admin_update_sub_category();
            } else {
              $this->lux_admin_giftcards_display();
            }
            ?>
          </div>
        </section>
      </main>
    <?php
    }

    public function lux_admin_update_sub_category()
    {
      $sub_category = $this->lux_dbh->lux_get_giftcard_sub_category_data($this->get_id) ?>
      <div class="row">
        <div class="col-12">
          <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $this->get_id ?>">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Create Giftcard Sub-Category</h4>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="category" class="col-sm-2 col-form-label">Select Category</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="category" name="category">
                      <option value="<?php echo $sub_category['category_id'] ?>"><?php echo $sub_category['category'] ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="sub-category" class="col-sm-2 col-form-label">Sub-Category Name</label>
                  <div class="col-sm-10 pr-0" style="max-width: 26rem;">
                    <input type="text" class="form-control mr-0" id="sub-category" name="sub-category" value="<?php echo $sub_category['sub_category'] ?>" />
                  </div>
                </div>
                <div class="form-group row">
                  <label for="icon" class="col-sm-2 col-form-label">Icon</label>
                  <div class="col-sm-10">
                    <div class="custom-file" style="max-width: 25rem;">
                      <input type="file" name="icon" id="icon" class="custom-file-input">
                      <label for="icon" id="icon-label" class="custom-file-label"><?php echo $sub_category['icon'] ?></label>
                      <input type="hidden" id="icon-id" name="icon-id" value="<?php echo $sub_category['icon_id'] ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="rate" class="col-sm-2 col-form-label">Rate</label>
                  <div class="col-sm-10 pr-0" style="max-width: 26rem;">
                    <input type="number" class="form-control mr-0" id="rate" name="rate" value="<?php echo $sub_category['rate'] ?>" />
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" name="update-sub-category" class="btn btn-primary l-bg-green">Create Sub-Category</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    public function lux_admin_create_sub_category()
    {
      $all_category = $this->lux_dbh->lux_get_all_giftcard_categories() ?>
      <div class="row">
        <div class="col-12">
          <form action="" method="POST">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Create Giftcard Sub-Category</h4>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="category" class="col-sm-2 col-form-label">Select Category</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="category" name="category">
                      <?php foreach ($all_category as $category) { ?>
                        <option value="<?php echo $category['id'] ?>"><?php echo $category['category'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="sub-category" class="col-sm-2 col-form-label">Sub-Category Name</label>
                  <div class="col-sm-10 pr-0" style="max-width: 26rem;">
                    <input type="text" class="form-control mr-0" id="sub-category" name="sub-category" />
                  </div>
                </div>
                <div class="form-group row">
                  <label for="icon" class="col-sm-2 col-form-label">Icon</label>
                  <div class="col-sm-10">
                    <div class="custom-file" style="max-width: 25rem;">
                      <input type="file" name="icon" id="icon" class="custom-file-input">
                      <label for="icon" id="icon-label" class="custom-file-label">Choose File</label>
                      <input type="hidden" id="icon-id" name="icon-id">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="rate" class="col-sm-2 col-form-label">Rate</label>
                  <div class="col-sm-10 pr-0" style="max-width: 26rem;">
                    <input type="number" class="form-control mr-0" id="rate" name="rate" />
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" name="create-sub-category" class="btn btn-primary l-bg-green">Create Sub-Category</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    public function lux_admin_create_category()
    { ?>
      <div class="row">
        <div class="col-12">
          <form action="" method="POST">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Create Giftcard Category</h4>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="category" class="col-sm-2 col-form-label">Category Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="category" name="category" style="max-width: 25rem;" />
                  </div>
                </div>
                <div class="form-group row">
                  <label for="icon" class="col-sm-2 col-form-label">Icon</label>
                  <div class="col-sm-10">
                    <div class="custom-file" style="max-width: 25rem;">
                      <input type="file" name="icon" id="icon" class="custom-file-input">
                      <label for="icon" id="icon-label" class="custom-file-label">Choose File</label>
                      <input type="hidden" id="icon-id" name="icon-id">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" name="create-category" class="btn btn-primary l-bg-green">Create Category</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    public function lux_admin_giftcards_display()
    { ?>
      <div class="row">
        <div class="col-12 col-md-4">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-7">
                  <h4 class="card-title">Giftcard Categories</h4>
                </div>
                <div class="col-5 text-right">
                  <a href="<?php echo admin_url("admin.php?page=lux-giftcards&tab=create-category") ?>" class="btn btn-danger btn-sm text-bold l-bg-green">Add Category</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <?php $all_category = $this->lux_dbh->lux_get_all_giftcard_categories() ?>
              <ul class="activity">
                <?php foreach ($all_category as $category) { ?>
                  <li class="activity-item success">
                    <div class="activity-info d-flex justify-content-between align-items-center">
                      <h5><?php echo $category['category'] ?></h5>
                      <a href="<?php echo admin_url("admin.php?page=lux-giftcards&tab=delete-category&id=" . $category['id'] . "") ?>" class="btn btn-primary l-bg-green btn-action btn-sm mx-1" title="Delete Category"><i class="fas fa-trash"></i></a>
                    </div>
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-8">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-7">
                  <h4 class="card-title">Giftcard Sub-Categories</h4>
                </div>
                <div class="col-5 text-right">
                  <a href="<?php echo admin_url("admin.php?page=lux-giftcards&tab=create-sub-category") ?>" class="btn btn-danger btn-sm text-bold l-bg-green">Add Sub-Category</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <?php $all_category = $this->lux_dbh->lux_get_all_giftcard_sub_categories() ?>
              <div class="table-responsive">
                <table class="table mb-0 table-bordered">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">Category</th>
                      <th scope="col">Sub-Category</th>
                      <th scope="col" class="text-center">Icon</th>
                      <th scope="col">Rate</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($all_category as $category) { ?>
                      <tr>
                        <th scope="row"><?php echo $category['category'] ?></th>
                        <td><?php echo $category['sub_category'] ?></td>
                        <td class="text-center p-1"><img src="<?php echo $category['icon'] ?>" alt="<?php echo $category['sub_category'] ?>" width="50px"></td>
                        <td><?php echo $category['rate'] ?></td>
                        <td>
                          <a href="<?php echo admin_url('admin.php?page=lux-giftcards&tab=update-sub-category&id=' . $category['id']) ?>" class="btn btn-primary l-bg-green btn-action btn-sm mx-1" title="Update Sub Category"><i class="fas fa-pencil"></i></a>
                          <a href="<?php echo admin_url('admin.php?page=lux-giftcards&tab=delete-sub-category&id=' . $category['id']) ?>" class="btn btn-primary l-bg-green btn-action btn-sm mx-1" title="Delete Sub Category"><i class="fas fa-trash"></i></a>
                        </td>
                      </tr>
                    <?php } ?>
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
