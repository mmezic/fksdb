<?php

namespace FKS\Authentication\SSO\MySQL;

use FKS\Authentication\SSO\IGSIDHolder;
use PDO;

/**
 * Reads and stores GSID from "real" session (expects its started).
 * Furthemore, GSID may be infered from a token in GET data
 * (this overrides "real" session data).
 * The token is verified against shared MySQL database.
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class TokenGSIDHolder implements IGSIDHolder {

    const TABLE = 'auth_token';
    const URL_PARAM = '_sso';
    const SESSION_KEY = '_sso';

    /**
     * @var PDO
     */
    private $connection;
    private $cachedGSID = false;

    function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function getGSID() {
        if ($this->cachedGSID === false) {
            if (isset($_GET[self::URL_PARAM])) {
                $token = $_GET[self::URL_PARAM];
                unset($_GET[self::URL_PARAM]);
                $this->cachedGSID = $this->getGSIDFromDB($token);
            }
            if (!$this->cachedGSID && isset($_SESSION[self::SESSION_KEY])) {
                $this->cachedGSID = $_SESSION[self::SESSION_KEY];
            }
            // no matter where the value comes from we re-store it to the session
            $this->setGSID($this->cachedGSID);
        }
        return $this->cachedGSID;
    }

    public function setGSID($gsid) {
        if ($gsid) {
            $_SESSION[self::SESSION_KEY] = $gsid;
            $this->cachedGSID = $gsid;
        } else {
            unset($_SESSION[self::SESSION_KEY]);
            $this->cachedGSID = null;
        }
    }

    private function getGSIDFromDB($token) {
        $sql = 'SELECT data FROM `' . self::TABLE . '`
            where token = ?
            and since <= now()
            and (until is null or until >= now())';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array($token));

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        } else {
            return $row['data'];
        }
    }

}
