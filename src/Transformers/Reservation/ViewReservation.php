<?php

namespace Projects\WellmedBackbone\Transformers\Reservation;

use Hanafalah\ModuleAppointment\Resources\Reservation\ViewReservation as ReservationViewReservation;

class ViewReservation extends ReservationViewReservation
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'nik' => $this->nik,
            'phone' => $this->phone
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
