<?php

namespace Tests\Utils;

use Hamcrest\MatcherAssert;

require_once __DIR__.'/../../vendor/hamcrest/hamcrest-php/hamcrest/Hamcrest.php';

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
trait Hamcrest
{
    use AssertCountIncrementer;

    /**
     * Make an assertion and throw {@link Hamcrest_AssertionError} if it fails.
     *
     * The first parameter may optionally be a string identifying the assertion
     * to be included in the failure message.
     *
     * If the third parameter is not a matcher it is passed to
     * {@link Hamcrest_Core_IsEqual#equalTo} to create one.
     *
     * Example:
     * <pre>
     * // With an identifier
     * verifyThat("apple flavour", $apple->flavour(), equalTo("tasty"));
     * // Without an identifier
     * verifyThat($apple->flavour(), equalTo("tasty"));
     * // Evaluating a boolean expression
     * verifyThat("some error", $a > $b);
     * verifyThat($a > $b);
     * </pre>
     */
    protected function verifyThat(/* args ..*/)
    {
        $args = func_get_args();
        call_user_func_array(
            array(MatcherAssert::class, 'assertThat'),
            $args
        );

        $this->incrementAssertionCounterByOne();
    }
}
