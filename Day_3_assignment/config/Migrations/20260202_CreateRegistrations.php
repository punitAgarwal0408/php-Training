<?php
use Migrations\AbstractMigration;

class CreateRegistrations extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('registrations');
        $table->addColumn('training_session_id', 'integer')
              ->addColumn('user_id', 'integer')
              ->addColumn('registered_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('created', 'datetime', ['null' => true])
              ->addColumn('modified', 'datetime', ['null' => true])
              ->addIndex(['training_session_id'])
              ->addIndex(['user_id'])
              ->addForeignKey('training_session_id', 'training_sessions', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->create();
    }
}
