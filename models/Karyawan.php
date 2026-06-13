<?php
/**
 * Karyawan Model - Handles employee data management
 * 
 * Manages employee records including CRUD operations and employee-related
 * database queries for the SPK TOPSIS system.
 * 
 * @author Development Team
 * @version 1.0
 */
class Karyawan {
    private \PDO $pdo;
    private ?array $columnsCache = null;
    
    /**
     * Constructor
     * 
     * @param \PDO $pdo Database connection object
     */
    public function __construct(\PDO $pdo) { 
        $this->pdo = $pdo; 
    }
    
    /**
     * Get all active employees
     * 
     * Retrieves employees with 'aktif' status, sorted by name.
     * Falls back to all employees if status column doesn't exist.
     * 
     * @return array Array of active employee records
     * @throws \PDOException
     */
    public function getAll(): array {
        try {
            $stmt = $this->pdo->query("SELECT * FROM karyawan WHERE status='aktif' ORDER BY nama");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if ($e->getCode() === '42S22') {
                // Column 'status' doesn't exist, get all employees
                $stmt = $this->pdo->query("SELECT * FROM karyawan ORDER BY nama");
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            throw $e;
        }
    }
    
    /**
     * Get all employees including inactive ones
     * 
     * Retrieves all employee records regardless of status, sorted by name.
     * 
     * @return array Array of all employee records
     * @throws \PDOException
     */
    public function getAllWithNonaktif(): array {
        $stmt = $this->pdo->query("SELECT * FROM karyawan ORDER BY nama");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get employee by ID
     * 
     * @param int|string $id Employee ID
     * @return array|null Employee data or null if not found
     * @throws \PDOException
     */
    public function getById(int|string $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM karyawan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Create new employee record
     * 
     * Inserts new employee with provided data. Automatically excludes
     * system columns (id, created_at, updated_at) from insert.
     * 
     * @param string $nama Employee name
     * @param string $jabatan Job title
     * @param string $divisi Division/department
     * @param string|null $tanggal_masuk Start date (YYYY-MM-DD)
     * @param string $status Employment status
     * @return bool True on success, false on failure
     * @throws \Exception
     * @throws \PDOException
     */
    public function create(
        string $nama,
        string $jabatan,
        string $divisi,
        ?string $tanggal_masuk,
        string $status
    ): bool {
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'divisi' => $divisi,
            'tanggal_masuk' => $tanggal_masuk,
            'status' => $status,
        ];
        
        $available = $this->getColumns();
        $exclude = ['id', 'created_at', 'updated_at'];
        $useCols = array_values(array_diff(
            array_intersect(array_keys($data), $available), 
            $exclude
        ));
        
        if (empty($useCols)) {
            throw new \Exception('No valid columns for insert');
        }
        
        $placeholders = rtrim(str_repeat('?,', count($useCols)), ',');
        $colList = implode(', ', $useCols);
        $values = array_map(fn($c) => $data[$c], $useCols);
        
        $stmt = $this->pdo->prepare("INSERT INTO karyawan ($colList) VALUES ($placeholders)");
        return $stmt->execute($values);
    }
    
    /**
     * Update employee record
     * 
     * Updates employee with provided data. Automatically excludes
     * system columns from update.
     * 
     * @param int|string $id Employee ID
     * @param string $nama Employee name
     * @param string $jabatan Job title
     * @param string $divisi Division/department
     * @param string|null $tanggal_masuk Start date (YYYY-MM-DD)
     * @param string $status Employment status
     * @return bool True on success, false on failure
     * @throws \Exception
     * @throws \PDOException
     */
    public function update(
        int|string $id,
        string $nama,
        string $jabatan,
        string $divisi,
        ?string $tanggal_masuk,
        string $status
    ): bool {
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'divisi' => $divisi,
            'tanggal_masuk' => $tanggal_masuk,
            'status' => $status,
        ];
        
        $available = $this->getColumns();
        $exclude = ['id', 'created_at', 'updated_at'];
        $useCols = array_values(array_diff(
            array_intersect(array_keys($data), $available), 
            $exclude
        ));
        
        if (empty($useCols)) {
            throw new \Exception('No valid columns for update');
        }
        
        $sets = [];
        $values = [];
        foreach ($useCols as $c) { 
            $sets[] = "$c = ?"; 
            $values[] = $data[$c]; 
        }
        $values[] = $id;
        
        $setList = implode(', ', $sets);
        $stmt = $this->pdo->prepare("UPDATE karyawan SET $setList WHERE id=?");
        return $stmt->execute($values);
    }

    /**
     * Get all column names from karyawan table
     * 
     * Caches column information to avoid repeated DESC queries.
     * 
     * @return array Array of column names
     * @throws \PDOException
     */
    private function getColumns(): array {
        if ($this->columnsCache !== null) {
            return $this->columnsCache;
        }
        
        $stmt = $this->pdo->query("DESCRIBE karyawan");
        $cols = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $cols[] = $row['Field'];
        }
        
        $this->columnsCache = $cols;
        return $cols;
    }
    
    /**
     * Delete employee record
     * 
     * Permanently removes employee from database.
     * 
     * @param int|string $id Employee ID
     * @return bool True on success, false on failure
     * @throws \PDOException
     */
    public function delete(int|string $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM karyawan WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
