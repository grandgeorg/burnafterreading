<?php

declare(strict_types=1);

namespace Bar;

class Client extends App
{

    public function __construct(string $dir)
    {
        parent::__construct($dir);
    }

    public function run(): void
    {
        $this->setSession();
        $this->setHeaders();
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($method === 'get') {
            $this->loginAction();
        } elseif ($method === 'post') {
            $this->authAction();
        }
    }

    public function loginAction(): void
    {
        $this->addCSRF('csrf-client');
        $this->config['page'] = 'login-client';
        $this->config['main_class'] = 'c-100vh';
        $this->addToPage('ClientHead', $this->config);
        if ($this->limit->check()) {
            $this->addToPage('ClientBlocked', $this->config);
        } else {
            $this->addToPage('ClientLogin', $this->config);
        }
        $this->addToPage('ClientFoot');
        echo $this->page;
    }

    public function authAction(): void
    {
        $check = $this->runChecks();

        if ($check) {
            if (isset($this->config['bar']['attachment'])) {
                $this->getAttachment();
            }
            $this->config['page'] = 'content-client';
            $this->config['main_class'] = 'c-100vh';
            // $this->setHeaders();
            $this->addToPage('ClientHead', $this->config);
            $this->addToPage('ClientContent', $this->config);
            $this->addToPage('ClientFoot');
            echo $this->page;
            $this->burn();
        } else {
            $this->limit->add();
            $this->config['errors'][] = 'something_went_wrong';
            $this->loginAction();
        }

    }

    public function runChecks(): bool
    {
        if ($this->limit->check()) {
            return false;
        }
        if (!$this->checkCSRF('csrf-client')) {
            return false;
        }
        if (!$this->checkPassword()) {
            return false;
        }
        if (!$this->getRequestedKey()) {
            return false;
        }
        if (!$this->getKeyparts()) {
            return false;
        }
        if (!$this->authenticate()) {
            return false;
        }
        if (!$this->getPost()) {
            return false;
        }
        return true;
    }

    public function checkPassword(): bool
    {
        if (
            !isset($_POST['password']) ||
            empty($_POST['password']) ||
            !is_string($_POST['password']) ||
            strlen($_POST['password']) !== 16
        ) {
            return false;
        }
        return true;
    }

    public function getRequestedKey(): bool
    {
        $keySheme = '/^[a-f0-9]{96}$/';
        foreach ($_GET as $key => $value) {
            if (($value === '') && preg_match($keySheme, $key, $match)) {
                $this->config['bar']['req_key'] = $match[0];
                return true;
            }
        }
        return false;
    }

    public function getKeyparts(): bool
    {
        if (!isset($this->config['bar']['req_key'])) {
            return false;
        }
        $dir = substr($this->config['bar']['req_key'], 0, 32);
        $dir = preg_replace('/[^a-f0-9]/', '', $dir);
        if (strlen($dir) !== 32) {
            return false;
        }
        $dir = $this->config['data_path'] . DIRECTORY_SEPARATOR . $dir;
        if (!is_dir($dir) || !is_readable($dir)) {
            return false;
        } else {
            $this->config['bar']['dir'] = $dir;
        }
        $key = substr($this->config['bar']['req_key'], 32);
        $key = preg_replace('/[^a-f0-9]/', '', $key);
        if (strlen($key) !== 64) {
            return false;
        } else {
            $this->config['bar']['key'] = $key;
        }
        return true;
    }

    public function authenticate(): bool
    {
        $infoFile = $this->config['bar']['dir'] . DIRECTORY_SEPARATOR . 'info.txt';
        if (!file_exists($infoFile) || !is_readable($infoFile)) {
            return false;
        }
        $info = file_get_contents($infoFile);
        if ($info === false) {
            return false;
        }
        $info = json_decode($info, true);
        if (!is_array($info)) {
            return false;
        }
        if (!isset($info['pwd'])) {
            return false;
        }
        if (!password_verify($_POST['password'], $info['pwd'])) {
            return false;
        }
        if (isset($info['attachment'])) {
            $this->config['bar']['attachment'] = $info['attachment'];
        }
        return true;
    }

    public function getPost(): bool
    {
        $key = sodium_hex2bin($this->config['bar']['key']);

        $postFile = $this->config['bar']['dir'] . DIRECTORY_SEPARATOR . 'post.txt';
        if (!file_exists($postFile) || !is_readable($postFile)) {
            return false;
        }
        $postEncoded = file_get_contents($postFile);
        if ($postEncoded === false) {
            return false;
        }
        $postDecoded = base64_decode($postEncoded);
        if ($postDecoded === false) {
            return false;
        }
        $nonce = mb_substr($postDecoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($postDecoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        $post = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        if ($post === false) {
            return false;
        } else {
            $this->config['bar']['post'] = $post;
        }

        return true;
    }

    public function getAttachment(): bool
    {
        if (!isset($this->config['bar']['attachment'])) {
            return false;
        }
        $attachmentFile = $this->config['bar']['dir'] . DIRECTORY_SEPARATOR . 'attachment.txt';
        if (!file_exists($attachmentFile) || !is_readable($attachmentFile)) {
            return false;
        }
        $attachmentEncoded = file_get_contents($attachmentFile);
        if ($attachmentEncoded === false) {
            return false;
        }
        $attachmentDecoded = base64_decode($attachmentEncoded);
        if ($attachmentDecoded === false) {
            return false;
        }
        $key = sodium_hex2bin($this->config['bar']['key']);
        $nonce = mb_substr($attachmentDecoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($attachmentDecoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        $attachment = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        if ($attachment === false) {
            return false;
        } else {
            $this->config['bar']['attachment_content'] = base64_encode($attachment);
        }
        return true;
    }

    public function burn(): void
    {
        $this->deleteFiles();
        $this->deleteDirectory();
        $this->deleteData();
    }

    public function deleteFiles()
    {
        $infoFile = $this->config['bar']['dir'] . DIRECTORY_SEPARATOR . 'info.txt';
        if (file_exists($infoFile)) {
            unlink($infoFile);
        }
        $postFile = $this->config['bar']['dir'] . DIRECTORY_SEPARATOR . 'post.txt';
        if (file_exists($postFile)) {
            unlink($postFile);
        }
        $attachmentFile = $this->config['bar']['dir'] . DIRECTORY_SEPARATOR . 'attachment.txt';
        if (file_exists($attachmentFile)) {
            unlink($attachmentFile);
        }
    }

    public function deleteDirectory()
    {
        $dir = $this->config['bar']['dir'];
        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function deleteData()
    {
        unset($this->config['bar']);
    }



}
