<?php

declare(strict_types=1);

#FILES
const PATH_ROOT = 'E:' . DIRECTORY_SEPARATOR . 'TRABALHO' . DIRECTORY_SEPARATOR . 'SERVIDOR' . DIRECTORY_SEPARATOR . 'HOSTESS';

#ROOT
const PATH_APP = PATH_ROOT . DIRECTORY_SEPARATOR . 'App';
const DIR_VENDOR = PATH_ROOT . DIRECTORY_SEPARATOR . 'Libraries';

#DIRS
const DIR_BOOT =  PATH_APP . DIRECTORY_SEPARATOR . 'Bootstrap';
const DIR_CACHE =  PATH_APP . DIRECTORY_SEPARATOR . 'Cache';
const DIR_CONFIG =  PATH_APP . DIRECTORY_SEPARATOR . 'Configs';
const DIR_CONTROL =  PATH_APP . DIRECTORY_SEPARATOR . 'Controllers';
const DIR_DOC =  PATH_APP . DIRECTORY_SEPARATOR . 'Documentation';
const DIR_LOGS =  PATH_APP . DIRECTORY_SEPARATOR . 'Logs';
const DIR_Middleware =  PATH_APP . DIRECTORY_SEPARATOR . 'Middlewares';

const DIR_MODEL =  PATH_APP . DIRECTORY_SEPARATOR . 'Models';
const DIR_PROVIDER =  PATH_APP . DIRECTORY_SEPARATOR . 'Providers';
const DIR_PUBLIC =  PATH_APP . DIRECTORY_SEPARATOR . 'Public';
const DIR_SESSIONS =  PATH_APP . DIRECTORY_SEPARATOR . 'Sessions';
const DIR_VIEW =  PATH_APP . DIRECTORY_SEPARATOR . 'View';

#SYSTEM
const SERVER_NAME = 'HTTP:SERVER';
const SERVER_HOST = 'hostessframework.com.br';
const SERVER_PORT = '80';
const SERVER_WORKER = '4';
const SERVER_IP = '0.0.0.0';


#APP
const APP_NAME = 'Hostess framework';
const APP_VERSION = '1.0.0';
const APP_DESCRIPTION = "Hostess is a web application framework. It doesn't exist with the purpose of reinventing the wheel, but as a simple structure and with little study time, it is easy to learn and assemble large systems/applications.";
define( 'APP_PROTOCOL' , 'HTTP' ?? (!empty($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP') );


#LOCALE
const APP_LOCALE = 'America/Sao_Paulo';
const APP_URL = APP_PROTOCOL . '://' . SERVER_HOST;

#PHP
const CONF_MEMORYLIMIT = '256M';
const CONF_TIMESESSION = '21600';

#DATABASE
const DB_PORT = '3306';
const DB_SERVE = '';
const DB_NAME = '';
const DB_USER = '';
const DB_PASS = '';

#LOGS
const CONF_SAVELOGS = true;
const CONF_REPORTING = E_ALL;

#MIGRATIONS
const MIGRATION_EXTENSION = ".migrate";
const MIGRATION_SYNC_STATE = "/migrate-sync-state.json";
const DIR_MIGRATIONS =  PATH_APP . DIRECTORY_SEPARATOR . 'Migrations';
const DIR_MIGRATIONS_TEMP =  DIR_MIGRATIONS . DIRECTORY_SEPARATOR . 'Temp';


#ENCRYPTION
const ENCRY_SALT = 32;
const ENCRY_AUTHKEY = "7c71e2dd56072664769cb577aeaaebe35a01dcc13756879549a3957cfd995e7b";