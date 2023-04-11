<?php

class Main {
    private const LANGUAGES = ['pl', /*to uncomment later: 'en'*/];
    private const MISSING_VARIABLE_TEXT = '!!!';

    private const RESOURCES_RELATED_PATH = '../../resources';
    private const TEMPLATE_FILE_PATH = self::RESOURCES_RELATED_PATH . '/html/index.html';
    private const VARIABLES_FILE_PATH = self::RESOURCES_RELATED_PATH . '/json/language-variables.json';

    public function run(): void {
        try {
            $template = $this->getFileContent(self::TEMPLATE_FILE_PATH);
            $variablesJson = $this->getFileContent(self::VARIABLES_FILE_PATH);

            $variables = $this->getVariablesData($variablesJson);
            $content = $this->getReplacedContent($template, $variables);

            $this->showContent($content);
        } catch (Throwable $e) {
            $this->showContent('Error: ' . $e->getMessage() . ' at line ' . $e->getLine());
        }
    }

    private function getSiteLanguage(): string {
        $domain = $_SERVER['SERVER_NAME'] ?? '';
        $domainElements = explode('.', $domain);
        $firstElement = $domainElements[0] ?? '';

        foreach (self::LANGUAGES as $language) {
            if ($language === $firstElement) {
                return $language;
            }
        }

        return self::LANGUAGES[0];
    }

    private function getFileContent(string $filePath): string {
        return @file_get_contents($filePath);
    }

    private function getVariablesData(string $variablesJson): array {
        return @json_decode($variablesJson, true);
    }

    private function getReplacedContent(string $content, array $variables): string {
        $language = $this->getSiteLanguage();

        foreach ($variables as $name => $values) {
            $content = str_replace("#$name#", $values[$language] ?? self::MISSING_VARIABLE_TEXT, $content);
        }

        return $content;
    }

    private function showContent(string $content): void {
        echo $content;
    }
}

(new Main())->run();
