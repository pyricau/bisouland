<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>;
use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>Handler;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
<?php if ($has_optional_params) { ?>use Symfony\Component\Console\Attribute\Option;
<?php } ?>
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'action:<?php echo $action_kebab; ?>',
    description: '<?php echo $description; ?>',
)]
final readonly class <?php echo $class_name; ?>

{
    public function __construct(
        private <?php echo $action_name; ?>Handler $<?php echo $action_camel; ?>Handler,
    ) {
    }

    public function __invoke(
<?php foreach ($action_parameters as $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        #[Argument(description: '<?php echo $param['description']; ?>')]
        <?php echo $param['type']; ?> $<?php echo $param['name']; ?>,
<?php } ?>
        SymfonyStyle $io,
<?php foreach ($action_parameters as $param) {
    if (null === $param['default']) {
        continue;
    } ?>
        #[Option(description: '<?php echo $param['description']; ?>')]
        <?php echo $param['type']; ?> $<?php echo $param['name']; ?> = <?php echo 'int' === $param['type'] ? $param['default'] : "'{$param['default']}'"; ?>,
<?php } ?>
    ): int {
        try {
            $output = $this-><?php echo $action_camel; ?>Handler->run(new <?php echo $action_name; ?>(<?php echo implode(', ', array_map(static fn ($p) => '$'.$p['name'], $action_parameters)); ?>));
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Successfully completed <?php echo $action_title; ?>');

        $rows = [];
        foreach ($output->toArray() as $field => $value) {
            $rows[] = [$field, $value];
        }

        $table = new Table($io);
        $table->setStyle('markdown');
        $table->setHeaders(['Field', 'Value']);
        $table->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
