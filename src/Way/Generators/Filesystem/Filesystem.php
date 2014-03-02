<?php namespace Way\Generators\Filesystem;

class Filesystem {

    public function make($file, $content)
    {
        return file_put_contents($file, $content);
    }

    public function exists($file)
    {
        return file_exists($file);
    }

    public function get($file)
    {
        return file_get_contents($file);
    }

} 