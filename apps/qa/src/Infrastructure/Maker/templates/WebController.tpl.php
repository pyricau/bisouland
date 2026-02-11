<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class <?= $class_name; ?> extends AbstractController
{
    #[Route('/actions/<?= $action_kebab; ?>', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('actions/<?= $action_kebab; ?>.html.twig');
    }
}
