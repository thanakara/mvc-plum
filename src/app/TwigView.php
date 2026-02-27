<?php

declare(strict_types=1);

namespace App;

use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class TwigView
{
    private static ?Environment $twig = null;

    public function __construct(
        protected string $view,
        protected array $params = [],
    ) {}

    private static function getTwig(): Environment
    {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(TEMPLATESDIR);
            self::$twig = new Environment($loader, [
                "cache" => false, // Disable for development;
                "debug" => true,
                // "cache"       => ROOT . "/storage/twig_cache",
                // "auto_reload" => true,
                // "debug"       => $_ENV["APP_DEBUG"] ?? false,
            ]);
            self::$twig->addExtension(new DebugExtension());
        }

        return self::$twig;
    }

    public function render(): string
    {
        try {
            return self::getTwig()->render($this->view . '.twig', $this->params);
        } catch (\Twig\Error\LoaderError $e) {
            throw new RuntimeException("View Not Found: {$this->view}", previous: $e);
        }
    }

    public static function make(string $view, array $params = []): static
    {
        return new static($view, $params);
    }

    // when App calls the run() method:
    public function __toString(): string
    {
        return $this->render();
    }
}
