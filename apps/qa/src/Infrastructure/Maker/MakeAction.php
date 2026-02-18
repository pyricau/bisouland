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

    /** @var list<array{name: string, type: string, description: string, default: string|null}> */
    private array $parameters = [];

    public static function getCommandName(): string
    {
        return 'make:action';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new Action with CLI, Web, API, and tests';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The action name in PascalCase (e.g. <fg=yellow>InstantFreeUpgrade</>)')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Short description for CLI command and page title')
            ->addOption('parameter', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Parameters as name:type:description[:default] (e.g. <fg=yellow>username:string:4-15 alphanumeric characters</>, <fg=yellow>levels:int:number of levels:1</>). Type defaults to string if omitted. Providing a default makes the parameter optional.')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $input->getArgument('name')) {
            $input->setArgument('name', $io->ask('Action name (PascalCase, e.g. InstantFreeUpgrade)'));
        }

        // Description: use --description option or prompt
        $descriptionOption = $input->getOption('description');
        if (\is_string($descriptionOption)) {
            $this->description = $descriptionOption;
        } else {
            $description = $io->ask('Short description (for CLI command and page title)');
            $this->description = \is_string($description) ? $description : '';
        }

        // Parameters: use --parameter options or prompt
        if ([] !== $input->getOption('parameter')) {
            $this->parseParameterOptions($input);
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

        if ([] === $this->parameters) {
            $this->parseParameterOptions($input);
        }

        $description = $this->description;
        $parameters = $this->parameters;

        $actionKebab = $this->toKebabCase($actionName);
        $actionTitle = $this->toTitleCase($actionName);

        $actionSnake = $this->toSnakeCase($actionName);
        $actionCamel = lcfirst($actionName);

        $templateDir = __DIR__.'/../../../templates/maker';
        $testsDir = __DIR__.'/../../../tests';
        $hasUsernameParam = false;
        $hasOptionalParams = false;
        foreach ($parameters as &$param) {
            $fixture = $this->discoverFixture($param['name'], $testsDir);
            $param['fixture_fqcn'] = $fixture['fqcn'] ?? null;
            $param['fixture_class'] = $fixture['class'] ?? null;
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
            "{$templateDir}/Action.tpl.php",
            $variables,
        );

        // 2. Action handler
        $generator->generateClass(
            "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}Handler",
            "{$templateDir}/ActionHandler.tpl.php",
            $variables,
        );

        // 3. Action output DTO
        $generator->generateClass(
            "Bl\\Qa\\Application\\Action\\{$actionName}\\{$actionName}Output",
            "{$templateDir}/ActionOutput.tpl.php",
            $variables,
        );

        // 4. CLI Command
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Cli\\Action\\{$actionName}Command",
            "{$templateDir}/CliCommand.tpl.php",
            $variables,
        );

        // 5. Web Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Web\\Action\\{$actionName}Controller",
            "{$templateDir}/WebController.tpl.php",
            $variables,
        );

        // 6. API Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Api\\Action\\{$actionName}Controller",
            "{$templateDir}/ApiController.tpl.php",
            $variables,
        );

        // 7. Twig template
        $generator->generateTemplate(
            "actions/{$actionKebab}.html.twig",
            "{$templateDir}/TwigTemplate.tpl.php",
            $variables,
        );

        // 8. Spec action input DTO test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Action\\{$actionName}Test",
            "{$templateDir}/SpecActionTest.tpl.php",
            $variables,
        );

        // 9. Spec action handler test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Action\\{$actionName}HandlerTest",
            "{$templateDir}/SpecHandlerTest.tpl.php",
            $variables,
        );

        // 10. CLI Command integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Cli\\Action\\{$actionName}CommandTest",
            "{$templateDir}/CliCommandTest.tpl.php",
            $variables,
        );

        // 11. Web Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Web\\Action\\{$actionName}ControllerTest",
            "{$templateDir}/WebControllerTest.tpl.php",
            $variables,
        );

        // 12. API Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Api\\Action\\{$actionName}ControllerTest",
            "{$templateDir}/ApiControllerTest.tpl.php",
            $variables,
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next steps:');
        $io->listing([
            "Implement domain logic in <fg=yellow>src/Application/Action/{$actionName}/{$actionName}Handler.php</>",
            'Fill in TODO comments in generated files',
            'Run <fg=yellow>make phpstan-analyze</> and <fg=yellow>make phpunit</> to verify',
        ]);
    }

    private function parseParameterOptions(InputInterface $input): void
    {
        /** @var list<string> $parameterOptions */
        $parameterOptions = $input->getOption('parameter');
        if ([] === $parameterOptions) {
            return;
        }

        $this->parameters = [];
        foreach ($parameterOptions as $parameterOption) {
            $parts = explode(':', $parameterOption, 4);
            if (\count($parts) >= 3 && \in_array($parts[1], ['string', 'int'], true)) {
                $this->parameters[] = [
                    'name' => $parts[0],
                    'type' => $parts[1],
                    'description' => $parts[2],
                    'default' => $parts[3] ?? null,
                ];
            } else {
                // Backwards-compatible: name:description (type defaults to string)
                $this->parameters[] = [
                    'name' => $parts[0],
                    'type' => 'string',
                    'description' => $parts[1] ?? '',
                    'default' => null,
                ];
            }
        }
    }

    /**
     * @return array{fqcn: string, class: string}|null
     */
    private function discoverFixture(string $paramName, string $testsDir): ?array
    {
        $pascalCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $paramName)));
        $pascalCase = lcfirst($pascalCase);
        $pascalCase = ucfirst($pascalCase);

        $className = "{$pascalCase}Fixture";
        $fileName = "{$className}.php";

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator("{$testsDir}/Fixtures"),
        );
        $filePath = null;
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->getFilename() === $fileName) {
                $filePath = $file->getPathname();
                break;
            }
        }

        if (null === $filePath) {
            return null;
        }

        $contents = file_get_contents($filePath);
        if (false === $contents) {
            return null;
        }

        if (1 !== preg_match('/namespace\s+(.+?);/', $contents, $nsMatch)) {
            return null;
        }

        return [
            'fqcn' => "{$nsMatch[1]}\\{$className}",
            'class' => $className,
        ];
    }

    private function toKebabCase(string $pascalCase): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $pascalCase));
    }

    private function toTitleCase(string $pascalCase): string
    {
        return trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', $pascalCase));
    }

    private function toSnakeCase(string $pascalCase): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $pascalCase));
    }
}
