# Html to Text Converter

Convert HTML documents to plain text.

## Installing

```
composer require kranemora/html2text
```

## Basic Usage

```php
$html = <<<EOF
<p>Welcome to <strong>html2text<strong></p>
<p>The <em>best<em> html to text converter!</p>
EOF;

$html2Text = new \kranemora\Html2Text\Html2Text;
$text = $html2Text->convert($html);
```

Output:

```
Welcome to html2text

The best html to text converter!
```

## Examples

### Default settings

```php
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
            <aside>
                <h2>Lorem ipsum</h2>
                <ul>
                    <li><a href="https://www.facebook.com">Facebook</a></li>
                    <li><a href="https://www.twitter.com">Twitter</a></li>
                    <li><a href="https://www.linkedin.com/">Linkedin</a></li>
                    <li><a href="https://www.instagram.com">Instagram</a></li>
                </ul>
            </aside>
        </footer>
        Lorem ipsum
    </body>
</html>
EOF;

$html2Text = new \kranemora\Html2Text\Html2Text;
$text = $html2Text->convert($html);
```

Output:

```
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

Lorem ipsum

- Facebook [https://www.facebook.com]
- Twitter [https://www.twitter.com]
- Linkedin [https://www.linkedin.com/]
- Instagram [https://www.instagram.com]

Lorem ipsum
```

### Custom settings

```php
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
        'between' => ', '
    ]
];

$html2Text = new \kranemora\Html2Text\Html2Text;
$html2Text->setDefaultOptions($options);
$text = $html2Text->convert($html);
```

Output:

```
[Ultrices magna vitae], [Gravida velit], [Nunc lobortis], [Tortor nec auctor ultricies]
[Tortor nec auctor ultricies], [Nunc lobortis], [Gravida velit], [Ultrices magna vitae]
```

### Custom Parser

./src/Parsers/OlParser.php

```php
<?php
namespace kranemora\Html2Text\Parsers;

use DOMElement;

class OlParser extends BaseParser
{
    // Overwrite this function and return the node in plain text
    public function getText(DOMElement $node)
    {
        $options = $this->getOptions(); // Gets the options that were set with Html2Tex :: setDefaultOptions

        // Write here the algorithm to convert the node to plain text

        return "node in plain text";
    }
}
```

Set the Parser to the HTML element

```php
$options = [
    'ol' => [
        'break' => "\n",
        'parser' => [
            'class' => '\kranemora\Html2Text\Parsers\OlParser',
            'options' => [
                'reverse' => 0
            ]
        ]
    ]
];
```

## Author

* **Fernando Pita** - **Initial work** - [Kranemora](https://github.com/kranemora)

## License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT).
