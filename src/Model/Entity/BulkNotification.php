<?php
namespace BulkNotifications\Model\Entity;

use Cake\ORM\Entity;

/**
 * BulkNotification Entity
 *
 * @property string $id
 * @property int $created
 * @property int|null $modified
 * @property bool $available
 * @property string $receiver_id
 * @property string $receiver_model
 * @property string $receiver_email_column
 * @property int|null $send_date
 * @property int|null $sent_date
 * @property string $template_path
 * @property string $subject
 * @property string|null $data
 * @property string|null $increment_data
 * @property bool $is_sent
 */
class BulkNotification extends Entity
{
  /**
   * Fields that can be mass assigned using newEntity() or patchEntity().
   *
   * Note that when '*' is set to true, this allows all unspecified fields to
   * be mass assigned. For security purposes, it is advised to set '*' to false
   * (or remove it), and explicitly make individual fields accessible as needed.
   *
   * @var array
   */
  protected $_accessible = [
    'created' => true,
    'modified' => true,
    'available' => true,
    'receiver_id' => true,
    'receiver_model' => true,
    'receiver_email_column' => true,
    'send_date' => true,
    'sent_date' => true,
    'template_path' => true,
    'subject' => true,
    'data' => true,
    'increment_data' => true,
    'is_increment' => true,
    'is_sent' => true,
  ];
}
