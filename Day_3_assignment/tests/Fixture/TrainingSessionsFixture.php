<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TrainingSessionsFixture extends TestFixture
{
    public $import = ['table' => 'training_sessions'];

    public function init(): void
    {
        $this->records = [
            ['id' => 1, 'title' => 'Session A', 'description' => 'Alpha', 'start_date' => '2026-03-01 09:00:00', 'max_participants' => 2, 'user_id' => 1, 'created' => '2026-01-01 10:00:00', 'modified' => '2026-01-01 10:00:00'],
            ['id' => 2, 'title' => 'Session B', 'description' => 'Beta', 'start_date' => '2026-04-01 09:00:00', 'max_participants' => null, 'user_id' => 2, 'created' => '2026-01-01 10:00:00', 'modified' => '2026-01-01 10:00:00'],
        ];
        parent::init();
    }
}
