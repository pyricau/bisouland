<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Maker;

use Symfony\Component\Console\Input\InputInterface;

final class MakerHelper
{
    /**
     * @return list<array{name: string, type: string, description: string, default: string|null}>
     */
    public function parseParameterOptions(InputInterface $input): array
    {
        /** @var list<string> $parameterOptions */
        $parameterOptions = $input->getOption('parameter');
        if ([] === $parameterOptions) {
            return [];
        }

        $parameters = [];
        foreach ($parameterOptions as $parameterOption) {
            $parts = explode(':', $parameterOption, 4);
            if (\count($parts) >= 3 && \in_array($parts[1], ['string', 'int'], true)) {
                $parameters[] = [
                    'name' => $parts[0],
                    'type' => $parts[1],
                    'description' => $parts[2],
                    'default' => $parts[3] ?? null,
                ];
            } else {
                // Backwards-compatible: name:description (type defaults to string)
                $parameters[] = [
                    'name' => $parts[0],
                    'type' => 'string',
                    'description' => $parts[1] ?? '',
                    'default' => null,
                ];
            }
        }

        return $parameters;
    }

    /**
     * @return array{fqcn: string, class: string, value_object_class: string|null, value_object_fqcn: string|null, value_object_var: string|null}|null
     */
    public function discoverFixture(string $paramName, string $testsDir): ?array
    {
        $pascalCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $paramName)));

        $className = "{$pascalCase}Fixture";
        $fileName = "{$className}.php";

        $searchDirs = ["{$testsDir}/Fixtures"];
        $packagesDir = __DIR__.'/../../../../../packages';
        foreach (glob("{$packagesDir}/*/src") ?: [] as $packageSrc) {
            if (is_dir($packageSrc)) {
                $searchDirs[] = $packageSrc;
            }
        }

        $filePath = null;
        foreach ($searchDirs as $searchDir) {
            if (!is_dir($searchDir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($searchDir),
            );
            /** @var \SplFileInfo $file */
            foreach ($iterator as $file) {
                if ($file->getFilename() === $fileName) {
                    $filePath = $file->getPathname();
                    break 2;
                }
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

        $valueObjectClass = null;
        $valueObjectFqcn = null;
        $valueObjectVar = null;
        if (1 === preg_match('/public static function make\(\)\s*:\s*([A-Z]\w+)/', $contents, $makeMatch)) {
            $valueObjectClass = $makeMatch[1];
            $valueObjectVar = strtolower($valueObjectClass[0]);
            if (1 === preg_match("/use ([\\w\\\\]+\\\\{$valueObjectClass});/", $contents, $useMatch)) {
                $valueObjectFqcn = $useMatch[1];
            }
        }

        return [
            'fqcn' => "{$nsMatch[1]}\\{$className}",
            'class' => $className,
            'value_object_class' => $valueObjectClass,
            'value_object_fqcn' => $valueObjectFqcn,
            'value_object_var' => $valueObjectVar,
        ];
    }

    public function toKebabCase(string $pascalCase): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $pascalCase));
    }

    public function toTitleCase(string $pascalCase): string
    {
        return trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', $pascalCase));
    }

    public function toSnakeCase(string $pascalCase): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $pascalCase));
    }
}
