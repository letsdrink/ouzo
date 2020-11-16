<?php

namespace Utilities;

use Ouzo\Config;
use Ouzo\Routing\Route;
use Ouzo\Uri\Es6UriHelperGenerator;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

use PHPUnit\Framework\TestCase;

class Es6UriHelperGeneratorTest extends TestCase
{
    private $path;

    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
        $this->path = Path::joinWithTemp(uniqid() . '_generatedUriHelper.js');
        Config::overrideProperty("global", "prefix_system")->with("/app");
    }

    protected function tearDown(): void
    {
        Config::revertProperty("global", "prefix_system");
        if (Files::exists($this->path)) {
            unlink($this->path);
        }
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForGet()
    {
        //given
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<EXPECTED
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const showItemUsersPath = () => {
    return "/app/users/show_item";
};\n
EXPECTED;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForPost()
    {
        //given
        Route::post('/users/save', 'Controller\\UsersController', 'save');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const saveUsersPath = () => {
    return "/app/users/save";
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForAny()
    {
        //given
        Route::any('/users/get_duplicated', 'Controller\\UsersController', 'get_duplicated');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const getDuplicatedUsersPath = () => {
    return "/app/users/get_duplicated";
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperWhenMultipleBindParameters()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'Controller\\UsersController', 'show');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const showUsersPath = (id, call_id) => {
    checkParameter(id);
    checkParameter(call_id);
    return "/app/users/show/id/" + id + "/call_id/" + call_id + "";
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForCustomRouteName()
    {
        //given
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item', ['as' => 'user_item']);

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const userItemPath = () => {
    return "/app/users/show_item";
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForResource()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const usersPath = () => {
    return "/app/users";
};

export const freshUserPath = () => {
    return "/app/users/fresh";
};

export const editUserPath = (id) => {
    checkParameter(id);
    return "/app/users/" + id + "/edit";
};

export const userPath = (id) => {
    checkParameter(id);
    return "/app/users/" + id + "";
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateCorrectNestedResources()
    {
        //given
        Route::get('/api/users/:id/orders', 'Controller\\Api\\UsersController', 'orders');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};

export const ordersUsersApiPath = (id) => {
    checkParameter(id);
    return "/app/api/users/" + id + "/orders";
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldSaveGeneratedUriHelperInFile()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $generator = Es6UriHelperGenerator::generate();

        //when
        $generator->saveToFile($this->path);

        //then
        $this->assertFileExists($this->path);
        $this->assertEquals($generator->getGeneratedFunctions(), file_get_contents($this->path));
    }

    /**
     * @test
     */
    public function shouldNotGenerateCorrectAllowAllResource()
    {
        //given
        Route::allowAll('/api', 'Controller\\Api\\UsersController');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameter = (parameter) => {
    if (typeof parameter !== 'string' && typeof parameter !== 'number') {
        throw new Error("Uri helper: Bad parameters");
    }
};\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }
}