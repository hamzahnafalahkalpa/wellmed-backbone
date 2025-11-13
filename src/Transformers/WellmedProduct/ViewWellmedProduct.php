<?php

namespace Projects\Hq\Resources\WellmedProduct;

use Hanafalah\LaravelSupport\Resources\Unicode\ViewUnicode;

class ViewWellmedProduct extends ViewUnicode
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray(\Illuminate\Http\Request $request): array
  {
    $arr = [
      "id" => $this->id,
      "name" => $this->name,
      "label" => $this->label,
      'product_code' => $this->product_code,
      'icon' => $this->icon,
      'product_items' => $this->relationValidation('productItems',function(){
          return $this->productItems->map(function($item){
              return $item->toViewApi();
          });
      }),
    ];    
    return $arr;
  }
}
