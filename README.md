# CakePHP Bulk Notifications plugin

Plugin lets you create bulk notifications. 
Lets imagine you send email notification to the topic's owner on new comment to the topic
In case when user have 5 comments within one topic user will get 5 emails which is may be annoying.

This plugin allows to create an queue for such situation and sends one email with 5 new comments
if between creating date of these comments no more than 30 seconds (configurable) 


## Installation

```sh
composer require thebeaverhead/cakephp-bulk-notifications
```

## Setup

In your app's console:

```sh
bin/cake plugin load BulkNotifications
```

Apply migration:

```sh
bin/cake migrations migrate -p BulkNotifications
```

## Usage:

To create an email to be sended (NOT increment):

```php
$userEntity = TableRegistry::getTableLocator()->get('Users')->get($userId);
$topic = ['title' => 'foo'];
$comments = [
    ['text' => 'foo'], 
    ['text' => 'bar']
];

$bulkNotification = new BulkNotification();
$bulkNotification->add(
    $userEntity,
    'Email subject',
    'email/template/path',
    ['topic' => $topic, 'comments' => $comments],
    ['sendDate' => 1580108540, 'receiverEmailColumn' => 'email']
);
```
To create increment email to be sended:

```php
$userEntity = TableRegistry::getTableLocator()->get('Users')->get($userId);
$userEntity2 = TableRegistry::getTableLocator()->get('Users')->get($userId2);

$topic = ['title' => 'foo'];
$comments = [
    ['text' => 'foo'], 
    ['text' => 'bar']
];

$bulkNotification = new BulkNotification();
$bulkNotification->addIncrement(
    [$userEntity, $userEntity2],
    'Email increment subject',
    'email/template/path',
    ['topic' => $topic],                    // not incremental data
    ['comments' => $comments],              // incremental data
    ['sendDate' => 1580108540, 'receiverEmailColumn' => 'email']
);


// Increment notification has been created
// in 10 sec new comment has been added to the same topic
sleep(10);
$comments = [
    ['text' => 'baz']
];

$bulkNotification->addIncrement(
    $userEntity,
    'Email increment subject',
    'email/template/path',
    ['topic' => $topic],                    // not incremental data
    ['comments' => $comments]               // incremental data
);
```

create email template.ctp

```php
<?php
/**
 * @var \App\View\AppView $this 
 * @var \App\Model\Entity\User $receiver  // plugin automatically pass UserEntity as $receiver
 * @var array $comments
 * @var \App\Model\Entity\Comment $comment
 * @var \App\Model\Entity\Topic $topic
 */
?>

Hi <?= $receiver['name'] ?>, your topic <?= $topic['title'] ?> has new comment(s):
<ul>
  <?php foreach ($comments as $key => $comment): ?>
    <li>
      Comment #<?= $key ?>
      <?= $comment['text'] ?>
    </li>
  <?php endforeach; ?>
</ul>
```

To send notifications run

```sh
bin/cake BulkNotifications run
```
Actually you need add this command to the crontab.
## Configure:

Both methods `add` and `addIncrement` support `$options` parameter:
```php
$options = [
 'sendDate' => 1580108540,         // (null by default) if this date isn't reached notification won't be sent
 'receiverEmailColumn' => 'email'  // (`email` by default) email column in the receiver entity
];
```

Shell BulkNotifications supports config `BulkNotifications.send_delay` in seconds (30sec by default)
It won't sent an incremental email if previous notification has been created or updated less than 30 sec ago.

In config/app.php you can add:
```php
'BulkNotifications' => [
  'send_delay' => 60
],
```
