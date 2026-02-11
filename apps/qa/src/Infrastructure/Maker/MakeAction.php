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

    /** @var list<array{name: string, description: string}> */
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
            ->addOption('parameter', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Parameters as name:description pairs (e.g. <fg=yellow>username:4-15 alphanumeric characters</>)')
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
        /** @var list<string> $parameterOptions */
        $parameterOptions = $input->getOption('parameter');
        if ([] !== $parameterOptions) {
            $this->parameters = [];
            foreach ($parameterOptions as $parameterOption) {
                $parts = explode(':', $parameterOption, 2);
                $this->parameters[] = [
                    'name' => $parts[0],
                    'description' => $parts[1] ?? '',
                ];
            }
        } else {
            $this->parameters = [];
            $io->note('Add parameters (empty name to stop):');

            while (true) {
                $paramName = $io->ask('Parameter name (camelCase, e.g. username)');

                if (!\is_string($paramName) || '' === $paramName) {
                    break;
                }

                $paramDescription = $io->ask("Description for '{$paramName}'");
                $this->parameters[] = [
                    'name' => $paramName,
                    'description' => \is_string($paramDescription) ? $paramDescription : '',
                ];
            }
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        /** @var string $actionName */
        $actionName = $input->getArgument('name');
        $description = $this->description;
        $parameters = $this->parameters;

        $actionKebab = $this->toKebabCase($actionName);
        $actionTitle = $this->toTitleCase($actionName);

        $actionSnake = $this->toSnakeCase($actionName);
        $actionCamel = lcfirst($actionName);

        $templateDir = __DIR__.'/templates';
        $variables = [
            'action_name' => $actionName,
            'action_kebab' => $actionKebab,
            'action_title' => $actionTitle,
            'action_snake' => $actionSnake,
            'action_camel' => $actionCamel,
            'description' => $description,
            'action_parameters' => $parameters,
        ];

        // 1. Action class
        $generator->generateClass(
            "Bl\\Qa\\Application\\Action\\{$actionName}",
            "{$templateDir}/Action.tpl.php",
            $variables,
        );

        // 2. CLI Command
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Cli\\Action\\{$actionName}Command",
            "{$templateDir}/CliCommand.tpl.php",
            $variables,
        );

        // 3. Web Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Web\\Action\\{$actionName}Controller",
            "{$templateDir}/WebController.tpl.php",
            $variables,
        );

        // 4. API Controller
        $generator->generateClass(
            "Bl\\Qa\\UserInterface\\Api\\Action\\{$actionName}Controller",
            "{$templateDir}/ApiController.tpl.php",
            $variables,
        );

        // 5. Twig template
        $generator->generateTemplate(
            "actions/{$actionKebab}.html.twig",
            "{$templateDir}/TwigTemplate.tpl.php",
            $variables,
        );

        // 6. Spec test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Spec\\Application\\Action\\{$actionName}Test",
            "{$templateDir}/SpecTest.tpl.php",
            $variables,
        );

        // 7. CLI Command integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Cli\\Action\\{$actionName}CommandTest",
            "{$templateDir}/CliCommandTest.tpl.php",
            $variables,
        );

        // 8. Web Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Web\\Action\\{$actionName}ControllerTest",
            "{$templateDir}/WebControllerTest.tpl.php",
            $variables,
        );

        // 9. API Controller integration test
        $generator->generateClass(
            "Bl\\Qa\\Tests\\Qalin\\Integration\\UserInterface\\Api\\Action\\{$actionName}ControllerTest",
            "{$templateDir}/ApiControllerTest.tpl.php",
            $variables,
        );

        $generator->writeChanges();

        // Append nav link to base.html.twig
        $this->appendNavLink($generator, $actionKebab, $actionTitle);

        $this->writeSuccessMessage($io);
        $io->text('Next steps:');
        $io->listing([
            "Implement domain logic in <fg=yellow>src/Application/Action/{$actionName}.php</>",
            'Fill in TODO comments in generated files',
            'Run <fg=yellow>make phpstan-analyze</> and <fg=yellow>make phpunit</> to verify',
        ]);
    }

    private function appendNavLink(Generator $generator, string $actionKebab, string $actionTitle): void
    {
        $baseTwigPath = $generator->getRootDirectory().'/templates/base.html.twig';
        $contents = file_get_contents($baseTwigPath);

        if (false === $contents) {
            return;
        }

        $newLink = "            <a href=\"/actions/{$actionKebab}\">{$actionTitle}</a>";
        $contents = str_replace('        </nav>', "{$newLink}\n        </nav>", $contents);

        file_put_contents($baseTwigPath, $contents);
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
