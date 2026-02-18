<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type UseCase
 */
final readonly class <?php echo $class_name; ?>

{
    public function __construct(
        // TODO: inject domain service dependencies
    ) {
    }

    /**
     * @throws ValidationFailedException If a parameter is invalid
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(<?php echo $action_name; ?> $input): <?php echo $action_name; ?>Output
    {
        // TODO: implement domain logic, return new <?php echo $action_name; ?>Output($player)
    }
}
