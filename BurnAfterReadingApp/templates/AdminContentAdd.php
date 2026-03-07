<div class="container container-lg">
  <h1><?php echo $data['_']['add_secret'] ?></h1>
  <form method="post" action="<?php echo $data['client_url'] ?>" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="52428800">
    <div class="mb-3">
      <label for="content" class="form-label"><?php echo $data['_']['content'] ?></label>
      <textarea name="content" id="content" class="form-control" rows="3" placeholder="<?php echo $data['_']['content'] ?>"></textarea>
    </div>
    <div class="mb-3">
      <label for="attachment" class="form-label"><?php echo $data['_']['attachment'] ?></label>
      <input type="file" name="attachment" id="attachment" class="form-control">
    </div>
    <?php if (!empty($data['mail']['enable'])) : ?>
    <div class="email-notification-settings">
      <div class="mb-3">
        <div class="form-check">
          <input type="checkbox" name="notify_enable" id="notify_enable" class="form-check-input" value="1"<?php echo (!empty($data['mail']['suggest_notify'])) ? ' checked' : '' ?>>
          <label for="notify_enable" class="form-check-label"><?php echo $data['_']['notify_enable'] ?></label>
        </div>
      </div>
      <div class="mb-3">
        <label for="notification_id" class="form-label"><?php echo $data['_']['notification_id'] ?></label>
        <input type="text" name="notification_id" id="notification_id" class="form-control" value="<?php echo $data['notification_id_suggestion'] ?>">
      </div>
    </div>
    <?php endif ?>
    <div class="text-center mt-3">
      <input type="submit" value="<?php echo $data['_']['add'] ?>">
    </div>
</div>