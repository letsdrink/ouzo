<?php
namespace Ouzo\Db\Dialect;

interface Dialect
{
    public function select();

    public function from();

    public function join();

    public function where();

    public function order();

    public function limit();

    public function offset();
}