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
   * @param EntityInterface | array $receivers
   * @param string $subject
   * @param string $template
   * @param array $data
   * @param array $options
   * @return mixed
   */
  public function add($receivers, string $subject, string $template, array $data = [], $options = [])
  {
    $defaultOptions = [
      'sendDate' => null,
      'receiverEmailColumn' => 'email'
    ];

    $options = array_merge($defaultOptions, $options);

    if ($receivers instanceof EntityInterface) {
      $receivers = [$receivers];
    }

    /** @var EntityInterface $receiver */
    foreach ($receivers as $receiver) {
      $bulkNotification = $this->BulkNotifications->newEntity([
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

      $this->BulkNotifications->save($bulkNotification);
    }
  }

  /**
   * Add notification
   *
   * @param EntityInterface | array $receiver
   * @param string $subject
   * @param string $template
   * @param array $data
   * @param array $incrementData
   * @param array $options
   * @return mixed
   */
  public function addIncrement($receivers, string $subject, string $template, array $data = [], array $incrementData = [], $options = [])
  {
    $defaultOptions = [
      'sendDate' => null,
      'receiverEmailColumn' => 'email',
    ];

    $options = array_merge($defaultOptions, $options);

    if ($receivers instanceof EntityInterface) {
      $receivers = [$receivers];
    }

    /** @var EntityInterface $receiver */
    foreach ($receivers as $receiver) {
      /** @var \BulkNotifications\Model\Entity\BulkNotification $bulkNotification */
      $bulkNotification = $this->BulkNotifications->find()
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

      if (!$bulkNotification) {
        $bulkNotification = $this->BulkNotifications->newEntity([
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

        $this->BulkNotifications->save($bulkNotification);

        continue;
      }

      $oldIncrementData = json_decode($bulkNotification->increment_data, true);
      $keys = array_unique(array_merge(array_keys($oldIncrementData), array_keys($incrementData)));

      $incrementDataToSave = [];

      foreach ($keys as $key) {
        $incrementDataToSave[$key] = array_merge($oldIncrementData[$key] ?? [], $incrementData[$key] ?? []);
      }

      $bulkNotification = $this->BulkNotifications->patchEntity($bulkNotification, [
        'data' => json_encode($data),
        'increment_data' => json_encode($incrementDataToSave),
        'sent_date' => $options['sendDate'],
      ]);

      $this->BulkNotifications->save($bulkNotification);
    }
  }
}