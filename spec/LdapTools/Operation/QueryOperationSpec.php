<?php
/**
 * This file is part of the LdapTools package.
 *
 * (c) Chad Sikorra <Chad.Sikorra@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\LdapTools\Operation;

use LdapTools\Operation\QueryOperation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueryOperationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('LdapTools\Operation\QueryOperation');
    }

    function it_should_implement_LdapOperationInterface()
    {
        $this->shouldImplement('\LdapTools\Operation\LdapOperationInterface');
    }

    function it_should_set_the_base_dn_for_the_query_operation()
    {
        $dn = 'dc=example,dc=local';
        $this->setBaseDn($dn);
        $this->getBaseDn()->shouldBeEqualTo($dn);
    }

    function it_should_set_the_filter_for_the_query_operation()
    {
        $filter = 'foo';
        $this->setFilter($filter);
        $this->getFilter()->shouldBeEqualTo($filter);
    }

    function it_should_set_the_page_size_for_the_query_operation()
    {
        $pageSize = 1000;
        $this->setPageSize($pageSize);
        $this->getPageSize()->shouldBeEqualTo($pageSize);
    }

    function it_should_set_the_scope_for_the_query_operation()
    {
        $scope = QueryOperation::SCOPE['BASE'];
        $this->setScope($scope);
        $this->getScope()->shouldBeEqualTo($scope);
    }

    function it_should_set_the_attributes_to_return_for_the_query_operation()
    {
        $attributes = ['foo'];
        $this->setAttributes($attributes);
        $this->getAttributes()->shouldBeEqualTo($attributes);
    }

    function it_should_chain_the_setters()
    {
        $this->setBaseDn('foo')->shouldReturnAnInstanceOf('\LdapTools\Operation\QueryOperation');
        $this->setFilter('foo')->shouldReturnAnInstanceOf('\LdapTools\Operation\QueryOperation');
        $this->setPageSize('9001')->shouldReturnAnInstanceOf('\LdapTools\Operation\QueryOperation');
        $this->setScope(QueryOperation::SCOPE['SUBTREE'])->shouldReturnAnInstanceOf('\LdapTools\Operation\QueryOperation');
        $this->setAttributes(['foo'])->shouldReturnAnInstanceOf('\LdapTools\Operation\QueryOperation');
    }

    function it_should_get_the_name_of_the_operation()
    {
        $this->getName()->shouldBeEqualTo('Query');
    }

    function it_should_get_the_correct_ldap_function_for_the_given_scope()
    {
        $this->setScope(QueryOperation::SCOPE['SUBTREE'])->getLdapFunction()->shouldBeEqualTo('ldap_search');
        $this->setScope(QueryOperation::SCOPE['ONELEVEL'])->getLdapFunction()->shouldBeEqualTo('ldap_list');
        $this->setScope(QueryOperation::SCOPE['BASE'])->getLdapFunction()->shouldBeEqualTo('ldap_read');
    }

    function it_should_throw_a_query_exception_when_an_invalid_scope_is_used()
    {
        $this->shouldThrow('\LdapTools\Exception\LdapQueryException')->duringSetScope('foo');
    }

    function it_should_return_the_arguments_for_the_ldap_function_in_the_correct_order()
    {
        $args = [
            'dc=foo,dc=bar',
            '(foo=bar)',
            ['foo'],
        ];
        $this->setBaseDn($args[0]);
        $this->setFilter($args[1]);
        $this->setAttributes($args[2]);
        $this->getArguments()->shouldBeEqualTo($args);
    }

    function it_should_get_a_log_formatted_array()
    {
        $this->getLogArray()->shouldBeArray();
        $this->getLogArray()->shouldHaveKey('Base DN');
        $this->getLogArray()->shouldHaveKey('Scope');
        $this->getLogArray()->shouldHaveKey('Page Size');
        $this->getLogArray()->shouldHaveKey('Filter');
        $this->getLogArray()->shouldHaveKey('Attributes');
    }
}
