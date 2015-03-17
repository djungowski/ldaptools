<?php
/**
 * This file is part of the LdapTools package.
 *
 * (c) Chad Sikorra <Chad.Sikorra@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\LdapTools\Query;

use LdapTools\Connection\LdapConnection;
use LdapTools\Factory\HydratorFactory;
use LdapTools\Query\LdapQuery;
use LdapTools\Schema\LdapObjectSchema;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LdapQuerySpec extends ObjectBehavior
{
    protected $ldapEntries = [
        "count" => 2,
        0 => [
            "givenname" => [
                "count" => 1,
                0 => "Jon",
            ],
            0 => "givenname",
            "cn" => [
                "count" => 1,
                0 => "Jon Bourke",
            ],
            1 => "cn",
            "sn" => [
                "count" => 1,
                0 => "Bourke",
            ],
            2 => "sn",
            "count" => 3,
            "dn" => "uid=jbourke,ou=People,dc=example,dc=local",
        ],
        1 => [
            "givenname" => [
                "count" => 1,
                0 => "Jon",
            ],
            0 => "givenname",
            "cn" => [
                "count" => 1,
                0 => "Jon Goldstein",
            ],
            1 => "cn",
            "sn" => [
                "count" => 1,
                0 => "Goldstein",
            ],
            2 => "sn",
            "count" => 3,
            "dn" => "uid=jgoldste,ou=People,dc=example,dc=local",
        ],
    ];

    function let(LdapConnection $ldap)
    {
        $ldap->search(Argument::any(), ["cn", "givenName", "foo"], Argument::any(), Argument::any(), Argument::any())
            ->willReturn($this->ldapEntries);
        $this->beConstructedWith($ldap);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LdapTools\Query\LdapQuery');
    }

    function it_should_return_a_LdapObjectCollection_by_default()
    {
        $this->setAttributes(["cn", "givenName", "foo"]);
        $this->execute()->shouldReturnAnInstanceOf('\LdapTools\Object\LdapObjectCollection');
    }

    function it_should_set_the_filter_when_calling_setLdapFilter()
    {
        $filter = '(objectClass=*)';
        $this->setLdapFilter($filter)->getLdapFilter()->shouldBeEqualTo($filter);
    }

    function it_should_set_the_baseDn_when_calling_setBaseDn()
    {
        $baseDn = 'dc=foo,dc=bar';
        $this->setBaseDn($baseDn)->getBaseDn()->shouldBeEqualTo($baseDn);
    }

    function it_should_set_the_page_size_when_calling_setPageSize()
    {
        $pageSize = 1000;
        $this->setPageSize($pageSize)->getPageSize()->shouldBeEqualTo($pageSize);
    }

    function it_should_set_the_attributes_to_get_when_calling_setAttributes()
    {
        $attributes = ['foo', 'bar'];
        $this->setAttributes($attributes)->getAttributes()->shouldBeEqualTo($attributes);
    }

    function it_should_set_the_scope_when_calling_setScope()
    {
        $this->setScope(LdapQuery::SCOPE_BASE)->getScope()->shouldBeEqualTo(LdapQuery::SCOPE_BASE);
    }

    function it_should_throw_InvalidArgumentException_when_setting_an_invalid_scope()
    {
        $this->shouldThrow('\InvalidArgumentException')->duringSetScope('foo');
    }

    function it_should_set_the_LdapObjectSchemas_when_calling_setLdapObjectSchemas()
    {
        $schema = new LdapObjectSchema('foo','bar');

        $this->setLdapObjectSchemas($schema)->getLdapObjectSchemas()->shouldBeEqualTo([$schema]);
    }

    function it_should_set_the_order_by_attributes()
    {
        $this->setOrderBy(['foo' => 'ASC'])->getOrderBy()->shouldBeEqualTo(['foo' => 'ASC']);
    }

    function it_should_have_an_empty_array_for_the_default_order_by()
    {
        $this->getOrderBy()->shouldBeEqualTo([]);
    }

    function it_should_add_order_by_attributes_to_the_selection_if_not_explicitly_done()
    {
        $this->setOrderBy(['foo' => 'ASC']);
        $this->setAttributes(['cn', 'givenName']);
        $this->setBaseDn('dc=foo,dc=bar');
        $this->execute(HydratorFactory::TO_ARRAY);
    }
}
