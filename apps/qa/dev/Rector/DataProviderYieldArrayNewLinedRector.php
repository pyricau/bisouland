<?php

declare(strict_types=1);

namespace Bl\Qa\Dev\Rector;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Expands single-line yield arrays to multi-line in data provider methods.
 *
 * Only works on static data provider methods following the "Provider" suffix convention.
 */
final class DataProviderYieldArrayNewLinedRector extends AbstractRector
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Yield arrays in data provider methods must be written on multiple lines.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                    class FooTest extends TestCase
                    {
                        public static function fooProvider(): \Generator
                        {
                            yield ['scenario' => 'foo', 'value' => 42];
                        }
                    }
                    CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                    class FooTest extends TestCase
                    {
                        public static function fooProvider(): \Generator
                        {
                            yield [
                                'scenario' => 'foo',
                                'value' => 42,
                            ];
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
        return [ClassMethod::class];
    }

    /** @param ClassMethod $node */
    public function refactor(Node $node): ?Node
    {
        // ─────────────────────────────────────────────────────────────────────
        // The method must be public, with name suffixed with `Provider`
        // ─────────────────────────────────────────────────────────────────────
        if (
            !$node->isPublic()
            || !str_ends_with($node->name->toString(), 'Provider')
        ) {
            return null;
        }

        // ─────────────────────────────────────────────────────────────────────
        // The method must contain statements with `yield` expressions
        // ─────────────────────────────────────────────────────────────────────
        if (null === $node->stmts) {
            return null;
        }

        /** @var Yield_[] $yields */
        $yields = $this->betterNodeFinder->findInstanceOf($node->stmts, Yield_::class);
        $hasChanged = false;
        foreach ($yields as $yield) {
            // ─────────────────────────────────────────────────────────────────
            // The yielded value must be a single-line array
            // ─────────────────────────────────────────────────────────────────
            $array = $yield->value;
            if (!$array instanceof Array_) {
                continue;
            }

            if (!$this->isSingleLineArray($array)) {
                continue;
            }

            // ─────────────────────────────────────────────────────────────────
            // Mark the array for multi-line printing, not preserving its original formatting
            // ─────────────────────────────────────────────────────────────────
            $array->setAttribute(AttributeKey::NEWLINED_ARRAY_PRINT, true);
            $array->setAttribute(AttributeKey::ORIGINAL_NODE, null);
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    private function isSingleLineArray(Array_ $array): bool
    {
        foreach ($array->items as $key => $item) {
            $nextItem = $array->items[$key + 1] ?? null;
            if (!$item instanceof ArrayItem) {
                continue;
            }

            if (!$nextItem instanceof ArrayItem) {
                continue;
            }

            if ($nextItem->getStartLine() === $item->getEndLine()) {
                return true;
            }
        }

        return false;
    }
}
