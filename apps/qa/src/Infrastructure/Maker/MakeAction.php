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

final class MakeAction extends AbstractMaker
{
    private string $description = '';

    private string $outputName = '';

    /** @var list<array{name: string, type: string, description: string, default: string|null}> */
    private array $parameters = [];

    public function __construct(private readonly MakerHelper $makerHelper)
    {
    }

    public static function getCommandName(): string
    {
        return 'make:qalin:action';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new Action with CLI, Web, API, and tests';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The action name in PascalCase (e.g. <fg=yellow>UpgradeInstantlyForFree</>)')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Short description for CLI command and page title')
            ->addOption('output-name', 'o', InputOption::VALUE_REQUIRED, 'Output DTO class name in PascalCase (e.g. <fg=yellow>UpgradeInstantlyForFreed</>)')
            ->addOption('parameter', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Parameters as name:type:description[:default] (e.g. <fg=yellow>username:string:4-15 alphanumeric characters</>, <fg=yellow>levels:int:number of levels:1</>). Type defaults to string if omitted. Providing a default makes the parameter optional.')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $input->getArgument('name')) {
            $input->setArgument('name', $io->ask('Action name (PascalCase, e.g. UpgradeInstantlyForFree)'));
        }

        // Description: use --description option or prompt
        $descriptionOption = $input->getOption('description');
        if (\is_string($descriptionOption)) {
            $this->description = $descriptionOption;
        } else {
            $description = $io->ask('Short description (for CLI command and page title)');
            $this->description = \is_string($description) ? $description : '';
        }

        // Output name: use --output-name option or prompt
        $outputNameOption = $input->getOption('output-name');
        if (\is_string($outputNameOption)) {
            $this->outputName = $outputNameOption;
        } else {
            /** @var string $actionName */
            $actionName = $input->getArgument('name');
            $outputName = $io->ask("Output DTO class name (PascalCase, e.g. {$actionName}ed)", "{$actionName}ed");
            $this->outputName = \is_string($outputName) ? $outputName : "{$actionName}ed";
        }

        // Parameters: use --parameter options or prompt
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
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        /** @var string $actionName */
        $actionName = $input->getArgument('name');

        // interact() is skipped when --no-interaction is used; parse options as fallback
        if ('' === $this->description) {
            $descriptionOption = $input->getOption('description');
            $this->description = \is_string($descriptionOption) ? $descriptionOption : '';
        }

        if ('' === $this->outputName) {
            $outputNameOption = $input->getOption('output-name');
            $this->outputName = \is_string($outputNameOption) ? $outputNameOption : "{$actionName}ed";
        }

        if ([] === $this->parameters) {
            $this->parameters = $this->makerHelper->parseParameterOptions($input);
        }

        $description = $this->description;
        $parameters = $this->parameters;

        $actionKebab = $this->makerHelper->toKebabCase($actionName);
        $actionTitle = $this->makerHelper->toTitleCase($actionName);

        $actionSnake = $this->makerHelper->toSnakeCase($actionName);
        $actionCamel = lcfirst($actionName);

        $templateDir = __DIR__.'/../../../templates/maker';
        $testsDir = __DIR__.'/../../../tests';
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

        $variables = [
            'action_name' => $actionName,
            'action_output_name' => $this->outputName,
            'action_kebab' => $actionKebab,
            'action_title' => $actionTitle,
            'action_snake' => $actionSnake,
            'action_camel' => $actionCamel,
            'description' => $description,
            'action_parameters' => $parameters,
            'has_username_param' => $hasUsernameParam,
            'has_optional_params' => $hasOptionalParams,
        ];

        // 1. Action input DTO
        $generator->generateClass(
            "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}",
            "{$templateDir}/Qalin/Action/HandlerInput.tpl.php",
            $variables,
        );

        // 2. Action handler
        $generator->generateClass(
            "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}Handler",
            "{$templateDir}/Qalin/Action/Handler.tpl.php",
            $variables,
        );

        // 3. Action output DTO
        $generator->generateClass(
            "Bl\\Qa\\Application\\Action\\{$actionName}\\{$this->outputName}",
            "{$templateDir}/Qalin/Action/HandlerOutput.tpl.php",
            $variables,
        );

        // 4. CLI Command
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Cli\\Action\\{$actionName}Command",
            "{$templateDir}/Qalin/Action/CliCommand.tpl.php",
            $variables,
        );

        // 5. Web Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Web\\Action\\{$actionName}Controller",
            "{$templateDir}/Qalin/Action/WebController.tpl.php",
            $variables,
        );

        // 6. API Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Api\\Action\\{$actionName}Controller",
            "{$templateDir}/Qalin/Action/ApiController.tpl.php",
            $variables,
        );

        // 7. Twig template
        $generator->generateTemplate(
            "qalin/action/{$actionKebab}.html.twig",
            "{$templateDir}/Qalin/Action/TwigTemplate.tpl.php",
            $variables,
        );

        // 8. Spec action input DTO test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Action\\{$actionName}Test",
            "{$templateDir}/Qalin/Action/HandlerInputSpecTest.tpl.php",
            $variables,
        );

        // 9. Spec action handler test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Action\\{$actionName}HandlerTest",
            "{$templateDir}/Qalin/Action/HandlerSpecTest.tpl.php",
            $variables,
        );

        // 10. CLI Command integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Cli\\Action\\{$actionName}CommandTest",
            "{$templateDir}/Qalin/Action/CliCommandTest.tpl.php",
            $variables,
        );

        // 11. Web Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Web\\Action\\{$actionName}ControllerTest",
            "{$templateDir}/Qalin/Action/WebControllerTest.tpl.php",
            $variables,
        );

        // 12. API Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Api\\Action\\{$actionName}ControllerTest",
            "{$templateDir}/Qalin/Action/ApiControllerTest.tpl.php",
            $variables,
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next steps:');
        $io->listing([
            "Implement domain logic in <fg=yellow>src/Application/Action/{$actionName}/{$actionName}Handler.php</>",
            "Return <fg=yellow>{$this->outputName}</> from the handler's <fg=yellow>run()</> method",
            'Fill in TODO comments in generated files',
            'Run <fg=yellow>make phpstan-analyze</> and <fg=yellow>make phpunit</> to verify',
        ]);
    }
}
