<?php

require_once __DIR__ . '/../../../shared/controllers/BaseController.php';

class ProfileController extends BaseController {
    private $mysqli;

    // Income range mappings
    private const INCOME_RANGES = [
        'below_5250' => 5000,
        'between_5250_11820' => 10000,
        'above_11820' => 15000
    ];

    public function __construct($mysqli) {
        parent::__construct();
        $this->mysqli = $mysqli;
    }

    /**
     * Convert database income value to range key
     */
    private function incomeValueToRange($income) {
        if ($income === null) {
            return '';
        }
        
        switch ((int)$income) {
            case 5000:
                return 'below_5250';
            case 10000:
                return 'between_5250_11820';
            case 15000:
                return 'above_11820';
            default:
                return '';
        }
    }

    /**
     * Convert range key to database income value
     */
    private function rangeToIncomeValue($range) {
        return self::INCOME_RANGES[$range] ?? null;
    }

    public function edit() {
        $this->requireAuth();
        $userId = $this->currentUser['id'];

        $stmt = $this->mysqli->prepare('SELECT id, name, username, email, phone_number, address, housing_status, marital_status, income FROM users WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $this->notFound();
        }

        // Convert income to range for display
        $user['income_range'] = $this->incomeValueToRange($user['income']);

        // Fetch Dependents
        $stmt = $this->mysqli->prepare('SELECT * FROM dependent WHERE user_id=? ORDER BY created_at ASC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $dependents = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Fetch Next of Kin
        $stmt = $this->mysqli->prepare('SELECT * FROM next_of_kin WHERE user_id=? ORDER BY created_at ASC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $nextOfKin = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return ['user' => $user, 'dependents' => $dependents, 'nextOfKin' => $nextOfKin];
    }

    public function update() {
        $this->requireAuth();
        $userId = $this->currentUser['id'];

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $housing_status = trim($_POST['housing_status'] ?? '');
        $marital_status = trim($_POST['marital_status'] ?? '');
        $income_range = trim($_POST['income_range'] ?? '');
        
        // Convert range to income value
        $income = !empty($income_range) ? $this->rangeToIncomeValue($income_range) : null;

        // Basic validation
        if (empty($name) || empty($email)) {
            // Return current POST data as user data to repopulate form
            return ['error' => 'Name and Email are required.', 'user' => $_POST];
        }

        // Update
        $stmt = $this->mysqli->prepare('UPDATE users SET name=?, email=?, phone_number=?, address=?, housing_status=?, marital_status=?, income=? WHERE id=?');
        $stmt->bind_param('ssssssii', $name, $email, $phone_number, $address, $housing_status, $marital_status, $income, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            // Refresh user data from DB to ensure we show what's saved
            $data = $this->edit();
            $data['success'] = 'Profile updated successfully.';
            return $data;
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['error' => 'Update failed: ' . $error, 'user' => $_POST];
        }
    }
}
