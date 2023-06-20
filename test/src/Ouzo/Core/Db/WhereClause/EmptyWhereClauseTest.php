<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class EmptyWhereClauseTest extends DbTransactionalTestCase
{
    #[Test]
    public function shouldTreatEmptyWhereClauseAsNothingWasGivenAsParameter()
    {
        // given
        Product::create(['name' => 'one']);
        Product::create(['name' => 'two']);

        // when
        $products = Product::where(new EmptyWhereClause())->fetchAll();

        // then
        Assert::thatArray($products)->hasSize(2);
    }
}
