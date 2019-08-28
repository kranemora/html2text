<?php
/**
 * Contain the class to test Html2Text\Parsers\DefaultParser.
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

use kranemora\Html2Text\Parsers\DefaultParser;
use DOMDocument;

/**
 * Test Html2Text\Parsers\DefaultParser.
 *
 * @package Test\TestCase\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class DefaultParserTest extends TestCase
{

    /**
     * Parser object
     *
     * @var Html2Text\Parsers\DefaultParser
     */
    protected $parser;

    /**
     * Sets up DefaultParser object to test.
     *
     * @return void.
     */
    public function setUp()
    {
        $this->parser = new DefaultParser;
    }

    /**
     * Test private method DefaultParser::_setElementDefaultOptions.
     *
     * @return void.
     */
    public function testPrivateSetElementDefaultOptions()
    {
        $dom = new DOMDocument();
        $html = <<<EOF
        <body>
            <p>Lorem Ipsum</p>
        </body>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);

        $this->invokeMethod($this->parser, '_setElementDefaultOptions', [$body]);

        $options = $this->parser->getOptions();

        $expectedOptions = [
            'body' => [
                'break' => '',
                'prepend' => '',
                'append' => '',
                'between' => '',
                'parser' => [
                    'class' => '',
                    'options' => []
                ]
            ]
        ];

        $this->assertEquals($expectedOptions, $options);

        $this->parser->setOptions(
            [
                'body' => [
                    'parser' => [
                        'class' => '\kranemora\Html2Text\Parsers\DefaultParser',
                    ]
                ]
            ]
        );

        $this->invokeMethod($this->parser, '_setElementDefaultOptions', [$body]);

        $options = $this->parser->getOptions();

        $expectedOptions = [
            'body' => [
                'break' => '',
                'prepend' => '',
                'append' => '',
                'between' => '',
                'parser' => [
                    'class' => '\kranemora\Html2Text\Parsers\DefaultParser',
                    'options' => []
                ]
            ]
        ];

        $this->assertEquals($expectedOptions, $options);
    }

    /**
     * Test private method DefaultParser::_getAttributes.
     *
     * @return void.
     */
    public function testPrivateGetAttributes()
    {
        $dom = new DOMDocument();
        $html = <<<EOF
        <body id="container" class="default" lang="es">
            <p>Lorem Ipsum</p>
        </body>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);

        $attributes = $this->invokeMethod($this->parser, '_getAttributes', [$body]);

        $expectedAttributes = [
            'id' => 'container',
            'class' => 'default',
            'lang' => 'es'
        ];
        $this->assertEquals($expectedAttributes, $attributes);
    }

    /**
     * Test private method DefaultParser::_processElementOption.
     *
     * @return void.
     */
    public function testPrivateProcessElementOption()
    {
        $value = '- ';
        $map = '';

        $result = $this->invokeMethod($this->parser, '_processElementOption', [$value, $map]);
        $this->assertEquals('- ', $result);

        $value = '';
        $map = [
            'class' => 'item'
        ];

        $result = $this->invokeMethod($this->parser, '_processElementOption', [$value, $map]);
        $this->assertEquals('', $result);

        $value = '{{class}} list';
        $map = [
            'class' => 'item'
        ];

        $result = $this->invokeMethod($this->parser, '_processElementOption', [$value, $map]);
        $this->assertEquals('item list', $result);

        $value = '{{id}} list';
        $map = [
            'class' => 'item'
        ];

        $result = $this->invokeMethod($this->parser, '_processElementOption', [$value, $map]);
        $this->assertEquals('{{id}} list', $result);

        $value = '- ';
        $map = [
            'class' => 'item'
        ];

        $result = $this->invokeMethod($this->parser, '_processElementOption', [$value, $map]);
        $this->assertEquals('- ', $result);
    }

    /**
     * Test methods DefaultParser::getText with default options.
     *
     * @return void.
     */
    public function testGetTextDefaultOptions()
    {
        $dom = new DOMDocument();

        // Test simple text
        $html = <<<EOF
Lorem Ipsum
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum", $this->parser->getText($body));

        // Test two simple text
        $html = <<<EOF
Lorem Ipsum
Lorem Ipsum
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum Lorem Ipsum", $this->parser->getText($body));

        // Test simple text with additional whitespaces
        $html = <<<EOF
        Lorem      Ipsum
        Lorem Ipsum
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum Lorem Ipsum", $this->parser->getText($body));

        // Test simple paragraph
        $html = <<<EOF
<p>Lorem Ipsum</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum", $this->parser->getText($body));

        // Test two simple paragraphs
        $html = <<<EOF
<p>Lorem Ipsum</p>
<p>dolor sit amet</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum dolor sit amet", $this->parser->getText($body));

        // Test two simple paragraphs with outer whitespaces
        $html = <<<EOF
        <p>Lorem Ipsum</p>
        <p>dolor sit amet</p>

EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum dolor sit amet", $this->parser->getText($body));

        // Test two simple paragraphs without whitespaces
        $html = <<<EOF
<p>Lorem Ipsum</p><p>dolor sit amet</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsumdolor sit amet", $this->parser->getText($body));

        // Test two simple paragraphs with innner and outer whitespaces
        $html = <<<EOF
        <p>Lorem Ipsum</p> <p>dolor sit amet</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum dolor sit amet", $this->parser->getText($body));

        $html = <<<EOF
             <p>      Lorem        Ipsum          </p>
             <p>    dolor                sit amet              </p>

EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum dolor sit amet", $this->parser->getText($body));

        // Test simple paragraphs with line break
        $html = <<<EOF
<p><strong>Lorem ipsum</strong> dolor sit <em>amet</em>, consectetur adipiscing elit.</p>
<p>Curabitur porttitor nisi nec finibus
bibendum. Donec at elementum leo.
Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus
orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.
Duis in tristique ligula, vel semper urna.</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis. Duis in tristique ligula, vel semper urna.", $this->parser->getText($body));

        // Test simple text and paragraphs
        $html = <<<EOF
Lorem ipsum
<p>dolor sit <em>amet</em>, consectetur adipiscing elit.</p>
<p>Curabitur porttitor nisi nec finibus
bibendum. Donec at elementum leo.
     Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
     Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
     Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus
     orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.</p>
     Duis in tristique ligula, vel semper urna.
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis. Duis in tristique ligula, vel semper urna.", $this->parser->getText($body));

        // Test simple text and paragraphs
        $html = <<<EOF
            Lorem ipsum
        <p>dolor sit <em>amet</em>, consectetur adipiscing elit.</p>
<p>Curabitur porttitor nisi nec finibus
bibendum. Donec at elementum leo.
     Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
     Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
     Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus
     orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.</p>
     Duis in tristique ligula, vel semper urna.
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis. Duis in tristique ligula, vel semper urna.", $this->parser->getText($body));

        // Test simple list
        $html = <<<EOF
        <ul>
            <li class="item">Ultrices magna vitae</li>
            <li class="item">Gravida velit</li>
            <li class="item">Nunc lobortis</li>
            <li class="item">Tortor nec auctor ultricies</li>
        </ul>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Ultrices magna vitae Gravida velit Nunc lobortis Tortor nec auctor ultricies", $this->parser->getText($body));

        // Test deep structure and attributes
        $html = <<<EOF
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
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("lorem@ipsum.com Facebook Twitter Linkedin Instagram", $this->parser->getText($body));

        // Test simple table
        $html = <<<EOF
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
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Position Gender Total Male Female Tutor 5 8 13 Professor 10 8 18", $this->parser->getText($body));

        // Test complex tables
        $html = <<<EOF
        <table>
            <tr>
                <td>Adaptability</td>
                <td>Confidence</td>
                <td>Communication</td>
                <td>Teamwork</td>
            </tr>
            <tr>
                <td>Continuous Learner</td>
                <td colspan="2" rowspan="2">Modern Teachers</td>
                <td>Mentoring</td>
            </tr>
            <tr>
                <td>Leadership</td>
                <td>Organization</td>
            </tr>
            <tr>
                <td>Innovative</td>
                <td>Commitment</td>
                <td>Patience</td>
                <td>Imagination</td>
            </tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Adaptability Confidence Communication Teamwork Continuous Learner Modern Teachers Mentoring Leadership Organization Innovative Commitment Patience Imagination", $this->parser->getText($body));
    }

    /**
     * Test methods DefaultParser::getText with regular options.
     *
     * @return void.
     */
    public function testGetTextRegularOptions()
    {

        $options = [
            'body' => [
                'break' => "\n"
            ],
            'article' => [
                'break' => "\n"
            ],
            'section' => [
                'break' => "\n"
            ],
            'nav' => [
                'break' => "\n"
            ],
            'aside' => [
                'break' => "\n"
            ],
            'header' => [
                'break' => "\n"
            ],
            'footer' => [
                'break' => "\n"
            ],
            'address' => [
                'break' => "\n"
            ],
            'ul' => [
                'break' => "\n\n"
            ],
            'ol' => [
                'break' => "\n\n"
            ],
            'li' => [
                'break' => "\n",
                'prepend' => '- '
            ],
            'dt' => [
                'break' => "\n"
            ],
            'figure' => [
                'break' => "\n"
            ],
            'figcaption' => [
                'break' => "\n"
            ],
            'main' => [
                'break' => "\n"
            ],
            'div' => [
                'break' => "\n"
            ],
            'h1' => [
                'break' => "\n"
            ],
            'h2' => [
                'break' => "\n\n"
            ],
            'h3' => [
                'break' => "\n\n"
            ],
            'h4' => [
                'break' => "\n\n"
            ],
            'h5' => [
                'break' => "\n\n"
            ],
            'h6' => [
                'break' => "\n\n"
            ],
            'p' => [
                'break' => "\n\n"
            ],
            'dd' => [
                'break' => "\n\n"
            ],
            'a' => [
                'append' => " [{{href}}]"
            ],
            'table' => [
                'break' => "\n\n"
            ],
            'tr' => [
                'break' => "\n"
            ],
            'th' => [
                'between' => ", "
            ],
            'td' => [
                'between' => ", "
            ],
        ];

        $this->parser->setOptions($options);

        $dom = new DOMDocument();

        // Test simple text
        $html = <<<EOF
Lorem Ipsum
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum", $this->parser->getText($body));

        // Test two simple text
        $html = <<<EOF
Lorem Ipsum
Lorem Ipsum
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum Lorem Ipsum", $this->parser->getText($body));

        // Test simple text with additional whitespaces
        $html = <<<EOF
        Lorem      Ipsum
        Lorem Ipsum
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum Lorem Ipsum", $this->parser->getText($body));

        // Test simple paragraph
        $html = <<<EOF
<p>Lorem Ipsum</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum", $this->parser->getText($body));

        // Test two simple paragraphs
        $html = <<<EOF
<p>Lorem Ipsum</p>
<p>dolor sit amet</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum\n\ndolor sit amet", $this->parser->getText($body));

        // Test two simple paragraphs with outer whitespaces
        $html = <<<EOF
        <p>Lorem Ipsum</p>
        <p>dolor sit amet</p>

EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum\n\ndolor sit amet", $this->parser->getText($body));

        // Test two simple paragraphs without whitespaces
        $html = <<<EOF
<p>Lorem Ipsum</p><p>dolor sit amet</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum\n\ndolor sit amet", $this->parser->getText($body));

        // Test two simple paragraphs with innner and outer whitespaces
        $html = <<<EOF
        <p>Lorem Ipsum</p> <p>dolor sit amet</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum\n\ndolor sit amet", $this->parser->getText($body));

        $html = <<<EOF
             <p>      Lorem        Ipsum          </p>
             <p>    dolor                sit amet              </p>

EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem Ipsum\n\ndolor sit amet", $this->parser->getText($body));

        // Test simple paragraphs with line break
        $html = <<<EOF
<p><strong>Lorem ipsum</strong> dolor sit <em>amet</em>, consectetur adipiscing elit.</p>
<p>Curabitur porttitor nisi nec finibus
bibendum. Donec at elementum leo.
Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus
orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.
Duis in tristique ligula, vel semper urna.</p>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n\nCurabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis. Duis in tristique ligula, vel semper urna.", $this->parser->getText($body));

        // Test simple text and paragraphs
        $html = <<<EOF
Lorem ipsum
<p>dolor sit <em>amet</em>, consectetur adipiscing elit.</p>
<p>Curabitur porttitor nisi nec finibus
bibendum. Donec at elementum leo.
     Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
     Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
     Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus
     orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.</p>
     Duis in tristique ligula, vel semper urna.
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem ipsum\n\ndolor sit amet, consectetur adipiscing elit.\n\nCurabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.\n\nDuis in tristique ligula, vel semper urna.", $this->parser->getText($body));

        // Test simple text and paragraphs
        $html = <<<EOF
            Lorem ipsum
        <p>dolor sit <em>amet</em>, consectetur adipiscing elit.</p>
<p>Curabitur porttitor nisi nec finibus
bibendum. Donec at elementum leo.
     Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis.
     Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante.
     Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus
     orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.</p>
     Duis in tristique ligula, vel semper urna.
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Lorem ipsum\n\ndolor sit amet, consectetur adipiscing elit.\n\nCurabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis.\n\nDuis in tristique ligula, vel semper urna.", $this->parser->getText($body));

        // Test simple list
        $html = <<<EOF
        <ul>
            <li class="item">Ultrices magna vitae</li>
            <li class="item">Gravida velit</li>
            <li class="item">Nunc lobortis</li>
            <li class="item">Tortor nec auctor ultricies</li>
        </ul>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("- Ultrices magna vitae\n- Gravida velit\n- Nunc lobortis\n- Tortor nec auctor ultricies", $this->parser->getText($body));

        // Test deep structure and attributes
        $html = <<<EOF
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
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("lorem@ipsum.com\n- Facebook [https://www.facebook.com]\n- Twitter [https://www.twitter.com]\n- Linkedin [https://www.linkedin.com/]\n- Instagram [https://www.instagram.com]", $this->parser->getText($body));

        // Test complex table
        $html = <<<EOF
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
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Position, Gender, Total\nMale, Female\nTutor, 5, 8, 13\nProfessor, 10, 8, 18", $this->parser->getText($body));

        $html = <<<EOF
        <table>
            <tr>
                <td>Adaptability</td>
                <td>Confidence</td>
                <td>Communication</td>
                <td>Teamwork</td>
            </tr>
            <tr>
                <td>Continuous Learner</td>
                <td colspan="2" rowspan="2">Modern Teachers</td>
                <td>Mentoring</td>
            </tr>
            <tr>
                <td>Leadership</td>
                <td>Organization</td>
            </tr>
            <tr>
                <td>Innovative</td>
                <td>Commitment</td>
                <td>Patience</td>
                <td>Imagination</td>
            </tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->assertEquals("Adaptability, Confidence, Communication, Teamwork\nContinuous Learner, Modern Teachers, Mentoring\nLeadership, Organization\nInnovative, Commitment, Patience, Imagination", $this->parser->getText($body));

    }
}
