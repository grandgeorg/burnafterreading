<?php

declare(strict_types=1);

namespace Bar;

class Config
{
    public static function get(string $name): array
    {
        $appPath = __DIR__;
        $dataPath = realpath($appPath . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'data');
        if (!file_exists($dataPath) || !is_dir($dataPath)) {
            throw new \Exception('Data directory not found');
        }
        $templatePath = realpath($appPath . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'templates');
        if (!file_exists($templatePath) || !is_dir($templatePath)) {
            throw new \Exception('Template directory not found');
        }
        $dbFile = realpath($appPath . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'db.sqlite3');
        if (!$dbFile || !file_exists($dbFile) || !is_readable($dbFile)) {
            throw new \Exception('Database file not found');
        }
        $configFile = realpath($appPath . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . $name . '.php');
        if (!file_exists($configFile) || !is_readable($configFile)) {
            throw new \Exception('Config file not found');
        }
        $config = require $configFile;
        if (!is_array($config)) {
            throw new \Exception('Config file is not an array');
        }
        $config['app_path'] = $appPath;
        $config['data_path'] = $dataPath;
        $config['template_path'] = $templatePath;
        $config['db_file'] = $dbFile;
        $config['lang'] = self::getLang($config);
        $config['_'] = self::getTranslations($appPath, "langs", $config['lang']);
        $config['errors'] = [];
        $config['page'] = '';

        return $config;
    }

    /**
     * get language from HTTP_ACCEPT_LANGUAGE
     *
     * @param array $config config array
     * @return string
     */
    private static function getLang(array $config): string
    {
        $lang = $config['fallback_lang'] ?? 'en';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($langs as $value) {
                $choice = substr($value, 0, 2);
                if (in_array($choice, $config['langs'])) {
                    $lang = $choice;
                    break;
                }
            }
        }
        return $lang;
    }

    private static function getTranslations(string $appPath, string $file, string $lang): array
    {
        $translationsFile = realpath($appPath . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . $file . '.php');
        if (!$translationsFile || !file_exists($translationsFile) || !is_readable($translationsFile)) {
            throw new \Exception('Translations file not found');
        }
        $translations = require $translationsFile;
        if (!is_array($translations)) {
            throw new \Exception('Translations file is not an array');
        }
        if (!isset($translations[$lang])) {
            throw new \Exception('Translations file does not contain language');
        }
        return $translations[$lang];
    }
}
