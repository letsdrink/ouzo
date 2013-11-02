<?php

namespace Ouzo\Db;

class QueryHumanizerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldHumanizeSqlQuery()
    {
        //given
        $sql = "SELECT t_customers.name AS t_customers_name, t_customers.surname AS t_customers_surname, t_customers.identifier AS t_customers_identifier, t_customer_phones.number AS t_customer_phones_number FROM t_customers AS t_customers LEFT JOIN t_customer_phones AS t_customer_phones ON t_customer_phones.id_customer = t_customers.id_customer WHERE hidden = false AND (t_customer_phones.primary_flag is true or t_customer_phones.id_customer_phone is null) ORDER BY surname ASC LIMIT ?";

        //when
        $humanized = QueryHumanizer::humanize($sql);

        //then
        $this->assertEquals("SELECT t_customers.*, t_customer_phones.* FROM t_customers AS t_customers LEFT JOIN t_customer_phones AS t_customer_phones ON t_customer_phones.id_customer = t_customers.id_customer WHERE hidden = false AND (t_customer_phones.primary_flag is true or t_customer_phones.id_customer_phone is null) ORDER BY surname ASC LIMIT ?", $humanized);
    }

    /**
     * @test
     */
    public function shouldHumanizeSqlQueryForOneTable()
    {
        //given
        $sql = "SELECT t_customers.name AS t_customers_name, t_customers.surname AS t_customers_surname, t_customers.identifier AS t_customers_identifier FROM t_customers AS t_customers WHERE hidden = false ORDER BY surname ASC LIMIT ?";

        //when
        $humanized = QueryHumanizer::humanize($sql);

        //then
        $this->assertEquals("SELECT t_customers.* FROM t_customers AS t_customers WHERE hidden = false ORDER BY surname ASC LIMIT ?", $humanized);
    }

    /**
     * @test
     */
    public function shouldNotHumanizeAllAliases()
    {
        //given
        $sql = "SELECT acl.id_group_acl AS id_menu, ts.id_submenu, acl.group_acl AS menu_name, ts.nazwa AS submenu_name, link, new_link, id, acl.group_acl AS acl_group_acl, acl.id_group_acl AS acl_id_group_acl FROM t_submenu AS ts LEFT JOIN t_acl_group AS acl ON acl.id_group_acl = ts.id_menu ORDER BY acl.kolejnosc, ts.kolejnosc, ts.nazwa";

        //when
        $humanized = QueryHumanizer::humanize($sql);

        //then
        $this->assertEquals("SELECT acl.id_group_acl AS id_menu, ts.id_submenu, acl.group_acl AS menu_name, ts.nazwa AS submenu_name, link, new_link, id, acl.* FROM t_submenu AS ts LEFT JOIN t_acl_group AS acl ON acl.id_group_acl = ts.id_menu ORDER BY acl.kolejnosc, ts.kolejnosc, ts.nazwa",
            $humanized);
    }
}
