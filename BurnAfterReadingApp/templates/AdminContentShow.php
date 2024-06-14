<div class="container container-lg">
  <h1><?php echo $data['_']['show_secret'] ?></h1>
  <div class="admin-content-controls">
      <div class="copy">
          <button class="btn btn-copy" id="copy-admin-content" data-clipboard-target=".admin-content"><?php echo $data['_']['copy_to_clipboard'] ?></button>
      </div>
  </div>
  <div class="admin-content">
  <?php echo $data['_']['link'] ?>:<br>
  <?php echo $data['sec']['link'] ?><br>
  <?php echo $data['_']['password_onetime'] ?>: <?php echo $data['sec']['pwd'] ?>
  </div>
</div>
<script src="../assets/js/admin.min.js"></script>