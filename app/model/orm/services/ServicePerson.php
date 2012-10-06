<?php

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServicePerson extends AbstractServiceSingle {

    protected static $staticTableName = DbNames::TAB_PERSON;
    protected static $staticModelClassName = 'ModelPerson';

    /**
     * @param NConnection $connection
     * @return ServicePerson
     */
    public static function getInstance(NConnection $connection = null) {
        if (!isset(self::$instances[self::$staticTableName])) {
            if ($connection === null) {
                $connection = NEnvironment::getService('nette.database.default');
            }
            self::$instances[self::$staticTableName] = new self(self::$staticTableName, $connection);
            self::$instances[self::$staticTableName]->modelClassName = self::$staticModelClassName;
        }
        return self::$instances[self::$staticTableName];
    }

}

?>
