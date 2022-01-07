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
            'eleven' => 11,
            'email' => 'email@mail.com',
            'foo' => 'foo',
            'homepage' => 'https://ekokurniawan.my.id/',
            'id_reg' => 'MEM123',
            'id_snake' => 'alpha_one',
            'id' => 'AlphaOne',
            'ip_google' => '172.217.194.113',
            'ip_private' => '192.168.1.10',
            'ip_reserved' => '127.0.0.1',
            'ipv6_google' => '2404:6800:4003:c04::71',
            'is_false' => 'false',
            'is_true' => '1',
            'json' => '{"foo":"bar","one":1,"is_true":true}',
            'nine' => 9,
            'number_str' => '10',
            'number' => 10,
            'password_confirmation' => 'secret',
            'password_secret' => 'secret',
            'password' => 'secret',
            'reject' => 'off',
            'role' => 'admin',
            'slug' => 'slug-the-whole-things',
            'ten' => 10,
            'text_none' => '',
            'text' => 'Pada zaman dahulu...',
            'today_date' => (new \DateTime())->format('Y-m-d'),
            'tomorrow_date' => (new \DateTime())->modify('+1 day')->format('Y-m-d'),
            'yesterday_date' => (new \DateTime())->modify('-1 day')->format('Y-m-d'),
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
        );
        $result = $this->validator->setThrowIfError(false)->validate($rules, $data);

        $this->assertEquals($expected, $result->success());
    }

    public function rulesProvider()
    {
        return array(
            'accepted' => array(array('accept' => 'accepted')),
            'accepted inverse' => array(array('homepage' => 'accepted'), false),
            'active_url' => array(array('homepage' => 'active_url')),
            'active_url inverse' => array(array('acc' => 'active_url'), false),
            'after' => array(array('today_date' => 'after:yesterday')),
            'after field' => array(array('today_date' => 'after:yesterday_date')),
            'after equals' => array(array('today_date' => 'after:today,null,true')),
            'after inverse' => array(array('today_date' => 'after:today'), false),
            'alpha' => array(array('id' => 'alpha')),
            'alpha multiple' => array(array('tags.*.name' => 'alpha')),
            'alpha inverse' => array(array('id_snake' => 'alpha'), false),
            'alpha_dash' => array(array('slug' => 'alpha_dash')),
            'alpha_dash inverse' => array(array('text' => 'alpha_dash'), false),
            'alpha_num' => array(array('id_reg' => 'alpha_num')),
            'alpha_num inverse' => array(array('date' => 'alpha_num'), false),
            'array' => array(array('user' => 'array')),
            'array keys' => array(array('user' => 'array:name,birthdate')),
            'array inverse' => array(array('accept' => 'array'), false),
            'before' => array(array('today_date' => 'before:tomorrow')),
            'before field' => array(array('today_date' => 'before:tomorrow_date')),
            'before equals' => array(array('today_date' => 'before:today,null,true')),
            'before inverse' => array(array('today_date' => 'before:today'), false),
            'between' => array(array('number' => 'between:10,10')),
            'between number' => array(array('number' => 'between:9,11')),
            'between inverse' => array(array('number' => 'between:11,12'), false),
            'boolean' => array(array('is_true' => 'boolean')),
            'boolean false' => array(array('is_false' => 'boolean')),
            'boolean unknown' => array(array('unknown' => 'boolean')),
            'boolean inverse' => array(array('number' => 'boolean'), false),
            'callback' => array(array('number' => array('callback' => fn(int $value) => $value === 10))),
            'callback inverse' => array(array('number' => array('callback' => fn(int $value) => $value > 10)), false),
            'confirmed' => array(array('password' => 'confirmed')),
            'confirmed another field' => array(array('password' => 'confirmed:password_secret')),
            'confirmed inverse' => array(array('accept' => 'confirmed'), false),
            'date' => array(array('today_date' => 'date')),
            'date format' => array(array('today_date' => 'date:Y-m-d')),
            'date inverse' => array(array('text' => 'date'), false),
            'date_equals' => array(array('today_date' => 'date_equals:today')),
            'date_equals inverse' => array(array('today_date' => 'date_equals:yesterday_date'), false),
            'different' => array(array('accept' => 'different:reject')),
            'different inverse' => array(array('accept' => 'different:agreed'), false),
            'digits' => array(array('number' => 'digits')),
            'digits size' => array(array('number' => 'digits:2')),
            'digits between' => array(array('number' => 'digits:1,3')),
            'digits inverse' => array(array('accept' => 'digits'), false),
            'distinct' => array(array('tags.*.name' => 'distinct')),
            'distinct inverse' => array(array('member.*.name' => 'distinct'), false),
            'email' => array(array('email' => 'email')),
            'email inverse' => array(array('homepage' => 'email'), false),
            'ends_with' => array(array('email' => 'ends_with:com,id')),
            'ends_with inverse' => array(array('accept' => 'ends_with:com'), false),
            'exclude_if' => array(array('slug' => 'exclude_if:accept,on')),
            'exclude_unless' => array(array('slug' => 'exclude_unless:accept,off')),
            'filled' => array(array('accept' => 'filled')),
            'filled inverse' => array(array('text_none' => 'filled'), false),
            'gt' => array(array('ten' => 'gt:nine')),
            'gt equals' => array(array('ten' => 'gt:ten,null,true')),
            'gt inverse' => array(array('ten' => 'gt:eleven'), false),
            'gt inverse same' => array(array('ten' => 'gt:ten'), false),
            'gt inverse different type' => array(array('ten' => 'gt:acc'), false),
            'in' => array(array('role' => 'in:user,admin,others')),
            'in array' => array(array('user.roles' => 'in:user,admin,others')),
            'in inverse' => array(array('accept' => 'in:user,admin,others'), false),
            'in_array' => array(array('foo' => 'in_array:tags.*.name')),
            'in_array array' => array(array('user.roles' => 'in_array:user.roles')),
            'in_array inverse' => array(array('accept' => 'in_array:tags.*.name'), false),
            'int' => array(array('ten' => 'int')),
            'int inverse' => array(array('accept' => 'int'), false),
            'ip' => array(array('ip_google' => 'ip')),
            'ip v6' => array(array('ipv6_google' => 'ip')),
            'ip v4 strict' => array(array('ip_google' => 'ip:4')),
            'ip v6 strict' => array(array('ipv6_google' => 'ip:6')),
            'ip reserved' => array(array('ip_reserved' => 'ip:null,res')),
            'ip private' => array(array('ip_private' => 'ip:4,priv')),
            'ip inverse' => array(array('accept' => 'ip'), false),
            'json' => array(array('json' => 'json')),
            'json with props' => array(array('json' => 'json:false,foo,one,is_true')),
            'json inverse' => array(array('accept' => 'json'), false),
            'lt' => array(array('ten' => 'lt:eleven')),
            'lt equals' => array(array('ten' => 'lt:ten,null,true')),
            'lt inverse' => array(array('ten' => 'lt:nine'), false),
            'lt inverse same' => array(array('ten' => 'lt:ten'), false),
            'lt inverse different type' => array(array('ten' => 'lt:acc'), false),
        );
    }
}
