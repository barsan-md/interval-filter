<?php

namespace Drupal\ds_interval_filter\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Query\Condition;

/**
 * Filter handler which allows to filter based on an interval (as two distinct fields).
 *
 * @ViewsFilter("ds_interval_filter")
 */
class IntervalFilter extends FilterPluginBase {
  
  /**
   * The operator "Interval contains value".
   * This is used as token.
   *
   * @var string
   */
  const CONTAINS = 'contains';
  
  /**
   * The operator "Interval does not contain value".
   * This is used as toke.
   *
   * @var string
   */
  const NOT_CONTAINS = 'not contains';

  /**
   *
   * {@inheritdoc}
   * @see FilterPluginBase::valueForm()
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#size' => 30,
      '#default_value' => $this->value];
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\views\Plugin\views\filter\FilterPluginBase::acceptExposedInput()
   */
  public function acceptExposedInput($input) {
    $value = &$input[$this->options['expose']['identifier']];
    return isset($value) && $value !== '' && parent::acceptExposedInput($input);
  }

  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['min'] = ['default' => ''];
    $options['max'] = ['default' => ''];
    $options['minIncluded'] = ['default' => TRUE];
    $options['maxIncluded'] = ['default' => TRUE];
    $options['operator']['default'] = self::CONTAINS;
    return $options;
  }

  /**
   *
   * {@inheritdoc}
   * @see FilterPluginBase::buildOptionsForm()
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $this->view->initStyle();
    if (!$this->view->style_plugin->usesFields()) {
      return;
    }
    $options = [];
    foreach ($this->view->display_handler->getHandlers('field') as $name => $field) {
      // Can't be comparable if it's not sortable
      if ($field->clickSortable()) {
        $options[$name] = $field->adminLabel(TRUE);
      }
    }
    if ($options) {
      $form['min'] = [
        '#type' => 'select',
        '#title' => $this->t('Field to be used as MIN value'),
        '#description' => $this->t('The field to be used as minimum. NULL value means left-unbounded.'),
        '#options' => $options,
        '#default_value' => $this->options['min']];
      $form['max'] = [
        '#type' => 'select',
        '#title' => $this->t('Field to be used as MAX value'),
        '#description' => $this->t('The field to be used as maximum. NULL value means right-unbounded.'),
        '#options' => $options,
        '#default_value' => $this->options['max']];
    }
    else {
      $form_state->setErrorByName('', $this->t('You have to add some fields to be able to use this filter.'));
    }
    $form['minIncluded'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('MIN is included'),
      '#description' => $this->t('The value of the field minimum will be considered part of the interval.'),
      '#default_value' => $this->options['minIncluded']];
    $form['maxIncluded'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('MAX is included'),
      '#description' => $this->t('The value of the field maximum will be considered part of the interval.'),
      '#default_value' => $this->options['maxIncluded']];
  }

  /**
   *
   * {@inheritdoc}
   * @see FilterPluginBase::query()
   */
  public function query() {
    $this->ensureMyTable();
    
    $minField = $this->view->field[$this->options['min']];
    $maxField = $this->view->field[$this->options['max']];
    
    $minField->ensureMyTable();
    $maxField->ensureMyTable();
    
    $min = "{$minField->tableAlias}.{$minField->realField}";
    $max = "{$maxField->tableAlias}.{$maxField->realField}";
    
    $condition = NULL;
    if ($this->operator === self::CONTAINS) {
      $opMin = $this->options['minIncluded'] ? '<=' : '<';
      $opMax = $this->options['maxIncluded'] ? '>=' : '>';
      $condition = new Condition('AND');
      $condition->condition((new Condition('OR'))->isNull($min)->condition($min, $this->value, $opMin));
      $condition->condition((new Condition('OR'))->isNull($max)->condition($max, $this->value, $opMax));
    }
    else {
      $opMin = $this->options['minIncluded'] ? '>' : '>=';
      $opMax = $this->options['maxIncluded'] ? '<' : '<=';
      $condition = new Condition('OR');
      // it is not necessary to controll nullity
      $condition->condition($min, $this->value, $opMin);
      $condition->condition($max, $this->value, $opMax);
      $condition->condition((new Condition('AND'))->isNull($min)->isNull($max));
    }
    
    $this->query->addWhere($this->options['group'], $condition);
  }

  /**
   *
   * {@inheritdoc}
   * @see FilterPluginBase::operatorOptions()
   */
  public function operatorOptions() {
    return [
      self::CONTAINS => $this->t('Contains'),
      self::NOT_CONTAINS => $this->t('Does not contain')];
  }
}
