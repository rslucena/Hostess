<?php

namespace app\Migrations;

use JetBrains\PhpStorm\NoReturn;

class SyncMigrations
{

    private array $MigrateTempFile;
    public mixed $DataBase;

    public function __construct()
    {

        global $DB;
        $this->DataBase = $DB;

    }

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
     * @return void
     */
    public function Get():void {

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

    #[NoReturn] public function Sync() : void{

        $SyncVersion = filemtime(DIR_MIGRATIONS . MIGRATION_SYNC_STATE);

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