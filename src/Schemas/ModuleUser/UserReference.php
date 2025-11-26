<?php

namespace Projects\WellmedBackbone\Schemas\ModuleUser;

use Hanafalah\ModuleUser\Contracts\Data\UserReferenceData;
use Hanafalah\ModuleUser\Schemas\UserReference as SchemasUserReference;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleUser\UserReference as ModuleUserUserReference;

class UserReference extends SchemasUserReference implements ModuleUserUserReference{
    public function prepareStoreUserReference(UserReferenceData $user_reference_dto): Model{
        $user_reference = parent::prepareStoreUserReference($user_reference_dto);        
        // $this->fillingProps($user_reference,$user_reference_dto->props);
        // $user_reference->save();
        return $this->user_reference_model = $user_reference;
    }
}