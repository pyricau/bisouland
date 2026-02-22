<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
<?php foreach ($action_dependencies as $dep) { ?>
use <?php echo $dep['input_fqcn']; ?>;
use <?php echo $dep['handler_fqcn']; ?>;
<?php } ?>

/**
 * @object-type UseCase
 */
final readonly class <?php echo $class_name; ?>

{
    public function __construct(
<?php if ([] !== $action_dependencies) { ?>
<?php foreach ($action_dependencies as $dep) { ?>
        private <?php echo $dep['handler_class']; ?> $<?php echo $dep['camel_name']; ?>Handler,
<?php } ?>
<?php } else { ?>
        // TODO: inject Action Handler dependencies
        // e.g. private SomeActionHandler $someActionHandler,
<?php } ?>
    ) {
    }

    /**
     * @throws ValidationFailedException If a parameter is invalid
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(<?php echo $scenario_name; ?> $<?php echo $scenario_camel; ?>): <?php echo $scenario_output_name; ?>
    {
<?php if ([] !== $action_dependencies) { ?>
<?php foreach ($action_dependencies as $dep) { ?>
        $<?php echo $dep['camel_name']; ?> = $this-><?php echo $dep['camel_name']; ?>Handler->run(
            new <?php echo $dep['input_class']; ?>(/* TODO: map from $<?php echo $scenario_camel; ?> */),
        );
<?php } ?>

        return new <?php echo $scenario_output_name; ?>(<?php echo implode(', ', array_map(static fn ($d) => '$'.$d['camel_name'], $action_dependencies)); ?>);
<?php } else { ?>
        // TODO: call action handlers and return new <?php echo $scenario_output_name; ?>(...)
<?php } ?>
    }
}
