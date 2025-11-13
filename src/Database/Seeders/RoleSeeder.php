<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Hanafalah\LaravelPermission\Facades\LaravelPermission;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        LaravelPermission::scanRoles(__DIR__.'/data/roles');
    }
}
