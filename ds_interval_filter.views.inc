<?php

function ds_interval_filter_views_data() {
  $data = [];
  $data['views']['interval_filter'] = [
    'title' => t('Interval filter (title string)'),
    'help' => t('Help string'),
    'filter' => ['id' => 'ds_interval_filter']];
  return $data;
}