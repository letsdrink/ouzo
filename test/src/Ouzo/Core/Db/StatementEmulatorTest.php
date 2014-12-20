<?php
use Ouzo\Db;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Options;
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Tests\Mock\Mock;

class SimpleModel extends Model
{
    public function __construct()
    {
        parent::__construct(array('fields' => array('name')));
    }
}

class StatementEmulatorTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldSubstituteParams()
    {
        //given
        $pdoStatement = Mock::mock();
        $pdo = Mock::mock();
        $db = Mock::mock('Ouzo\Db');
        $db->_dbHandle = $pdo;

        Mock::when($pdo)->query(Mock::anyArgList())->thenReturn($pdoStatement);
        Mock::when($pdo)->quote("bob")->thenReturn("'bob'");
        Mock::when($pdoStatement)->fetchAll(Mock::anyArgList())->thenReturn(array());

        $modelQueryBuilder = new ModelQueryBuilder(SimpleModel::metaInstance(), $db);

        //when
        $modelQueryBuilder->where(array('name' => 'bob'))
            ->options(array(Options::EMULATE_PREPARES => true))
            ->fetchAll();

        //then
        Mock::verify($pdo)->query("SELECT simple_models.name AS _simple_models_name, simple_models.id AS _simple_models_id FROM simple_models WHERE name = 'bob'");
    }
}
