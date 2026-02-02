<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RegistrationsFixture extends TestFixture
{
    public $import = ['table' => 'registrations'];

    public function init(): void
    {
        $this->records = [
            ['id' => 1, 'training_session_id' => 1, 'user_id' => 1, 'created' => '2026-01-02 10:00:00', 'modified' => '2026-01-02 10:00:00'],
            ['id' => 2, 'training_session_id' => 1, 'user_id' => 2, 'created' => '2026-01-02 11:00:00', 'modified' => '2026-01-02 11:00:00'],
        ];
        parent::init();
    }
}
