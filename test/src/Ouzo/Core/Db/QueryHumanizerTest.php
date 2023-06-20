<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\QueryHumanizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class QueryHumanizerTest extends TestCase
{
    #[Test]
    public function shouldHumanizeSqlQuery()
    {
        //given
        $sql = "SELECT t_customers.name, t_customers.surname, t_customers.identifier, t_customer_phones.number FROM t_customers AS t_customers LEFT JOIN t_customer_phones AS t_customer_phones ON t_customer_phones.id_customer = t_customers.id_customer WHERE hidden = false AND (t_customer_phones.primary_flag is true or t_customer_phones.id_customer_phone is null) ORDER BY surname ASC LIMIT ? /* " . ModelQueryBuilder::MODEL_QUERY_MARKER_COMMENT . ' */';

        //when
        $humanized = QueryHumanizer::humanize($sql);

        //then
        $this->assertEquals("SELECT t_customers.*, t_customer_phones.* FROM t_customers AS t_customers LEFT JOIN t_customer_phones AS t_customer_phones ON t_customer_phones.id_customer = t_customers.id_customer WHERE hidden = false AND (t_customer_phones.primary_flag is true or t_customer_phones.id_customer_phone is null) ORDER BY surname ASC LIMIT ?", $humanized);
    }

    #[Test]
    public function shouldHumanizeSqlQueryForOneTable()
    {
        //given
        $sql = "SELECT t_customers.name, t_customers.surname, t_customers.identifier FROM t_customers AS t_customers WHERE hidden = false ORDER BY surname ASC LIMIT ? /* " . ModelQueryBuilder::MODEL_QUERY_MARKER_COMMENT . ' */';

        //when
        $humanized = QueryHumanizer::humanize($sql);

        //then
        $this->assertEquals("SELECT t_customers.* FROM t_customers AS t_customers WHERE hidden = false ORDER BY surname ASC LIMIT ?", $humanized);
    }

    #[Test]
    public function shouldNotHumanizeAllAliases()
    {
        //given
        $sql = "SELECT acl.id_group_acl AS id_menu, ts.id_submenu, acl.group_acl AS menu_name, ts.nazwa AS submenu_name, link, new_link, id, acl.group_acl AS acl_group_acl, acl.id_group_acl AS acl_id_group_acl FROM t_submenu AS ts LEFT JOIN t_acl_group AS acl ON acl.id_group_acl = ts.id_menu ORDER BY acl.kolejnosc, ts.kolejnosc, ts.nazwa";

        //when
        $humanized = QueryHumanizer::humanize($sql);

        //then
        $this->assertEquals("SELECT acl.id_group_acl AS id_menu, ts.id_submenu, acl.group_acl AS menu_name, ts.nazwa AS submenu_name, link, new_link, id, acl.group_acl AS acl_group_acl, acl.id_group_acl AS acl_id_group_acl FROM t_submenu AS ts LEFT JOIN t_acl_group AS acl ON acl.id_group_acl = ts.id_menu ORDER BY acl.kolejnosc, ts.kolejnosc, ts.nazwa",
            $humanized);
    }
}
