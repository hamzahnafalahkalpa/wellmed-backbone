<?php

namespace Projects\Hq\Models;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\Unicode\Unicode;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Projects\Hq\Resources\WellmedProduct\{
    ViewWellmedProduct,
    ShowWellmedProduct
};

class WellmedProduct extends Unicode
{
    use HasUlids, HasProps, SoftDeletes;

    protected $table = 'products';

    protected $casts = [
        'name' => 'string', 
        'flag' => 'string',
        'product_code' => 'string',
        'label'  => 'string'
    ];

    public function getPropsQuery(): array{
        return [
            'product_code' => 'props->product_code'
        ];
    }

    protected function isUsingService(): bool{
        return true;
    }

    public function viewUsingRelation(): array{
        return ['productItems'];
    }

    public function showUsingRelation(): array{
        return ['productItems'];
    }

    public function getViewResource(){
        return ViewWellmedProduct::class;
    }

    public function getShowResource(){
        return ShowWellmedProduct::class;
    }

    public function productItems(){return $this->hasManyModel('ProductItem','product_id');}
}
