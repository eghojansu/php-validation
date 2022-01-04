# PHP Validation

PHP Validation.

## Usage

```php
<?php

use Ekok\Validation\Validator;

$validator = new Validator();
$data = array(
    'username' => 'foo',
    'password' => 'bar',
);
$rules = array(
    'username' => 'trim|min:5',
    'password' => 'trim|min:5',
);
// any validation error will throw Ekok\Validation\ValidationException
$result = $validator->validate($rules, $data);

// $result['username'] is ok
// $result['password'] is ok
```