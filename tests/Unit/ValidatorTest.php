<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Ekok\Validation\DynamicRule;
use Ekok\Validation\Rule;
use Ekok\Validation\Rules\Callback;
use Ekok\Validation\ValidationException;

class ValidatorTest extends TestCase
{
    /** @var Validator */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testValidator()
    {
        $this->assertTrue($this->validator->isThrowIfError());

        $this->validator->setNamespaces(array('foo', 'bar', 'baz'));
        $this->validator->setMessages(array('foo' => 'Default foo message'));
        $this->validator->setRules(array(
            'foo' => new class extends Rule implements DynamicRule {
                public function setArguments(array $arguments): Rule
                {
                    $this->arguments = $arguments;

                    return $this;
                }

                protected function doValidate($value)
                {
                    $this->context->stopPropagation();

                    return count($this->arguments) == 2 && 'bar' === $value;
                }
            },
        ));

        $this->assertCount(4, $this->validator->getNamespaces());
        $this->assertEquals(array(Validator::RULE_NAMESPACE, 'foo\\', 'bar\\', 'baz\\'), $this->validator->getNamespaces());
        $this->assertCount(1, $this->validator->getRules());
        $this->assertCount(1, $this->validator->getMessages());

        // do validate
        $result = $this->validator->validate(
            array(
                'foo' => 'alpha|foo:1,2',
                'bar' => 'exclude_if:baz,qux',
                'baz' => 'exclude_unless:baz,quux', // same field check
                'qux' => 'exclude_unless:baz,qux',
            ),
            array(
                'foo' => 'bar',
                'bar' => 'baz',
                'baz' => 'qux',
                'qux' => 'quux',
            ),
        );
        $actual = $result->getData();
        $expected = array(
            'foo' => 'bar',
            'qux' => 'quux',
        );

        $this->assertTrue($result->success());
        $this->assertEquals($expected, $actual);
    }

    public function testUnknownValidationRule()
    {
        $this->expectExceptionMessage('Validation rule not found: foo');

        $this->validator->validate(array('foo' => 'foo'), array());
    }

    public function testValidationFailure()
    {
        try {
            $this->validator->validate(
                array('cb' => array('callback' => function () {
                    /** @var Callback */
                    $that = $this;

                    return !$that->setMessage('Error from callback validation');
                })),
                array('cb' => 'foo'),
            );
        } catch (ValidationException $error) {
            $this->assertSame('Unprocessable entity', $error->getMessage());
            $this->assertSame(array('cb' => array('Error from callback validation')), $error->errors);
        }
    }

    /** @dataProvider rulesProvider */
    public function testRules(array $rules, bool $expected = true)
    {
        $data = array(
            'accept' => 'on',
            'agreed' => 'on',
            'reject' => 'off',
            'homepage' => 'https://ekokurniawan.my.id/',
            'email' => 'email@mail.com',
            'today_date' => (new \DateTime())->format('Y-m-d'),
            'yesterday_date' => (new \DateTime())->modify('-1 day')->format('Y-m-d'),
            'tomorrow_date' => (new \DateTime())->modify('+1 day')->format('Y-m-d'),
            'id' => 'AlphaOne',
            'id_snake' => 'alpha_one',
            'id_reg' => 'MEM123',
            'slug' => 'slug-the-whole-things',
            'text' => 'Pada zaman dahulu...',
            'number' => 10,
            'number_str' => '10',
            'is_true' => '1',
            'is_false' => 'false',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'password_secret' => 'secret',
            'user' => array(
                'name' => 'User',
                'birthdate' => '2022-01-01',
                'roles' => array('user', 'admin'),
            ),
            'tags' => array(
                array('name' => 'foo'),
                array('name' => 'bar'),
            ),
            'member' => array(
                array('name' => 'foo'),
                array('name' => 'bar'),
                array('name' => 'foo'),
            ),
            'text_none' => '',
        );
        $result = $this->validator->setThrowIfError(false)->validate($rules, $data);

        $this->assertEquals($expected, $result->success());
    }

    public function rulesProvider()
    {
        return array(
            'accepted' => array(array('accept' => 'accepted')),
            'not accepted' => array(array('homepage' => 'accepted'), false),
            'active_url' => array(array('homepage' => 'active_url')),
            'not active_url' => array(array('acc' => 'active_url'), false),
            'after' => array(array('today_date' => 'after:yesterday')),
            'after field' => array(array('today_date' => 'after:yesterday_date')),
            'after equals' => array(array('today_date' => 'after:today,true')),
            'not after' => array(array('today_date' => 'after:today'), false),
            'alpha' => array(array('id' => 'alpha')),
            'alpha multiple' => array(array('tags.*.name' => 'alpha')),
            'not alpha' => array(array('id_snake' => 'alpha'), false),
            'alpha_dash' => array(array('slug' => 'alpha_dash')),
            'not alpha_dash' => array(array('text' => 'alpha_dash'), false),
            'alpha_num' => array(array('id_reg' => 'alpha_num')),
            'not alpha_num' => array(array('date' => 'alpha_num'), false),
            'array' => array(array('user' => 'array')),
            'array keys' => array(array('user' => 'array:name,birthdate')),
            'not array' => array(array('accept' => 'array'), false),
            'before' => array(array('today_date' => 'before:tomorrow')),
            'before field' => array(array('today_date' => 'before:tomorrow_date')),
            'before equals' => array(array('today_date' => 'before:today,true')),
            'not before' => array(array('today_date' => 'before:today'), false),
            'between' => array(array('number' => 'between:10,10')),
            'between number' => array(array('number' => 'between:9,11')),
            'not between' => array(array('number' => 'between:11,12'), false),
            'boolean' => array(array('is_true' => 'boolean')),
            'boolean false' => array(array('is_false' => 'boolean')),
            'boolean unknown' => array(array('unknown' => 'boolean')),
            'not boolean' => array(array('number' => 'boolean'), false),
            'callback' => array(array('number' => array('callback' => fn(int $value) => $value === 10))),
            'not callback' => array(array('number' => array('callback' => fn(int $value) => $value > 10)), false),
            'confirmed' => array(array('password' => 'confirmed')),
            'confirmed another field' => array(array('password' => 'confirmed:password_secret')),
            'not confirmed' => array(array('accept' => 'confirmed'), false),
            'date' => array(array('today_date' => 'date')),
            'date format' => array(array('today_date' => 'date:Y-m-d')),
            'not date' => array(array('text' => 'date'), false),
            'date_equals' => array(array('today_date' => 'date_equals:today')),
            'not date_equals' => array(array('today_date' => 'date_equals:yesterday_date'), false),
            'different' => array(array('accept' => 'different:reject')),
            'not different' => array(array('accept' => 'different:agreed'), false),
            'digits' => array(array('number' => 'digits')),
            'digits size' => array(array('number' => 'digits:2')),
            'digits between' => array(array('number' => 'digits:1,3')),
            'not digits' => array(array('accept' => 'digits'), false),
            'distinct' => array(array('tags.*.name' => 'distinct')),
            'not distinct' => array(array('member.*.name' => 'distinct'), false),
            'email' => array(array('email' => 'email')),
            'not email' => array(array('homepage' => 'email'), false),
            'ends_with' => array(array('email' => 'ends_with:com,id')),
            'not ends_with' => array(array('accept' => 'ends_with:com'), false),
            'exclude_if' => array(array('slug' => 'exclude_if:accept,on')),
            'exclude_unless' => array(array('slug' => 'exclude_unless:accept,off')),
            'filled' => array(array('accept' => 'filled')),
            'not filled' => array(array('text_none' => 'filled'), false),
        );
    }
}
