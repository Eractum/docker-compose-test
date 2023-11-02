<?php
require_once __DIR__.'/database_connection_sql.php';

/** Connects to a database where "participant" table is stored.
 */
class ParticipantDatabaseConnection extends DatabaseConnectionSQL
{
    protected static $path = '/../../config/dbconnection.ini';
}