<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
<?php if ($has_username_param) { ?>
use Bl\Auth\Account\Username;
<?php } ?>

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
<?php foreach ($action_parameters as $param) { ?>
<?php if ('username' === $param['name']) { ?>
     * @throws ValidationFailedException If the username is invalid (size out of bounds, characters not allowed)
     * @throws ValidationFailedException If the username is not an already existing one
<?php } else { ?>
     * @throws ValidationFailedException If the <?php echo $param['name']; ?> is invalid
<?php } ?>
<?php } ?>
     * @throws ServerErrorException      If an unexpected error occurs
     */
    public function run(<?php echo $action_name; ?> $input): <?php echo $action_name; ?>Output
    {
<?php if ($has_username_param) { ?>
        $username = Username::fromString($input->username);
<?php } ?>
        // TODO: implement domain logic, return new <?php echo $action_name; ?>Output(...)
    }
}
