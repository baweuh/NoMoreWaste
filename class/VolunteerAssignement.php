<?php
class VolunteerAssignment
{
    private $conn;
    private $table_name = "benevoles_services";

    public $assignment_id;
    public $volunteer_id;
    public $service_id;
    public $task;
    public $date;
    public $status;

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
        $query = "SELECT * FROM " . $this->table_name . " WHERE assignment_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->assignment_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->volunteer_id = $row['volunteer_id'];
            $this->service_id = $row['service_id'];
            $this->task = $row['task'];
            $this->date = $row['date'];
            $this->status = $row['status'];
        }
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (volunteer_id, service_id, task, date, status) VALUES (:volunteer_id,  :service_id, :task,  :date, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':task', $this->task);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':status', $this->status);

        return $stmt->execute();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET volunteer_id = :volunteer_id, service_id = :service_id, task = :task, date = :date, status = :status WHERE assignment_id = :assignment_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':assignment_id', $this->assignment_id);
        $stmt->bindParam(':volunteer_id', $this->volunteer_id);
        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':task', $this->task);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':status', $this->status);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE assignment_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->assignment_id);

        return $stmt->execute();
    }
}
?>
