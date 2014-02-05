<?php

namespace Way\Generators\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Pluralizer;

class ResourceGenerator {

    /**
     * File system instance
     *
     * @var File
     */
    protected $file;

    /**
     * Constructor
     *
     * @param $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Update app/routes.php if necessary
     *
     * @param  string $name
     * @return void
     */
    public function updateRoutesFile($name)
    {
        $name = strtolower(Pluralizer::plural($name));

        $routesPath = app_path() . '/routes.php';
        $routesContent = $this->file->get($routesPath);

        $resourceRoute = "Route::resource('" . $name . "', '" . ucwords($name) . "Controller');";

        if ( ! strpos($routesContent, $resourceRoute))
        {
            return $this->file->append($routesPath, "\n\n".$resourceRoute);
        }

        return false;
    }

    /**
     * Create any number of folders
     *
     * @param  string|array $folders
     * @return void
     */
    public function folders($folders)
    {
        foreach((array) $folders as $folderPath)
        {
            if (! $this->file->exists($folderPath))
            {
                $this->file->makeDirectory($folderPath);
            }
        }
    }

}
