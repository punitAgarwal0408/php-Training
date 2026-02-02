<?php
use Migrations\AbstractMigration;

class AlterTrainingSessionsMaxParticipants extends AbstractMigration
{
    public function change()
    {
        if ($this->hasTable('training_sessions')) {
            $table = $this->table('training_sessions');
            $table->changeColumn('max_participants', 'integer', ['null' => true, 'default' => null])
                  ->update();
        }
    }
}
