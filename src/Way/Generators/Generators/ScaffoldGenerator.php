<?php

namespace Way\Generators\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Pluralizer;

class ScaffoldGenerator {

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
     * Update app/routes.php
     *
     * @param  string $name
     * @return void
     */
    public function updateRoutesFile($name)
    {
        $name = strtolower(Pluralizer::plural($name));

        $resourceRoute  = "\n\nRoute::resource('" . $name . "', '" . ucwords($name) . "Controller');";
        $routesFilePath =  app_path() . '/routes.php';

        if (strpos(file_get_contents($routesFilePath),$resourceRoute) === false) {
            $this->file->append($routesFilePath, $resourceRoute);
	}
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
