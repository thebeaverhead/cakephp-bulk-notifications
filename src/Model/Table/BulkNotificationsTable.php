<?php
namespace BulkNotifications\Model\Table;

use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use BulkNotifications\Model\Entity\BulkNotification;

/**
 * BulkNotifications Model
 *
 * @method BulkNotification get($primaryKey, $options = [])
 * @method BulkNotification newEntity($data = null, array $options = [])
 * @method BulkNotification[] newEntities(array $data, array $options = [])
 * @method BulkNotification|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method BulkNotification saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method BulkNotification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method BulkNotification[] patchEntities($entities, array $data, array $options = [])
 * @method BulkNotification findOrCreate($search, callable $callback = null, $options = [])
 */
class BulkNotificationsTable extends Table
{
  /**
   * Initialize method
   *
   * @param array $config The configuration for the Table.
   * @return void
   */
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('bulk_notifications');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');
  }

  /**
   * Default validation rules.
   *
   * @param Validator $validator Validator instance.
   * @return Validator
   */
  public function validationDefault(Validator $validator)
  {
    $validator
      ->boolean('available')
      ->allowEmptyString('available', false);

    $validator
      ->scalar('receiver_model')
      ->maxLength('receiver_model', 64)
      ->requirePresence('receiver_model', 'create')
      ->allowEmptyString('receiver_model', false);

    $validator
      ->integer('send_date')
      ->allowEmptyString('send_date');

    $validator
      ->integer('sent_date')
      ->allowEmptyString('sent_date');

    $validator
      ->scalar('template_path')
      ->requirePresence('template_path', 'create')
      ->allowEmptyString('template_path', false);

    $validator
      ->scalar('subject')
      ->requirePresence('subject', 'create')
      ->allowEmptyString('subject', false);

    $validator
      ->allowEmptyString('data');

    $validator
      ->allowEmptyString('increment_data');

    $validator
      ->boolean('is_increment')
      ->allowEmptyString('is_increment', false);

    $validator
      ->boolean('is_sent')
      ->allowEmptyString('is_sent', false);

    return $validator;
  }

  /**
   * @param Event $event
   * @param BulkNotification $BulkNotification
   * @param $options
   * @return bool
   */
  public function beforeSave(Event $event, BulkNotification $BulkNotification, \ArrayObject $options)
  {
    if ($BulkNotification->isNew()) {
      $BulkNotification->created = Time::now()->timestamp;
    } else {
      $BulkNotification->modified = Time::now()->timestamp;
    }

    return true;
  }

  /**
   * @param BulkNotification $BulkNotification
   */
  public function markSent(BulkNotification $BulkNotification)
  {
    $BulkNotification->sent_date = Time::now()->timestamp;
    $BulkNotification->is_sent = true;

    $this->save($BulkNotification);
  }
}
