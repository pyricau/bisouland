<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class MakeScenario extends AbstractMaker
{
    private string $description = '';

    /** @var list<array{name: string, type: string, description: string, default: string|null}> */
    private array $parameters = [];

    /** @var list<string> */
    private array $actions = [];

    public function __construct(private readonly MakerHelper $makerHelper)
    {
    }

    public static function getCommandName(): string
    {
        return 'make:qalin:scenario';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new Scenario with CLI, Web, API, and tests';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The scenario name in PascalCase (e.g. <fg=yellow>SignInNewPlayer</>)')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Short description for CLI command and page title')
            ->addOption('parameter', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Parameters as name:type:description[:default] (e.g. <fg=yellow>username:string:4-15 alphanumeric characters</>, <fg=yellow>levels:int:number of levels:1</>). Type defaults to string if omitted. Providing a default makes the parameter optional.')
            ->addOption('action', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Actions to compose in PascalCase (e.g. <fg=yellow>SignUpNewPlayer</>, <fg=yellow>SignInPlayer</>)')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $input->getArgument('name')) {
            $input->setArgument('name', $io->ask('Scenario name (PascalCase, e.g. SignInNewPlayer)'));
        }

        $descriptionOption = $input->getOption('description');
        if (\is_string($descriptionOption)) {
            $this->description = $descriptionOption;
        } else {
            $description = $io->ask('Short description (for CLI command and page title)');
            $this->description = \is_string($description) ? $description : '';
        }

        if ([] !== $input->getOption('parameter')) {
            $this->parameters = $this->makerHelper->parseParameterOptions($input);
        } else {
            $this->parameters = [];
            $io->note('Add parameters (empty name to stop):');

            while (true) {
                $paramName = $io->ask('Parameter name (camelCase, e.g. username)');

                if (!\is_string($paramName) || '' === $paramName) {
                    break;
                }

                $paramType = $io->choice("Type for '{$paramName}'", ['string', 'int'], 'string');
                $paramDescription = $io->ask("Description for '{$paramName}'");
                $paramDefault = $io->ask("Default value for '{$paramName}' (leave empty to make it required)");
                $this->parameters[] = [
                    'name' => $paramName,
                    'type' => \is_string($paramType) ? $paramType : 'string',
                    'description' => \is_string($paramDescription) ? $paramDescription : '',
                    'default' => \is_string($paramDefault) && '' !== $paramDefault ? $paramDefault : null,
                ];
            }
        }

        if ([] !== $input->getOption('action')) {
            /** @var list<string> $actionOptions */
            $actionOptions = $input->getOption('action');
            $this->actions = $actionOptions;
        } else {
            $this->actions = [];
            $io->note('Add actions to compose (empty name to stop):');

            while (true) {
                $actionName = $io->ask('Action name (PascalCase, e.g. SignUpNewPlayer)');

                if (!\is_string($actionName) || '' === $actionName) {
                    break;
                }

                $this->actions[] = $actionName;
            }
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        /** @var string $scenarioName */
        $scenarioName = $input->getArgument('name');

        // interact() is skipped when --no-interaction is used; parse options as fallback
        if ('' === $this->description) {
            $descriptionOption = $input->getOption('description');
            $this->description = \is_string($descriptionOption) ? $descriptionOption : '';
        }

        if ([] === $this->parameters) {
            $this->parameters = $this->makerHelper->parseParameterOptions($input);
        }

        if ([] === $this->actions) {
            /** @var list<string> $actionOptions */
            $actionOptions = $input->getOption('action');
            $this->actions = $actionOptions;
        }

        $description = $this->description;
        $parameters = $this->parameters;

        $scenarioKebab = $this->makerHelper->toKebabCase($scenarioName);
        $scenarioTitle = $this->makerHelper->toTitleCase($scenarioName);
        $scenarioSnake = $this->makerHelper->toSnakeCase($scenarioName);
        $scenarioCamel = lcfirst($scenarioName);

        $templateDir = __DIR__.'/../../../templates/maker';
        $testsDir = __DIR__.'/../../../tests';
        $srcDir = __DIR__.'/../../../src';
        $hasUsernameParam = false;
        $hasOptionalParams = false;
        foreach ($parameters as &$param) {
            $fixture = $this->makerHelper->discoverFixture($param['name'], $testsDir);
            $param['fixture_fqcn'] = $fixture['fqcn'] ?? null;
            $param['fixture_class'] = $fixture['class'] ?? null;
            $param['value_object_class'] = $fixture['value_object_class'] ?? null;
            $param['value_object_fqcn'] = $fixture['value_object_fqcn'] ?? null;
            $param['value_object_var'] = $fixture['value_object_var'] ?? null;
            if ('username' === $param['name']) {
                $hasUsernameParam = true;
            }

            if (null !== $param['default']) {
                $hasOptionalParams = true;
            }
        }

        unset($param);

        // Required params must come before optional ones (PHP default value constraint)
        usort($parameters, static fn (array $a, array $b): int => (null !== $a['default']) <=> (null !== $b['default']));

        $actionDependencies = [];
        foreach ($this->actions as $action) {
            $discovered = $this->discoverAction($action, $srcDir);
            if (null !== $discovered) {
                $actionDependencies[] = $discovered;
            } else {
                $io->warning("Action handler not found for \"{$action}\", skipped. Check the name is PascalCase and its handler exists under src/Application/Action/{$action}/.");
            }
        }

        $variables = [
            'scenario_name' => $scenarioName,
            'scenario_kebab' => $scenarioKebab,
            'scenario_title' => $scenarioTitle,
            'scenario_snake' => $scenarioSnake,
            'scenario_camel' => $scenarioCamel,
            'description' => $description,
            'scenario_parameters' => $parameters,
            'action_dependencies' => $actionDependencies,
            'has_username_param' => $hasUsernameParam,
            'has_optional_params' => $hasOptionalParams,
        ];

        // 1. Scenario input DTO
        $generator->generateClass(
            "Bl\\Qa\\Application\\Scenario\\{$scenarioName}\\{$scenarioName}",
            "{$templateDir}/Qalin/Scenario/HandlerInput.tpl.php",
            $variables,
        );

        // 2. Scenario handler
        $generator->generateClass(
            "Bl\\Qa\\Application\\Scenario\\{$scenarioName}\\{$scenarioName}Handler",
            "{$templateDir}/Qalin/Scenario/Handler.tpl.php",
            $variables,
        );

        // 3. Scenario output DTO
        $generator->generateClass(
            "Bl\\Qa\\Application\\Scenario\\{$scenarioName}\\{$scenarioName}Output",
            "{$templateDir}/Qalin/Scenario/HandlerOutput.tpl.php",
            $variables,
        );

        // 4. CLI Command
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Cli\\Scenario\\{$scenarioName}Command",
            "{$templateDir}/Qalin/Scenario/CliCommand.tpl.php",
            $variables,
        );

        // 5. Web Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Web\\Scenario\\{$scenarioName}Controller",
            "{$templateDir}/Qalin/Scenario/WebController.tpl.php",
            $variables,
        );

        // 6. API Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Api\\Scenario\\{$scenarioName}Controller",
            "{$templateDir}/Qalin/Scenario/ApiController.tpl.php",
            $variables,
        );

        // 7. Twig template
        $generator->generateTemplate(
            "qalin/scenario/{$scenarioKebab}.html.twig",
            "{$templateDir}/Qalin/Scenario/TwigTemplate.tpl.php",
            $variables,
        );

        // 8. Spec scenario input DTO test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Scenario\\{$scenarioName}Test",
            "{$templateDir}/Qalin/Scenario/HandlerInputSpecTest.tpl.php",
            $variables,
        );

        // 9. Spec scenario handler test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Scenario\\{$scenarioName}HandlerTest",
            "{$templateDir}/Qalin/Scenario/HandlerSpecTest.tpl.php",
            $variables,
        );

        // 10. CLI Command integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Cli\\Scenario\\{$scenarioName}CommandTest",
            "{$templateDir}/Qalin/Scenario/CliCommandTest.tpl.php",
            $variables,
        );

        // 11. Web Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Web\\Scenario\\{$scenarioName}ControllerTest",
            "{$templateDir}/Qalin/Scenario/WebControllerTest.tpl.php",
            $variables,
        );

        // 12. API Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Api\\Scenario\\{$scenarioName}ControllerTest",
            "{$templateDir}/Qalin/Scenario/ApiControllerTest.tpl.php",
            $variables,
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next steps:');
        $io->listing([
            "Implement the handler in <fg=yellow>src/Application/Scenario/{$scenarioName}/{$scenarioName}Handler.php</>",
            "Implement the output in <fg=yellow>src/Application/Scenario/{$scenarioName}/{$scenarioName}Output.php</>",
            'Fill in TODO comments in generated files',
            'Run <fg=yellow>make phpstan-analyze</> and <fg=yellow>make phpunit</> to verify',
        ]);
    }

    /**
     * @return array{name: string, camel_name: string, handler_class: string, handler_fqcn: string, input_class: string, input_fqcn: string, output_class: string, output_fqcn: string}|null
     */
    private function discoverAction(string $actionName, string $srcDir): ?array
    {
        $handlerFile = "{$srcDir}/Application/Action/{$actionName}/{$actionName}Handler.php";

        if (!file_exists($handlerFile)) {
            return null;
        }

        return [
            'name' => $actionName,
            'camel_name' => lcfirst($actionName),
            'handler_class' => "{$actionName}Handler",
            'handler_fqcn' => "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}Handler",
            'input_class' => $actionName,
            'input_fqcn' => "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}",
            'output_class' => "{$actionName}Output",
            'output_fqcn' => "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}Output",
        ];
    }
}
