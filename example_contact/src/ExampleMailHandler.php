<?php

namespace Drupal\example_contact;

use Drupal\contact\MessageInterface;
use Drupal\contact\MailHandler as ContactMailHandler;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a class for handling assembly and dispatch of contact mail messages.
 */
class ExampleMailHandler extends ContactMailHandler {

  /**
   * The department selected, if applicable.
   *
   * @var string
   */
  protected $department;

  /**
   * {@inheritdoc}
   */
  public function sendMailMessages(MessageInterface $message, AccountInterface $sender) {
    $contact_form = $message->getContactForm();

    // If it's not the contact form just do the normal mail handler.
    if($contact_form->id() != 'contact') {
      parent::sendMailMessages($message, $sender);
      return;
    }

    $this->department = NULL;

    // Find Departmental contact
    $this->department = $message->get('field_contact_department')->getValue()[0]['value'];

    if($this->department !== NULL) {
      $this->setDepartmentRecipients($contact_form);
    }

    parent::sendMailMessages($message, $sender);

  }

  /**
   * Set recipients for a contact form based on departmental
   * field selection for field_contact_department.
   *
   * @param Drupal\contact\Entity\ContactForm
   *   The contact form to modify.
   */
  public function setDepartmentRecipients(&$contact_form) {

    switch($this->department) {
      case 'web':
        $recipients = ['example@example.com'];
        break;
      case 'general':
        $recipients = ['example@example.com', 'example2@example.com'];
        break;
      case 'blog':
        $recipients = ['blog@example.com'];
        break;
      default:
        $recipients = ['me@me.com'];
    }

    $contact_form->setRecipients($recipients);

  }

}
