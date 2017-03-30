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
        $this->assertEquals([
            'GET' => [
                'customers' => [
                    '/' => [
                        'action' => 'customers#index',
                        'uri' => '/customers'
                    ],
                    'fresh' => [
                        '/' => [
                            'action' => 'customers#fresh',
                            'uri' => '/customers/fresh'
                        ]
                    ],
                    ':id' => [
                        'edit' => [
                            '/' => [
                                'action' => 'customers#edit',
                                'uri' => '/customers/:id/edit'
                            ]
                        ],
                        '/' => [
                            'action' => 'customers#show',
                            'uri' => '/customers/:id'
                        ]
                    ],
                ],
            ],
            'POST' => [
                'customers' => [
                    '/' => [
                        'action' => 'customers#create',
                        'uri' => '/customers'
                    ],
                    'search' => [
                        '/' => [
                            'action' => 'customers#search',
                            'uri' => '/customers/search'
                        ]
                    ]
                ],
            ],
            'PUT' => [
                'customers' => [
                    ':id' => [
                        '/' => [
                            'action' => 'customers#update',
                            'uri' => '/customers/:id'
                        ]
                    ],
                ],
            ],
            'PATCH' => [
                'customers' => [
                    ':id' => [
                        '/' => [
                            'action' => 'customers#update',
                            'uri' => '/customers/:id'
                        ]
                    ],
                ],
            ],
            'DELETE' => [
                'customers' => [
                    ':id' => [
                        '/' => [
                            'action' => 'customers#destroy',
                            'uri' => '/customers/:id'
                        ]
                    ],
                ],
            ],
        ], $trie);
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
        $this->assertEquals($trie, array(
            'GET' => array(
                'customers' => array(
                    '*' => ['action' => 'customers', 'uri' => '/customers']
                )
            ),
            'POST' => array(
                'customers' => array(
                    '*' => ['action' => 'customers', 'uri' => '/customers']
                )
            ),
            'PUT' => array(
                'customers' => array(
                    '*' => ['action' => 'customers', 'uri' => '/customers']
                )
            ),
            'PATCH' => array(
                'customers' => array(
                    '*' => ['action' => 'customers', 'uri' => '/customers']
                )
            ),
            'DELETE' => array(
                'customers' => array(
                    '*' => ['action' => 'customers', 'uri' => '/customers']
                )
            ),
            'OPTIONS' => array(
                'customers' => array(
                    '*' => [
                        'action' => 'customers', 'uri' => '/customers'
                    ]
                )
            )
        ));
    }
}
