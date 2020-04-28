<?php

namespace App\Service;

use Slim\Views\Twig;
use Twig\Loader\FilesystemLoader;

class View extends Twig
{

    /**
     * @param string|array $path     Path(s) to templates directory
     * @param array        $settings Twig environment settings
     *
     * @throws LoaderError When the template cannot be found
     *
     * @return Twig
     */
    public static function create($path, array $settings = []): self
    {
        $loader = new FilesystemLoader();

        $paths = is_array($path) ? $path : [$path];
        foreach ($paths as $namespace => $path) {
            if (is_string($namespace)) {
                $loader->setPaths($path, $namespace);
            } else {
                $loader->addPath($path);
            }
        }

        return new self($loader, $settings);
    }
}