<?php

namespace Projects\WellmedBackbone\Transformers\Reservation;

use Hanafalah\ModuleAppointment\Resources\Reservation\ShowReservation as ReservationShowReservation;

class ShowReservation extends ViewReservation
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'visit_patient' => $this->relationValidation('visitPatient',function(){
                return $this->visitPatient->toShowApi()->resolve();
            })
        ];
        $show = $this->resolveNow(new ReservationShowReservation($this));
        $arr = $this->mergeArray(parent::toArray($request), $show, $arr);
        return $arr;
    }
}
