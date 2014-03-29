<?php

namespace Bisouland\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @Route("/{file}", name="page")
     */
    public function pagesAction(Request $request, $file)
    {
        $locale = $request->getSession()->get('locale', 'en');
        $filename = $this->makeFilename($locale, array($file));
        $page = file_get_contents($filename);

        return new Response($page);
    }

    /**
     * @Route("/{year}/{month}/{day}/{file}")
     */
    public function postAction(Request $request, $year, $month, $day, $file)
    {
        $locale = $request->getSession()->get('locale', 'en');
        $filename = $this->makeFilename($locale, array($year, $month, $day, $file));
        $page = file_get_contents($filename);

        return new Response($page);
    }

    /**
     * @param string locale
     * @param array $parameters
     *
     * @return string
     */
    private function makeFilename($locale, array $parameters)
    {
        $blogDir = $this->container->get('kernel')->getBlogDir($locale);
        array_unshift($parameters , $blogDir);

        return implode('/', $parameters);
    }
}
