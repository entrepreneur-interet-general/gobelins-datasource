<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportScom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gobelins:import_scom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load the PostgresQL dump file of SCOM into the default `postgres` DB';

    /**
     * Database credentials
     */
    protected $host     = '';
    protected $db       = '';
    protected $username = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load SCOM dump into local database
     *
     * @return void
     */
    private function loadDump()
    {
        $scom_dump_file_path = env('SCOM_DUMP_FILE_PATH');
        
        $this->comment('Loading database dump: ' . $scom_dump_file_path);

        $sql_disconnect_other_users = "SELECT pg_terminate_backend(pg_stat_activity.pid) 
                                       FROM pg_stat_activity
                                       WHERE pg_stat_activity.datname = '$this->db'
                                       AND pid <> pg_backend_pid();";
        exec("psql -h $this->host -U $this->username -w -d template1 -c \"$sql_disconnect_other_users\"");
        exec("psql -h $this->host -U $this->username -w -d template1 -c \"DROP DATABASE $this->db\"");
        exec("psql -h $this->host -U $this->username -w -d template1 -c \"CREATE DATABASE $this->db\"");
        
        // The dump file is about 250 MB, this takes a few seconds on my laptop.
        exec("psql -h $this->host -U $this->username -w -d $this->db -f $scom_dump_file_path");
        
        $this->comment('Load complete');
    }

    private function wrangleSchema()
    {
        $this->comment('Wrangling schema');
        
        // Add single column relations, for the ORM.
        $wrangle_schema  = "ALTER TABLE obj ADD id varchar(21);";
        $wrangle_schema .= "UPDATE obj SET id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        
        $wrangle_schema .= "ALTER TABLE objaut ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE objaut SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE objaut ADD COLUMN id BIGSERIAL PRIMARY KEY;";
        
        $wrangle_schema .= "ALTER TABLE photo ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE photo SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE photo ADD COLUMN id BIGSERIAL PRIMARY KEY;";
        
        $wrangle_schema .= "ALTER TABLE ancnum ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE ancnum SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        
        exec("psql -h $this->host -U $this->username -w -d $this->db -c \"$wrangle_schema\"");
        
        $this->comment('Wrangling complete');
    }

    /**
     * Load SCOM data into the app.
     */
    private function more()
    {
        // OBJ table
        // AUT table
        // EPO table
        // GAR table (garnitures)
        // MAT table
        // STY table
    }

    private function withEnvPassword($password, $func)
    {
        putenv("PGPASSWORD=$password");
        $func();
        putenv("PGPASSWORD");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->host     = \Config::get('database.connections.pgsql.host');
        $this->db       = \Config::get('database.connections.pgsql.database');
        $this->username = \Config::get('database.connections.pgsql.username');
        $password       = \Config::get('database.connections.pgsql.password');

        $this->withEnvPassword($password, function () {
            $this->loadDump();
            $this->wrangleSchema();
        });
        
        $this->comment('Done');
    }
}
