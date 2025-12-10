<?php
/**
 * DeathNotification Entity
 * Represents a death notification record
 */

class DeathNotification {
    public $id;
    public $deceased_name;
    public $ic_number;
    public $date_of_death;
    public $place_of_death;
    public $cause_of_death;
    public $next_of_kin_name;
    public $next_of_kin_phone;
    public $reported_by;
    public $verified;
    public $verified_by;
    public $verified_at;
    public $created_at;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function isVerified() {
        return (bool) $this->verified;
    }

    public function getVerificationStatus() {
        return $this->verified ? 'Verified' : 'Pending';
    }

    public function getDisplayName() {
        return htmlspecialchars($this->deceased_name);
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'deceased_name' => $this->deceased_name,
            'ic_number' => $this->ic_number,
            'date_of_death' => $this->date_of_death,
            'place_of_death' => $this->place_of_death,
            'cause_of_death' => $this->cause_of_death,
            'next_of_kin_name' => $this->next_of_kin_name,
            'next_of_kin_phone' => $this->next_of_kin_phone,
            'reported_by' => $this->reported_by,
            'verified' => $this->verified,
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
?>
