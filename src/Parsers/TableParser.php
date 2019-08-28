<?php
/**
 * Contain the class to parse html tables.
 *
 * PHP version 5.6
 *
 * @package Html2Text\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
namespace kranemora\Html2Text\Parsers;

use DOMElement;

/**
 * HTML table parser.
 *
 * Parse an HTML document.
 *
 * @package Html2Text\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class TableParser extends BaseParser
{

    /**
     * Default options for html table
     *
     * @var array
     */
    protected $defaultOptions = [
        'padding' => '0',
        'align' => [
            'numbers' => 'right',
            'strings' => 'left'
        ]
    ];

    /**
     * Table schema
     *
     * @var array
     */
    private $_schema = [
        'colSize' => [],
        'colParams' => [],
        'cellBorder' => [],
        'cellData' => []
    ];

    /**
     * Parse the provided HTML table and convert it to plain text.
     *
     * @param DOMElement $node HTML table element.
     *
     * @return string Element in plain text format.
     */
    public function getText(DOMElement $node)
    {
        $this->_setSchema($node);

        $out = $this->_getLine(0)."\n";
        $rowCount = count($this->_schema['cellData']);
        for ($r = 0; $r < $rowCount; $r++) {
            $out .= $this->_getRow($r)."\n";
            $out .= $this->_getLine($r+1)."\n";
        }

        return trim($out);
    }

    /**
     * Set the table schema.
     *
     * The table schema contains the necessary parameters for the conversion of an HTML table to plain text.
     *
     * @param DOMElement $node HTML table element.
     *
     * @return void.
     */
    private function _setSchema(DOMElement $node)
    {
        $colCount = 0;
        $rowCount = 0;
        $schema = [
            'colSize' => [],
            'colParams' => [],
            'cellBorder' => [],
            'cellData' => []
        ];

        foreach ($node->childNodes as $row) {
            if ($row->nodeName == 'tr') {

                foreach ($row->childNodes as $cell) {
                    if (in_array($cell->nodeName, ['th', 'td'])) {
                        if ($cell->hasAttributes()) {
                            foreach ($cell->attributes as $attr) {
                                if ($attr->name == 'colspan') {
                                    $colspan = $attr->value;
                                }
                                if ($attr->name == 'rowspan') {
                                    $rowspan = $attr->value;
                                }
                            }
                        }

                        // La propiedad cellBorder sirve aquí para determina si la celda se ha llenado antes como
                        // resultado de la combinación de celdas de forma vertical
                        while (!empty($schema['cellBorder'][$rowCount][$colCount])) {
                            $colCount++;
                        }
                        $schema['cellData'][$rowCount][$colCount] = trim(
                            preg_replace(
                                '/\s\s+/', ' ', $cell->textContent
                            )
                        );

                        // Se determina el ancho de columna
                        if (empty($colspan)) {
                            $len = strlen($schema['cellData'][$rowCount][$colCount]);
                            if (empty($schema['colSize'][$colCount])
                                || $len > $schema['colSize'][$colCount]
                            ) {
                                $schema['colSize'][$colCount] = $len;
                            }
                        } else {
                            if (empty($schema['colSize'][$colCount])) {
                                $schema['colSize'][$colCount] = 0;
                            }
                        }

                        // Se determinan los bordes y datos nulos para las celdas combinadas
                        if (!empty($colspan) && empty($rowspan)) {
                            $schema['cellBorder'][$rowCount][$colCount] = 'h';

                            for ($c=1; $c < $colspan; $c++) {
                                $schema['cellData'][$rowCount][$colCount+$c] = '';
                                if (empty($schema['colSize'][$colCount+$c])) {
                                    $schema['colSize'][$colCount+$c] = 0;
                                }
                                // Si es el últinmo de la fila
                                if ($colspan -$c == 1) {
                                    $schema['cellBorder'][$rowCount][$colCount+$c] = 'b';
                                } else {
                                    $schema['cellBorder'][$rowCount][$colCount+$c] = 'h';
                                }
                            }

                            $schema['colParams'][] = [
                                'pos' => $colCount,
                                'cols' => $colspan,
                                'length' => strlen($schema['cellData'][$rowCount][$colCount])
                            ];
                            $colCount = $colCount+$colspan;
                            $colspan = 0;

                        } elseif (!empty($rowspan) && empty($colspan)) {
                            $schema['cellBorder'][$rowCount][$colCount] = 'v';
                            for ($r=1; $r<$rowspan; $r++) {
                                for ($c=0; $c<=$colCount; $c++) {
                                    $schema['cellData'][$rowCount+$r][$c] = '';
                                    if (empty($schema['cellBorder'][$rowCount+$r][$c])) {
                                        $schema['cellBorder'][$rowCount+$r][$c] = '';
                                    }
                                }
                                $schema['cellData'][$rowCount+$r][$colCount] = "";

                                // Si es el último elemento de la columna
                                if ($rowspan - $r == 1) {
                                    $schema['cellBorder'][$rowCount+$r][$colCount] = 'b';
                                } else {
                                    $schema['cellBorder'][$rowCount+$r][$colCount] = 'v';
                                }
                            }
                            $rowspan = 0;

                        } elseif (!empty($rowspan) && !empty($colspan)) {
                            $schema['cellBorder'][$rowCount][$colCount] = 'n';

                            // Se generan datos nulos para las celdas combinadas horizontalmente
                            for ($c=1; $c < $colspan; $c++) {
                                $schema['cellData'][$rowCount][$colCount+$c] = '';
                                if (empty($schema['colSize'][$colCount+$c])) {
                                    $schema['colSize'][$colCount+$c] = 0;
                                }
                                // Si es el últinmo de la fila
                                if ($colspan -$c == 1) {
                                    $schema['cellBorder'][$rowCount][$colCount+$colspan-1] = 'v';
                                } else {
                                    $schema['cellBorder'][$rowCount][$colCount+$c] = 'n';
                                }
                            }

                            for ($r=1; $r<$rowspan; $r++) {
                                for ($c=0; $c<=$colCount; $c++) {
                                    $schema['cellData'][$rowCount+$r][$c] = '';
                                    if (empty($schema['cellBorder'][$rowCount+$r][$c])) {
                                        $schema['cellBorder'][$rowCount+$r][$c] = '';
                                    }
                                }

                                // Si es el último elemento de la columna
                                if ($rowspan - $r == 1) {
                                    $schema['cellBorder'][$rowCount+$r][$colCount] = 'h';
                                } else {
                                    $schema['cellBorder'][$rowCount+$r][$colCount] = 'n';
                                }

                                for ($c=1; $c < $colspan; $c++) {
                                    $schema['cellData'][$rowCount+$r][$colCount+$c] = '';
                                    if (empty($schema['colSize'][$colCount+$c])) {
                                        $schema['colSize'][$colCount+$c] = 0;
                                    }
                                    // Si es el últinmo de la fila
                                    if ($colspan -$c == 1) {
                                        // Si es la esquina inferior derecha
                                        if ($rowspan - $r == 1) {
                                            $schema['cellBorder'][$rowCount+$r][$colCount+$c] = 'b';
                                        } else {
                                            $schema['cellBorder'][$rowCount+$r][$colCount+$c] = 'v';
                                        }
                                    } else {
                                        $schema['cellBorder'][$rowCount+$r][$colCount+$c]
                                            = $schema['cellBorder'][$rowCount+$r][$colCount];
                                    }
                                }
                            }

                            $schema['colParams'][] = [
                                'pos' => $colCount,
                                'cols' => $colspan,
                                'length' => strlen($schema['cellData'][$rowCount][$colCount])
                            ];

                            $colCount = $colCount+$colspan;
                            $colspan = 0;
                            $rowspan = 0;

                        } else {
                            $schema['cellBorder'][$rowCount][$colCount] = 'b';
                        }
                    }
                }
                $colCount = 0;
                $rowCount++;
            }
        }

        // Reajuste del tamaño de las columnas
        foreach ($schema['colParams'] as $colParam) {
            $length = 0;
            for ($i = $colParam['pos']; $i < $colParam['cols']; $i++) {
                $length += $schema['colSize'][$i];
            }
            $length += ($colParam['cols'] - 1)*(2*$this->options['padding']+1);
            if ($length < $colParam['length']) {
                $diff = $colParam['length'] - $length;
                $delta = (integer) floor($diff / $colParam['cols']);
                $remainder = $diff % $colParam['cols'];
                for ($i = $colParam['pos']; $i < $colParam['cols']; $i++) {
                    $schema['colSize'][$i] += $delta;
                }
                $schema['colSize'][$colParam['pos']+$colParam['cols']-1] += $remainder;
            }
        }
        $this->_schema = $schema;
    }

    /**
     * Get the table schema.
     *
     * The table schema contains the necessary parameters for the conversion of an HTML table to plain text.
     *
     * @return array Table schema.
     */
    private function _getSchema()
    {
        return $this->_schema;
    }

    /**
     * Get the horizontal division of the table given its position.
     *
     * @param integer $pos position
     *
     * @return string Horizontal division of the table
     */
    private function _getLine($pos)
    {

        $out = $this->_getJunction($pos, 0);
        $schema = $this->_getSchema();

        foreach ($schema['colSize'] as $i => $colSize) {
            if ($pos == 0 || in_array($schema['cellBorder'][$pos-1][$i], ['b', 'h'])) {
                $simbol = "-";
            } else {
                $simbol = " ";
            }
            $out .= str_repeat($simbol, $colSize + 2 * $this->options['padding']).$this->_getJunction($pos, $i+1);
        }
        return $out;
    }

    /**
     * Get the junction of the horizontal division of the table given its position.
     *
     * @param integer $x Horizontal position.
     * @param integer $y Vertical position.
     *
     * @return string Table line junction.
     */
    private function _getJunction($x, $y)
    {
        $out = '+';
        $schema = $this->_getSchema();

        if ($x>0 && $x<count($schema['cellData']) && $y>0 && $y<count($schema['colSize'])) {
            if (($schema['cellBorder'][$x-1][$y-1] == 'h'
                && in_array($schema['cellBorder'][$x-1][$y], ['h', 'b']))
                || ($schema['cellBorder'][$x-1][$y-1] == 'b'
                && in_array($schema['cellBorder'][$x-1][$y], ['h', 'b'])
                && in_array($schema['cellBorder'][$x][$y-1], ['n', 'h']))
            ) {
                $out = '-';
            } elseif (($schema['cellBorder'][$x-1][$y-1] == 'v'
                && in_array($schema['cellBorder'][$x][$y-1], ['v', 'b']))
                || ($schema['cellBorder'][$x-1][$y-1] == 'b'
                && in_array($schema['cellBorder'][$x][$y-1], ['v', 'b'])
                && in_array($schema['cellBorder'][$x-1][$y], ['n', 'v']))
            ) {
                $out = '|';
            } elseif ($schema['cellBorder'][$x-1][$y-1] == 'n'
                && in_array($schema['cellBorder'][$x-1][$y], ['v', 'n'])
                && in_array($schema['cellBorder'][$x][$y-1], ['h', 'n'])
            ) {
                $out = ' ';
            }
        } else {
            if ($x == 0 && $y > 0) {
                if (in_array($schema['cellBorder'][$x][$y-1], ['h', 'n'])) {
                    $out = '-';
                }
            } elseif ($x>0 && $y == 0) {
                if (in_array($schema['cellBorder'][$x-1][$y], ['n', 'v'])) {
                    $out = '|';
                }
            } elseif ($y == count($schema['colSize'])) {
                if (in_array($schema['cellBorder'][$x-1][$y-1], ['n', 'v'])) {
                    $out = '|';
                }
            } elseif ($x == count($schema['cellData'])) {
                if (in_array($schema['cellBorder'][$x-1][$y-1], ['n', 'h'])) {
                    $out = '-';
                }
            }
        }
        return $out;
    }

    /**
     * Get table row.
     *
     * @param integer $row Row position of the HTML table.
     *
     * @return string Table row in plain text.
     */
    private function _getRow($row)
    {
        $out = "|";
        $schema = $this->_getSchema();

        $k = 0;
        $colSize = 0;
        $cellCount = count($schema['colSize']);

        while ($k<$cellCount) {
            $cell = str_repeat(" ", $this->options['padding'])
                .$schema['cellData'][$row][$k].str_repeat(" ", $this->options['padding']);
            $cellSize = strlen($cell);
            $colSize = $schema['colSize'][$k]+2*$this->options['padding'];

            if (in_array($schema['cellBorder'][$row][$k], ['h', 'n'])) {
                $next = $k+1;
                while ($next<$cellCount) {
                    if (empty($schema['cellData'][$row][$next])) {
                        $colSize+=$schema['colSize'][$next]+2*$this->options['padding']+1;
                        $next++;
                    } else {
                        break;
                    }
                }

                $k = $next-1;
            }

            if ($colSize > $cellSize) {
                if (is_numeric($schema['cellData'][$row][$k])) {
                    $type = 'numbers';
                } else {
                    $type = 'strings';
                }
                if ($this->options['align'][$type] == 'right') {
                    $cell = str_repeat(" ", $colSize-$cellSize).$cell;
                } else {
                    $cell .= str_repeat(" ", $colSize-$cellSize);
                }
            }

            $out .= $cell;

            if (in_array($schema['cellBorder'][$row][$k], ['b', 'v'])) {
                $out .= "|";
            }

            $k++;
        }
        return $out;
    }

}
