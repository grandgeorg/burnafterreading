<?php

declare(strict_types = 1);

namespace Bar;

use Bar\Config;
use Bar\View;
use Bar\Limit;

class App
{

    protected array $config;
    protected View $view;
    protected Limit $limit;
    protected string $page = '';

    public function __construct(string $dir)
    {
        $this->checkRequestUri();
        $this->config = Config::get('config');
        $this->config['client_path'] = $dir;
        $this->config['client_url'] = $this->getClientUrl();
        $this->view = new View();
        $this->limit = new Limit($this->config['db_file']);
    }

    public function addTrailingSlash(string $url): string
    {
        return rtrim($url, '/') . '/';
    }

    public function checkRequestUri(): void
    {
        $request = explode('?', $_SERVER['REQUEST_URI'], 2);
        $queryParams = (isset($request[1]) && !empty($request[1])) ? '?' . $request[1] : '';
        if (substr($request[0], -1) !== '/') {
            header('Location: ' . $this->addTrailingSlash($request[0]) . $queryParams);
            exit;
        }
    }

    public function getClientUrl(): string
    {
        return explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    }

    protected function addToPage(string $template, ?array $data = []): void
    {
        $this->page .= $this->view->render($template, $data);
    }

    public function setSession(): void
    {
        ini_set('session.use_strict_mode', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $this->config['client_url'],
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }

    public function destroySession(): void
    {
        session_unset();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        session_write_close();
    }

    public function addCSRF(string $name): void
    {
        $csrf = bin2hex(random_bytes(16));
        $_SESSION[$name] = $csrf;
        output_add_rewrite_var($name, $csrf);
    }

    public function checkCSRF(string $name): bool
    {
        if (
            isset($_POST[$name], $_SESSION[$name]) &&
            !empty($_POST[$name]) && !empty($_SESSION[$name]) &&
            $_SESSION[$name] === $_POST[$name]
        ) {
            return true;
        }
        return false;
    }

    public function setHeaders(): void
    {
        $cspheader = 'default-src \'none\'; '
            . 'base-uri \'self\'; '
            . 'form-action \'self\'; '
            . 'manifest-src \'self\'; '
            . 'connect-src \'none\'; '
            . 'script-src \'self\'; '
            . 'style-src \'self\'; '
            . 'font-src \'self\'; '
            . 'frame-src \'none\'; '
            . 'frame-ancestors \'none\'; '
            . 'img-src \'self\' data: blob:; '
            . 'media-src blob:; '
            . 'object-src \'none\'; '
            . 'worker-src \'none\'; '
            . 'child-src \'none\';';

        $time = gmdate('D, d M Y H:i:s \G\M\T');
        header('Cache-Control: no-store, no-cache, no-transform, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: ' . $time);
        header('Last-Modified: ' . $time);
        header('Content-Security-Policy: ' . $cspheader);
        header('Cross-Origin-Resource-Policy: same-origin');
        header('Cross-Origin-Embedder-Policy: require-corp');
        header('X-Content-Type-Options: nosniff');
        header('X-Robots-Tag: noindex, nofollow', true);

    }

}