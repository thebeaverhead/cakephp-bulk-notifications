<?php
namespace BulkNotifications\Core;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use BulkNotifications\Model\Table\BulkNotificationsTable;

class BulkNotification
{
  /** @var BulkNotificationsTable */
  protected $BulkNotifications;

  public function __construct()
  {
    $this->BulkNotifications = TableRegistry::getTableLocator()->get('BulkNotifications.BulkNotifications');
  }

  /**
   * Add notification
   *
   * @param EntityInterface $receiver
   * @param string $subject
   * @param string $template
   * @param array $data
   * @param array $options
   * @return mixed
   */
  public function add(EntityInterface $receiver, string $subject, string $template, array $data = [], $options = [])
  {
    $defaultOptions = [
      'sendDate' => null,
      'receiverEmailColumn' => 'email'
    ];

    $options = array_merge($defaultOptions, $options);

    $BulkNotification = $this->BulkNotifications->newEntity([
      'receiver_id' => $receiver->id,
      'receiver_model' => $receiver->getSource(),
      'receiver_email_column' => $options['receiverEmailColumn'],
      'subject' => $subject,
      'template_path' => $template,
      'data' => json_encode($data),
      'increment_data' => null,
      'is_increment' => false,
      'send_date' => $options['sendDate'],
    ]);

    $this->BulkNotifications->save($BulkNotification);
  }

  /**
   * Add notification
   *
   * @param EntityInterface $receiver
   * @param string $subject
   * @param string $template
   * @param array $data
   * @param array $incrementData
   * @param array $options
   * @return mixed
   */
  public function addIncrement(EntityInterface $receiver, string $subject, string $template, array $data = [], array $incrementData = [], $options = [])
  {
    $defaultOptions = [
      'sendDate' => null,
      'receiverEmailColumn' => 'email',
    ];

    $options = array_merge($defaultOptions, $options);

    /** @var \BulkNotifications\Model\Entity\BulkNotification $BulkNotification */
    $BulkNotification = $this->BulkNotifications->find()
      ->where([
        'receiver_id' => $receiver->id,
        'receiver_model' => $receiver->getSource(),
        'receiver_email_column' => $options['receiverEmailColumn'],
        'subject' => $subject,
        'template_path' => $template,
        'is_increment IS' => true,
        'is_sent IS' => false
      ])
      ->first();

    if ($BulkNotification) {
      $BulkNotification->data = json_encode($data);
      $BulkNotification->sent_date = $options['sendDate'];

      $oldIncrementData = json_decode($BulkNotification->increment_data, true);
      $keys = array_unique(array_merge(array_keys($oldIncrementData), array_keys($incrementData)));

      $incrementDataToSave = [];

      foreach ($keys as $key) {
        $incrementDataToSave[$key] = array_merge($oldIncrementData[$key] ?? [], $incrementData[$key] ?? []);
      }

      $BulkNotification->increment_data = json_encode($incrementDataToSave);

      $this->BulkNotifications->save($BulkNotification);

      return;
    }

    $BulkNotification = $this->BulkNotifications->newEntity([
      'receiver_id' => $receiver->id,
      'receiver_model' => $receiver->getSource(),
      'receiver_email_column' => $options['receiverEmailColumn'],
      'subject' => $subject,
      'template_path' => $template,
      'data' => json_encode($data),
      'increment_data' => json_encode($incrementData),
      'is_increment' => true,
      'send_date' => $options['sendDate'],
    ]);

    $this->BulkNotifications->save($BulkNotification);
  }
}