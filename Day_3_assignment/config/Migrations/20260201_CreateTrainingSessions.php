<?php
use Migrations\AbstractMigration;

class CreateTrainingSessions extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('training_sessions');
        $table->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('instructor_id', 'integer')
              ->addColumn('start_date', 'datetime')
              ->addColumn('end_date', 'datetime')
              ->addColumn('max_participants', 'integer', ['default' => 20])
              ->addColumn('status', 'string', ['limit' => 20, 'null' => true])
              ->addColumn('created', 'datetime', ['null' => true])
              ->addColumn('modified', 'datetime', ['null' => true])
              ->addIndex(['instructor_id'])
              ->addForeignKey('instructor_id', 'users', 'id', ['delete'=> 'CASCADE', 'update' => 'NO_ACTION'])
              ->create();
    }
}
