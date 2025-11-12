<?php

namespace Projects\WellmedBackbone\Models;

use Hanafalah\ModuleRegional\Models\Regional\Address;

class WellmedAddress extends Address{
    public function getForeignKey(){
        return 'address_id';
    }
}
