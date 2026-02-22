<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Output;
<?php foreach ($action_dependencies as $dep) { ?>
use <?php echo $dep['output_fqcn']; ?>;
<?php } ?>

/**
 * @object-type DataTransferObject
 */
final readonly class <?php echo $class_name; ?> implements Output
{
    public function __construct(
<?php if ([] !== $action_dependencies) { ?>
<?php foreach ($action_dependencies as $dep) { ?>
        public <?php echo $dep['output_class']; ?> $<?php echo $dep['camel_name']; ?>,
<?php } ?>
<?php } else { ?>
        // TODO: add output fields (e.g. action outputs or specific values)
<?php } ?>
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
<?php if ([] !== $action_dependencies) { ?>
        return [<?php foreach ($action_dependencies as $dep) { ?>...$this-><?php echo $dep['camel_name']; ?>->toArray(), <?php } ?>];
<?php } else { ?>
        return [
            // TODO: return output fields
        ];
<?php } ?>
    }
}
