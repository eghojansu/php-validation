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
            'today_date' => date('Y-m-d'),
            'id' => 'AlphaOne',
            'id_snake' => 'alpha_one',
            'id_reg' => 'MEM123',
            'slug' => 'slug-the-whole-things',
            'text' => 'Pada zaman dahulu...',
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
            'after' => array(array('today_date' => 'after:yesterday')),
            'after equals' => array(array('today_date' => 'after:today,true')),
            'not after' => array(array('today_date' => 'after:today'), array('success' => false)),
            'alpha' => array(array('id' => 'alpha')),
            'not alpha' => array(array('id_snake' => 'alpha'), array('success' => false)),
            'alpha_dash' => array(array('slug' => 'alpha_dash')),
            'not alpha_dash' => array(array('text' => 'alpha_dash'), array('success' => false)),
            'alpha_num' => array(array('id_reg' => 'alpha_num')),
            'not alpha_num' => array(array('date' => 'alpha_num'), array('success' => false)),
        );
    }
}
