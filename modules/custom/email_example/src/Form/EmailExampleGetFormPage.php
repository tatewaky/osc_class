<?php

/**
 * @file
 * Contains \Drupal\email_example\Form\EmailExampleGetFormPage.
 */

namespace Drupal\email_example\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * File test form class.
 *
 * @ingroup email_example
 */
class EmailExampleGetFormPage extends FormBase {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Constructs a new EmailExampleGetFormPage.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   */
  public function __construct(MailManagerInterface $mail_manager) {
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.mail'));
  }

  
  public function getFormID() {
    return 'email_example';
  }

  /**
   * ya se definen los tipos en los valores, el form es de tipo array
   * el form state es de tipo interfaz
   */
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['intro'] = array(
      '#markup' => t('Formulario para enviar mensajes'),
    );
    $form['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Correo'),
      '#required' => TRUE,
    );
    $form['message'] = array(
      '#type' => 'textarea',
      '#title' => t('Mensaje'),
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );
    return $form;
  }

  /**
   * ya se definen los tipos en los valores, el form es de tipo array
   * el form state es de tipo interfaz
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!valid_email_address($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', t('El correo no es valido.'));
    }
  }

  /**
   * ya se definen los tipos en los valores, el form es de tipo array
   * el form state es de tipo interfaz
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValues();
        
    $to = $form_values['email'];
    $from = \Drupal::config('system.site')->get('mail');
    $params = $form_values;

    $language_code = \Drupal::languageManager()->getDefaultLanguage()->getId();
    
    $result = $this->mailManager->mail('email_example', 'contact_message', $to, $language_code, $params, $from);
    if ($result['result'] == TRUE) {
      drupal_set_message(t('El correo ha sido enviado.'));
    }
    else {
      drupal_set_message(t('Hubo un problema al mandar el correo.'), 'error');
    }
  }
}
