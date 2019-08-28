<?php
/**
 * Contain the base class to parse html documents.
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
 * HTML document base parser.
 *
 * Transform an HTML document into plain text.
 *
 * @package Html2Text\Parsers
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class BaseParser
{
    /**
     * Default options
     *
     * These are merged with user-provided options when the object is used.
     *
     * @var array
     */
    protected $options = [];
    protected $defaultOptions = [];

    /**
     * Constructor
     *
     * @param array $options List of properties to be set on this object
     */
    public function __construct(array $options = [])
    {
        $this->options = array_replace_recursive($this->defaultOptions, $options);
    }

    /**
     * Set options.
     *
     * @param array $options List of options to be set on this object.
     *
     * @return void.
     */
    public function setOptions(array $options = [])
    {
        $this->options = array_replace_recursive($this->options, $options);
    }

    /**
     * Get options.
     *
     * @return array List of options to be set on this object.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Parse the provided HTML document and convert it to plain text.
     *
     * @param DOMElement $node HTML element.
     *
     * @return string Element in plain text format.
     */
    public function getText(DOMElement $node)
    {
        $dom = new \DOMDocument();
        $dom->appendChild($dom->importNode($node, true));
        return trim(preg_replace('/\s\s+/', ' ', strip_tags($dom->saveHTML())));
    }
}
