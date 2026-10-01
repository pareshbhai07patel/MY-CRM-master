<?php

class Company
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Get all companies belonging to a user.
     */
    public function getAllUserCompanies($user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM companies
             WHERE user_id = ?
             ORDER BY company_name ASC"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    /**
     * Get a single company by ID for a specific user.
     */
    public function getCompanyById($id, $user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM companies
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create a new company.
     */
    public function createCompany(
        $company_name,
        $city,
        $sector,
        $email,
        $creation_date,
        $user_id
    ) {
        $stmt = $this->conn->prepare(
            "INSERT INTO companies
                (company_name, city, sector, email, creation_date, user_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssssi",
            $company_name,
            $city,
            $sector,
            $email,
            $creation_date,
            $user_id
        );

        return $stmt->execute();
    }

    /**
     * Update company details.
     */
    public function updateCompany(
        $id,
        $company_name,
        $city,
        $sector,
        $email,
        $creation_date,
        $user_id
    ) {
        $stmt = $this->conn->prepare(
            "UPDATE companies
             SET
                company_name = ?,
                city = ?,
                sector = ?,
                email = ?,
                creation_date = ?
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param(
            "sssssii",
            $company_name,
            $city,
            $sector,
            $email,
            $creation_date,
            $id,
            $user_id
        );

        return $stmt->execute();
    }

    /**
     * Delete a company belonging to a specific user.
     */
    public function deleteCompany($id, $user_id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM companies
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $id, $user_id);

        return $stmt->execute();
    }

    /**
     * Search companies by name, city, or sector.
     */
    public function searchCompanies($user_id, $search)
    {
        $searchTerm = "%" . $search . "%";

        $stmt = $this->conn->prepare(
            "SELECT *
             FROM companies
             WHERE user_id = ?
             AND (
                company_name LIKE ?
                OR city LIKE ?
                OR sector LIKE ?
             )
             ORDER BY company_name ASC"
        );

        $stmt->bind_param(
            "isss",
            $user_id,
            $searchTerm,
            $searchTerm,
            $searchTerm
        );

        $stmt->execute();

        return $stmt->get_result();
    }

    /**
     * Get companies by sector.
     */
    public function getCompaniesBySector($user_id, $sector)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM companies
             WHERE user_id = ?
             AND sector = ?
             ORDER BY company_name ASC"
        );

        $stmt->bind_param("is", $user_id, $sector);
        $stmt->execute();

        return $stmt->get_result();
    }

    /**
     * Get companies by city.
     */
    public function getCompaniesByCity($user_id, $city)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM companies
             WHERE user_id = ?
             AND city = ?
             ORDER BY company_name ASC"
        );

        $stmt->bind_param("is", $user_id, $city);
        $stmt->execute();

        return $stmt->get_result();
    }

    /**
     * Check whether a company already exists for a user.
     */
    public function companyExists($company_name, $user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id
             FROM companies
             WHERE company_name = ?
             AND user_id = ?
             LIMIT 1"
        );

        $stmt->bind_param("si", $company_name, $user_id);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Get total number of companies owned by a user.
     */
    public function getCompanyCount($user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM companies
             WHERE user_id = ?"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return (int) $result['total'];
    }
}
?>
