<?php
class User
{
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $password;
    public $role;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT user_id, username, role FROM " . $this->table_name . " WHERE user_id = :user_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($stmt->execute()) {
            return $stmt; // Assurez-vous que ceci retourne bien le statement
        } else {
            return null; // En cas d'erreur, retourne null
        }
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);

        return $stmt->execute();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET username = :username, role = :role" . (!empty($this->password) ? ", password = :password" : "") . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':username', $this->username);
        if (!empty($this->password)) {
            $stmt->bindParam(':password', $this->password);
        }
        $stmt->bindParam(':role', $this->role);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);

        return $stmt->execute();
    }

    public function register($username, $password, $role)
    {
        // Préparer la requête d'insertion
        $query = "INSERT INTO " . $this->table_name . " (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $this->conn->prepare($query);

        // Hash du mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Lier les paramètres
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        // Exécuter la requête
        try {
            if ($stmt->execute()) {
                // Ajouter l'utilisateur à la table du rôle
                return $this->addUserToRoleTable($username, $role);
            } else {
                error_log("Error executing query: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }

    private function addUserToRoleTable($username, $role)
    {
        $table = $this->getRoleTable($role);
        if ($table) {
            $query = "INSERT INTO " . $table . " (name) VALUES (:name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $username);

            try {
                if ($stmt->execute()) {
                    return true;
                } else {
                    error_log("Error adding user to role table: " . implode(", ", $stmt->errorInfo()));
                    return false;
                }
            } catch (Exception $e) {
                error_log("Exception: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    private function getRoleTable($role)
    {
        $tables = [
            'clients' => 'Clients',
            'commercants' => 'Commercants',
            'benevoles' => 'Benevoles'
        ];
        return $tables[$role] ?? null;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function login($username, $password)
    {
        $query = "SELECT user_id, password, role FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $row['password'])) {
                    $this->role = $row['role'];
                    $this->user_id = $row['user_id'];
                    return true;
                }
            }
        }
        return false;
    }
}
