<?php

namespace app\models;

use flight\database\PdoWrapper;
use Flight;

class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Flight::db();
    }

    /**
     * Get all records
     */
    public function getAll($limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table}";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($query, [':limit' => $limit, ':offset' => $offset]);
        }
        
        return $this->db->fetchAll($query);
    }

    /**
     * Get record by ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->fetchRow($query, [':id' => $id]);
    }

    /**
     * Get records by condition
     */
    public function getBy($conditions = [], $limit = null, $offset = 0)
    {
        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Get one record by condition
     */
    public function getOneBy($conditions = [])
    {
        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " LIMIT 1";

        return $this->db->fetchRow($query, $params);
    }

    /**
     * Insert a new record
     */
    public function create($data)
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $query = "INSERT INTO {$this->table} (" . implode(", ", $columns) . ") 
                  VALUES (" . implode(", ", $placeholders) . ")";

        $params = [];
        foreach ($data as $key => $value) {
            $params[":{$key}"] = $value;
        }

        $this->db->runQuery($query, $params);
        
        return $this->db->lastInsertId();
    }

    /**
     * Update a record
     */
    public function update($id, $data)
    {
        $set = [];
        $params = [];

        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $params[':id'] = $id;

        $query = "UPDATE {$this->table} SET " . implode(", ", $set) . " 
                  WHERE {$this->primaryKey} = :id";

        return $this->db->runQuery($query, $params);
    }

    /**
     * Delete a record
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->runQuery($query, [':id' => $id]);
    }

    /**
     * Get count of records
     */
    public function count($conditions = [])
    {
        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $result = $this->db->fetchRow($query, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Execute raw query
     */
    public function rawQuery($query, $params = [])
    {
        return $this->db->fetchAll($query, $params);
    }

    /**
     * Execute raw query and get one result
     */
    public function rawQueryOne($query, $params = [])
    {
        return $this->db->fetchRow($query, $params);
    }

    /**
     * Begin database transaction
     */
    public function beginTransaction()
    {
        return $this->db->pdo->beginTransaction();
    }

    /**
     * Commit database transaction
     */
    public function commitTransaction()
    {
        return $this->db->pdo->commit();
    }

    /**
     * Rollback database transaction
     */
    public function rollbackTransaction()
    {
        return $this->db->pdo->rollback();
    }

    /**
     * Check if in transaction
     */
    public function inTransaction()
    {
        return $this->db->pdo->inTransaction();
    }
}

//fin
