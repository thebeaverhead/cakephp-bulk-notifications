<?php

use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
  /**
   * Change Method.
   *
   * More information on this method is available here:
   * http://docs.phinx.org/en/latest/migrations.html#the-change-method
   * @return void
   */
  public function change()
  {
    $table = $this->table('bulk_notifications');

    $table->addColumn('created', 'integer', [
      'default' => null,
      'null' => false,
    ]);
    $table->addColumn('modified', 'integer', [
      'default' => null,
      'null' => true,
    ]);
    $table->addColumn('available', 'boolean', [
      'default' => true,
      'null' => false,
    ]);
    $table->addColumn('receiver_id', 'string', [
      'default' => null,
      'limit' => 36,
      'null' => false
    ]);
    $table->addColumn('receiver_model', 'string', [
      'default' => null,
      'limit' => 64,
      'null' => false
    ]);
    $table->addColumn('receiver_email_column', 'string', [
      'default' => 'email',
      'limit' => 64,
      'null' => false
    ]);
    $table->addColumn('send_date', 'integer', [
      'default' => null,
      'null' => true
    ]);
    $table->addColumn('sent_date', 'integer', [
      'default' => null,
      'null' => true
    ]);
    $table->addColumn('template_path', 'text', [
      'default' => null,
      'null' => false
    ]);
    $table->addColumn('subject', 'text', [
      'default' => null,
      'null' => false
    ]);
    $table->addColumn('data', 'text', [
      'default' => null,
      'null' => true
    ]);
    $table->addColumn('increment_data', 'text', [
      'default' => null,
      'null' => true
    ]);
    $table->addColumn('is_increment', 'boolean', [
      'default' => false,
      'null' => false,
    ]);
    $table->addColumn('is_sent', 'boolean', [
      'default' => false,
      'null' => false,
    ]);

    $table->create();
  }
}
