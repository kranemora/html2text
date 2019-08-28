<?php
/**
 * Contain the class to convert html documents to plain text.
 *
 * PHP version 5.6
 *
 * @package Html2Text
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
namespace kranemora\Html2Text;

use DOMDocument;

/**
 * HTML to plain text converter.
 *
 * Convert HTML documents to plain text.
 *
 * @package Html2Text
 * @author  Fernando Pita <kranemora@gmail.comt>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link    https://github.com/kranemora/html2text
 * @since   1.0
 */
class Html2Text
{
    /**
     * Default options.
     *
     * These are merged with user-provided options when the object is used.
     *
     * @var array
     */
    private $_defaultOptions = [
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
            'break' => "\n\n",
            'parser' => [
                'class' => '\kranemora\Html2Text\Parsers\TableParser',
                'options' => [
                    'padding' => 1
                ]
            ]
        ]
    ];

    /**
     * Set default options.
     *
     * @param array $defaultOptions List of options to be set on this object.
     *
     * @return void.
     */
    public function setDefaultOptions(array $defaultOptions = [])
    {
        $this->_defaultOptions = array_replace_recursive($this->_defaultOptions, $defaultOptions);
    }

    /**
     * Get default options.
     *
     * @return array List of options to be set on this object.
     */
    public function getDefaultOptions()
    {
        return $this->_defaultOptions;
    }

    /**
     * Convert the provided HTML document into plain text.
     *
     * @param string $html    Document in HTML format.
     * @param array  $options A list of properties to be set on this object.
     *
     * @return string Document in plain text format.
     */
    public function convert($html, array $options = [])
    {
        $options = array_merge($this->_defaultOptions, $options);

        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $body = $dom->getElementsByTagName('body')->item(0);

        $parser = new \kranemora\Html2Text\Parsers\DefaultParser($options);
        $text = $parser->getText($body);
        return $text;
    }

}
