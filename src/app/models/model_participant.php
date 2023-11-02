<?php
require_once __DIR__.'/model_factories/participant_factory.php';
/**
 * A Participant model.
 */
class Participant extends ParticipantFactory
{
    private $firstName;
    private $lastName;
    private $mailTo;
    private $position;
    private $shares;
    private $startDate;

    protected function __construct(
        string $firstName,
        string $lastName,
        string $mailTo,
        string $position,
        int    $shares,
        int    $startDate)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->mailTo = $mailTo;
        $this->position = $position;
        $this->shares = $shares;
        $this->startDate = $startDate;
        /*
		$this->parent 			  = $parent;
		$this->affiliateRelevance = $affiliateRelevance;

		if ($parent != null) {
            $parent->connect($this);
            $this->position = 'novice';
            if ($this->parent->get_db_values()['position'] == 'president') $this->update_position();
        }
		else
			$this->position	= 'president';
        */
    }

    /** Returns all values as an associative array.
     * @return array
     */
    public function get_db_values(): array
    {
        return [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'mailto' => $this->mailTo,
            'position' => $this->position,
            'shares_amount' => $this->shares,
            'start_date' => $this->startDate,
        ];
    }

    public function getFirstName()
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function getMail()
    {
        return $this->mailTo;
    }
    public function setMail(string $email)
    {
        $this->mailTo = $email;
    }

    public function getPosition()
    {
        return $this->position;
    }
    public function setPosition(string $position)
    {
        $this->position = $position;
    }

    public function getShares()
    {
        return $this->shares;
    }
    public function setShares(int $shares)
    {
        $this->shares = $shares;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }
    public function setStartDate(int $date)
    {
        $this->startDate = $date;
    }
}