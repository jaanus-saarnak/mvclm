<?php
/**
 * MvcLM v1.0 - MVC Framework with Login Manager
 * Copyright (c) 2021 Jaanus Saarnak
 *
 * LICENSE AND DISCLAIMER
 *
 * THIS SOFTWARE CAN BE USED ON ONE DOMAIN FOR TESTING USE ONLY.
 * ALL OTHER RIGHTS ARE RESERVED.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Software website: https://www.MvcLM.com
 */

namespace Core;


/**
 * Base model
 */
class Model
{
    /**
     * @return \mysqli
     * @throws \Exception
     */
    protected static function getDB()
    {
        $mysqli = new \mysqli(
            CONFIG['db_host'],
            CONFIG['db_user'],
            CONFIG['db_password'],
            CONFIG['db_name']
        );

        /**
         * Change character set to utf8
         */
        if (!$mysqli->set_charset("utf8")) {

            throw new \Exception("Error loading character set utf8");
        }

        if (!$mysqli->connect_errno) {

            return $mysqli;

        } else {

            throw new \Exception("mysqli connection error: " .  $mysqli->connect_error);
        }
    }


    /**
     * @param string $query
     * @return array|bool
     * @throws \Exception
     */
    static function getMultiRow($query)
    {
        $mysqli = Model::getDB();

        if (!$result = $mysqli->query($query)) {

            throw new \Exception("mysqli error: " .  $mysqli->error);
        }

        $return = [];
        while ($results = $result->fetch_assoc()) {

            $return[] = $results;
        }

        $result->close();

        return $return;
    }


    /**
     * @param string $query
     * @param array $bindVariablesArray
     * @return array|bool
     * @throws \Exception
     */
    static function getMultiRowPrepared($query, $bindVariablesArray)
    {
        if (!is_array($bindVariablesArray)) {

            return false;
        }

        if (count($bindVariablesArray) == 0) {

            return Model::getMultiRow($query);
        }

        $mysqli = Model::getDB();
        $sqlQuery = $mysqli->prepare($query);

        if ($sqlQuery === false) {

            throw new \Exception("prepare() failed: " .  $mysqli->error);
        }

        Model::sqlBind($sqlQuery, $bindVariablesArray);

        $executeResult = $sqlQuery->execute();

        if ($executeResult === false) {

            throw new \Exception("execute() failed: " .  $mysqli->error);
        }

        $result = $sqlQuery->get_result();
        $sqlQuery->close();

        $return = [];
        while ($resultRow = $result->fetch_assoc()) {

            $return[] = $resultRow ;
        }
        return $return;
    }


    /**
     * @param string $query
     * @return array|bool|null
     * @throws \Exception
     */
    static function getSingleRow($query)
    {
        $mysqli = Model::getDB();

        $queryResult = $mysqli->query($query);

        if ($queryResult === false) {
            throw new \Exception("mysqli error: " .  $mysqli->error);
        }

        $results = $queryResult->fetch_assoc();
        $queryResult->close();

        if ($results !== null) {

            return $results;

        } else {

            return false;
        }
    }


    /**
     * @param string $query
     * @param array $bindVariablesArray
     * @return array|bool|null
     * @throws \Exception
     */
    static function getSingleRowPrepared($query, $bindVariablesArray)
    {
        if (!is_array($bindVariablesArray) || count($bindVariablesArray) < 1) {

            return self::getSingleRow($query);
        }

        $mysqli = Model::getDB();

        $sqlQuery = $mysqli->prepare($query);

        if ($sqlQuery === false) {

            throw new \Exception("prepare() failed: " .  $mysqli->error);
        }

        Model::sqlBind($sqlQuery, $bindVariablesArray);

        $executeResult = $sqlQuery->execute();

        if ($executeResult === false) {

            throw new \Exception("execute() failed: " .  $mysqli->error);
        }

        $result = $sqlQuery->get_result();
        $sqlQuery->close();

        $return = $result->fetch_assoc();

        if ($return !== null) {

            return $return;

        } else {

            return false;
        }
    }


    /**
     * @param string $query
     * @return mixed
     * @throws \Exception
     */
    static function sqlQuery($query)
    {
        $mysqli = Model::getDB();

        $queryResult = $mysqli->query($query);

        if ($queryResult === false) {

            throw new \Exception("mysqli error: " .  $mysqli->error);
        }

        if (isset($mysqli->insert_id)) {

            return $mysqli->insert_id;
        }
    }


    /**
     * @param string $query
     * @param array $bindVariablesArray
     * @return mixed
     * @throws \Exception
     */
    static function sqlQueryPrepared($query, $bindVariablesArray)
    {
        if (!is_array($bindVariablesArray) || count($bindVariablesArray) < 1) {

            throw new \Exception("Bind variable missing");
        }

        $mysqli = Model::getDB();

        $sqlQuery = $mysqli->prepare($query);

        if ($sqlQuery === false) {

            throw new \Exception("prepare() failed: " .  $mysqli->error);
        }

        Model::sqlBind($sqlQuery, $bindVariablesArray);

        $executeResult = $sqlQuery->execute();

        if ($executeResult === false) {

            throw new \Exception("execute() failed: " .  $mysqli->error);
        }

        if (isset($mysqli->insert_id)) {

            return $mysqli->insert_id;
        }

        $sqlQuery->close();
    }


    /**
     * @param string $queryPrepare
     * @param array $bindVariablesArray
     * @throws \Exception
     */
    static function sqlBind($queryPrepare, $bindVariablesArray)
    {
        if (count($bindVariablesArray) == 1) {

            $result = $queryPrepare->bind_param(
                's',
                $bindVariablesArray[0]
            );

        } elseif (count($bindVariablesArray) == 2) {

            $result = $queryPrepare->bind_param(
                'ss',
                $bindVariablesArray[0],
                $bindVariablesArray[1]
            );

        } elseif (count($bindVariablesArray) == 3) {

            $result = $queryPrepare->bind_param(
                'sss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2]
            );

        } elseif (count($bindVariablesArray) == 4) {

            $result = $queryPrepare->bind_param(
                'ssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3]
            );

        } elseif (count($bindVariablesArray) == 5) {

            $result = $queryPrepare->bind_param(
                'sssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3],
                $bindVariablesArray[4]
            );

        } elseif (count($bindVariablesArray) == 6) {

            $result = $queryPrepare->bind_param(
                'ssssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3],
                $bindVariablesArray[4],
                $bindVariablesArray[5]
            );

        } elseif (count($bindVariablesArray) == 7) {

            $result = $queryPrepare->bind_param(
                'sssssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3],
                $bindVariablesArray[4],
                $bindVariablesArray[5],
                $bindVariablesArray[6]
            );

        } elseif (count($bindVariablesArray) == 8) {

            $result = $queryPrepare->bind_param(
                'ssssssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3],
                $bindVariablesArray[4],
                $bindVariablesArray[5],
                $bindVariablesArray[6],
                $bindVariablesArray[7]
            );

        } elseif (count($bindVariablesArray) == 9) {

            $result = $queryPrepare->bind_param(
                'sssssssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3],
                $bindVariablesArray[4],
                $bindVariablesArray[5],
                $bindVariablesArray[6],
                $bindVariablesArray[7],
                $bindVariablesArray[8]
            );

        } elseif (count($bindVariablesArray) == 10) {

            $result = $queryPrepare->bind_param(
                'ssssssssss',
                $bindVariablesArray[0],
                $bindVariablesArray[1],
                $bindVariablesArray[2],
                $bindVariablesArray[3],
                $bindVariablesArray[4],
                $bindVariablesArray[5],
                $bindVariablesArray[6],
                $bindVariablesArray[7],
                $bindVariablesArray[8],
                $bindVariablesArray[9]
            );

        } elseif (count($bindVariablesArray) > 10) {

            throw new \Exception("mysqli error: Too many variables to bind");

        } else {

            throw new \Exception("mysqli error: Bind variable not found");
        }

        if (isset($result) && $result === false) {

            throw new \Exception("bind_param() failed: " .  $queryPrepare->error);
        }
    }
}

