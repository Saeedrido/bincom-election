<?php

require_once __DIR__ . '/../config/database.php';

class PollingModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllLgas(): array
    {
        $sql = "SELECT uniqueid, lga_id, lga_name FROM lga ORDER BY lga_name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getAllStates(): array
    {
        $sql = "SELECT state_id, state_name FROM state ORDER BY state_name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getWardsByLga(int $lgaId): array
    {
        $sql = "SELECT uniqueid, ward_id, ward_name FROM ward WHERE lga_id = :lga_id ORDER BY ward_name ASC";
        return $this->db->query($sql, ['lga_id' => $lgaId])->fetchAll();
    }

    public function getAllPollingUnits(): array
    {
        $sql = "
            SELECT 
                pu.uniqueid,
                pu.polling_unit_name,
                pu.polling_unit_number,
                pu.polling_unit_id,
                w.ward_name,
                l.lga_name
            FROM polling_unit pu
            JOIN ward w ON pu.ward_id = w.uniqueid
            JOIN lga l ON pu.lga_id = l.uniqueid
            ORDER BY pu.polling_unit_name ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    // Question 1 — Fetch scores for a given polling unit
    public function getPollingUnitResults(int $pollingUnitId): array
    {
        $sql = "
            SELECT 
                party_abbreviation,
                party_score
            FROM announced_pu_results
            WHERE polling_unit_uniqueid = :polling_unit_id
            ORDER BY party_score DESC
        ";
        return $this->db->query($sql, ['polling_unit_id' => $pollingUnitId])->fetchAll();
    }

    // Get full polling unit info together with ward/lga/state names
    public function getPollingUnitDetails(int $pollingUnitId): ?array
    {
        $sql = "
            SELECT 
                pu.uniqueid,
                pu.polling_unit_name,
                pu.polling_unit_number,
                pu.polling_unit_id,
                w.ward_name,
                l.lga_name,
                s.state_name
            FROM polling_unit pu
            JOIN ward w ON pu.ward_id = w.uniqueid
            JOIN lga l ON pu.lga_id = l.uniqueid
            JOIN state s ON pu.state_id = s.state_id
            WHERE pu.uniqueid = :polling_unit_id
        ";
        $result = $this->db->query($sql, ['polling_unit_id' => $pollingUnitId])->fetch();
        return $result ?: null;
    }

    // Question 2 — Walk the LGA → Ward → PU → announced_pu_results chain
    // and sum every party's score across all polling units in the LGA.
    // We deliberately avoid the pre-computed announced_lga_results table.
    public function getLgaSummedResults(int $lgaId): array
    {
        $sql = "
            SELECT 
                apr.party_abbreviation,
                SUM(apr.party_score) AS total_score
            FROM lga l
            JOIN ward w ON l.uniqueid = w.lga_id
            JOIN polling_unit pu ON w.uniqueid = pu.ward_id
            JOIN announced_pu_results apr ON pu.uniqueid = apr.polling_unit_uniqueid
            WHERE l.uniqueid = :lga_id
            GROUP BY apr.party_abbreviation
            ORDER BY total_score DESC
        ";
        return $this->db->query($sql, ['lga_id' => $lgaId])->fetchAll();
    }

    // How many unique polling units does this LGA have?
    public function getPollingUnitCountByLga(int $lgaId): int
    {
        $sql = "
            SELECT COUNT(DISTINCT pu.uniqueid) AS unit_count
            FROM lga l
            JOIN ward w ON l.uniqueid = w.lga_id
            JOIN polling_unit pu ON w.uniqueid = pu.ward_id
            WHERE l.uniqueid = :lga_id
        ";
        $result = $this->db->query($sql, ['lga_id' => $lgaId])->fetch();
        return (int)($result['unit_count'] ?? 0);
    }

    public function getLgaDetails(int $lgaId): ?array
    {
        $sql = "
            SELECT l.*, s.state_name 
            FROM lga l
            JOIN state s ON l.state_id = s.state_id
            WHERE l.uniqueid = :lga_id
        ";
        $result = $this->db->query($sql, ['lga_id' => $lgaId])->fetch();
        return $result ?: null;
    }

    public function getAllParties(): array
    {
        $sql = "SELECT uniqueid, partyid, party_name FROM party ORDER BY party_name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    // Question 3 — Create a new polling unit row, return its new uniqueid
    public function insertPollingUnit(array $data): int
    {
        $sql = "
            INSERT INTO polling_unit (
                polling_unit_id, ward_id, lga_id, state_id,
                polling_unit_number, polling_unit_name, polling_unit_description,
                entered_by_user, date_entered, user_ip_address
            ) VALUES (
                :polling_unit_id, :ward_id, :lga_id, :state_id,
                :polling_unit_number, :polling_unit_name, :polling_unit_description,
                :entered_by_user, NOW(), :user_ip_address
            )
        ";

        $this->db->query($sql, [
            'polling_unit_id'          => $data['polling_unit_id'],
            'ward_id'                  => $data['ward_id'],
            'lga_id'                   => $data['lga_id'],
            'state_id'                 => $data['state_id'],
            'polling_unit_number'      => $data['polling_unit_number'],
            'polling_unit_name'        => $data['polling_unit_name'],
            'polling_unit_description' => $data['polling_unit_description'] ?? '',
            'entered_by_user'          => $data['entered_by_user'],
            'user_ip_address'          => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);

        return (int)$this->db->lastInsertId();
    }

    // Insert one row per party into announced_pu_results
    // Called inside a transaction alongside insertPollingUnit
    public function insertPollingUnitResults(int $pollingUnitUniqueId, array $scores): void
    {
        $sql = "
            INSERT INTO announced_pu_results (
                polling_unit_uniqueid, party_abbreviation, party_score,
                entered_by_user, date_entered, user_ip_address
            ) VALUES (
                :polling_unit_uniqueid, :party_abbreviation, :party_score,
                :entered_by_user, NOW(), :user_ip_address
            )
        ";

        $userIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        foreach ($scores as $partyAbbr => $score) {
            $this->db->query($sql, [
                'polling_unit_uniqueid' => $pollingUnitUniqueId,
                'party_abbreviation'    => $partyAbbr,
                'party_score'           => (int)$score,
                'entered_by_user'       => $_SESSION['username'] ?? 'admin',
                'user_ip_address'       => $userIp,
            ]);
        }
    }

    // Quick search by name or number — used in the topbar search
    public function searchPollingUnits(string $query): array
    {
        $searchTerm = '%' . $query . '%';
        $sql = "
            SELECT 
                pu.uniqueid,
                pu.polling_unit_name,
                pu.polling_unit_number,
                w.ward_name,
                l.lga_name
            FROM polling_unit pu
            JOIN ward w ON pu.ward_id = w.uniqueid
            JOIN lga l ON pu.lga_id = l.uniqueid
            WHERE pu.polling_unit_name LIKE :query 
               OR pu.polling_unit_number LIKE :query2
            ORDER BY pu.polling_unit_name ASC
            LIMIT 20
        ";
        return $this->db->query($sql, [
            'query'  => $searchTerm,
            'query2' => $searchTerm,
        ])->fetchAll();
    }

    public function getPollingUnitsPaginated(int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                pu.uniqueid,
                pu.polling_unit_name,
                pu.polling_unit_number,
                w.ward_name,
                l.lga_name
            FROM polling_unit pu
            JOIN ward w ON pu.ward_id = w.uniqueid
            JOIN lga l ON pu.lga_id = l.uniqueid
            ORDER BY pu.polling_unit_name ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTotalPollingUnitCount(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM polling_unit";
        $result = $this->db->query($sql)->fetch();
        return (int)($result['total'] ?? 0);
    }

    // Make sure we don't get duplicate polling unit numbers
    public function pollingUnitNumberExists(string $number): bool
    {
        $sql = "SELECT COUNT(*) AS cnt FROM polling_unit WHERE polling_unit_number = :number";
        $result = $this->db->query($sql, ['number' => $number])->fetch();
        return ($result['cnt'] ?? 0) > 0;
    }
}
