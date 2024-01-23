<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('format_date'))
{
  function format_date($date, $style='d/m/Y')
  {
    if (isset($date))
      return date($style, strtotime( $date ) );
    return '';
  }
}

if ( ! function_exists('prepare_date'))
{
  function prepare_date($date)
  {
    if (isset($date) && $date != '')
      return implode( "-", array_reverse( explode("/", $date ) ) );
    return null;
  }
}
