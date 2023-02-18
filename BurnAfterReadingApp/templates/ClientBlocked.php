<div class="container container-sm">
    <h1><?php echo $data['_']['login'] ?></h1>
    <div class="alert alert-danger mb-3">
      <?php echo $data['_']['too_many_attempts'] ?><br>
      <?php echo $data['_']['you_have_been_blocked'] ?>
    </div>
</div>