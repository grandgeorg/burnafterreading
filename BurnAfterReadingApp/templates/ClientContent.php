<div class="container container-80ch gap-3 my-4">
    <div class="alert alert-info">
        <?php echo $data['_']['bar_info'] ?>
    </div>
    <div class="wrapper client-content-wrapper">
        <h1 class="text-center"><?php echo $data['_']['content'] ?></h1>
        <div class="client-content-controls">
            <div class="copy">
                <button class="btn btn-copy" id="copy-client-content" data-clipboard-target=".client-content"><?php echo $data['_']['copy_to_clipboard'] ?></button>
            </div>
        </div>
        <pre class="client-content"><?php echo htmlspecialchars($data['bar']['post']); ?></pre>
    </div>
    <?php if (isset($data['bar']['attachment'])) : ?>
        <div class="wrapper attachment-wrapper">
            <h2 class="text-center"><?php echo $data['_']['attachment'] ?></h2>
            <div class="attachment text-center">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z" />
                        <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z" />
                    </svg>
                </div>
                <div class="filename">
                    <?php echo $data['bar']['attachment'] ?>
                </div>
                <div class="download">
                    <a href="data:application/octet-stream;base64,<?php echo $data['bar']['attachment_content'] ?>" download="<?php echo $data['bar']['attachment'] ?>" class="btn"><?php echo $data['_']['download'] ?></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- add js -->
<script src="./assets/js/client.min.js"></script>