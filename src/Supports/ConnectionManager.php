<?php

namespace Projects\WellmedBackbone\Supports;

use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Supports\ConnectionManager as SupportsConnectionManager;

class ConnectionManager implements SupportsConnectionManager{
     public function handle(Model $tenant): void{
        $connection_name = $tenant->getConnectionFlagName();
        $connection_path = "database.connections.".$connection_name;
        switch (env('DB_DRIVER',null)) {
            case 'mysql':
                config([
                    "$connection_path.database" => $tenant->db_name,
                    "$connection_path.username" => $tenant->tenancy_db_username,
                    "$connection_path.password" => $tenant->tenancy_db_password
                ]);
            break;
            case 'pgsql': 
                config([
                    "$connection_path.database"    => $tenant->db_name ?? env('DB_DATABASE'),
                    "$connection_path.search_path" => $tenant->tenancy_db_name == $tenant->db_name ? 'public' : $tenant->tenancy_db_name,
                    "$connection_path.username"    => $tenant->tenancy_db_username ?? env('DB_USERNAME'),
                    "$connection_path.password"    => $tenant->tenancy_db_password ?? env('DB_PASSWORD')
                ]);
            break;
            case 'sqlite':
                config([
                    "$connection_path.database"    => $tenant->db_name
                ]);
            break;
            default:
                throw new \Exception('Database driver not supported');
            break;
        }
    }   
}