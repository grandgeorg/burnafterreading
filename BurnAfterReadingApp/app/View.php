<?php

declare (strict_types = 1);

namespace Bar;

use Throwable;

class View
{

    public function render(string $template, ?array $data = []): string
    {
        $templateFile = $this->getTemplatePath($template);
        $output = '';
        $renderer = static function($template, $data): void {
            require $template;
        };
        try {
            ob_start();
            $renderer($templateFile, $data);
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return $output;
    }

    protected function getTemplatePath(string $template): string
    {
        $templateFile = realpath(__DIR__ . '/../templates/' . $template . '.php');
        if (!file_exists($templateFile)) {
            throw new \Exception('Template file not found');
        }
        return $templateFile;
    }
}
