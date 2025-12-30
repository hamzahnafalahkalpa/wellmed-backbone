<?php

namespace Projects\WellmedBackbone\Models\ModuleAppointment;

use Projects\WellmedBackbone\Transformers\Reservation\{
    ViewReservation, ShowReservation
};

class Reservation extends Appointment
{
    protected $table = 'appointments';

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [];
    }

    public function getViewResource(){
        return ViewReservation::class;
    }

    public function getShowResource(){
        return ShowReservation::class;
    }
    public function visitPatient(){return $this->hasOneModel('VisitPatient','reservation_id');}
}
