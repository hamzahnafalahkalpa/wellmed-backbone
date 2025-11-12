<?php

namespace Projects\WellmedBackbone\Transformers\Room;

use Hanafalah\ModuleWarehouse\Resources\Room\ViewRoom as RoomViewRoom;

class ViewRoom extends RoomViewRoom
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
      'employee_ids'          => $this->employee_ids ?? [],
      'service_cluster_id'    => $this->service_cluster_id,
      'service_cluster'       => $this->prop_service_cluster,
      'medic_service_id'      => $this->medic_service_id,
      'medic_service'         => $this->prop_medic_service,
    ];
    $arr = $this->mergeArray(parent::toArray($request),$arr);
    return $arr;
  }
}
