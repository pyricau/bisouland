<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class <?php echo $class_name; ?> extends AbstractController
{
    #[Route('/actions/<?php echo $action_kebab; ?>', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('actions/<?php echo $action_kebab; ?>.html.twig');
    }
}
