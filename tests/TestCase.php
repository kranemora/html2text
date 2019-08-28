<?php
/**
 * Contain the class to test private methods.
 *
 * PHP version 5.6
 *
 * @package Test
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
namespace kranemora\Test;

/**
 * Test private methods.
 *
 * @package Test
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * Invoke private method to test.
     *
     * @param stdClass $object     Object to test.
     * @param string   $methodName Private method name.
     * @param array    $parameters Private method parameters.
     *
     * @return mixed Private method result.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
