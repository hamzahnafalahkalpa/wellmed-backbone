<?php

namespace Projects\WellmedBackbone\Facades;

class WellmedBackbone extends \Illuminate\Support\Facades\Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return \Projects\WellmedBackbone\Contracts\WellmedBackbone::class;
  }
}
