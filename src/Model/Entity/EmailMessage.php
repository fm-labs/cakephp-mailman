<?php
namespace Mailman\Model\Entity;

use Cake\ORM\Entity;

/**
 * EmailMessage Entity.
 *
 * @property int $id
 * @property string $folder
 * @property string $transport
 * @property string $from
 * @property string $sender
 * @property string $to
 * @property string $cc
 * @property string $bcc
 * @property string $reply_to
 * @property string $subject
 * @property string $headers
 * @property string $message
 * @property bool $read_receipt
 * @property string $return_path
 * @property string $email_format
 * @property string $charset
 * @property string $result_headers
 * @property string $result_message
 * @property int $error_code
 * @property string $error_msg
 * @property bool $sent
 * @property \Cake\I18n\Time $date_delivery
 * @property string $messageid
 * @property \Cake\I18n\Time $created
 */
class EmailMessage extends Entity
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
        '*' => true,
        'id' => false,
    ];
}
