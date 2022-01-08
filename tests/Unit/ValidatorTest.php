<?php

namespace Ekok\Validation\Tests;

use Ekok\Validation\Validator;
use PHPUnit\Framework\TestCase;
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
            'foo' => new class extends Rule {
                protected function defineParameters()
                {
                    parent::defineParameters();

                    $this->setDefinitions(array(
                        'name' => array('type' => 'string', 'required' => true),
                        array('name' => 'tags', 'type' => 'string', 'variadic' => true),
                    ));
                }

                protected function doValidate($value)
                {
                    return (
                        $this->hasDefinitions()
                        && 2 == count($this->getDefinitions())
                        && array('name' => 'foo', 'tags' => array('bar', 'baz')) == $this->getParameters()
                        && $this->params['name'] == 'foo'
                        && in_array($value, $this->params['tags'])
                    );
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
                'foo' => 'alpha|foo:foo,bar,baz',
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

    public function testUnspecifiedParameter()
    {
        $this->expectExceptionMessage('Please specify parameter name at position 0');

        $this->validator->addRule(new class extends Rule {
            protected function defineParameters()
            {
                $this->addDefinition('name', null, true);
            }

            protected function doValidate($value)
            {
                return !!$value;
            }
        }, 'foo');
        $this->validator->validate(array('foo' => 'foo'), array());
    }

    public function testInvalidParameterType()
    {
        $this->expectExceptionMessage('Parameter name should be type of integer but NULL given');

        $this->validator->addRule(new class extends Rule {
            protected function defineParameters()
            {
                $this->addDefinition('name', 'integer');
            }

            protected function doValidate($value)
            {
                return !!$value;
            }
        }, 'foo');
        $this->validator->validate(array('foo' => 'foo'), array());
    }

    public function testInvalidVariadicParameterType()
    {
        $this->expectExceptionMessage('Parameter name should be type of string but integer given');

        $this->validator->addRule(new class extends Rule {
            protected function defineParameters()
            {
                $this->addDefinition('name', 'string', false, true);
            }

            protected function doValidate($value)
            {
                return !!$value;
            }
        }, 'foo');
        $this->validator->validate(array('foo' => 'foo:foo,bar,3,baz'), array());
    }

    public function testInvalidRule()
    {
        $this->expectExceptionMessage('Rule foo should be subclass of Ekok\\Validation\\Rule');

        $this->validator->addRule('foo');
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
            'bar' => 'bar',
            'baz' => 'baz',
            'eleven' => 11,
            'email' => 'email@mail.com',
            'foo' => 'foo',
            'homepage' => 'https://ekokurniawan.my.id/',
            'id_reg' => 'MEM123',
            'id_snake' => 'alpha_one',
            'id' => 'AlphaOne',
            'ip_google' => '172.217.194.113',
            'ip_private' => '192.168.1.1',
            'ip_reserved' => '127.0.0.1',
            'ipv6_google' => '2404:6800:4003:c04::71',
            'is_false' => 'false',
            'is_true' => '1',
            'json' => '{"foo":"bar","one":1,"is_true":true}',
            'my_timezone' => 'Asia/Jakarta',
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
            'uuidv1' => '7e9d9b86-6fdc-11ec-90d6-0242ac120003',
            'uuidv4' => '9de558db-b747-41e2-b5aa-9dcac9cb6eb5',
            'yesterday_date' => (new \DateTime())->modify('-1 day')->format('Y-m-d'),
            'member' => array(
                array('name' => 'foo'),
                array('name' => 'bar'),
                array('name' => 'foo'),
            ),
            'tags' => array(
                array('name' => 'foo'),
                array('name' => 'bar'),
            ),
            'user' => array(
                'name' => 'User',
                'birthdate' => '2022-01-01',
                'roles' => array('user', 'admin'),
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
            'ends_with inverse' => array(array('homepage' => 'ends_with:com'), false),
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
            'ip reserved' => array(array('ip_google' => 'ip:null,no_res')),
            'ip private' => array(array('ip_google' => 'ip:null,no_priv')),
            'ip inverse reserved' => array(array('ip_reserved' => 'ip:null,no_res'), false),
            'ip inverse private' => array(array('ip_private' => 'ip:4,no_priv'), false),
            'ip inverse' => array(array('accept' => 'ip'), false),
            'json' => array(array('json' => 'json')),
            'json with props' => array(array('json' => 'json:false,foo,one,is_true')),
            'json inverse' => array(array('accept' => 'json'), false),
            'lt' => array(array('ten' => 'lt:eleven')),
            'lt equals' => array(array('ten' => 'lt:ten,null,true')),
            'lt inverse' => array(array('ten' => 'lt:nine'), false),
            'lt inverse same' => array(array('ten' => 'lt:ten'), false),
            'lt inverse different type' => array(array('ten' => 'lt:acc'), false),
            'match' => array(array('password' => 'match:/^secret$/')),
            'match expect false' => array(array('password' => 'match:/^foo$/,false')),
            'match inverse' => array(array('password' => 'match:/^foo$/'), false),
            'max' => array(array('ten' => 'max:10')),
            'max inverse' => array(array('eleven' => 'max:10'), false),
            'min' => array(array('ten' => 'min:10')),
            'min inverse' => array(array('nine' => 'min:10'), false),
            'not_in' => array(array('role' => 'not_in:foo,bar')),
            'not_in array' => array(array('user.roles' => 'not_in:foo,bar')),
            'not_in inverse' => array(array('foo' => 'not_in:foo,bar'), false),
            'not_in_array' => array(array('baz' => 'not_in_array:tags.*.name')),
            'not_in_array array' => array(array('user.roles' => 'not_in_array:tags.*.name')),
            'not_in_array inverse' => array(array('foo' => 'not_in_array:tags.*.name'), false),
            'nullable' => array(array('accept' => 'nullable')),
            'nullable unknown' => array(array('unknown' => 'nullable')),
            'numeric' => array(array('number' => 'numeric')),
            'numeric string' => array(array('number_str' => 'numeric')),
            'numeric inverse' => array(array('accept' => 'numeric'), false),
            'present' => array(array('accept' => 'present')),
            'present unknown' => array(array('unknown' => 'present'), false),
            'prohibited' => array(array('unknown' => 'prohibited')),
            'prohibited inverse' => array(array('accept' => 'prohibited'), false),
            'prohibited_if' => array(array('unknown' => 'prohibited_if:reject,off')),
            'prohibited_if inverse' => array(array('accept' => 'prohibited_if:reject,off'), false),
            'prohibited_unless' => array(array('unknown' => 'prohibited_unless:reject,on')),
            'prohibited_unless inverse' => array(array('accept' => 'prohibited_unless:reject,off'), false),
            'required' => array(array('accept' => 'required')),
            'required number' => array(array('number' => 'required')),
            'required inverse' => array(array('text_none' => 'required'), false),
            'required_if' => array(array('accept' => 'required_if:reject,off')),
            'required_if number' => array(array('number' => 'required_if:reject,off')),
            'required_if inverse' => array(array('text_none' => 'required_if:reject,off'), false),
            'required_unless' => array(array('accept' => 'required_unless:reject,on')),
            'required_unless number' => array(array('number' => 'required_unless:reject,on')),
            'required_unless inverse' => array(array('text_none' => 'required_unless:reject,off'), false),
            'required_with' => array(array('accept' => 'required_with:reject')),
            'required_with inverse' => array(array('accept' => 'required_with:unknown'), false),
            'required_with_all' => array(array('accept' => 'required_with_all:reject,agreed')),
            'required_with_all inverse' => array(array('accept' => 'required_with_all:unknown'), false),
            'required_without' => array(array('accept' => 'required_without:unknown')),
            'required_without inverse' => array(array('accept' => 'required_without:reject'), false),
            'required_without_all' => array(array('accept' => 'required_without_all:unknown')),
            'required_without_all inverse' => array(array('accept' => 'required_without_all:reject'), false),
            'same' => array(array('accept' => 'same:agreed')),
            'same inverse' => array(array('accept' => 'same:reject'), false),
            'size' => array(array('accept' => 'size:2')),
            'size numbers' => array(array('number' => 'size:10')),
            'size inverse' => array(array('accept' => 'size:3'), false),
            'starts_with' => array(array('email' => 'starts_with:email,others')),
            'starts_with inverse' => array(array('homepage' => 'starts_with:email'), false),
            'string' => array(array('email' => 'string')),
            'string inverse' => array(array('number' => 'string'), false),
            'timezone' => array(array('my_timezone' => 'timezone')),
            'timezone group' => array(array('my_timezone' => 'timezone:asia')),
            'timezone inverse' => array(array('text' => 'timezone'), false),
            'trim' => array(array('accept' => 'trim')),
            'trim inverse' => array(array('number' => 'trim'), false),
            'url' => array(array('homepage' => 'url')),
            'url required' => array(array('homepage' => 'url:host,scheme,path')),
            'url inverse' => array(array('email' => 'url'), false),
            'uuid' => array(array('uuidv1' => 'uuid')),
            'uuid v4' => array(array('uuidv4' => 'uuid')),
            'uuid inverse' => array(array('text' => 'uuid'), false),
        );
    }
}
