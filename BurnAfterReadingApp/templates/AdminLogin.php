<div class="container container-sm">
    <h1><?php echo $data['_']['login'] ?></h1>
    <?php if (in_array('wrong_password', $data['errors'])): ?>
    <div class="alert alert-danger mb-3"><?php echo $data['_']['wrong_password'] ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $data['client_url'] ?>">
        <input type="password" name="password" placeholder="<?php echo $data['_']['password'] ?>">
        <div class="text-center mt-3">
            <input type="submit" value="Login">
        </div>
    </form>
</div>