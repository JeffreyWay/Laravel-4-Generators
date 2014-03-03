<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Console\Tester\CommandTester;
use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Generator;
use Way\Generators\Laravel\ModelGeneratorCommand;
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
        @unlink(app_path('models/Order.php'));
        @unlink(app_path('database/seeds/OrdersTableSeeder.php'));

        $this->tester = null;
    }

    /**
     * @When /^I generate a model with "([^"]*)"$/
     */
    public function iGenerateAModelWith($modelName)
    {
        $this->tester = new CommandTester(App::make('Way\Generators\Laravel\ModelGeneratorCommand'));
        $this->tester->execute(compact('modelName'));
    }

    /**
     * @When /^I generate a seed with "([^"]*)"$/
     */
    public function iGenerateASeedWith($tableName)
    {
        $this->tester = new CommandTester(App::make('Way\Generators\Laravel\SeederGeneratorCommand'));
        $this->tester->execute(compact('tableName'));
    }

    /**
     * @Then /^I should see "([^"]*)"$/
     */
    public function iShouldSee($output)
    {
        $display = $this->tester->getDisplay();

        assertContains($output, $display);
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
