<?php
class ControllerMain extends Controller
{
    function actionIndex()
    {
        $this->view->generate('main_view.php', 'template_view.php');
    }
}


/*$connection = parse_ini_file('./../../config/dbconnection.ini');

$action = $_GET['action'];
if($action==="Generate 99 users") 
{
	require_once("./../services/service_participant.php");
    require_once (__DIR__ . "/../../vendor/autoload.php");

    reset_db($connection);

    $participantsTotalAmountRequest = 100;
    $pdo = set_connection($connection);
    $query = $pdo->query('SELECT * From participants');

    $participantsIniInfo = [];

    while ($value = $query->fetch()) {
        $participantsIniInfo[] = [];
        foreach ($value as $element) {
            $participantsIniInfo[count($participantsIniInfo)-1][] = $element;
        }
    }

    $participantsFetch = [];
    foreach ($participantsIniInfo as $participantIniInfo) {
        $parentParticipant = null;
        if ($participantIniInfo[7] != 0) {
            $parentParticipant = $participantsFetch[$participantIniInfo[7]];
        }
        $participantsFetch[] = ParticipantFactory::create($participantIniInfo[1],
            $participantIniInfo[2],
            $participantIniInfo[3],
            $participantIniInfo[5],
            $participantIniInfo[6],
            $parentParticipant);
    }

    $newParticipantPointer = count($participantsFetch);

    $connectableParticipants = [];

    for ($i=0; $i<count($participantsFetch); $i++) {
        if ($participantsFetch[$i]->connectable()) {
            $connectableParticipants[] = $i;
        }
    }

    $faker = Faker\Factory::create();
    for ($i = $newParticipantPointer; $i < $participantsTotalAmountRequest; $i++) {
        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $email = 'email:'.$faker->email;
        $shares = rand(0,500);
        $parentID = rand(0, count($connectableParticipants)-1);
        $startDate = rand($participantsFetch[$connectableParticipants[$parentID]]->get_db_values()['start_date'], time()-86400);

        $participantsFetch[] = ParticipantFactory::create($firstName, $lastName, $email, $shares, $startDate, $participantsFetch[$connectableParticipants[$parentID]]);

        $connectableParticipants[] = count($participantsFetch)-1;
        if (!$participantsFetch[$connectableParticipants[$parentID]]->connectable()) {
            unset($connectableParticipants[$parentID]);
            $connectableParticipants = array_values($connectableParticipants);
        }


    }

    for ($i = $newParticipantPointer; $i < $participantsTotalAmountRequest; $i++) {

        $parentPosition = null;
        for ($j = 0; $j < $i; $j++) {
            if ($participantsFetch[$i]->get_db_values()['parent_id'] == $participantsFetch[$j]) $parentPosition = $j+1;
        }
        $fetchedFirstName = $participantsFetch[$i]->get_db_values()['firstname'];
        $fetchedLastName = $participantsFetch[$i]->get_db_values()['lastname'];
        $fetchedMailTo = $participantsFetch[$i]->get_db_values()['mailto'];
        $fetchedPosition = $participantsFetch[$i]->get_db_values()['position'];
        $fetchedShares = $participantsFetch[$i]->get_db_values()['shares_amount'];
        $fetchedStartDate = $participantsFetch[$i]->get_db_values()['start_date'];
        $valueString = "\"$fetchedFirstName\", \"$fetchedLastName\", \"$fetchedMailTo\", \"$fetchedPosition\", $fetchedShares, $fetchedStartDate, $parentPosition";
        $queryInsert = 'INSERT INTO `participants` (`firstname`, `lastname`, `mailto`, `position`, `shares_amount`, `start_date`, `parent_id`) VALUES ('.$valueString.');';
        $pdo->query($queryInsert);
    }

} elseif ($action==="Reset")
{
	reset_db($connection);
}
header("Location: ./../..");
die();

function set_connection($connectionInfo) {
    $dsn = "mysql:host=".$connectionInfo['db_host'].";dbname=".$connectionInfo['db_name'].";charset=".$connectionInfo['db_charset'];
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $connectionInfo['db_user'], $connectionInfo['db_password'], $options);
    return $pdo;
}

function reset_db($connectionInfo)
{
	$pdo = set_connection($connectionInfo);

	$jsonFile = file_get_contents('./../../config/dbinfo.json');
	$dbInfo = json_decode($jsonFile,true);
	
	foreach($dbInfo as $tableName => $tableVals) {
		$queryDelete = "DELETE FROM `$tableName`";
		$queryAlter = "ALTER TABLE $tableName AUTO_INCREMENT=1";
		$pdo->query($queryDelete);
		$pdo->query($queryAlter);
		
		foreach ($tableVals as $val) {
			$valRowNames = array_keys($val);
			$valRowNamesString = implode(", ", $valRowNames);
			
			$valRows = array_values($val);
			foreach ($valRows as &$row)
			{
				if (!is_numeric($row)) $row = '"'.$row.'"';
			}
			$valRowsString = '('.implode(', ', $valRows).')';
				
			$queryUpdate = 
				"INSERT INTO `$tableName` (".
				$valRowNamesString.
				') VALUES '.
				$valRowsString;
			$pdo->query($queryUpdate);
		}
	}
}
*/