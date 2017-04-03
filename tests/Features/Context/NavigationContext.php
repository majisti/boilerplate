<?php

namespace Tests\Features\Context;

use Behatch\Context\BaseContext;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware;
use Tests\Features\Utils\PageObjectContextTrait;

class NavigationContext extends BaseContext implements PageObjectAware
{
    use PageObjectContextTrait;

    /**
     * @Given I visited the :pageName
     */
    public function iVisitedThe(string $pageName)
    {
        $this->getPage($pageName)->open();
    }
}
