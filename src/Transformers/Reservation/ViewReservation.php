<?php

namespace Projects\WellmedBackbone\Transformers\Reservation;

use Hanafalah\ModuleAppointment\Resources\Reservation\ViewReservation as ReservationViewReservation;

class ViewReservation extends ReservationViewReservation
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'nik' => $this->nik,
            'phone' => $this->phone,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
