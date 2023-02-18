<div class="container container-sm">
    <h1><?php echo $data['_']['login'] ?></h1>
    <?php if (in_array('something_went_wrong', $data['errors'])): ?>
    <div class="alert alert-danger mb-3"><?php echo $data['_']['something_went_wrong'] ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <input type="password" name="password" placeholder="<?php echo $data['_']['password'] ?>" autocomplete="off" >
        <div class="text-center mt-3">
            <input type="submit" value="Login">
        </div>
    </form>
</div>