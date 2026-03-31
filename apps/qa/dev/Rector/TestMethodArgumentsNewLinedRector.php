<?php

declare(strict_types=1);

namespace Bl\Qa\Dev\Rector;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\TestCase;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Expands test method parameter lists to multi-line in PHPUnit test cases.
 *
 * Only works on test methods following the "test" prefix convention.
 */
final class TestMethodArgumentsNewLinedRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Test methods with at least one argument must be written on multiple lines.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                    use PHPUnit\Framework\TestCase;

                    final class FooTest extends TestCase
                    {
                        public function test_it_has_required_parameters(string $scenario, string $username): void
                        {
                        }
                    }
                    CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                    use PHPUnit\Framework\TestCase;

                    final class FooTest extends TestCase
                    {
                        public function test_it_has_required_parameters(
                            string $scenario,
                            string $username,
                        ): void
                        {
                        }
                    }
                    CODE_SAMPLE,
                ),
            ],
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /** @param Class_ $node */
    public function refactor(Node $node): ?Node
    {
        // ─────────────────────────────────────────────────────────────────────
        // The class must extend PHPUnit's TestCase
        // ─────────────────────────────────────────────────────────────────────
        if (
            !$node->extends instanceof Node
            || !$this->isName($node->extends, TestCase::class)
        ) {
            return null;
        }

        $hasChanged = false;
        foreach ($node->getMethods() as $classMethod) {
            // ─────────────────────────────────────────────────────────────────────
            // The method must be public and prefixed with "test"
            // ─────────────────────────────────────────────────────────────────────
            if (!$classMethod->isPublic()) {
                continue;
            }

            if (!str_starts_with($classMethod->name->toString(), 'test')) {
                continue;
            }

            // ─────────────────────────────────────────────────────────────────────
            // There must be at least one argument, on the same line as the method
            // ─────────────────────────────────────────────────────────────────────
            $firstParam = $classMethod->params[0] ?? null;
            if (null === $firstParam) {
                continue;
            }

            if ($classMethod->name->getStartLine() !== $firstParam->getStartLine()) {
                continue;
            }

            // ─────────────────────────────────────────────────────────────────
            // Add an empty comment to the first argument, to force a multi-line reprint
            // ─────────────────────────────────────────────────────────────────
            $firstParam->setAttribute('comments', [new Comment('')]);
            $classMethod->setAttribute(AttributeKey::ORIGINAL_NODE, null);
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }
}
