<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportScom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gobelins:import_scom {--all : Import all tables}';

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
     * Drop the default postgresql database.
     */
    private function dropDatabase()
    {
        $sql_disconnect_other_users = "SELECT pg_terminate_backend(pg_stat_activity.pid) 
                                       FROM pg_stat_activity
                                       WHERE pg_stat_activity.datname = '$this->db'
                                       AND pid <> pg_backend_pid();";
        exec("psql -h $this->host -U $this->username -w -d template1 -c \"$sql_disconnect_other_users\"");
        exec("psql -h $this->host -U $this->username -w -d template1 -c \"DROP DATABASE $this->db\"");
        exec("psql -h $this->host -U $this->username -w -d template1 -c \"CREATE DATABASE $this->db\"");
    }

    /**
     * Load the consolidated SCOM dump file into the local database.
     * This dump file is exported manually from the PSQL database on
     * the IT dept of the Mobilier National.
     *
     * @return void
     */
    private function loadConsolidatedDump()
    {
        $scom_dump_file_path = \Config::get('app.scom_dump_file_path');
        
        $this->comment('Loading database dump: ' . $scom_dump_file_path);
        
        // The dump file is about 250 MB, this takes a few seconds on my laptop.
        exec("psql -h $this->host -U $this->username -w -d $this->db -f $scom_dump_file_path");
        
        $this->comment('Load complete');
    }
    
    private function loadTablesDump()
    {
        $dir = storage_path(\Config::get('app.scom_dump_directory_path'));
        $files = scandir($dir);

        $this->comment('Loading database dumps from directory: ' . $dir);

        putenv("PGCLIENTENCODING=WIN1252");
        
        $create_tables_file = $dir . '/createTables.sql';
        $this->comment('Create tables: '.$create_tables_file);
        //exec("iconv -f WINDOWS-1252 -t UTF-8 -o $create_tables_file $create_tables_file");
        exec("psql -h $this->host -U $this->username -w -d $this->db -f $create_tables_file");
        
        $files_of_interest = [
            'Export_ANCNUM.sql',
            'Export_AUT.sql',
            'Export_DIFFUSION.sql',
            'Export_EPO.sql',
            'Export_GAR.sql',
            'Export_GRACAT.sql',
            'Export_MAT.sql',
            'Export_OBJ.sql',
            'Export_OBJAUT.sql',
            'Export_OBJGAR.sql',
            'Export_OBJMAT.sql',
            'Export_OBJNOTE.sql',
            'Export_PHOTO.sql',
            'Export_STY.sql',
            'Export_MODEENTREECOLLECTION.sql',
        ];

        collect($files)->filter(function ($f) use ($files_of_interest) {
            if ($this->option('all')) {
                // Import all SCOM tables, even the ones the app doesn't use.
                return preg_match('/.*Export_.+\.sql$/i', $f) === 1;
            } else {
                return in_array($f, $files_of_interest);
            }
        })->map(function ($f) use ($dir) {
            return "$dir/$f";
        })->map(function ($f) {
            $this->comment('Loading: ' . $f);
            exec("psql -h $this->host -U $this->username -w -d $this->db -f $f");
            return null;
        });
        
        $this->comment('Load complete');
        
        putenv("PGCLIENTENCODING");
    }

    private function wrangleSchema()
    {
        $this->comment('Wrangling schema');
        
        // Add single column relations, for the ORM.
        // Add foreign key relations, for Metabase ?
        
        $wrangle_schema  = "";
        $wrangle_schema  = "ALTER TABLE obj ADD id varchar(21);";
        $wrangle_schema .= "UPDATE obj SET id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE obj ADD PRIMARY KEY (id);";

        
        $wrangle_schema .= "ALTER TABLE objaut ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE objaut SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE objaut ADD COLUMN id BIGSERIAL PRIMARY KEY;";
        $wrangle_schema .= "ALTER TABLE objaut ADD CONSTRAINT objaut_obj_id_fkey FOREIGN KEY (obj_id) REFERENCES obj (id);";
        
        $wrangle_schema .= "ALTER TABLE photo ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE photo SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE photo ADD COLUMN id BIGSERIAL PRIMARY KEY;";
        // Can't add fkey, because referential integrity is not assured :(
        // $wrangle_schema .= "ALTER TABLE photo ADD CONSTRAINT photo_obj_id_fkey FOREIGN KEY (obj_id) REFERENCES obj (id);";

        
        $wrangle_schema .= "ALTER TABLE ancnum ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE ancnum SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE ancnum ADD PRIMARY KEY (ancnum);";
        $wrangle_schema .= "ALTER TABLE ancnum ADD CONSTRAINT ancnum_obj_id_fkey FOREIGN KEY (obj_id) REFERENCES obj (id);";

        $wrangle_schema .= "ALTER TABLE mat ADD PRIMARY KEY (codmat);";
        
        $wrangle_schema .= "ALTER TABLE gar ADD PRIMARY KEY (codgar);";

        $wrangle_schema .= "ALTER TABLE objmat ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE objmat SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE objmat ADD CONSTRAINT objmat_obj_id_fkey FOREIGN KEY (obj_id) REFERENCES obj (id);";
        $wrangle_schema .= "ALTER TABLE objmat ADD CONSTRAINT objmat_codmat_fkey FOREIGN KEY (codmat) REFERENCES mat (codmat);";
        
        $wrangle_schema .= "ALTER TABLE objgar ADD obj_id varchar(21);";
        $wrangle_schema .= "UPDATE objgar SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
        $wrangle_schema .= "ALTER TABLE objgar ADD CONSTRAINT objgar_obj_id_fkey FOREIGN KEY (obj_id) REFERENCES obj (id);";
        $wrangle_schema .= "ALTER TABLE objgar ADD CONSTRAINT objgar_codgar_fkey FOREIGN KEY (codgar) REFERENCES gar (codgar);";
        

        if ($this->option('all')) {
            $wrangle_schema .= "ALTER TABLE ben ADD PRIMARY KEY (codben);";
            $wrangle_schema .= "ALTER TABLE cad ADD PRIMARY KEY (codcad);";
            $wrangle_schema .= "ALTER TABLE cat ADD PRIMARY KEY (codcat);";

            $wrangle_schema .= "ALTER TABLE cli ADD obj_id varchar(21);";
            $wrangle_schema .= "UPDATE cli SET obj_id = CONCAT(numinv1, '-', numinv2, '-', numinv3);";
            // Can't add a fkey, because faulty referential integrity (ref to 'GMEC-252-003' that doesn't exist in obj table).
            // $wrangle_schema .= "ALTER TABLE cli ADD CONSTRAINT cli_obj_id_fkey FOREIGN KEY (obj_id) REFERENCES obj (id);";
        }

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
            //$this->dropDatabase();
            //$this->loadConsolidatedDump();
            $this->loadTablesDump();
            $this->wrangleSchema();
        });
        
        $this->comment('Done');
    }
}
