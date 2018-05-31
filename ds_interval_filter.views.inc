<?php

function ds_interval_filter_views_data() {
  $data['views']['interval_filter'] = [
    'title' => t('Interval filter'),
    'help' => t('Choose two MIN and MAX fields respectively and search if the interval contains or not a value.'),
    'filter' => ['id' => 'ds_interval_filter']];
  return $data;
}
