<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Bl\Qa\Application\Action\<?php echo $action_name; ?>\<?php echo $action_name; ?>;
<?php foreach ($action_parameters as $param) { ?>
<?php if ($param['fixture_fqcn']) { ?>
use <?php echo $param['fixture_fqcn']; ?>;
<?php } ?>
<?php } ?>
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(<?php echo $action_name; ?>::class)]
#[Small]
final class <?php echo $class_name; ?> extends TestCase
{
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
<?php foreach ($action_parameters as $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        <?php echo $param['type']; ?> $<?php echo $param['name']; ?>,
<?php } ?>
    ): void {
        $<?php echo $action_camel; ?> = new <?php echo $action_name; ?>(<?php echo implode(', ', array_map(static fn ($p) => '$'.$p['name'], array_filter($action_parameters, static fn ($p) => null === $p['default']))); ?>);

<?php foreach ($action_parameters as $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        $this->assertSame($<?php echo $param['name']; ?>, $<?php echo $action_camel; ?>-><?php echo $param['name']; ?>);
<?php } ?>
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
<?php foreach ($action_parameters as $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
     *     <?php echo $param['name']; ?>: <?php echo $param['type']; ?>,
<?php } ?>
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
<?php foreach ($action_parameters as $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        yield ['scenario' => '<?php echo $param['name']; ?> as a required parameter'<?php foreach ($action_parameters as $otherParam) {
            if (null !== $otherParam['default']) {
                continue;
            } ?>, '<?php echo $otherParam['name']; ?>' => <?php if ($otherParam['fixture_fqcn']) { ?><?php echo $otherParam['fixture_class']; ?>::make<?php echo 'int' === $otherParam['type'] ? 'Int' : 'String'; ?>()<?php } elseif ('int' === $otherParam['type']) { ?>1<?php } else { ?>'valid_<?php echo $otherParam['name']; ?>'<?php } ?><?php } ?>];
<?php } ?>
    }
<?php if ($has_optional_params) { ?>

    #[DataProvider('optionalParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_optional_parameters(string $scenario, <?php echo implode(', ', array_map(static fn ($p) => 'int $expected'.ucfirst($p['name']), array_filter($action_parameters, static fn ($p) => null !== $p['default'] && 'int' === $p['type']))); ?>): void
    {
<?php foreach ($action_parameters as $param) {
    if (null !== $param['default']) {
        continue;
    } ?>
        $<?php echo $param['name']; ?> = <?php if ($param['fixture_fqcn']) { ?><?php echo $param['fixture_class']; ?>::make<?php echo 'int' === $param['type'] ? 'Int' : 'String'; ?>();<?php } elseif ('int' === $param['type']) { ?>1;<?php } else { ?>'valid_<?php echo $param['name']; ?>';<?php } ?>

<?php } ?>
        $<?php echo $action_camel; ?> = new <?php echo $action_name; ?>(<?php echo implode(', ', array_map(static fn ($p) => null === $p['default'] ? '$'.$p['name'] : '', $action_parameters)); ?>);

<?php foreach ($action_parameters as $param) {
    if (null === $param['default']) {
        continue;
    } ?>
        $this->assertSame($expected<?php echo ucfirst($param['name']); ?>, $<?php echo $action_camel; ?>-><?php echo $param['name']; ?>);
<?php } ?>
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
<?php foreach ($action_parameters as $param) {
    if (null === $param['default']) {
        continue;
    } ?>
     *     expected<?php echo ucfirst($param['name']); ?>: <?php echo $param['type']; ?>,
<?php } ?>
     * }>
     */
    public static function optionalParametersProvider(): \Iterator
    {
<?php foreach ($action_parameters as $param) {
    if (null === $param['default']) {
        continue;
    } ?>
        yield ['scenario' => '<?php echo $param['name']; ?> as an optional parameter (defaults to <?php echo $param['default']; ?>)', 'expected<?php echo ucfirst($param['name']); ?>' => <?php echo $param['default']; ?>];
<?php } ?>
    }
<?php } ?>
}
