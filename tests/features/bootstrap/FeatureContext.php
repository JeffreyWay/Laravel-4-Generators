<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

require_once __DIR__.'/../../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * The command that we're testing
     *
     * @var CommandTester
     */
    protected $tester;

    /**
     * @beforeSuite
     */
    public static function bootstrapLaravel()
    {
        require __DIR__.'/../../../../../../vendor/autoload.php';
        require __DIR__.'/../../../../../../bootstrap/start.php';
    }

    /**
     * @AfterScenario
     */
    public function tearDown()
    {
        array_map('unlink', glob(app_path('database/migrations/*')));
        array_map('unlink', glob(app_path('database/seeds/*')));
        array_map('unlink', glob(app_path('models/*')));

        $this->tester = null;
    }

    /**
     * @When /^I generate a migration with name \'([^\']*)\' and fields \'([^\']*)\'$/
     */
    public function iGenerateAMigrationWithNameAndFields($migrationName, $fields)
    {
        $this->tester = new CommandTester(App::make('Way\Generators\Commands\MigrationGeneratorCommand'));
        $this->tester->execute([
            'migrationName' => $migrationName,
            '--fields' => $fields,
            '--testing' => true
        ]);
    }

    /**
     * @When /^I generate a model with "([^"]*)"$/
     */
    public function iGenerateAModelWith($modelName)
    {
        $this->tester = new CommandTester(App::make('Way\Generators\Commands\ModelGeneratorCommand'));
        $this->tester->execute(compact('modelName'));
    }

    /**
     * @When /^I generate a seed with "([^"]*)"$/
     */
    public function iGenerateASeedWith($tableName)
    {
        $this->tester = new CommandTester(App::make('Way\Generators\Commands\SeederGeneratorCommand'));
        $this->tester->execute(compact('tableName'));
    }

    /**
     * @When /^I generate a migration with "([^"]*)"$/
     */
    public function iGenerateAMigrationWith($migrationName)
    {
        $this->tester = new CommandTester(App::make('Way\Generators\Commands\MigrationGeneratorCommand'));
        $this->tester->execute(compact('migrationName'));
    }

    /**
     * @Given /^the generated migration should match my \'([^\']*)\' stub$/
     */
    public function theGeneratedMigrationShouldMatchMyStub($stubName)
    {
        $expected = file_get_contents(__DIR__."/../../stubs/{$stubName}.txt");
        $actual = file_get_contents(glob(base_path('app/database/migrations/*'))[0]);

        // Let's compare the stub against what was actually generated.
        assertEquals($expected, $actual);
    }

    /**
     * @Then /^I should see "([^"]*)"$/
     */
    public function iShouldSee($output)
    {
        assertContains($output, $this->tester->getDisplay());
    }

    /**
     * @Given /^"([^"]*)" should match my stub$/
     */
    public function shouldMatchMyStub($generatedFilePath)
    {
        // We'll use the name of the generated file as
        // the basic for our stub lookup.
        $stubName = pathinfo($generatedFilePath)['filename'];

        $expected = file_get_contents(__DIR__."/../../stubs/{$stubName}.txt");
        $actual = file_get_contents(base_path($generatedFilePath));

        // Let's compare the stub against what was actually generated.
        assertEquals($expected, $actual);
    }
}
