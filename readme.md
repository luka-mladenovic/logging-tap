# Laravel logging tap

Adds a [logging tap](https://laravel.com/docs/5.6/logging#advanced-monolog-channel-customization) functionality to Laravel versions from 5.0 to 5.3.



## Installation

Install the package

@todo



Open up `config/app.php` and add the service provider to your existing `providers` array.

```php
'providers' => [	
	LoggingTap\LogServiceProvider::class
]
```



## Usage

Create a new logging tap using the artisan `make:tap` command. This will create a new tap file inside the `App\Logging` folder.

```
php artisan make:tap ExampleTap
```



Open up `config/app.php`, scroll down to "Logging Configuration" and add a `log_tap` array entry. Add your logging tap classes to this array. Any classes in this array will customize the Monolog instance when created.

```php
'log_tap' => [App\Taps\ExampleTap::class],
```



For more information on how to work with logging taps refer to Laravel's [logging documentation](https://laravel.com/docs/5.6/logging#advanced-monolog-channel-customization).



## Examples

Create a logging tap to push a processor to Monolog instance:

```php
<?php

namespace App\Logging;
use Monolog\Processor\UidProcessor;

class PushProcessor
{
    public function __invoke($logger, $arguments)
    {
        $logger->getMonolog()->pushProcessor(new UidProcessor);
    }
}
```



Set Monolog instance formatter:

```php
<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class Formatter
{
    public function __invoke($logger, $arguments)
    {
        $format = "[%datetime%] - [%level_name%]: %message% %context% %extra%\n";
        $formatter = new LineFormatter($format, null, true, true);

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }
    }
```



## Testing

```
phpunit
```



## License

The MIT License (MIT). See the license file for more information.