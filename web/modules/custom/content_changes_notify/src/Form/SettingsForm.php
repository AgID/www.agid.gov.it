<?php

namespace Drupal\content_changes_notify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {
  protected function getEditableConfigNames() {
    // TODO: Implement getEditableConfigNames() method.
    return [
      'content_changes_notify.settings'
    ];
  }
  public function getFormId() {
    return 'content_changes_notify_admin_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $conf = $this->config('content_changes_notify.settings');
    $form['fieldset_types'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => t('Content types'),
    ];
    $form['fieldset_types']['types'] = [
      '#type' => 'checkboxes',
      '#title' => t('Content types'),
      '#options' => $this->getContentTypes(),
      '#default_value' => empty($conf->get('types')) ? [] : $conf->get('types'),
    ];
    $form['emails'] = [
      '#type' => 'textarea',
      '#title' => t('Emails list'),
      '#default_value' => $conf->get('emails'),
      '#description' => t('Write each email one for line'),
      '#required' => TRUE,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Mail title'),
      '#default_value' => !empty($conf->get('title')) ? $conf->get('title') : t('Content changed') . ' ([node:nid])',
      '#required' => TRUE,
      '#description' => t('Mail\'s title. Supports token like as for mail\'s message'),
    ];

    $form['message'] = [
      '#type' => 'text_format',
      '#title' => t('Mail message'),
      '#format' => 'full_html',
      '#default_value' => !empty($conf->get('message')) ? $conf->get('message')['value'] : [],
      '#required' => TRUE,
    ];
    // Add the token tree UI.
    $form['message']['token_tree'] = array(
      '#theme' => 'token_tree_link',
      '#token_types' => array('node'),
      '#show_restricted' => TRUE,
      '#global_types' => FALSE,
      '#weight' => 90,
    );

    return parent::buildForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('content_changes_notify.settings')
        ->set('types', $form_state->getValue('types'))
        ->set('emails', $form_state->getValue('emails'))
        ->set('title', $form_state->getValue('title'))
        ->set('message', $form_state->getValue('message'))
        ->save();
    parent::submitForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  private function getContentTypes() {
    $contentTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $contentTypes = array_map(function($item) {
      return $item->get('name');
    }, $contentTypes);
    return $contentTypes;
  }
}
