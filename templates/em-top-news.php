<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

if (!class_exists('LuxAdminTopNewsPage')) {
  class LuxAdminTopNewsPage
  {
    private int $get_id = 0;
    private string $get_tab = '';
    private LuxDBH $lux_dbh;

    public function __construct()
    {
      $this->lux_dbh = new LuxDBH;
    }

    public function lux_admin_top_news_template()
    {
      if (isset($_GET['tab'])) {
        $this->get_tab = $_GET['tab'];
      }

      if (isset($_GET['id'])) {
        $this->get_id = $_GET['id'];
      }

      if ($this->get_tab === 'delete') {
        $this->lux_dbh->lux_delete_news($this->get_id);
        echo "<script>location.replace('admin.php?page=lux-top-news');</script>";
        die();
      }

      if (isset($_POST['add_news_form'])) {
        $details = [
          'title' => $_POST['news_title'],
          'newsPicture' => $_POST['news_image_id']
        ];
        $this->lux_dbh->lux_set_news($details);
        echo "<script>location.replace('admin.php?page=lux-top-news');</script>";
        die();
      }

      if (isset($_POST['update_news_form'])) {
        $details = [
          'title' => $_POST['news_title'],
          'newsPicture' => $_POST['news_image_id'],
          'dateAdded' => date("Y-m-d H:i:s")
        ];
        $where = ['id' => $_POST['newsId']];
        $this->lux_dbh->lux_update_news($details, $where);
        echo "<script>location.replace('admin.php?page=lux-top-news');</script>";
        die();
      }

      $addUrl = admin_url("admin.php?page=lux-top-news&tab=add") ?>
      <main class="exchange-manager-wrapper">
        <section class="exchange-manager-wrapper-header">
          <div class="container mt-5 mb-0">
            <div class="row">
              <div class="col-6">
                <div class="flex-start">
                  <h3 class="text-bold">Top News.</h3>
                </div>
              </div>
              <div class="col-6 text-right">
                <a href="<?php echo $addUrl ?>" class="btn btn-danger text-bold l-bg-green">Add News</a>
              </div>
            </div>
          </div>
        </section>
        <section class="exchange-manager-wrapper-body">
          <div class="container pt-1">
            <?php
            if ($this->get_tab === 'add') {
              $this->lux_add_news_component();
            } elseif ($this->get_tab === 'update') {
              $this->lux_update_news_component($this->get_id);
            } else {
              $this->lux_all_news_component();
            }
            ?>
          </div>
        </section>
      </main>
    <?php
    }

    public function lux_all_news_component()
    {
      $all_news = $this->lux_dbh->lux_get_news() ?>
      <div class="row">
        <div class="col-12">
          <div class="card text-dark">
            <div class="card-header">
              <h5 class="text-bold">News Table.</h5>
            </div>
            <div class="card-body">
              <?php if (!empty($all_news)) {  ?>
                <div class="table-responsive pt-5">
                  <table class="table table-striped" id="database-table">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Date</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $i = 0;
                      foreach ($all_news as $news) {
                        $i++;
                        $update_url = admin_url("admin.php?page=lux-top-news&tab=update&id=$news->id");
                        $delete_url = admin_url("admin.php?page=lux-top-news&tab=delete&id=$news->id");
                        $news_img = wp_get_attachment_url($news->newsPicture) ?>
                        <tr>
                          <td>
                            <?php echo $i ?>
                          </td>
                          <td>
                            <strong><?php echo $news->title ?></strong>
                          </td>
                          <td>
                            <img src="<?php echo $news_img ?>" alt="<?php echo $news->title ?>" width="50">
                          </td>
                          <td>
                            <div class="badge badge-pill l-bg-green badge-shadow p-2"><?php echo $news->dateAdded ?></div>
                          </td>
                          <td>
                            <a href="<?php echo $update_url ?>" class="btn l-bg-green btn-action mr-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            <a href="<?php echo $delete_url ?>" class="btn btn-danger btn-action mr-1" title="Delete"><i class="fas fa-trash"></i></a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              <?php } else { ?>
                <p class="lead text-dark pl-4 pt-2">Sorry, No news to display.</p>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

    public function lux_add_news_component()
    { ?>
      <form action="<?php echo admin_url("admin.php?page=lux-top-news") ?>" method="POST">
        <div class="card text-dark">
          <div class="card-header">
            <h4>Add News</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="news_title">Title</label>
              <input type="text" class="form-control" id="news_title" name="news_title" />
            </div>
            <div class="section-title">Image</div>
            <div class="custom-file mt-3">
              <input type="file" name="newsImage" id="news_image" class="custom-file-input">
              <label for="news_image" id="news_image_label" class="custom-file-label">Choose File</label>
              <input type="hidden" id="news_image_id" name="news_image_id">
            </div>
          </div>
          <div class="card-footer text-right">
            <button class="btn btn-primary mr-1" name="add_news_form" type="submit">
              Add News
            </button>
            <button class="btn btn-secondary" type="reset">Reset</button>
          </div>
        </div>
      </form>
    <?php
    }

    public function lux_update_news_component($id)
    {
      $news = $this->lux_dbh->lux_get_single_news($id) ?>
      <form action="<?php echo admin_url("admin.php?page=lux-top-news") ?>" method="POST">
        <div class="card text-dark">
          <div class="card-header">
            <h4>Update <?php echo $news->title ?></h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="news_title">Title</label>
              <input type="hidden" name="newsId" value="<?php echo $news->id ?>">
              <input type="text" class="form-control" id="news_title" name="news_title" value="<?php echo $news->title ?>" />
            </div>
            <div class="section-title">Image</div>
            <div class="custom-file mt-3">
              <input type="file" name="newsImage" id="newsImage" class="custom-file-input">
              <label for="newsImage" id="newsImageLabel" class="custom-file-label"><?php echo wp_get_attachment_url($news->newsPicture) ?></label>
              <input type="hidden" id="news_image_id" name="news_image_id" value="<?php echo $news->newsPicture ?>">
            </div>
          </div>
          <div class="card-footer text-right">
            <button class="btn btn-primary l-bg-green mr-1" name="update_news_form" type="submit">
              Update News
            </button>
            <button class="btn btn-secondary" type="reset">Reset</button>
          </div>
        </div>
      </form>
<?php
    }
  }
}
