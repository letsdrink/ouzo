<?php
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\Notice;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Session;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Utilities\Arrays;

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
        $this->notice(array('Keep this'), true);
    }

    public function keep_set_url()
    {
        $this->notice(array('Keep this'), false, '/simple_test/read_kept');
    }

    public function do_not_keep()
    {
        $this->notice(array('Keep this'), false);
    }

    public function read_kept()
    {
        $this->layout->renderAjax(Arrays::firstOrNull(Session::get('messages') ?: array()));
        $this->layout->unsetLayout();
    }

    public function add_notice_for_full_url()
    {
        $this->notice(array('notice'), false, 'prefix/simple_test/read_kept');
    }

    public function add_notice_for_short_url()
    {
        $this->notice(array('notice'), false, '/simple_test/read_kept');
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
}

class ControllerTest extends ControllerTestCase
{
    public function __construct()
    {
        Config::overrideProperty('namespace', 'controller')->with('\\');
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        Route::$routes = array();
    }

    public function tearDown()
    {
        parent::tearDown();
        Config::clearProperty('namespace', 'controller');
    }

    /**
     * @test
     */
    public function shouldReturnClassNameInUnderscoreAsDefaultTab()
    {
        //when
        $tab = SimpleTestController::getTab();

        //then
        $this->assertEquals('simple_test', $tab);
    }

    /**
     * @test
     * @covers \Ouzo\DownloadHandler
     */
    public function shouldDownloadFile()
    {
        //given
        Route::get('/simple_test/download', 'simple_test#download');

        //when
        $this->get('/simple_test/download');

        //then
        $this->assertDownloadsFile('file.txt');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfMethodDoesNotExist()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        CatchException::when($this)->get('/simple_test/invalid');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\NoControllerActionException');
    }

    /**
     * @test
     */
    public function shouldParseQueryString()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params?p1=v1&p2=v2');

        //then
        $this->assertEquals(array('p1' => 'v1', 'p2' => 'v2'), $this->getAssigned('params'));
    }

    /**
     * @test
     */
    public function shouldParseQueryStringWithNestedParams()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params?p1=v1&id[]=1&id[]=2&id[]=3');

        //then
        $actual = $this->getAssigned('params');
        Assert::thatArray($actual)
            ->hasSize(2)
            ->containsKeyAndValue(array('p1' => 'v1'));
        Assert::thatArray($actual['id'])->hasSize(3)->containsExactly(1, 2, 3);
    }

    /**
     * @test
     */
    public function shouldParseQueryStringIfParamHasNoValue()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params?p1');

        //then
        $this->assertEquals(array('p1' => null), $this->getAssigned('params'));
    }

    /**
     * @test
     */
    public function shouldSetEmptyParamsIfNoParameters()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params');

        //then
        $this->assertEmpty($this->getAssigned('params'));
    }

    /**
     * @test
     */
    public function shouldKeepNoticeToNextRequest()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/keep');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo('Keep this');
    }

    /**
     * @test
     */
    public function shouldNotKeepNoticeToNextRequest()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/do_not_keep');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo(null);
    }

    /**
     * @test
     */
    public function shouldKeepNoticeToFirstUrlVisit()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/keep_set_url');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo('Keep this');
    }

    /**
     * @test
     */
    public function shouldRemoveNoticeOnFirstUrlVisit()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/keep_set_url');

        //when
        $this->get('/simple_test/read_kept');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo(null);
    }

    /**
     * @test
     */
    public function shouldCheckIsHeaderIsCorrectly()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/check_http_header');

        //then
        $this->assertResponseHeader('HTTP/1.1 200 OK');
    }

    /**
     * @test
     */
    public function shouldRemoveNoticeIfShortUrlMatches()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/add_notice_for_short_url');

        //when
        $this->get('/simple_test/read_kept');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isNull();
    }

    /**
     * @test
     */
    public function shouldRemoveNoticeIfFullUrlMatches()
    {
        //given
        Config::overrideProperty('global', 'prefix_system')->with('prefix');

        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/add_notice_for_full_url');

        //when
        $this->get('/simple_test/read_kept');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isNull();

        Config::revertProperty('global', 'prefix_system');
    }

    /**
     * @test
     */
    public function shouldNotRemoveNoticeIfUrlDoesNotMatch()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/add_notice_for_short_url');

        //when
        $this->get('/simple_test/other_action');
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRenderedContent()->isEqualTo('notice');
    }

    /**
     * @test
     */
    public function shouldNotStoreEmptyUrlForNotices()
    {
        //given
        Config::overridePropertyArray(array('global', 'prefix_system'), 'prefix');
        $_SESSION = array();
        $controller = new Controller(new RouteRule('', '', '', false));

        //when
        $controller->notice('hello');

        //then
        Assert::thatArray(Session::get('messages'))->containsOnly(new Notice('hello', null));

        Config::revertPropertyArray(array('global', 'prefix_system'));
    }

    /**
     * @test
     */
    public function shouldRenderAjaxViewWithoutViewName()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        CatchException::when($this)->get('/simple_test/empty_view_name');

        //then
        CatchException::assertThat()->hasMessage('No view found [SimpleTest/empty_view_name]');
    }
}
