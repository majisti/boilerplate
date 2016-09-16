<?php

namespace Tests\Features\Context\Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element as BaseElement;
use Tests\Utils\Hamcrest;

abstract class Element extends BaseElement
{
    use Hamcrest;

    public function verifyNonEmpty()
    {
        $this->verifyThat($this->getHtml(), is(nonEmptyString()));
    }

    public function verifyElementsAreContainedWithinElement(int $maximumExpectedCount, string $containerSelector)
    {
        $elements = $this->findAll('css', $containerSelector);

        $this->verifyThat($elements, is(notNullValue()));
        $this->verifyThat('The number of expected elements mismatches',
            count($elements), atMost($maximumExpectedCount));
    }
}
