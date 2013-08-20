<?php

namespace Way\Generators\Generators;

class TestGenerator extends Generator {

    /**
     * Fetch the compiled template for a test
     *
     * @param  string $template Path to template
     * @param  string $className
     * @return string Compiled template
     */
    protected function getTemplate($template, $className)
    {
        $pluralModel = strtolower(str_replace('Test', '', $className)); //  dogs
        $model = str_singular($pluralModel); // dog
        $Model = ucwords($model); // Dog

        $methods = $this->generateTestMethods($className);

        if($methods) {
            return $this->generateTemplate($className, $methods);
        }

        $template = $this->file->get($template);

        foreach(array('pluralModel', 'model', 'Model', 'className') as $var)
        {
            $template = str_replace('{{'.$var.'}}', $$var, $template);
        }

        return $template;
    }

    /**
     * Generate test methods for a given class
     *
     * @param  string $className
     * @return array|false List of methods or false if the class does not exist
     */
    protected function generateTestMethods($className) {
        try {
            $class = new \ReflectionClass(str_replace('Test', '', $className));
            $methods = $class->getMethods();
            $testMethods = array();
        } catch (\Exception $e) {
            return false;
        }

        foreach ($methods as $method) {
            if($method->class == $class->name && $method->name != '__construct')
                $testMethods[] = 'test' . ucwords($method->name);
        }
        return $testMethods;
    }

    /**
     * Generate a template with methods for the given class
     *
     * @param  string $className
     * @param  array List of method names
     * @return string Compiled template
     */
    protected  function generateTemplate($className, $methods) {
        $template = "<?php \n\nclass $className extends TestCase {\n\n";

        foreach ($methods as $m) {
            $template .= "\tpublic function $m()\n\t{\n\n\t}\n\n";
        }
        $template .= "}";
        return $template;
    }

}