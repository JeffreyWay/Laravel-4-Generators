<?php

namespace Way\Generators\Generators;

use Way\Generators\NameParser;

class SeedGenerator extends Generator {

    /**
     * Fetch the compiled template for a seed
     *
     * @param  string $template Path to template
     * @param  string $classNameparser
     * @return string Compiled template
     */
    protected function getTemplate($template, NameParser $nameparser)
    {
        $this->template = $this->file->get($template);
        $pluralModel = strtolower($nameparser->get('controller'));

        $this->template = str_replace('{{className}}', $nameparser->get('controller'), $this->template);

        return str_replace('{{pluralModel}}', $pluralModel, $this->template);
    }

    /**
    * Updates the DatabaseSeeder file's run method to
    * call this new seed class
    * @return void
    */
    public function updateDatabaseSeederRunMethod($className)
    {
        $databaseSeederPath = app_path() . '/database/seeds/DatabaseSeeder.php';

        $content = $this->file->get($databaseSeederPath);
        $content = preg_replace("/(run\(\).+?)}/us", "$1\t\$this->call('{$className}');\n\t}", $content);

        $this->file->put($databaseSeederPath, $content);
    }

}