<?php

namespace Projects\WellmedBackbone\Schemas\SatuSehat;

use Hanafalah\SatuSehat\Contracts\Data\SatuSehatLogData;
use Hanafalah\SatuSehat\Schemas\SatuSehatLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\SatuSehat\GeneralSetting as SatuSehatGeneralSetting;

class GeneralSetting extends SatuSehatLog implements SatuSehatGeneralSetting{
    protected string $__entity = 'GeneralSetting';

    public function usingEntity(): Model{
        return $this->SatuSehatLogModel();
    }

    // public function showEntityResource(callable $callback,array $options = []): array{
    //     return $this->transforming($this->SatuSehatLogModel()->getShowResource(),function() use ($callback){
    //         return $callback();
    //     },$options);
    // }

    public function generalPrepareStore(mixed $dto = null): Model{
        if (is_array($dto)) $dto = $this->requestDTO(config("app.contracts.{$this->getEntity()}Data",null));
        $model = $this->SatuSehatLogModel()->updateOrCreate([
            'id' => $dto->id ?? null
        ], [
            'name' => $dto->name,
            'reference_type' => $dto->reference_type,
            'reference_id' => $dto->reference_id,
            'method' => $dto->method,
            'env_type' => $dto->env_type,
            'url' => $dto->url
        ]);
        $this->fillingProps($model,$dto->props);
        $model->save();
        return $model;
    }

    public function generalPrepareStoreMultiple(array $datas): Collection{
        $collection = new Collection();
        foreach ($datas as $data) {
            if (is_array($data)){
                foreach($data as $single_data){
                    $collection->push($this->generalPrepareStore($this->requestDTO(config("app.contracts.SatuSehatLogData"),$single_data)));
                }
            }else{
                $collection->push($this->generalPrepareStore($this->requestDTO(config("app.contracts.SatuSehatLogData"),$single_data)));
            }
        }
        return $collection;
    }

    public function generalSetting(mixed $conditionals = null): Builder{
        return $this->satuSehatLog($conditionals);
    }
}