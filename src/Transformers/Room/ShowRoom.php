<?php

namespace Projects\WellmedBackbone\Transformers\Room;

use Hanafalah\ModuleWarehouse\Resources\Room\ShowRoom as RoomShowRoom;

class ShowRoom extends ViewRoom
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
      'employees' => $this->relationValidation('employees',function(){
        return $this->employees->transform(function($employee){
          return $employee->toViewApi()->resolve();
        });
      })
    ];
    $show = $this->resolveNow(new RoomShowRoom($this));
    $arr = $this->mergeArray(parent::toArray($request), $show, $arr);
    return $arr;
  }
}
