# sbooker/persistent-pointer

[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

## Installation
Install via [Composer][composer]:
```bash
composer require sbooker/persistent-pointer
```

### Usage

```php
<?php

use Sbooker\PersistentPointer\Pointer;
use Sbooker\PersistentPointer\Repository;
use Sbooker\PersistentPointer\PointerStorage;

$pointer = new Pointer('name');
$pointer->getValue(); // returns 0

// Increase pointer
$pointer->increaseTo(2); 
$pointer->getValue(); // returns 2

// Decrease pointer
$pointer->increaseTo(1); // throws \InvalidArgumentException
```

## License
See [LICENSE][license] file.

[badge-release]: https://img.shields.io/packagist/v/sbooker/persistent-pointer.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/sbooker/persistent-pointer.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/sbooker/persistent-pointer.svg?style=flat-square

[release]: https://img.shields.io/packagist/v/sbooker/persistent-pointer
[license]: https://github.com/sbooker/persistent-pointer/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/sbooker/persistent-pointer

[composer]: https://getcomposer.org