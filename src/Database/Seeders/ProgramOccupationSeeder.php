<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleEvent\Contracts\Data\ProgramOccupationData;
use Illuminate\Database\Seeder;

class ProgramOccupationSeeder extends Seeder
{
    use HasRequestData;

    public $occupations = [
        ["name" => "Pic"]
    ];


    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->occupations as $group) {
            $group['flag'] = 'ProgramOccupation';
            app(config('app.contracts.ProgramOccupation'))->prepareStoreProgramOccupation(
                $this->requestDTO(ProgramOccupationData::class,$group)
            );
        }
    }
}
