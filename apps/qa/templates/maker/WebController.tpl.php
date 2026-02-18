<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final readonly class <?php echo $class_name; ?>

{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/actions/<?php echo $action_kebab; ?>', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('actions/<?php echo $action_kebab; ?>.html.twig'));
    }
}
