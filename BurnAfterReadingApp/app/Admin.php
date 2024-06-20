<?php

declare(strict_types=1);

namespace Bar;

class Admin extends App
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
            if (isset($_GET['logout'])) {
                $this->destroySession();
                header('Location: ' . $this->config['client_url']);
            } elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $this->addContentAction();
            } else {
                $this->loginAction();
            }
        } elseif ($method === 'post') {
            if (
                isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true &&
                $this->checkCSRF('csrf-addcontent')
            ) {
                $this->showContentAction();
            } else {
                $this->authAction();
            }
        }
    }

    public function loginAction(): void
    {
        $this->addCSRF('csrf');
        $this->config['page'] = 'login';
        $this->config['main_class'] = 'c-100vh';
        $this->addToPage('AdminHead', $this->config);
        if ($this->limit->check()) {
            $this->addToPage('AdminBlocked', $this->config);
        } else {
            $this->addToPage('AdminLogin', $this->config);
        }
        $this->addToPage('AdminFoot');

        echo $this->page;
    }

    public function authAction(): void
    {
        $pwdFile = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'pwd.php');
        if (!$pwdFile || !file_exists($pwdFile) || !is_readable($pwdFile)) {
            throw new \Exception('Data directory not found');
        }
        $pwd = require $pwdFile;
        if (
            $this->checkCSRF('csrf') &&
            password_verify($_POST['password'], $pwd) &&
            !$this->limit->check()
        ) {
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            header('Location: ' . $this->config['client_url']);
            exit;
        } else {
            $this->limit->add();
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            $this->config['errors'][] = 'wrong_password';
            $this->loginAction();
        }
    }

    public function logoutAction(): void
    {
        $this->destroySession();
        header('Location: ' . $this->config['client_url']);
        exit;
    }

    public function addContentAction(): void
    {
        $this->addCSRF('csrf-addcontent');
        $this->config['page'] = 'addcontent';
        $this->addToPage('AdminHead', $this->config);
        $this->addToPage('AdminContentAdd', $this->config);
        $this->addToPage('AdminFoot');

        echo $this->page;
    }

    public function showContentAction(): void
    {
        $this->config['page'] = 'showcontent';
        $dir = $this->makeDir(); // 32 chars
        $dirPath = $this->config['data_path'] . DIRECTORY_SEPARATOR . $dir;
        $key = sodium_crypto_secretbox_keygen();
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $pwd = $this->getRandomPassword(16);
        $settings = [
            'pwd' => password_hash($pwd, PASSWORD_DEFAULT),
        ];
        $this->savePost($dirPath, $key, $nonce);
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
            $settings['attachment'] = $this->saveAttachment($dirPath, $key, $nonce);
        }
        $this->saveInfo($dirPath, $settings);

        $this->config['sec']['key'] = $dir . sodium_bin2hex($key);
        $this->config['sec']['pwd'] = $pwd;
        $this->config['sec']['link'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
            '://' . $_SERVER['HTTP_HOST'] .
            dirname($this->config['client_url']) .
            '/?' . $this->config['sec']['key'];
        $this->addToPage('AdminHead', $this->config);
        $this->addToPage('AdminContentShow', $this->config);
        $this->addToPage('AdminFoot');

        echo $this->page;
    }

    public function makeDir(): string
    {
        $random = bin2hex(random_bytes(16));
        $dir = $this->config['data_path'] . DIRECTORY_SEPARATOR . $random;
        if (is_dir($dir)) {
            return $this->makeDir();
        } else {
            if (!mkdir($dir, 0755)) {
                throw new \Exception('Could not create directory');
            }
        }
        return $random;
    }

    public function savePost(string $dir, string $key, string $nonce): void
    {
        $post = '';
        if (isset($_POST['content']) && !empty($_POST['content'])) {
            $post = $_POST['content'];
        }
        $encrypted = sodium_crypto_secretbox($post, $nonce, $key);
        $encoded = base64_encode($nonce . $encrypted);
        $file = $dir . DIRECTORY_SEPARATOR . 'post.txt';
        if (!file_put_contents($file, $encoded)) {
            throw new \Exception('Could not save post');
        }
    }

    public function saveAttachment(string $dir, string $key, string $nonce): string
    {
        $file = $_FILES['attachment'];
        $error = $file['error'];
        $size = $file['size'];
        $name = $file['name'];
        $maxSize = 1024 * 1024 * 50; // 50 MB

        if ($error !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload error');
        }

        if ($size > $maxSize) {
            throw new \Exception('File too large');
        }

        $fileOriginal = $dir . DIRECTORY_SEPARATOR . $file['name'];
        move_uploaded_file($file['tmp_name'], $fileOriginal);

        $fileEncrypted = $dir . DIRECTORY_SEPARATOR . 'attachment.txt';
        $encrypted = sodium_crypto_secretbox(file_get_contents($fileOriginal), $nonce, $key);
        $encoded = base64_encode($nonce . $encrypted);

        if (!file_put_contents($fileEncrypted, $encoded)) {
            throw new \Exception('Could not save attachment');
        }

        if (!unlink($fileOriginal)) {
            throw new \Exception('Could not delete original file');
        }

        return $name;
    }

    public function saveInfo(string $dir, array $settings): void
    {
        $file = $dir . DIRECTORY_SEPARATOR . 'info.txt';
        $encoded = json_encode($settings);
        if (!file_put_contents($file, $encoded)) {
            throw new \Exception('Could not save info');
        }
    }

    public function getRandomPassword(int $length): string
    {
        $ascii = array_merge(range(48, 57), range(65, 90), range(97, 122), [33, 35, 36, 37, 63, 64]);
        $asciiLenght = count($ascii);
        $pwd = '';
        for ($i = 0; $i < $length; $i++) {
            $pwd .= chr($ascii[random_int(0, $asciiLenght - 1)]);
        }
        return $pwd;
    }
}
