<?php

namespace Projects\WellmedBackbone\Transformers\Employee;

use Hanafalah\ModuleEmployee\Resources\Employee\ShowEmployee as EmployeeShowEmployee;

class ShowEmployee extends ViewEmployee
{

    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'room' => $this->relationValidation('room',function(){
                return $this->room->toViewApi()->resolve();
            }),
            'rooms' => $this->relationValidation('rooms',function(){
                return $this->rooms->transform(function($room){
                    return $room->toViewApi()->resolve();
                });
            })
        ];
        $show = $this->resolveNow(new EmployeeShowEmployee($this));
        $arr = array_merge(parent::toArray($request),$show,$arr);
        return $arr;
    }
}
