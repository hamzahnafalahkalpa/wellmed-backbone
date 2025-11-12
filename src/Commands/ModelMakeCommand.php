<?php

namespace Projects\WellmedBackbone\Commands;

use Hanafalah\LaravelPackageGenerator\Commands\ModelMakeCommand as CommandsModelMakeCommand;

class ModelMakeCommand extends CommandsModelMakeCommand
{
    protected $signature = 'wellmed-backbone:make-model 
                {name}
                {--pattern= : Pattern yang digunakan}
                {--class-basename= : Nama class yang digunakan}';
}