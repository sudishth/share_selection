<?php

/**
 * @file
 * Contains \Drupal\share_selection\Form\ShareSelectionConfigServicesForm.
 */

namespace Drupal\share_selection\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class ShareSelectionConfigServicesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'share_selection_config_services_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $share_selection_settings = $this->config('share_selection.settings');
    $share_selection_settings->set('share_selection_show', $values['share_selection_show'])
      ->set('share_selection_weight', $values['share_selection_weight'])
      ->set('share_selection_custom', $values['share_selection_custom'])
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['share_selection.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $settings = [];

    $services = share_selection_get_links(NULL, TRUE);
    $settings['show'] = \Drupal::config('share_selection.settings')->get('share_selection_show');
    $settings['weight'] = \Drupal::config('share_selection.settings')->get('share_selection_weight');
    $settings['custom'] = \Drupal::config('share_selection.settings')->get('share_selection_custom');

    $form['share_selection'] = [
      '#theme' => 'share_selection_services_drag_table'
      ];
    $form['share_selection']['share_selection_show'] = ['#tree' => TRUE];
    $form['share_selection']['share_selection_weight'] = ['#tree' => TRUE];
    // Custom service options.
    $form['share_selection_custom'] = [
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#title' => t('Custom services options'),
      '#description' => t('Set the custom options per service.'),
      '#tree' => TRUE,
    ];

    foreach ($services as $service_id => $service) {
      $icon_path = drupal_get_path('module', $service['module']) . '/images/' . $service['icon'];
      $icon = isset($service['icon']) ? '<img src="' . base_path() . $icon_path . '"> ' : '';

      $weight = isset($settings['weight'][$service_id]) ? $settings['weight'][$service_id] : 0;

      $form['share_selection']['share_selection_show'][$service_id] = array(
        '#service' => ucwords(str_replace('_', ' ', $service['module'])),
        '#weight' => $weight,
        '#type' => 'checkbox',
        '#title' => $icon . t('Show %name', array('%name' => $service['name'])),
        '#return_value' => 1,
        '#default_value' => isset($settings['show'][$service_id]) ? $settings['show'][$service_id] : 0,
      );

      $form['share_selection']['share_selection_weight'][$service_id] = [
        '#type' => 'weight',
        '#delta' => 100,
        '#default_value' => $weight,
      ];

      if (isset($service['custom_options'])) {
        $form['share_selection_custom'][$service_id] = [
          '#type' => 'fieldset',
          '#collapsible' => TRUE,
          '#collapsed' => FALSE,
          '#title' => $service['name'],
        ];
        foreach ($service['custom_options'] as $option_key => $option_label) {
          $form['share_selection_custom'][$service_id][$option_key] = [
            '#type' => 'textfield',
            '#title' => $option_label,
            '#default_value' => isset($settings['custom'][$service_id][$option_key]) ? $settings['custom'][$service_id][$option_key] : '',
            '#size' => '60',
          ];
        }
      }
    }
    $form['replacement_tokens'] = [
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#title' => t('Replacement tokens'),
    ];
    $form['replacement_tokens']['tokens'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => [
        'node'
        ],
      '#global_types' => TRUE,
      '#click_insert' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

}
