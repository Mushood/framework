<?php

namespace Illuminate\Tests\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseSQLiteQueryGrammarTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testToRawSql()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('escape')->with('foo', false)->andReturn("'foo'");
        $grammar = new SQLiteGrammar;
        $grammar->setConnection($connection);

        $query = $grammar->substituteBindingsIntoRawSql(
            'select * from "users" where \'Hello\'\'World?\' IS NOT NULL AND "email" = ?',
            ['foo'],
        );

        $this->assertSame('select * from "users" where \'Hello\'\'World?\' IS NOT NULL AND "email" = \'foo\'', $query);
    }

    public function testOrderByPriority()
    {
        $grammar = new SQLiteGrammar;
        $queryString = $grammar->orderByPriority('name', ['john', 'doe']);
        $this->assertSame('CASE WHEN name = ? THEN 0 WHEN name = ? THEN 1 ELSE 2 END asc', $queryString);
    }
}
