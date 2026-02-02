<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public $import = ['table' => 'users'];

    public function init(): void
    {
        $this->records = [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com', 'created' => '2026-01-01 10:00:00', 'modified' => '2026-01-01 10:00:00'],
            ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com', 'created' => '2026-01-01 10:00:00', 'modified' => '2026-01-01 10:00:00'],
            ['id' => 3, 'name' => 'Carol', 'email' => 'carol@example.com', 'created' => '2026-01-01 10:00:00', 'modified' => '2026-01-01 10:00:00'],
        ];
        parent::init();
    }
}
