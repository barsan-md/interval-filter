<?php

namespace Drupal\barsan_ds\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Filter handler which allows to filter based on an interval (as two distinct fields).
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("ds_interval_filter")
 */
class IntervalFilter extends FilterPluginBase {

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\views\Plugin\views\filter\FilterPluginBase::valueForm()
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#size' => 30,
      '#default_value' => $this->value];
  }

  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['min'] = ['default' => []];
    $options['max'] = ['default' => []];
    return $options;
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\views\Plugin\views\filter\FilterPluginBase::buildOptionsForm()
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $this->view->initStyle();
    if (!$this->view->style_plugin->usesFields()) {
      return;
    }
    $options = [];
    foreach ($this->view->display_handler->getHandlers('field') as $name => $field) {
      if ($field->clickSortable()) {
        $options[$name] = $field->adminLabel(TRUE);
      }
    }
    if ($options) {
      $form['min'] = [
        '#type' => 'select',
        '#title' => $this->t('Choose the field to be used as minimum value'),
        '#description' => $this->t('The field to be used as minimum. The value can also be NULL, which means non bounded for minimum.'),
        '#options' => $options];
      $form['max'] = [
        '#type' => 'select',
        '#title' => $this->t('Choose the field to be used as maximum value'),
        '#description' => $this->t('The field to be used as maximum. The value can also be NULL, which means non bounded for maximum.'),
        '#options' => $options];
    }
    else {
      $form_state->setErrorByName('', $this->t('You have to add some fields to be able to use this filter.'));
    }
  }
}
