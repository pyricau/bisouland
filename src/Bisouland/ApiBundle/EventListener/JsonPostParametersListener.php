<?php

namespace Bisouland\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * PHP doesn't populate $_POST when post parameters are sent in json, which
 * means that $request->request->all() will be empty.
 *
 * This subscriber fixes this by extracting the parameters from the request
 * content and then populate the request's post parameters with the decoded
 * data.
 */
class JsonPostParametersListener
{
    /** @param KernelEvent $event */
    public function onKernelRequest(KernelEvent $event)
    {
        $request = $event->getRequest();

        $isPost = ($request->isMethod('POST'));
        $isJson = ($request->headers->get('Content-Type') === 'application/json');
        if (!$isPost || !$isJson) {
            return;
        }

        $postParameters = json_decode($request->getContent(), true);
        $request->request->replace($postParameters);
    }
}
