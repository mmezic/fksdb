<?php

namespace Authentication;

use FKSDB\ORM\ModelLogin;
use FKSDB\ORM\ModelPerson;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use ServiceLogin;
use ServicePerson;
use YearCalculator;

/**
 * Users authenticator.
 */
class PasswordAuthenticator extends AbstractAuthenticator implements IAuthenticator {

    /**
     * @var ServicePerson
     */
    private $servicePerson;

    /**
     * PasswordAuthenticator constructor.
     * @param ServiceLogin $serviceLogin
     * @param YearCalculator $yearCalculator
     * @param ServicePerson $servicePerson
     */
    function __construct(ServiceLogin $serviceLogin, YearCalculator $yearCalculator, ServicePerson $servicePerson) {
        parent::__construct($serviceLogin, $yearCalculator);
        $this->servicePerson = $servicePerson;
    }

    /**
     * Performs an authentication.
     * @param array $credentials
     * @return IIdentity
     * @throws InactiveLoginException
     * @throws InvalidCredentialsException
     * @throws NoLoginException
     * @throws UnknownLoginException
     */
    public function authenticate(array $credentials) {
        list($id, $password) = $credentials;

        $login = $this->findLogin($id);

        if ($login->hash !== $this->calculateHash($password, $login)) {
            throw new InvalidCredentialsException();
        }

        $this->logAuthentication($login);

        $login->injectYearCalculator($this->yearCalculator);

        return $login;
    }

    /**
     * @param $id
     * @return ModelLogin
     * @throws InactiveLoginException
     * @throws NoLoginException
     * @throws UnknownLoginException
     */
    public function findLogin($id) {
        $row = $this->servicePerson->getTable()->where('person_info:email = ?', $id)->fetch();
        $login = null;

        if ($row) {
            $person = ModelPerson::createFromTableRow($row);
            $login = $person->getLogin();
            if (!$login) {
                throw new NoLoginException();
            }
        }
        if (!$login) {
            $login = $this->serviceLogin->getTable()->where('login = ?', $id)->fetch();
        }


        if (!$login) {
            throw new UnknownLoginException();
        }

        if (!$login->active) {
            throw new InactiveLoginException();
        }
        return $login;
    }

    /**
     * @param string $password
     * @param \FKSDB\ORM\ModelLogin $login
     * @return string
     */
    public static function calculateHash($password, $login) {
        return sha1($login->login_id . md5($password));
    }

}
