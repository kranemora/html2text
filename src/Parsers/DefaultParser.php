<?php
/**
 * Contain the class to parse html documents.
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
 * HTML document parser.
 *
 * Transform an HTML document into plain text.
 *
 * @package Html2Text\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class DefaultParser extends BaseParser
{
    /**
     * Default options for html elements
     *
     * @var array
     */
    private $_elementDefaultOptions = [
        'break' => '',
        'prepend' => '',
        'append' => '',
        'between' => '',
        'parser' => [
            'class' => '',
            'options' => []
        ]
    ];

    /**
     * Parse the provided HTML document and convert it to plain text.
     *
     * @param DOMElement $node HTML element.
     *
     * @return string Element in plain text format.
     */
    public function getText(DOMElement $node)
    {
        return trim($this->_getText($node));
    }

    /**
     * Recursive parse the provided HTML element and convert it to plain text.
     *
     * @param DOMElement $node HTML element.
     *
     * @return string Element in plain text format.
     */
    private function _getText(DOMElement $node)
    {
        $out = "";

        if ($node->hasChildNodes()) {
            $prevBetween = "";
            foreach ($node->childNodes as $element) {
                // Si es un texto
                if ($element->nodeName == "#text") {
                    // Si el contenido es no vacío
                    if (!empty(trim($element->nodeValue))) {
                        // Se convierten los saltos de línea en espacios y se eliminan los espacios adicionales
                        $text = preg_replace('/\h\h+/', ' ', preg_replace('/\s/', ' ', $element->nodeValue));
                        // Si el contenido es vacío
                    } else {
                        $text = " ";
                    }
                    // Si el elemento previo termina en un espacios
                    if (!$element->previousSibling
                        || preg_match('/.*\s+$/i', $element->previousSibling->nodeValue)
                    ) {
                        $text = ltrim($text);
                    }
                    $out .= $text;

                    // Si es un elemento
                } else {

                    $this->_setElementDefaultOptions($element);

                    $prepend = $this->_processElementOption(
                        $this->options[$element->nodeName]['prepend'],
                        $this->_getAttributes($element)
                    );
                    $append = $this->_processElementOption(
                        $this->options[$element->nodeName]['append'],
                        $this->_getAttributes($element)
                    );

                    if (!empty($prevBetween) && $prevBetween == $this->options[$element->nodeName]['between']) {
                        $out = rtrim($out).$this->options[$element->nodeName]['between'];
                    }
                    $prevBetween = $this->options[$element->nodeName]['between'];

                    if (!empty($this->options[$element->nodeName]['parser']['class'])) {
                        $parser = new $this->options[$element->nodeName]['parser']['class'](
                            $this->options[$element->nodeName]['parser']['options']
                        );
                        $out .= $prepend.$parser->getText($element).$append;
                    } else {
                        $out .= $prepend.$this->_getText($element).$append;
                    }

                    // Si no es el primer elemento y es un elemento de bloque
                    if (!empty($out) && !empty($this->options[$element->nodeName]['break'])) {

                        // El salto de línea es el mayor de los saltos de línea de todos los elementos anidados
                        if (preg_match('/.*(\n+)$/i', $out, $matches)) {
                            if ($matches[1] == "\n" &&  $this->options[$element->nodeName]['break'] == "\n\n") {
                                $out .= "\n";
                            }
                        } else {
                            $out .= $this->options[$element->nodeName]['break'];
                        }
                    }
                }
            }
        }
        $out = preg_replace('/\h*(\n+)\h*/i', '$1', $out);
        return ltrim($out);
    }

    /**
     * Set HTML element default options.
     *
     * @param DOMElement $element HTML element.
     *
     * @return void.
     */
    private function _setElementDefaultOptions(DOMElement $element)
    {
        if (!empty($this->options[$element->nodeName])) {
            $this->options[$element->nodeName] = array_replace_recursive(
                $this->_elementDefaultOptions,
                $this->options[$element->nodeName]
            );
        } else {
            $this->options[$element->nodeName] = $this->_elementDefaultOptions;
        }
    }

    /**
     * Get HTML element attributes.
     *
     * @param DOMElement $element HTML element.
     *
     * @return array HTML element attributes.
     */
    private function _getAttributes(DOMElement $element)
    {
        $attributes = [];
        if ($element->hasAttributes()) {
            foreach ($element->attributes as $attr) {
                $attributes[$attr->name] = $attr->value;
            }
        }
        return $attributes;
    }

    /**
     * Process HTML element option.
     *
     * Replace variables in an option with the values of the attributes they represent
     *
     * @param string $value Option value.
     * @param array  $map   List of attributes.
     *
     * @return string Option value processed.
     */
    private function _processElementOption($value, $map)
    {
        if (empty($value) || empty($map)) {
            return $value;
        }

        if (preg_match_all('/\{\{([^}]*)\}\}/i', $value, $matches)) {
            $replace = [];
            foreach ($matches[1] as $k => $var) {
                if (!empty($map[$var])) {
                    $replace[] = $map[$var];
                } else {
                    $replace[] = $matches[0][$k];
                }
            }
            return str_replace($matches[0], $replace, $value);
        }

        return $value;
    }
}
?>
