<?php namespace Way\Generators;

use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Filesystem\FileAlreadyExists;

class Generator {

    /**
     * @var Filesystem
     */
    protected $file;

    public function __construct(Filesystem $file)
    {
        $this->file = $file;
    }

    public function generate($file, $content)
    {
        if ( $this->file->exists($file))
        {
            throw new FileAlreadyExists;
        }

        $this->file->make($file, $content);
    }
}
