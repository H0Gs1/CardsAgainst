<?php

namespace Symfony\Component\Routing\Tests\Fixtures\AnnotationFixtures;

use Symfony\Component\Routing\Attribute\Route;

/**
 * @Route("/prefix")
 */
class RouteWithPrefixController
{
    /**
     * @Route("/path", name="action")
     */
    public function action()
    {
    }
}
