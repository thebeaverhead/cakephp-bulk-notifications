<?php
namespace BulkNotifications\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use BulkNotifications\Model\Entity\BulkNotification;
use BulkNotifications\Model\Table\BulkNotificationsTable;

/**
 * BulkNotifications shell command.
 *
 * @property BulkNotificationsTable BulkNotifications
 */
class BulkNotificationsShell extends Shell
{
  public $modelClass = 'BulkNotifications.BulkNotifications';

  public function run()
  {
    $notifications = $this->BulkNotifications->find()
      ->where(['is_sent IS' => false])
      ->where([
        'OR' => [
          ['send_date IS' => null],
          ['send_date <=' => Time::now()->timestamp],
        ]
      ])
      ->where([
        'OR' => [
          ['is_increment IS' => false],
          [
            'is_increment IS' => true,
            'created <' => Time::now()->timestamp - Configure::read('BulkNotifications.send_delay', 30),
            'OR' => [
              ['modified IS' => null],
              ['modified <' => Time::now()->timestamp - Configure::read('BulkNotifications.send_delay', 30)]
            ]
          ]
        ]
      ]);

    /** @var BulkNotification $notification */
    foreach ($notifications as $notification) {

      $model = TableRegistry::getTableLocator()->get($notification->receiver_model);
      $receiver = $model->find()
        ->where(['id' => $notification->receiver_id])
        ->first();

      if (!$receiver) {
        $this->BulkNotifications->markSent($notification);

        continue;
      }

      $data = (array) json_decode($notification->data, true);
      $incrementData = (array) json_decode($notification->increment_data, true);

      $email = new Email();
      $email->viewBuilder()->setTemplate($notification->template_path);

      $email->setEmailFormat('html')
        ->setTo($receiver->email)
        ->setSubject($notification->subject)
        ->setViewVars($data + $incrementData + ['receiver' => $receiver])
        ->send();

      $this->BulkNotifications->markSent($notification);
    }
  }
}
