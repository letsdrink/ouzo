<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\ContentType;
use Ouzo\Controller;
use Ouzo\Db\Stats;
use Ouzo\NoControllerActionException;
use Ouzo\Notice;
use Ouzo\Request\RequestParameters;
use Ouzo\Request\RoutingService;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Session;
use Ouzo\Stats\SessionStats;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\StreamStub;
use Ouzo\Uri;
use Ouzo\Uri\PathProvider;
use Ouzo\Utilities\Arrays;
use Ouzo\View\ViewException;
use PHPUnit\Framework\Attributes\Test;

class SimpleTestController extends Controller
{
    public function download()
    {
        $this->downloadFile('file.txt', 'text/plain', '/tmp/file.txt');
    }

    public function params()
    {
        $this->view->params = $this->params;
    }

    public function keep()
    {
        $this->notice(['Keep this'], true);
    }

    public function keep_set_url()
    {
        $this->notice(['Keep this'], false, '/simple_test/read_kept');
    }

    public function do_not_keep()
    {
        $this->notice(['Keep this'], false);
    }

    public function read_kept()
    {
        $this->layout->renderAjax(Arrays::firstOrNull(Session::get('messages') ?: []));
        $this->layout->unsetLayout();
    }

    public function add_notice_for_full_url()
    {
        $this->notice(['notice'], false, 'prefix/simple_test/read_kept');
    }

    public function add_notice_for_short_url()
    {
        $this->notice(['notice'], false, '/simple_test/read_kept');
    }

    public function other_action()
    {
        $this->layout->renderAjax("other_action");
        $this->layout->unsetLayout();
    }

    public function empty_view_name()
    {
        $this->renderAjaxView();
    }

    public function check_http_header()
    {
        $this->header('HTTP/1.1 200 OK');
    }

    public function notice_with_query()
    {
        $this->notice(['notice'], false, '/simple_test/notice_with_query?data=some-data');
    }

    public function string_output()
    {
        $this->layout->renderAjax('ONLY OUTPUT');
        $this->layout->unsetLayout();
    }

    public function receive_params($user, $page)
    {
        $this->layout->renderAjax("Param1: $user Param2: $page");
        $this->layout->unsetLayout();
    }

    public function tracing()
    {
        Stats::trace('select * form table', [], function () {
            usleep(800);
        });
    }
}

class ControllerTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        StreamStub::register('json');
        SimpleTestController::$stream = 'json://input';
        Route::clear();
        Config::overrideProperty('debug')->with(true);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        StreamStub::unregister();
        SimpleTestController::$stream = 'php://input';
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldReturnClassNameInUnderscoreAsDefaultTab()
    {
        // given
        $controller = new SimpleTestController();

        //when
        $tab = $controller->getTab();

        //then
        $this->assertEquals('simple_test', $tab);
    }

    #[Test]
    public function shouldReturnRequestHeaders()
    {
        //given
        $_SERVER["HTTP_HOST"] = "localhost";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0";
        $_SERVER["HTTP_ACCEPT"] = "text/html";
        $_SERVER["HTTP_ACCEPT_LANGUAGE"] = "en-US,en;q=0.5";
        $_SERVER["HTTP_ACCEPT_ENCODING"] = "gzip, deflate";
        $_SERVER["HTTP_REFERER"] = "http://localhost/index";
        $_SERVER["HTTP_COOKIE"] = "PHPSESSID=6j8kkq2r62n32rtf4tmlnbspn1";
        $_SERVER["HTTP_CONNECTION"] = "keep-alive";

        $controller = new SimpleTestController();

        //when
        $requestHeaders = $controller->getRequestHeaders();

        //then
        $this->assertEquals([
            'Host' => 'localhost',
            'User-Agent' => 'Mozilla/5.0',
            'Accept' => 'text/html',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Encoding' => 'gzip, deflate',
            'Referer' => 'http://localhost/index',
            'Cookie' => 'PHPSESSID=6j8kkq2r62n32rtf4tmlnbspn1',
            'Connection' => 'keep-alive',
        ], $requestHeaders);
    }

    /**
     * @test
     * @covers \Ouzo\DownloadHandler
     */
    public function shouldDownloadFile()
    {
        //given
        Route::get('/simple_test/download', SimpleTestController::class, 'download');

        //when
        $this->get('/simple_test/download');

        //then
        $this->assertDownloadsFile('file.txt');
    }

    #[Test]
    public function shouldPassUrlParametersToControllerAction()
    {
        // given
        Route::get('/simple_test/receive_params/:user/:page', SimpleTestController::class, 'receive_params');

        // when
        $this->get('/simple_test/receive_params/Cersei/about-us');

        // then
        $this->assertRenderedContent()->isEqualTo('Param1: Cersei Param2: about-us');
    }

    #[Test]
    public function shouldPassOnlyUrlParametersNotPostOrGet()
    {
        // given
        Route::post('/simple_test2/receive_params/:user', SimpleTestController::class, 'receive_params');

        // when
        CatchException::when($this)->post('/simple_test2/receive_params/Cersei', [
            'page' => 'about-us'
        ]);

        // then
        $this->assertRenderedContent()->isEqualTo(null);
    }

    #[Test]
    public function shouldThrowExceptionIfMethodDoesNotExist()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        CatchException::when($this)->get('/simple_test/invalid');

        //then
        CatchException::assertThat()->isInstanceOf(NoControllerActionException::class);
    }

    #[Test]
    public function shouldParseQueryString()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/params?p1=v1&p2=v2');

        //then
        $this->assertEquals(['p1' => 'v1', 'p2' => 'v2'], $this->getAssigned('params'));
    }

    #[Test]
    public function shouldAppendParamsToUrlWithoutParams()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $data = [
            'p1' => 'v1',
            'p2' => 'v2'
        ];

        //when
        $this->get('/simple_test/params', $data);

        //then
        $this->assertEquals(['p1' => 'v1', 'p2' => 'v2'], $this->getAssigned('params'));
    }

    #[Test]
    public function shouldAppendParamsToUrlWithParams()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $data = [
            'p2' => 'v2',
            'p3' => 'v3'
        ];

        //when
        $this->get('/simple_test/params?p1=v1', $data);

        //then
        $this->assertEquals(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $this->getAssigned('params'));
    }


    #[Test]
    public function shouldParseQueryStringWithNestedParams()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/params?p1=v1&id[]=1&id[]=2&id[]=3');

        //then
        $actual = $this->getAssigned('params');
        Assert::thatArray($actual)
            ->hasSize(2)
            ->containsKeyAndValue(['p1' => 'v1']);
        Assert::thatArray($actual['id'])->hasSize(3)->containsExactly(1, 2, 3);
    }

    #[Test]
    public function shouldParseQueryStringIfParamHasNoValue()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/params?p1');

        //then
        $this->assertEquals(['p1' => null], $this->getAssigned('params'));
    }

    #[Test]
    public function shouldSetEmptyParamsIfNoParameters()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/params');

        //then
        $this->assertEmpty($this->getAssigned('params'));
    }

    #[Test]
    public function shouldKeepNoticeToNextRequest()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/keep');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo('Keep this');
    }

    #[Test]
    public function shouldNotKeepNoticeToNextRequest()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/do_not_keep');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo(null);
    }

    #[Test]
    public function shouldKeepNoticeToFirstUrlVisit()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/keep_set_url');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo('Keep this');
    }

    #[Test]
    public function shouldRemoveNoticeOnFirstUrlVisit()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/keep_set_url');

        //when
        $this->get('/simple_test/read_kept');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo(null);
    }

    #[Test]
    public function shouldCheckIsHeaderIsCorrectly()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/check_http_header');

        //then
        $this->assertResponseHeader('HTTP/1.1 200 OK');
    }

    #[Test]
    public function shouldRemoveNoticeIfShortUrlMatches()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/add_notice_for_short_url');

        //when
        $this->get('/simple_test/read_kept');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isNull();
    }

    #[Test]
    public function shouldRemoveNoticeIfFullUrlMatches()
    {
        //given
        Config::overrideProperty('global', 'prefix_system')->with('prefix');

        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/add_notice_for_full_url');

        //when
        $this->get('/simple_test/read_kept');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isNull();

        Config::revertProperty('global', 'prefix_system');
    }

    #[Test]
    public function shouldNotRemoveNoticeIfUrlDoesNotMatch()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/add_notice_for_short_url');

        //when
        $this->get('/simple_test/other_action');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo('notice');
    }

    #[Test]
    public function shouldNotStoreEmptyUrlForNotices()
    {
        //given
        Config::overridePropertyArray(['global', 'prefix_system'], 'prefix');
        $_SESSION = [];

        $routingService = Mock::create(RoutingService::class);
        Mock::when($routingService)->getUri()->thenReturn(new Uri(new PathProvider()));

        $requestParameters = Mock::create(RequestParameters::class);
        Mock::when($requestParameters)->getRoutingService()->thenReturn($routingService);

        $sessionStats = Mock::create(SessionStats::class);

        $controller = Controller::createInstance(new RouteRule('', '', '', '', false), $requestParameters, $sessionStats);

        //when
        $controller->notice('hello');

        //then
        Assert::thatArray(Session::get('messages'))->containsOnly(new Notice('hello', null));

        Config::revertPropertyArray(['global', 'prefix_system']);
    }

    #[Test]
    public function shouldRenderAjaxViewWithoutViewName()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        CatchException::when($this)->get('/simple_test/empty_view_name');

        //then
        CatchException::assertThat()->isInstanceOf(ViewException::class);
    }

    #[Test]
    public function shouldRemoveNoticeIfUrlIsWithQueryPath()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);
        $this->get('/simple_test/notice_with_query?data=some-data');

        //when
        $this->get('/simple_test/other_action');

        //then
        $this->assertEmpty(Session::get('messages'));
    }

    #[Test]
    public function shouldGetStringOutput()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/string_output');

        //then
        $this->assertEquals('ONLY OUTPUT', $this->getActualContent());
    }

    #[Test]
    public function shouldReadParametersFromStream()
    {
        //given
        SimpleTestController::$stream = 'json://input';
        Route::allowAll('/simple_test', SimpleTestController::class);
        StreamStub::$body = '{"key":"value"}';
        ContentType::set('application/json');

        //when
        $this->post('/simple_test/params', []);

        //then
        $this->assertEquals(['key' => 'value'], $this->getAssigned('params'));
    }

    #[Test]
    public function shouldCountTimeAndNumberOfQueries()
    {
        //given
        Route::get('/simple_test/tracing', SimpleTestController::class, 'tracing');

        //when
        $this->get('/simple_test/tracing');
        $this->get('/simple_test/tracing');

        //then
        $this->assertCount(2, $_SESSION['stats_queries']['/simple_test/tracing#']['queries']);
    }

    #[Test]
    public function shouldTraceInfoAboutHttpRequest()
    {
        //given
        Route::get('/simple_test/tracing', SimpleTestController::class, 'tracing');

        //when
        $this->get('/simple_test/tracing', ['param1' => 'value1', 'param2' => 'value2']);

        //then
        Assert::thatArray($_SESSION['stats_queries']['/simple_test/tracing#']['request_params'][0])
            ->containsKeyAndValue(['param1' => 'value1', 'param2' => 'value2']);
    }

    #[Test]
    public function shouldGroupStatsByRequest()
    {
        //given
        Route::allowAll('/simple_test', SimpleTestController::class);

        //when
        $this->get('/simple_test/tracing', ['param1' => 'value1', 'param2' => 'value2']);
        $this->post('/simple_test/params', []);
        $this->get('/simple_test/tracing', ['param3' => 'value3', 'param4' => 'value4']);

        //then
        $queries = $_SESSION['stats_queries'];
        Assert::thatArray($queries)->hasSize(2);
        Assert::thatArray($queries['/simple_test/tracing#']['request_params'])->hasSize(2);
        Assert::thatArray($queries['/simple_test/params#']['request_params'])->hasSize(1);
    }
}
