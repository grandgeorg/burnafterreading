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
    <div class="text-center mt-3">
      <input type="submit" value="<?php echo $data['_']['add'] ?>">
    </div>
</div>