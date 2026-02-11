<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;

/**
 * @object-type Action
 */
final readonly class <?= $class_name; ?>

{
    public function __construct(
        // TODO: inject domain service dependencies
    ) {
    }

    /**
     * @throws ValidationFailedException If a parameter is invalid
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(
<?php foreach ($action_parameters as $i => $param): ?>
        string $<?= $param['name']; ?>,
<?php endforeach; ?>
    ): void {
        // TODO: implement domain logic
    }
}
