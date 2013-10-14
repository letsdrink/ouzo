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

}
