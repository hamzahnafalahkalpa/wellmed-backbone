<?php

namespace Projects\WellmedBackbone\Models\ModuleEmployee;

use Hanafalah\LaravelHasProps\Models\Scopes\HasCurrentScope;
use Hanafalah\ModuleEmployee\Models\Employee\Employee as EmployeeEmployee;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Transformers\Employee\{ViewEmployee, ShowEmployee};

class Employee extends EmployeeEmployee{
    public function getShowResource(){
        return ShowEmployee::class;
    }

    public function getViewResource(){
        return ViewEmployee::class;
    }

    public function switchRoomTo(object|string $room): Model{
        $room = is_string($room) ? $this->RoomModel()->find($room) : $room;
        $model_has_room = $this->modelHasRoom()
            ->where('model_id', $this->getKey())
            ->where('model_type', $this->getMorphClass())
            ->where('warehouse_id', $room->getKey())
            ->where('warehouse_type', 'Room')
            ->first();
        $model_has_room->current = 1;
        $model_has_room->save();
        return $room;
    }
    
    //EIGER SECTION
    public function rooms(){        
        return $this->belongsToManyModel(
            'Room','ModelHasRoom',
            'model_id','warehouse_id'
        )->where('model_type',$this->getMorphClass())
        ->where('warehouse_type','Room')
        ->select($this->RoomModel()->getTable().'.*',$this->ModelHasRoomModel()->getTable().'.current')
        ->withoutGlobalScopes([HasCurrentScope::class]);
    }
    public function room(){
        return $this->hasOneThroughModel(
            'Room', 'ModelHasRoom',
            'model_id',
            $this->RoomModel()->getKeyName(),
            $this->getKeyName(),
            'warehouse_id',
        )
        ->select($this->RoomModel()->getTable().'.*',$this->ModelHasRoomModel()->getTable().'.current')
        ->where('model_type',$this->getMorphClass())
        ->where('warehouse_type','Room')
        ->where('current',1);
    }

    public function modelHasRoom(){
        return $this->morphOneModel('ModelHasRoom','model')->withoutGlobalScopes();
    }

    public function consultationService(){
        return $this->morphOneModel('EmployeeService','model')->where('props->flag','CONSULTATION_FEE');
    }
}
