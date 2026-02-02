<?php
use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('created', 'datetime', ['null' => true])
              ->addColumn('modified', 'datetime', ['null' => true])
              ->addIndex(['email'], ['unique' => true])
              ->create();
    }
}
