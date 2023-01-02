<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring;

final class Translator
{
    private const DEFAULT_LANGUAGE = 'english';

    private static ?self $instance = null;

    /**
     * @var array<mixed>
     */
    private array $whmcsConfig = [];

    /**
     * @param array<mixed> $whmcsConfig
     */
    public static function getInstance(array $whmcsConfig): self
    {
        if (self::$instance === null) {
            self::$instance = new self($whmcsConfig);
        }

        return self::$instance;
    }

    /**
     * @param array<mixed> $whmcsConfig
     */
    private function __construct(array $whmcsConfig)
    {
        $this->whmcsConfig = $whmcsConfig;

        $this->loadTranslations();
    }

    public function translate(string $key): string
    {
        global $_LANG;

        return $_LANG[$key] ?? $key;
    }

    private function loadTranslations(): void
    {
        global $_LANG;

        $language = $_SESSION['Language'] ?? $_SESSION['adminlang'] ?? $this->whmcsConfig['Language'];

        if (!in_array($language, self::getAvailableLanguages(), true)) {
            $language = self::DEFAULT_LANGUAGE;
        }

        require self::getLanguageDir() . '/' . $language . '.php';
    }

    private static function getLanguageDir(): string
    {
        return dirname(__DIR__) . '/lang';
    }

    /**
     * @return string[]
     */
    private static function getAvailableLanguages(): array
    {
        $results = glob(self::getLanguageDir() . '/*.php');

        if ($results === false) {
            return [];
        }

        $languages = [];

        foreach ($results as $filename) {
            $languages[] = pathinfo($filename, PATHINFO_FILENAME);
        }

        return $languages;
    }
}
