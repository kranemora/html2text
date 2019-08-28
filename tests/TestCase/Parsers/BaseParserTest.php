<?php
/**
 * Contain the class to test Html2Text\Parsers\BaseParser.
 *
 * PHP version 5.6
 *
 * @package Test\TestCase\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
namespace kranemora\Test;

use kranemora\Html2Text\Parsers\BaseParser;
use DOMDocument;

/**
 * Test Html2Text\Parsers\BaseParser.
 *
 * @package Test\TestCase\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class BaseParserTest extends TestCase
{

    /**
     * Parser object
     *
     * @var Html2Text\Parsers\BaseParser
     */
    protected $parser;

    /**
     * Sets up BaseParser object to test.
     *
     * @return void.
     */
    public function setUp()
    {
        $this->parser = new BaseParser;
    }

    /**
     * Test methods BaseParser::setOptions and BaseParser::getOptions.
     *
     * @return void.
     */
    public function testSetGetOptions()
    {
        $this->parser->setOptions(
            [
                'element1' => [
                    'key1' => "value1"
                ]
            ]
        );

        $expectedOptions = [
            'element1' => [
                'key1' => "value1"
            ]
        ];
        $options = $this->parser->getOptions();
        $this->assertEquals($expectedOptions, $options);

        $this->parser->setOptions(
            [
                'element1' => [
                    'key2' => "value2"
                ]
            ]
        );

        $expectedOptions = [
            'element1' => [
                'key1' => "value1",
                'key2' => "value2"
            ]
        ];
        $options = $this->parser->getOptions();
        $this->assertEquals($expectedOptions, $options);

        $this->parser->setOptions(
            [
                'element2' => [
                    'key1' => "value1"
                ]
            ]
        );

        $expectedOptions = [
            'element1' => [
                'key1' => "value1",
                'key2' => "value2"
            ],
            'element2' => [
                'key1' => "value1"
            ]
        ];
        $options = $this->parser->getOptions();
        $this->assertEquals($expectedOptions, $options);
    }

    /**
     * Test methods BaseParser::getText.
     *
     * @return void.
     */
    public function testGetText()
    {
        $dom = new DOMDocument();

        $html = <<<EOF
        <!DOCTYPE html>
        <html lang="es">
            <head>
                <title>Test Html2Text</title>
            </head>
            <body>
                <header>
                    <h1>Test Document</h1>
                </header>
                <main>
                    <div>
                        <div>
                            <h2>Lorem ipsum</h2>
                            <p><strong>Lorem ipsum</strong> dolor sit <em>amet</em>, consectetur adipiscing elit.
                            Curabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis
                            vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
                            Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
                            Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci
                            luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc
                            ultrices quis. Duis in tristique ligula, vel semper urna.</p>
                            <dl>
                                <dt>Dolor sit amet</dt>
                                <dd>consectetur adipiscing elit.</dd>
                                <dt>Curabitur porttitor nisi nec finibus bibendum</dt>
                                <dd>Donec at elementum leo.</dd>
                                <dt>Donec eu felis vehicula</dt>
                                <dd>Efficitur est at.</dd>
                            </dl>
                        </div>
                        <table>
                            <tr>
                                <th rowspan="2">Position</th>
                                <th colspan="2">Gender</th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <th>Male</th>
                                <th>Female</th>
                            </tr>
                            <tr>
                                <th>Tutor</th>
                                <td>5</td>
                                <td>8</td>
                                <td>13</td>
                            </tr>
                            <tr>
                                <th>Professor</th>
                                <td>10</td>
                                <td>8</td>
                                <td>18</td>
                            </tr>
                        </table>
                        <h2>Aenean a massa convallis</h2>
                        <ul>
                            <li class="item">Ultrices magna vitae</li>
                            <li class="item">Gravida velit</li>
                            <li class="item">Nunc lobortis</li>
                            <li class="item">Tortor nec auctor ultricies</li>
                        </ul>
                        <h3>Curabitur bibendum eu diam et venenatis</h3>
                        <ol>
                            <li>Donec vitae enim suscipit</li>
                            <li>Porta nunc tincidunt</li>
                            <li>Consequat leo</li>
                            <li>Nunc eu risus rutrum</li>
                        </ol>
                    </div>
                </main>
                <footer>
                    <address>lorem@ipsum.com</address>
                    <div>
                        <ul>
                            <li><a href="https://www.facebook.com">Facebook</a></li>
                            <li><a href="https://www.twitter.com">Twitter</a></li>
                            <li><a href="https://www.linkedin.com/">Linkedin</a></li>
                            <li><a href="https://www.instagram.com">Instagram</a></li>
                        </ul>
                    </div>
                </footer>
                Lorem ipsum
            </body>
        </html>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $text = $this->parser->getText($body);
        $expectedText = "Test Document Lorem ipsum Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis. Duis in tristique ligula, vel semper urna. Dolor sit amet consectetur adipiscing elit. Curabitur porttitor nisi nec finibus bibendum Donec at elementum leo. Donec eu felis vehicula Efficitur est at. Position Gender Total Male Female Tutor 5 8 13 Professor 10 8 18 Aenean a massa convallis Ultrices magna vitae Gravida velit Nunc lobortis Tortor nec auctor ultricies Curabitur bibendum eu diam et venenatis Donec vitae enim suscipit Porta nunc tincidunt Consequat leo Nunc eu risus rutrum lorem@ipsum.com Facebook Twitter Linkedin Instagram Lorem ipsum";
        $this->assertEquals($expectedText, $text);
    }
}
