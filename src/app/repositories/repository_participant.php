<?php
require_once __DIR__.'/../database_connection/database_connection_participant.php';

/** Executes reading, insertion and reset queries.
 */
class ParticipantRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = ParticipantDatabaseConnection::setConnection();
    }

    /** Resets database to its initial state.
     * @return PDO|string|null
     */
    public function requestReset()
    {
        if (!$this->checkPDOConnectionError() === null) {
            return $this->pdo;
        }

        if(!$jsonFile = file_get_contents(__DIR__ . '/../../config/dbinfo.json')) {
            return 'ERROR: Cannot reset database, config file not found.';
        }
        $dbInfo = json_decode($jsonFile, true);

        foreach ($dbInfo as $tableName => $tableVals) {
            $queryDelete = "DELETE FROM `$tableName`";
            $queryAlter = "ALTER TABLE $tableName AUTO_INCREMENT=1";
            $this->pdo->query($queryDelete);
            $this->pdo->query($queryAlter);

            foreach ($tableVals as $val) {
                $valRowNames = array_keys($val);
                $valRowNamesString = implode(", ", $valRowNames);

                $valRows = array_values($val);
                foreach ($valRows as &$row) {
                    if (!is_numeric($row)) $row = '"' . $row . '"';
                }
                $valRowsString = '(' . implode(', ', $valRows) . ')';

                $queryUpdate =
                    "INSERT INTO `$tableName` (" .
                    $valRowNamesString .
                    ') VALUES ' .
                    $valRowsString;
                $this->pdo->query($queryUpdate);
            }
        }

        return null;
    }

    /** Inserts participant values as a new Participant. Requires a following array:
     * - 'firstname':string - Participant's first name
     * - 'lastname':string - Participant's last name
     * - 'mailto' :string - Participant's email
     * - 'position' :string - Participant's position
     * - 'shares_amount' :int - Participant's shares
     * - 'start_date' :int - Participant's start date timestamp
     * - 'parent_id' :int - Participant's parent ID
     * @param array $values
     * @return PDO|string|null
     */
    public function requestInsert(array $values)
    {
        if (!$this->checkPDOConnectionError() === null) {
            return $this->pdo;
        }

        $rowValueString = '"' . $values['firstname'] . '", "' . $values['lastname'] . '", "' . $values['mailto'] . '", "' . $values['position'] . '", ' . $values['shares_amount'] . ', ' . $values['start_date'] . ', ' . $values['parent_id'];

        $queryInsert = 'INSERT INTO `participants` (`firstname`, `lastname`, `mailto`, `position`, `shares_amount`, `start_date`, `parent_id`) VALUES (' . $rowValueString . ');';

        $this->pdo->query($queryInsert);

        return null;
    }

    /** Returns array of all participant values stored in database.
     * @return array|PDO|string
     */
    public function requestRead()
    {
        if (!$this->checkPDOConnectionError() === null) {
            return $this->pdo;
        }
        $query = $this->pdo->query('SELECT * From participants');

        $participantsInfo = [];

        while ($row = $query->fetch()) {
            $participantsInfo[] = [];
            foreach ($row as $rowElement) {
                $participantsInfo[count($participantsInfo) - 1][] = $rowElement;
            }
        }

        return $participantsInfo;
    }

    /** Returns error message on database connection failure.
     * @return string|null
     */
    private function checkPDOConnectionError()
    {
        if (is_string($this->pdo)) {
            return $this->pdo;
        }
        else {
            return null;
        }
    }
}