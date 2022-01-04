<?php

namespace app\Migrations;

use JetBrains\PhpStorm\NoReturn;

class SyncMigrations extends BaseMigrations
{

    /**
     * Initialize your database parameters:
     */
    public function Message( array $argv = array()  ): string
    {

        $message = "";

        if (count($argv) <= 1) {
            $message .= "Usage: To add new migration: \n php migrate add <name>";
            $message .= "To migrate your database: \n php migrate run <name>";
        }

        return $message;

    }

    /**
     * Compre current database version
     * @return string
     */
    public function CompareVersion() : string{

        return  "";

    }

    /**
     * Return all migration files and version
     * @return array
     */
    private function Get():array {

        $Files = array();

        while ( $File = readdir( opendir(DIR_MIGRATIONS_TEMP ) ) ) {

            if (str_starts_with( $File , MIGRATION_EXTENSION)) {

                $Files[] = array(
                    'file' => $File,
                    'version' => filemtime($File)
                );

            }

        }

        asort($Files);

        $this->MigrateTempFile = $Files;

    }

    #[NoReturn] public function Sync() : string{

        if( file_exists(DIR_MIGRATIONS . MIGRATION_SYNC_STATE) === false ){

            $Json = json_encode(array(
                "version" => "1.0.0",
                "generatedTime" => "1641317333818",
                "data" => $this->Get()
            ));

            if ( file_put_contents(DIR_MIGRATIONS . MIGRATION_SYNC_STATE, $Json ) === false )
            {
                return "Error performing migrations.";
            }

            return "Migrations completed successfully.";

        }


        $FileSync = file_get_contents(DIR_MIGRATIONS . MIGRATION_SYNC_STATE );

        $FileSync = json_decode( $FileSync, true  ) ?? array();

        var_dump($FileSync);
        die();

        if(  empty($FileSync) ){



        }

        try {

            foreach ( $this->MigrateTempFile as $migrate => $query ){

                $this->DataBase->beginTrans();

                $this->MigrateTempFile[$migrate]['status'] = $this->DataBase->query( $query );

                if( $this->MigrateTempFile[$migrate]['status'] ) {

                    $this->DataBase->commitTrans();
                    continue;

                }

                $this->DataBase->rollBackTrans();

            }

        }catch ( \Exception $exception ){

            var_dump($exception);

        }

    }
}