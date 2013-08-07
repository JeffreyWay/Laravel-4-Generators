<?php namespace Way\Generators;

use Illuminate\Support\Pluralizer;

class NameParser {
    /**
     * @var array
     */
    protected $parts;
    public function __construct($name = null)
    {
        if(! is_null($name)) {
            $this->parse($name);
        }
    }
    /**
     * Parse name and store it for further use
     * @param $name
     * @return int
     */
    public function parse($name)
    {

        $parts = pathinfo($name);

        //Complete argument
        $parts['full'] = $name; // admin\dogs

        $parts['controller'] = Pluralizer::plural(ucfirst($parts['filename'])); //Dogs
        $parts['model'] = Pluralizer::singular(ucfirst($parts['filename'])); //Dog

        $parts['url'] = $parts['dirname'] . '/' . strtolower($parts['controller']); // admin/dogs
        $parts['has_namespace'] = strstr($name, '\\') !== false; // true/false

        if($parts['has_namespace']) {
            $full_class = '';
            $l = strlen($name);
            while($l > 0 && $name[--$l] != '/') {
                $full_class = $name[$l] . $full_class;
            }
            $parts['full_controller'] = Pluralizer::plural($this->format_namespace($full_class)); // Admin\Dogs
            $parts['full_model'] = Pluralizer::singular($this->format_namespace($full_class)); // Admin\Dog

            $parts['namespace'] = str_replace('\\' . $parts['controller'], '', $parts['full_controller']); // Admin
        }else {
            $parts['full_controller'] = $parts['controller']; // Dogs
            $parts['full_model'] = $parts['model']; // Dog
        }
        $this->parts = $parts;
    }

    /**
     * Get parsed name part
     * @param null $part
     * @return array|string
     */
    public function get($part = null) {
        if(is_null($part)) {
            return $this->parts;
        }
        return isset($this->parts[$part]) ? $this->parts[$part] : '';
    }

    private function format_namespace($string) {
        $string = ucwords(str_replace('\\', ' ', $string));
        return str_replace(' ', '\\', $string);
    }
}