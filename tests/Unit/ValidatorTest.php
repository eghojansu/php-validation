<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @var Validator */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    /** @dataProvider rulesProvider */
    public function testRules(array $rules, array $expected = null)
    {
        $data = array(
            'acc' => 'on',
            'homepage' => 'https://ekokurniawan.my.id/',
        );
        $actual = $this->validator->setThrowIfError(false)->validate($rules, $data);

        $this->assertEquals($expected['success'] ?? true, $actual->success());
    }

    public function rulesProvider()
    {
        return array(
            'accepted' => array(array('acc' => 'accepted')),
            'not accepted' => array(array('homepage' => 'accepted'), array('success' => false)),
            'active_url' => array(array('homepage' => 'active_url')),
            'not active_url' => array(array('acc' => 'active_url'), array('success' => false)),
        );
    }
}
