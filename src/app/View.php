<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

class View
{
    public function __construct(
        protected string $view,
        protected array $params = [],
    ) {}

    public function render(): string
    {
        $viewPath = VIEWDIR . "/" . $this->view . ".php";

        if (! file_exists($viewPath)) {
            throw new RuntimeException("View Not Found {$this->view}");
        }

        // not safe for sensitive information
        extract($this->params);

        ob_start();
        include $viewPath;

        return (string) ob_get_clean();
    }

    public static function make(string $view, array $params = []): static
    {
        return new static($view, $params);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function __get(string $name)
    {
        return $this->params[$name] ?? null;
    }
}
