<?php
/**
 * Contain the class to test Html2Text.
 *
 * PHP version 5.6
 *
 * @package Test\TestCase
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
namespace kranemora\Test;

use kranemora\Html2Text\Html2Text;

/**
 * Test Html2Text.
 *
 * @package Test\TestCase
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class Html2TextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Html2Text object
     *
     * @var Html2Text
     */
    protected $html2Text;

    /**
     * Sets up object Html2Text object to test.
     *
     * @return void.
     */
    public function setUp()
    {
        $this->Html2Text = new Html2Text;
    }

    /**
     * Test methods Html2Text::setDefaultOptions and Html2Text::getDefaultOptions.
     *
     * @return void.
     */
    public function testSetGetOptions()
    {
        $options = $this->Html2Text->getDefaultOptions();
        $expectedTableOptions = [
            'break' => "\n\n",
            'parser' => [
                'class' => '\kranemora\Html2Text\Parsers\TableParser',
                'options' => [
                    'padding' => 1
                ]
            ]
        ];
        $this->assertArrayHasKey('table', $options);
        $this->assertEquals($expectedTableOptions, $options['table']);

        $defaultOptions = [
            'table' => [
                'parser' => [
                    'options' => [
                        'padding' => 0
                    ]
                ]
            ]
        ];

        $this->Html2Text->setDefaultOptions($defaultOptions);

        $options = $this->Html2Text->getDefaultOptions();
        $expectedTableOptions = [
            'break' => "\n\n",
            'parser' => [
                'class' => '\kranemora\Html2Text\Parsers\TableParser',
                'options' => [
                    'padding' => 0
                ]
            ]
        ];
        $this->assertArrayHasKey('table', $options);
        $this->assertEquals($expectedTableOptions, $options['table']);
    }

    /**
     * Test Html2Text object.
     *
     * @return void.
     */
    public function testHtml2Text()
    {
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

        $text = $this->Html2Text->convert($html);
        $expectedText = <<<EOF
Test Document
Lorem ipsum

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur porttitor nisi nec finibus bibendum. Donec at elementum leo. Donec eu felis vehicula, efficitur est at, fringilla nisi. Donec congue tortor vel pulvinar mattis. Etiam id ornare magna. In dapibus et nisl eget convallis. Etiam eu feugiat ante. Phasellus vulputate nec velit nec sagittis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut gravida accumsan lorem, id viverra nunc ultrices quis. Duis in tristique ligula, vel semper urna.

Dolor sit amet
consectetur adipiscing elit.

Curabitur porttitor nisi nec finibus bibendum
Donec at elementum leo.

Donec eu felis vehicula
Efficitur est at.

+-----------+---------------+-------+
| Position  | Gender        | Total |
|           |---------------|       |
|           | Male | Female |       |
+-----------+------+--------+-------+
| Tutor     |    5 |      8 |    13 |
+-----------+------+--------+-------+
| Professor |   10 |      8 |    18 |
+-----------+------+--------+-------+

Aenean a massa convallis

- Ultrices magna vitae
- Gravida velit
- Nunc lobortis
- Tortor nec auctor ultricies

Curabitur bibendum eu diam et venenatis

- Donec vitae enim suscipit
- Porta nunc tincidunt
- Consequat leo
- Nunc eu risus rutrum

lorem@ipsum.com
- Facebook [https://www.facebook.com]
- Twitter [https://www.twitter.com]
- Linkedin [https://www.linkedin.com/]
- Instagram [https://www.instagram.com]

Lorem ipsum
EOF;
        $this->assertEquals($expectedText, $text);

        $html = <<<EOF
        <ul>
            <li>Ultrices magna vitae</li>
            <li>Gravida velit</li>
            <li>Nunc lobortis</li>
            <li>Tortor nec auctor ultricies</li>
        </ul>
        <ul>
            <li>Tortor nec auctor ultricies</li>
            <li>Nunc lobortis</li>
            <li>Gravida velit</li>
            <li>Ultrices magna vitae</li>
        </ul>
EOF;

        $options = [
            // You can set only one option ...
            'ul' => [
                'break' => "\n"
            ],
            // ... or set them all
            'li' => [
                'break' => '',
                'prepend' => '[',
                'append' => ']',
                'between' => ' | '
            ]
        ];

        $this->Html2Text->setDefaultOptions($options);
        $text = $this->Html2Text->convert($html);

        $expectedText = <<<EOF
[Ultrices magna vitae] | [Gravida velit] | [Nunc lobortis] | [Tortor nec auctor ultricies]
[Tortor nec auctor ultricies] | [Nunc lobortis] | [Gravida velit] | [Ultrices magna vitae]
EOF;
        $this->assertEquals($expectedText, $text);
    }

}
