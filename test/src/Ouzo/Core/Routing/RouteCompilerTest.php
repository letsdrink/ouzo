<?php

namespace Ouzo\Routing;


class RouteCompilerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Route::clear();
    }

    /**
     * @test
     */
    public function shouldGenerateTrie()
    {
        //given
        Route::resource('customers');
        Route::post('/customers/search', 'customers#search');
        $routeCompiler = new RouteCompiler();

        //when
        $trie = $routeCompiler->generateTrie(Route::getRoutes());

        //then
        $this->assertEquals(array(
            'GET' => array(
                'customers' => array(
                    '/' => 'customers#index',
                    'fresh' => array(
                        '/' => 'customers#fresh',
                    ),
                    ':id' => array(
                        'edit' => array(
                            '/' => 'customers#edit',
                        ),
                        '/' => 'customers#show',
                    ),
                ),
            ),
            'POST' => array(
                'customers' => array(
                    '/' => 'customers#create',
                    'search' => array(
                        '/' => 'customers#search'
                    )
                ),
            ),
            'PUT' => array(
                'customers' => array(
                    ':id' => array(
                        '/' => 'customers#update',
                    ),
                ),
            ),
            'PATCH' => array(
                'customers' => array(
                    ':id' => array(
                        '/' => 'customers#update',
                    ),
                ),
            ),
            'DELETE' => array(
                'customers' => array(
                    ':id' => array(
                        '/' => 'customers#destroy',
                    ),
                ),
            ),
        ), $trie);
    }

    /**
     * @test
     */
    public function shouldGenerateTrieForAllowAll()
    {
        //given
        Route::allowAll('/customers', 'customers');


        $routeCompiler = new RouteCompiler();

        //when
        $trie = $routeCompiler->generateTrie(Route::getRoutes());

        //then
        $this->assertEquals(array(
            'GET' => array(
                'customers' => array(
                    '*' => 'customers'
                )
            ),
            'POST' => array(
                'customers' => array(
                    '*' => 'customers'
                )
            ),
            'PUT' => array(
                'customers' => array(
                    '*' => 'customers'
                )
            ),
            'PATCH' => array(
                'customers' => array(
                    '*' => 'customers'
                )
            ),
            'DELETE' => array(
                'customers' => array(
                    '*' => 'customers'
                )
            ),
            'OPTIONS' => array(
                'customers' => array(
                    '*' => 'customers'
                )
            )
        ), $trie);
    }
}
