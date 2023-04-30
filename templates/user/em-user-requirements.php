<?php

if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

function em_main_header($user)
{ ?>
  <!-- begin app-header -->
  <header class="app-header top-bar">
    <!-- begin navbar -->
    <nav class="navbar navbar-expand-md">
      <!-- begin navbar-header -->
      <div class="navbar-header d-flex align-items-center l-bg-green">
        <a href="javascript:void:(0)" class="mobile-toggle"><i class="ti ti-align-right"></i></a>
        <a class="navbar-brand" href="<?php echo site_url('./') ?>">
          Luxtrade
        </a>
      </div>
    </nav>
    <!-- end navbar -->
  </header>
  <!-- end app-header -->
<?php
}

function em_sidebar($data)
{ ?>
  <!-- begin app-nabar -->
  <aside class="app-navbar">
    <!-- begin sidebar-nav -->
    <div class="sidebar-nav scrollbar scroll_light">
      <div class="sidebar__logo text-center py-3">
        <img src="<?php echo EMURL . "assets/imgs/logo-white.png" ?>" alt="sidebar logo" class="w-75">
      </div>
      <ul class="metismenu" id="sidebarNav">
        <li class="active">
          <ul aria-expanded="false">
            <li <?php if ($data == 'dashboard') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/dashboard/') ?>">Overview</a></li>
            <li <?php if ($data == 'wallet') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/wallet/') ?>">Wallet</a></li>
            <li <?php if ($data == 'buy') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/buy/') ?>">Buy Asset</a></li>
            <li <?php if ($data == 'sell') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/sell/') ?>">Sell Asset</a></li>
            <li <?php if ($data == 'referral') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/referral/') ?>">Referral</a></li>
            <li <?php if ($data == 'rate') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/rate/') ?>">Today's Rate</a></li>
            <li <?php if ($data == 'announcement') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/announcement/') ?>">Announcement</a></li>
            <li <?php if ($data == 'statement') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/statement/') ?>">Statement</a></li>
            <li <?php if ($data == 'settings') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/settings/') ?>">Settings</a></li>
            <li <?php if ($data == 'support') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/support/') ?>">Support</a></li>
            <li><a href="<?php echo site_url('/lux-user/logout/') ?>">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <!-- end sidebar-nav -->
  </aside>
  <!-- end app-navbar -->
<?php
}

function em_page_header($data)
{ ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title><?php echo $data['title'] ?> | Luxtrade</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Luxtrade customers dashboard" />
    <meta name="author" content="JaminHood" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php wp_head() ?>
  </head>

  <body>
    <div class="app">
    <?php
  }

  function em_page_footer()
  { ?>
    </div>
    <?php wp_footer() ?>
  </body>

  </html>
<?php
  }
