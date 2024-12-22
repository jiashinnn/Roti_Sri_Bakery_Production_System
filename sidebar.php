<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <div class="dropdown">
    <a href="javascript:void(0)" class="brand-link dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
      <span class="brand-image img-circle elevation-3 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 38px;height:50px"><?php echo strtoupper(substr($_SESSION['login_fullname'], 0, 1)) ?></span>
      <span class="brand-text font-weight-light"><?php echo ucwords($_SESSION['login_fullname']) ?></span>
    </a>
    <div class="dropdown-menu">
      <a class="dropdown-item manage_account" href="javascript:void(0)" data-id="<?php echo $_SESSION['login_id'] ?>">Manage Account</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" href="ajax.php?action=logout">Logout</a>
    </div>
  </div>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Dashboard -->
        <li class="nav-item dropdown">
          <a href="./" class="nav-link nav-home">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>

        <!-- Inbox -->
        <li class="nav-item">
          <a href="./index.php?page=inbox" class="nav-link nav-inbox">
            <i class="nav-icon fas fa-envelope"></i>
            <p>
              Inbox
            </p>
          </a>
        </li>

        <!-- Recipe -->
        <li class="nav-item">
          <a href="#" class="nav-link nav-edit_recipe">
            <i class="nav-icon fas fa-utensils"></i>
            <p>
              Recipe
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./index.php?page=new_recipe" class="nav-link nav-new_recipe tree-item">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>Add New</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./index.php?page=recipe_list" class="nav-link nav-recipe_list tree-item">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>View All</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Production Scheduling -->
        <li class="nav-item">
          <a href="./index.php?page=production_scheduling" class="nav-link nav-production_scheduling">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>
              Production Scheduling
            </p>
          </a>
        </li>

        <!-- Batch Tracking -->
        <li class="nav-item">
          <a href="./index.php?page=batch_tracking" class="nav-link nav-batch_tracking">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <p>
              Batch Tracking
            </p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
<script>
  $(document).ready(function() {
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    if ($('.nav-link.nav-' + page).length > 0) {
      $('.nav-link.nav-' + page).addClass('active')
      console.log($('.nav-link.nav-' + page).hasClass('tree-item'))
      if ($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
        $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active')
        $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open')
      }
      if ($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
        $('.nav-link.nav-' + page).parent().addClass('menu-open')
      }

    }
    $('.manage_account').click(function() {
      uni_modal('Manage Account', 'manage_user.php?id=' + $(this).attr('data-id'))
    })
  })
</script>