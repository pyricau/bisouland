<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Bl\Qa\Application\Action\<?= $action_name; ?>;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'action:<?= $action_kebab; ?>',
    description: '<?= $description; ?>',
)]
final class <?= $class_name; ?> extends Command
{
    public function __construct(
        private readonly <?= $action_name; ?> $<?= $action_camel; ?>,
    ) {
        parent::__construct();
    }

    public function __invoke(
<?php foreach ($action_parameters as $param): ?>
        #[Argument(description: '<?= $param['description']; ?>')]
        string $<?= $param['name']; ?>,
<?php endforeach; ?>
        SymfonyStyle $io,
    ): int {
        try {
            $this-><?= $action_camel; ?>->run(<?= implode(', ', array_map(fn ($p) => '$' . $p['name'], $action_parameters)); ?>);
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return self::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        $io->success('Successfully completed <?= $action_title; ?>');

        // TODO: display result (e.g. Table with markdown style)

        return self::SUCCESS;
    }
}
