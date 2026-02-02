<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PHPUnit\Framework\TestCase;

class RegistrationsTableTest extends TestCase
{
    protected $Registrations;
    protected $Users;
    protected $TrainingSessions;

    public function setUp(): void
    {
        parent::setUp();
        // Make sure Cake is bootstrapped
        require_once __DIR__ . '/../../../bootstrap.php';

        $this->Registrations = TableRegistry::getTableLocator()->get('Registrations');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->TrainingSessions = TableRegistry::getTableLocator()->get('TrainingSessions');

        // Ensure all tables use the test connection
        $testConn = ConnectionManager::get('test');
        $this->Registrations->setConnection($testConn);
        $this->Users->setConnection($testConn);
        $this->TrainingSessions->setConnection($testConn);

        // Clean state in FK order
        $this->Registrations->deleteAll([]);
        $this->TrainingSessions->deleteAll([]);
        $this->Users->deleteAll([]);

        // Insert users first (so instructor_id FKs are valid)
        $this->Users->saveOrFail($this->Users->newEntity(['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com']));
        $this->Users->saveOrFail($this->Users->newEntity(['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com']));
        $this->Users->saveOrFail($this->Users->newEntity(['id' => 3, 'name' => 'Carol', 'email' => 'carol@example.com']));

        // Now insert training sessions
        $this->TrainingSessions->saveOrFail($this->TrainingSessions->newEntity([
            'id' => 1,
            'title' => 'Session A',
            'description' => null,
            'start_date' => '2099-03-01 09:00:00',
            'end_date' => '2099-03-01 17:00:00',
            'max_participants' => 2,
            'status' => null,
            'instructor_id' => 1,
        ]));
        $this->TrainingSessions->saveOrFail($this->TrainingSessions->newEntity([
            'id' => 2,
            'title' => 'Session B',
            'description' => null,
            'start_date' => '2099-04-01 09:00:00',
            'end_date' => '2099-04-01 17:00:00',
            'max_participants' => null,
            'status' => null,
            'instructor_id' => 2,
        ]));

        // Two existing registrations for session 1
        $this->Registrations->saveOrFail($this->Registrations->newEntity(['training_session_id' => 1, 'user_id' => 1]));
        $this->Registrations->saveOrFail($this->Registrations->newEntity(['training_session_id' => 1, 'user_id' => 2]));

        // After seeding, clear TableRegistry to ensure fresh objects
        TableRegistry::getTableLocator()->clear();
        $this->Registrations = TableRegistry::getTableLocator()->get('Registrations');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->TrainingSessions = TableRegistry::getTableLocator()->get('TrainingSessions');
    }

    public function tearDown(): void
    {
        // Clean up
        $this->Registrations->deleteAll([]);
        $this->TrainingSessions->deleteAll([]);
        $this->Users->deleteAll([]);

        unset($this->Registrations, $this->Users, $this->TrainingSessions);
        parent::tearDown();
    }

    public function testCannotRegisterWhenFull()
    {
        // Session 1 has max_participants = 2 and already has 2 registrations
        $data = ['training_session_id' => 1, 'user_id' => 3];
        $registration = $this->Registrations->newEntity($data);
        $result = $this->Registrations->save($registration);
        $this->assertFalse($result, 'Should not save when session is full');

        // Count should remain 2
        $count = $this->Registrations->find()->where(['training_session_id' => 1])->count();
        $this->assertEquals(2, $count);
    }

    public function testCanRegisterWhenNoLimit()
    {
        // Debug: print sessions and users
        $data = ['training_session_id' => 2, 'user_id' => 3];
        $registration = $this->Registrations->newEntity($data);
        $result = $this->Registrations->save($registration);
        $this->assertNotFalse($result);
    }
}
