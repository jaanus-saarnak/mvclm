<?php
/**
 * Base model. A thin static wrapper over mysqli.
 *
 * Every method opens its own connection through getDB() and does not reuse it,
 * so one page rendering several queries opens several connections. Character
 * set is forced to utf8mb4 on connect.
 *
 * Failures throw mysqli_sql_exception rather than returning false, so callers
 * get the framework error handler unless they catch. Nothing here checks a
 * return value for that reason.
 *
 * Run this on PHP 8.1 or newer. Older versions return false instead of
 * throwing, and no code in this file would notice.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

use RuntimeException;

/**
 * Base model providing database operations using mysqli.
 */
class Model
{
    /**
     * Establish a mysqli database connection.
     *
     * @return \mysqli Database connection
     * @throws \mysqli_sql_exception If the connection or the character set fails
     */
    protected static function getDB(): \mysqli
    {
        $mysqli = new \mysqli(
            CONFIG['db_host'],
            CONFIG['db_user'],
            CONFIG['db_password'],
            CONFIG['db_name']
        );

        $mysqli->set_charset("utf8mb4");

        return $mysqli;
    }

    /**
     * Execute a query that retrieves multiple rows.
     *
     * @param string $query The SQL query
     * @return array Array of result rows
     * @throws \mysqli_sql_exception If the query fails
     */
    public static function getMultiRow(string $query): array
    {
        $mysqli = self::getDB();

        $result = $mysqli->query($query);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->close();
        return $rows;
    }

    /**
     * Execute a prepared query that retrieves multiple rows.
     *
     * Always pass the values. An empty array throws. For a query with no
     * placeholders, use getMultiRow().
     *
     * @param string $query The SQL query with placeholders
     * @param array $bindVariablesArray Parameters to bind
     * @return array Array of result rows
     * @throws RuntimeException If no bind variables are provided
     * @throws \mysqli_sql_exception If the query fails
     */
    public static function getMultiRowPrepared(string $query, array $bindVariablesArray): array
    {
        if (count($bindVariablesArray) < 1) {
            throw new RuntimeException("Bind variable missing");
        }

        $mysqli = self::getDB();
        $sqlQuery = $mysqli->prepare($query);

        self::sqlBind($sqlQuery, $bindVariablesArray);

        $sqlQuery->execute();

        $result = $sqlQuery->get_result();
        $sqlQuery->close();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Execute a query that retrieves a single row.
     *
     * @param string $query The SQL query
     * @return array|false Row data or false if not found
     * @throws \mysqli_sql_exception If the query fails
     */
    public static function getSingleRow(string $query): array|false
    {
        $mysqli = self::getDB();
        $queryResult = $mysqli->query($query);

        $result = $queryResult->fetch_assoc();
        $queryResult->close();

        return $result !== null ? $result : false;
    }

    /**
     * Execute a prepared query that retrieves a single row.
     *
     * Always pass the values. An empty array throws. For a query with no
     * placeholders, use getSingleRow().
     *
     * @param string $query The SQL query with placeholders
     * @param array $bindVariablesArray Parameters to bind
     * @return array|false Row data or false if not found
     * @throws RuntimeException If no bind variables are provided
     * @throws \mysqli_sql_exception If the query fails
     */
    public static function getSingleRowPrepared(string $query, array $bindVariablesArray): array|false
    {
        if (count($bindVariablesArray) < 1) {
            throw new RuntimeException("Bind variable missing");
        }

        $mysqli = self::getDB();
        $sqlQuery = $mysqli->prepare($query);

        self::sqlBind($sqlQuery, $bindVariablesArray);

        $sqlQuery->execute();

        $result = $sqlQuery->get_result();
        $sqlQuery->close();

        $row = $result->fetch_assoc();
        return $row !== null ? $row : false;
    }

    /**
     * Execute a query that doesn't return rows.
     *
     * @param string $query The SQL query
     * @return int|null Insert ID if applicable, null otherwise
     * @throws \mysqli_sql_exception If the query fails
     */
    public static function sqlQuery(string $query): ?int
    {
        $mysqli = self::getDB();

        $mysqli->query($query);

        return $mysqli->insert_id ?: null;
    }

    /**
     * Execute a prepared query that doesn't return rows.
     *
     * @param string $query The SQL query with placeholders
     * @param array $bindVariablesArray Parameters to bind
     * @return int|null Insert ID if applicable, null otherwise
     * @throws RuntimeException If no bind variables are provided
     * @throws \mysqli_sql_exception If the query fails
     */
    public static function sqlQueryPrepared(string $query, array $bindVariablesArray): ?int
    {
        if (count($bindVariablesArray) < 1) {
            throw new RuntimeException("Bind variable missing");
        }

        $mysqli = self::getDB();
        $sqlQuery = $mysqli->prepare($query);

        self::sqlBind($sqlQuery, $bindVariablesArray);

        $sqlQuery->execute();

        $insertId = $mysqli->insert_id ?: null;
        $sqlQuery->close();
        
        return $insertId;
    }

    /**
     * Bind variables to a prepared statement.
     *
     * @param \mysqli_stmt $queryPrepare The prepared statement
     * @param array $bindVariablesArray Parameters to bind (max 10)
     * @return void
     * @throws RuntimeException If no variables are given, or more than 10
     * @throws \mysqli_sql_exception If binding fails
     */
    public static function sqlBind(\mysqli_stmt $queryPrepare, array $bindVariablesArray): void
    {
        $count = count($bindVariablesArray);

        if ($count === 0) {
            throw new RuntimeException("mysqli error: Bind variable not found");
        }
        
        if ($count > 10) {
            throw new RuntimeException("mysqli error: Too many variables to bind (max 10)");
        }

        $types = str_repeat('s', $count);
        $queryPrepare->bind_param($types, ...$bindVariablesArray);
    }
}

