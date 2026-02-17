<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

/**
 * @object-type DataTransferObject
 */
final readonly class <?php echo $class_name; ?>

{
    public function __construct(
<?php foreach ($action_parameters as $param) { ?>
        public <?php echo $param['type']; ?> $<?php echo $param['name']; ?>,
<?php } ?>
    ) {
    }
}
