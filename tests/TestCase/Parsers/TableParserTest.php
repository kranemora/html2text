<?php
/**
 * Contain the class to test Html2Text\Parsers\TableParser.
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

use kranemora\Html2Text\Parsers\TableParser;
use DOMDocument;

/**
 * Test Html2Text\Parsers\TableParser.
 *
 * @package Test\TestCase\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class TableParserTest extends TestCase
{

    /**
     * Parser object
     *
     * @var Html2Text\Parsers\TableParser
     */
    protected $parser;

    /**
     * Sets up TableParser object to test.
     *
     * @return void.
     */
    public function setUp()
    {
        $this->parser = new TableParser;
    }

    /**
     * Test methods TableParser::getText.
     *
     * @return void.
     */
    public function testGetText()
    {
        $dom = new DOMDocument();

        // Simple table, one cell
        $html = <<<EOF
        <table>
            <tr><td>lorem ipsum</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------+
|lorem ipsum|
+-----------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11],
            'colParams' => [],
            'cellBorder' => [
                ['b']
            ],
            'cellData' => [
                ['lorem ipsum']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, two horizontal merged cells
        $html = <<<EOF
        <table>
            <tr><td colspan="2">lorem ipsum dolor</td></tr>
            <tr><td>lorem</td><td>Ipsum</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------------+
|lorem ipsum dolor|
+-----------------+
|lorem   |Ipsum   |
+--------+--------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [8, 8],
            'colParams' => [
                ['pos' => 0, 'cols' => 2, 'length' => 17]
            ],
            'cellBorder' => [
                ['h', 'b'],
                ['b', 'b']
            ],
            'cellData' => [
                ['lorem ipsum dolor', ''],
                ['lorem', 'Ipsum']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr><td>lorem</td><td>Ipsum</td></tr>
            <tr><td colspan="2">lorem ipsum dolor</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+--------+--------+
|lorem   |Ipsum   |
+-----------------+
|lorem ipsum dolor|
+-----------------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [8, 8],
            'colParams' => [
                ['pos' => 0, 'cols' => 2, 'length' => 17]
            ],
            'cellBorder' => [
                ['b', 'b'],
                ['h', 'b']
            ],
            'cellData' => [
                ['lorem', 'Ipsum'],
                ['lorem ipsum dolor', '']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr>
                <td colspan="2">finibus</td>
                <td>bibendum</td>
            </tr>
            <tr>
                <th>lorem</th>
                <th>ipsum</th>
                <th>dolor</th>
            </tr>
            <tr>
                <td>consectetur</td>
                <td>adipiscing</td>
                <td>elit</td>
            </tr>
            <tr>
                <td>porttitor</td>
                <td>nisi</td>
                <td>nec</td>
            </tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);

        $expectedText = <<<EOF
+----------------------+--------+
|finibus               |bibendum|
+----------------------+--------+
|lorem      |ipsum     |dolor   |
+-----------+----------+--------+
|consectetur|adipiscing|elit    |
+-----------+----------+--------+
|porttitor  |nisi      |nec     |
+-----------+----------+--------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 10, 8],
            'colParams' => [
                ['pos' => 0, 'cols' => 2, 'length' => 7]
            ],
            'cellBorder' => [
                ['h', 'b', 'b'],
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
            ],
            'cellData' => [
                ['finibus', '', 'bibendum'],
                ['lorem', 'ipsum', 'dolor'],
                ['consectetur', 'adipiscing', 'elit'],
                ['porttitor', 'nisi', 'nec']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr>
                <td>bibendum</td>
                <td colspan="2">finibus</td>
            </tr>
            <tr>
                <th>lorem</th>
                <th>ipsum</th>
                <th>dolor</th>
            </tr>
            <tr>
                <td>consectetur</td>
                <td>adipiscing</td>
                <td>elit</td>
            </tr>
            <tr>
                <td>porttitor</td>
                <td>nisi</td>
                <td>nec</td>
            </tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);

        $expectedText = <<<EOF
+-----------+----------------+
|bibendum   |finibus         |
+-----------+----------------+
|lorem      |ipsum     |dolor|
+-----------+----------+-----+
|consectetur|adipiscing|elit |
+-----------+----------+-----+
|porttitor  |nisi      |nec  |
+-----------+----------+-----+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 10, 5],
            'colParams' => [
                ['pos' => 1, 'cols' => 2, 'length' => 7]
            ],
            'cellBorder' => [
                ['b', 'h', 'b'],
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
            ],
            'cellData' => [
                ['bibendum', 'finibus', ''],
                ['lorem', 'ipsum', 'dolor'],
                ['consectetur', 'adipiscing', 'elit'],
                ['porttitor', 'nisi', 'nec']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr>
                <th>lorem</th>
                <th>ipsum</th>
                <th>dolor</th>
            </tr>
            <tr>
                <td>consectetur</td>
                <td>adipiscing</td>
                <td>elit</td>
            </tr>
            <tr>
                <td>porttitor</td>
                <td>nisi</td>
                <td>nec</td>
            </tr>
            <tr>
                <td>bibendum</td>
                <td colspan="2">finibus</td>
            </tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);

        $expectedText = <<<EOF
+-----------+----------+-----+
|lorem      |ipsum     |dolor|
+-----------+----------+-----+
|consectetur|adipiscing|elit |
+-----------+----------+-----+
|porttitor  |nisi      |nec  |
+-----------+----------------+
|bibendum   |finibus         |
+-----------+----------------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 10, 5],
            'colParams' => [
                ['pos' => 1, 'cols' => 2, 'length' => 7]
            ],
            'cellBorder' => [
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
                ['b', 'h', 'b'],
            ],
            'cellData' => [
                ['lorem', 'ipsum', 'dolor'],
                ['consectetur', 'adipiscing', 'elit'],
                ['porttitor', 'nisi', 'nec'],
                ['bibendum', 'finibus', '']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr>
                <th>lorem</th>
                <th>ipsum</th>
                <th>dolor</th>
            </tr>
            <tr>
                <td>consectetur</td>
                <td>adipiscing</td>
                <td>elit</td>
            </tr>
            <tr>
                <td>porttitor</td>
                <td>nisi</td>
                <td>nec</td>
            </tr>
            <tr>
                <td colspan="2">finibus</td>
                <td>bibendum</td>
            </tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);

        $expectedText = <<<EOF
+-----------+----------+--------+
|lorem      |ipsum     |dolor   |
+-----------+----------+--------+
|consectetur|adipiscing|elit    |
+-----------+----------+--------+
|porttitor  |nisi      |nec     |
+----------------------+--------+
|finibus               |bibendum|
+----------------------+--------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 10, 8],
            'colParams' => [
                ['pos' => 0, 'cols' => 2, 'length' => 7]
            ],
            'cellBorder' => [
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
                ['b', 'b', 'b'],
                ['h', 'b', 'b'],
            ],
            'cellData' => [
                ['lorem', 'ipsum', 'dolor'],
                ['consectetur', 'adipiscing', 'elit'],
                ['porttitor', 'nisi', 'nec'],
                ['finibus', '', 'bibendum']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, two horizontal merged cells and numeric data
        $html = <<<EOF
        <table>
            <tr><td colspan="2">lorem ipsum dolor</td></tr>
            <tr><td>1.00</td><td>10</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------------+
|lorem ipsum dolor|
+-----------------+
|     1.00|     10|
+---------+-------+
EOF;
        $this->assertEquals($expectedText, $text);

        $this->parser->setOptions(
            [
                'align' => [
                    'numbers' => 'left'
                 ]
            ]
        );
        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------------+
|lorem ipsum dolor|
+-----------------+
|1.00     |10     |
+---------+-------+
EOF;
        $this->assertEquals($expectedText, $text);

        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [9, 7],
            'colParams' => [
                ['pos' => 0, 'cols' => 2, 'length' => 17]
            ],
            'cellBorder' => [
                ['h', 'b'],
                ['b', 'b']
            ],
            'cellData' => [
                ['lorem ipsum dolor', ''],
                ['1.00', '10']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, two rows with alternate horizontal merged cells
        $html = <<<EOF
        <table>
            <tr><td colspan="2">lorem ipsum dolor</td><td>sit amet</td></tr>
            <tr><td>lorem</td><td colspan="2">consectetur</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-------------------+--------+
|lorem ipsum dolor  |sit amet|
+----------------------------+
|lorem     |consectetur      |
+----------+-----------------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [10, 8, 8],
            'colParams' => [
                ['pos' => 0, 'cols' => 2, 'length' => 17],
                ['pos' => 1, 'cols' => 2, 'length' => 11]
            ],
            'cellBorder' => [
                ['h', 'b', 'b'],
                ['b', 'h', 'b']
            ],
            'cellData' => [
                ['lorem ipsum dolor', '', 'sit amet'],
                ['lorem', 'consectetur', '']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, three horizontal merged cells
        $html = <<<EOF
        <table>
            <tr><td colspan="3">lorem ipsum dolor</td><td>sit amet</td></tr>
            <tr><td>lorem</td><td colspan="2">consectetur</td><td>adipiscing</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+---------------------+----------+
|lorem ipsum dolor    |sit amet  |
+---------------------+----------+
|lorem   |consectetur |adipiscing|
+--------+------------+----------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [8, 6, 5, 10],
            'colParams' => [
                ['pos' => 0, 'cols' => 3, 'length' => 17],
                ['pos' => 1, 'cols' => 2, 'length' => 11]
            ],
            'cellBorder' => [
                ['h', 'h', 'b', 'b'],
                ['b', 'h', 'b', 'b']
            ],
            'cellData' => [
                ['lorem ipsum dolor', '', '', 'sit amet'],
                ['lorem', 'consectetur', '', 'adipiscing']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, two vertical merged cells
        $html = <<<EOF
        <table>
            <tr><td rowspan="2">lorem ipsum</td><td>dolor</td></tr>
            <tr><td>consectetur</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------+-----------+
|lorem ipsum|dolor      |
|           |-----------+
|           |consectetur|
+-----------+-----------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 11],
            'colParams' => [],
            'cellBorder' => [
                ['v', 'b'],
                ['b', 'b']
            ],
            'cellData' => [
                ['lorem ipsum', 'dolor'],
                ['', 'consectetur']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, three vertical merged cells
        $html = <<<EOF
        <table>
            <tr><td rowspan="3">lorem ipsum</td><td>dolor</td></tr>
            <tr><td>consectetur</td></tr>
            <tr><td>adipiscing</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------+-----------+
|lorem ipsum|dolor      |
|           |-----------+
|           |consectetur|
|           |-----------+
|           |adipiscing |
+-----------+-----------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 11],
            'colParams' => [],
            'cellBorder' => [
                ['v', 'b'],
                ['v', 'b'],
                ['b', 'b']
            ],
            'cellData' => [
                ['lorem ipsum', 'dolor'],
                ['', 'consectetur'],
                ['', 'adipiscing']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        // Complex table, three vertical and horizontal merged cells
        $html = <<<EOF
        <table>
            <tr><td rowspan="3" colspan="3">lorem ipsum</td><td>dolor</td></tr>
            <tr><td>consectetur</td></tr>
            <tr><td>adipiscing</td></tr>
            <tr><td>elit</td><td>porttitor</td><td>nisi</td><td>nec</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-------------------+-----------+
|lorem ipsum        |dolor      |
|                   |-----------+
|                   |consectetur|
|                   |-----------+
|                   |adipiscing |
+-------------------+-----------+
|elit|porttitor|nisi|nec        |
+----+---------+----+-----------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [4, 9, 4, 11],
            'colParams' => [
                ['pos' => 0, 'cols' => 3, 'length' => 11]
            ],
            'cellBorder' => [
                ['n', 'n', 'v', 'b'],
                ['n', 'n', 'v', 'b'],
                ['h', 'h', 'b', 'b'],
                ['b', 'b', 'b', 'b'],
            ],
            'cellData' => [
                ['lorem ipsum', '', '', 'dolor'],
                ['', '', '', 'consectetur'],
                ['', '', '', 'adipiscing'],
                ['elit', 'porttitor', 'nisi', 'nec']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr><td>dolor</td><td rowspan="3" colspan="3">lorem ipsum</td></tr>
            <tr><td>consectetur</td></tr>
            <tr><td>adipiscing</td></tr>
            <tr><td>elit</td><td>porttitor</td><td>nisi</td><td>nec</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------+------------------+
|dolor      |lorem ipsum       |
+-----------|                  |
|consectetur|                  |
+-----------|                  |
|adipiscing |                  |
+-----------+------------------+
|elit       |porttitor|nisi|nec|
+-----------+---------+----+---+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 9, 4, 3],
            'colParams' => [
                ['pos' => 1, 'cols' => 3, 'length' => 11]
            ],
            'cellBorder' => [
                ['b', 'n', 'n', 'v'],
                ['b', 'n', 'n', 'v'],
                ['b', 'h', 'h', 'b'],
                ['b', 'b', 'b', 'b'],
            ],
            'cellData' => [
                ['dolor', 'lorem ipsum', '', ''],
                ['consectetur', '', '', ''],
                ['adipiscing', '', '', ''],
                ['elit', 'porttitor', 'nisi', 'nec']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr><td>elit</td><td>porttitor</td><td>nisi</td><td>nec</td></tr>
            <tr><td>dolor</td><td rowspan="3" colspan="3">lorem ipsum</td></tr>
            <tr><td>consectetur</td></tr>
            <tr><td>adipiscing</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+-----------+---------+----+---+
|elit       |porttitor|nisi|nec|
+-----------+------------------+
|dolor      |lorem ipsum       |
+-----------|                  |
|consectetur|                  |
+-----------|                  |
|adipiscing |                  |
+-----------+------------------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [11, 9, 4, 3],
            'colParams' => [
                ['pos' => 1, 'cols' => 3, 'length' => 11]
            ],
            'cellBorder' => [
                ['b', 'b', 'b', 'b'],
                ['b', 'n', 'n', 'v'],
                ['b', 'n', 'n', 'v'],
                ['b', 'h', 'h', 'b'],
            ],
            'cellData' => [
                ['elit', 'porttitor', 'nisi', 'nec'],
                ['dolor', 'lorem ipsum', '', ''],
                ['consectetur', '', '', ''],
                ['adipiscing', '', '', '']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);

        $html = <<<EOF
        <table>
            <tr><td>elit</td><td>porttitor</td><td>nisi</td><td>nec</td></tr>
            <tr><td rowspan="3" colspan="3">lorem ipsum</td><td>dolor</td></tr>
            <tr><td>consectetur</td></tr>
            <tr><td>adipiscing</td></tr>
        </table>
EOF;

        @$dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table')->item(0);

        $text = $this->parser->getText($table);
        $expectedText = <<<EOF
+----+---------+----+-----------+
|elit|porttitor|nisi|nec        |
+-------------------+-----------+
|lorem ipsum        |dolor      |
|                   |-----------+
|                   |consectetur|
|                   |-----------+
|                   |adipiscing |
+-------------------+-----------+
EOF;
        $this->assertEquals($expectedText, $text);
        $schema = $this->invokeMethod($this->parser, '_getSchema');

        $expectedSchema = [
            'colSize' => [4, 9, 4, 11],
            'colParams' => [
                ['pos' => 0, 'cols' => 3, 'length' => 11]
            ],
            'cellBorder' => [
                ['b', 'b', 'b', 'b'],
                ['n', 'n', 'v', 'b'],
                ['n', 'n', 'v', 'b'],
                ['h', 'h', 'b', 'b']
            ],
            'cellData' => [
                ['elit', 'porttitor', 'nisi', 'nec'],
                ['lorem ipsum', '', '', 'dolor'],
                ['', '', '', 'consectetur'],
                ['', '', '', 'adipiscing']
            ]
        ];
        $this->assertEquals($expectedSchema, $schema);
    }

    /**
     * Test methods TableParser::setOptions and TableParser::getOptions.
     *
     * @return void.
     */
    public function testSetGetOptions()
    {
        $this->parser->setOptions(
            [
                'padding' => 1
            ]
        );

        $expectedOptions = [
            'padding' => 1,
            'align' => [
                'numbers' => 'right',
                'strings' => 'left'
            ]
        ];
        $options = $this->parser->getOptions();

        $this->assertEquals($expectedOptions, $options);
    }
}
