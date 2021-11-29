<?php

namespace LampSchool\Tests\Support;

require_once __DIR__ . '../../../install/funzioni_install.php';

class Installer
{
    /** @var string */
    private $dbHost;

    /** @var string */
    private $dbName;

    /** @var string */
    private $dbUser;

    /** @var string */
    private $dbPassword;

    /** @var string */
    private $dbTablePrefix;

    /** @var string */
    private $adminUser;

    /** @var string */
    private $adminPassword;

    /** @var string */
    private $installationSuffix;

    /** @var string */
    private $schoolName;

    public function __construct($dbHost, $dbName, $dbUser, $dbPassword, $dbTablePrefix, $adminUser, $adminPassword, $installationSuffix, $schoolName)
    {
        /**
         * TODO: validate data
         */

        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbTablePrefix = $dbTablePrefix;
        $this->adminUser = $adminUser;
        $this->adminPassword = $adminPassword;
        $this->installationSuffix = $installationSuffix;
        $this->schoolName = $schoolName;
    }

    private function getDbConnection()
    {
        $dbCon = \mysqli_connect($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbName);

        \mysqli_set_charset($dbCon, "utf8");

        return $dbCon;
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->checkRequirements())
        {
            $errorMessage = 'Errore: Server non conforme con i criteri d\' installazione. ';
            $errorMessage .= 'Verificare i requisiti minimi di sistema.';
            throw new \RuntimeException($errorMessage);
        }

        $this->executeDbMigration();

        $this->addSchoolName($this->schoolName);

        $this->addAdminPassword($this->adminPassword);

        $this->createSchoolAssets($this->installationSuffix);

        $this->saveIniFile();

    }

    public function saveIniFile()
    {
        /**
         * ORIGINAL CODE
         *
         * ORIGINAL FILE: install/installdb.php (line 83-90)
         *
         * $str = file_get_contents('php-ini.php');
         * $str = str_replace("{DBHOST}", "$par_db_server", $str);
         * $str = str_replace("{DBNAME}", "$par_db_nome", $str);
         * $str = str_replace("{DBUSER}", "$par_db_user", $str);
         * $str = str_replace("{DBPWD}", "$par_db_password", $str);
         * $str = str_replace("{DBPREFIX}", "$par_prefisso_tabelle", $str);
         *
         * file_put_contents('newphp-ini.php', $str);
         */

        $phpIniModel = file_get_contents(__DIR__ . '/../../install/php-ini.php');

        $phpIniModel = str_replace("{DBHOST}", "$this->dbHost", $phpIniModel);
        $phpIniModel = str_replace("{DBNAME}", "$this->dbName", $phpIniModel);
        $phpIniModel = str_replace("{DBUSER}", "$this->dbUser", $phpIniModel);
        $phpIniModel = str_replace("{DBPWD}", "$this->dbPassword", $phpIniModel);
        $phpIniModel = str_replace("{DBPREFIX}", "$this->dbTablePrefix", $phpIniModel);

        file_put_contents(__DIR__ . '/../../php-ini'.$this->installationSuffix.'.php', $phpIniModel);
    }

    public function executeDbMigration()
    {
        /**
         * ORIGINAL CODE - function call: esecuzioneFile($sqlFile, $credenziali)
         *
         * ORIGINAL FILE: install/funzioni_install.php (line 267-289)
         */

        $credenziali = [
            'server' => $this->dbHost,
            'user' => $this->dbUser,
            'password' => $this->dbPassword,
            'nomedb' => $this->dbName,
            'prefisso' => $this->dbTablePrefix
        ];

        $sqlFile = __DIR__ . '/../../install/sql/release/2020.11.sql';

        esecuzioneFile($sqlFile, $credenziali);
    }

    private function addSchoolName($schoolName)
    {
        /**
         * ORIGINAL CODE
         *
         * ORIGINAL FILE: install/installsalva.php (line 75-77)
         *
         * $con = mysqli_connect($par_db_server, $par_db_user, $par_db_password, $par_db_nome);
         * $query = "update $par_prefisso_tabelle" . "tbl_parametri set valore='" . $par_nomescuola . "' where parametro='nome_scuola'";
         * mysqli_query($con, $query) or die("Errore in settaggio nome scuola");
         **/

        $query = "update $this->dbTablePrefix" . "tbl_parametri set valore='" . $schoolName . "' where parametro='nome_scuola'";

        try {
            \mysqli_query($this->getDbConnection(), $query); // or die("Errore in settaggio nome scuola");
        }catch (\Exception $exception){
            throw $exception;
        }
    }

    private function addAdminPassword($plainPassword)
    {
        /**
         * ORIGINAL CODE
         *
         * ORIGINAL FILE: install/installsalva.php (line 80-81)
         *
         * $query = "update $par_prefisso_tabelle" . "tbl_utenti set password=md5(md5('" . $par_passwordadmin . "')) where idutente=0";
         * mysqli_query($con, $query) or die("Errore in impostazione password");
         */

        $query = "update $this->dbTablePrefix" . "tbl_utenti set password=md5(md5('" . $plainPassword . "')) where idutente=0";

        try {
            \mysqli_query($this->getDbConnection(), $query); // or die("Errore in impostazione password");
        }catch (\Exception $exception){
            throw $exception;
        }
    }

    public function createSchoolAssets($installationSuffix)
    {
        /**
         * ORIGINAL CODE
         *
         * ORIGINAL FILE: install/installsalva.php (line 39-48)
         *
         * mkdir("../abc/$par_suffisso_installazione", 0700);
         *
         * mkdir("../lampschooldata/$par_suffisso_installazione", 0700);
         * copy("../abc/index.html", "../abc/$par_suffisso_installazione/index.html");
         * copy("../abc/firmadirigente.png", "../abc/$par_suffisso_installazione/firmadirigente.png");
         * copy("../abc/testata.jpg", "../abc/$par_suffisso_installazione/testata.jpg");
         * copy("../abc/timbro.png", "../abc/$par_suffisso_installazione/timbro.png");
         * //  copy("../abc/*", "../abc/$par_suffisso_installazione");
         * copy("../lampschooldata/index.html", "../lampschooldata/$par_suffisso_installazione/index.html");
         * copy("../css/stile.css", "../css/stile$par_suffisso_installazione.css");
         **/

        $globalAssetsDirectory = __DIR__ . "/../../abc/";
        $schoolAssetsDirectory = __DIR__ . "/../../abc/".$installationSuffix;

        if ($globalAssetsDirectory != $schoolAssetsDirectory)
        {
            if (!is_dir($schoolAssetsDirectory))
            {
                mkdir($schoolAssetsDirectory, 0700);
            }
            copy($globalAssetsDirectory . "index.html", $schoolAssetsDirectory . "/index.html");
            copy($globalAssetsDirectory . "firmadirigente.png", $schoolAssetsDirectory . "/firmadirigente.png");
            copy($globalAssetsDirectory . "testata.jpg", $schoolAssetsDirectory . "/testata.jpg");
            copy($globalAssetsDirectory . "timbro.png", $schoolAssetsDirectory . "/timbro.png");
        }

        $globalDataDirectory = __DIR__ . '/../../lampschooldata/';
        $schoolDataDirectory = __DIR__ . '/../../lampschooldata/' . $installationSuffix;
        if (!is_dir($schoolDataDirectory))
        {
            mkdir($schoolDataDirectory, 0700);
        }
        copy($globalDataDirectory .'/index.html', $schoolDataDirectory . '/index.html');
        copy(__DIR__ . "/../../css/stile.css", __DIR__ . "/../../css/stile$installationSuffix.css");
    }

    public function checkRequirements()
    {
        /**
         * ORIGINAL CODE
         *
         * ORIGINAL FILE: install/installprerequisiti.php (line 21-25)
         *
         * $versionephp = phpversion();
         * $versionephpok = version_compare(substr($versionephp, 0, 3), '5.0', '>=');
         * $autostart = ini_get('session.auto_start');
         * $estensionemysql = extension_loaded('mysqli');
         * $estensionezip = extension_loaded('zip');
         *
         **/

        $result =  false;

        $phpVersion = phpversion();
        $isSupportedPhpVersion = version_compare(substr($phpVersion, 0, 3), '5.0', '>=');
        $parameterAutostart = ini_get('session.auto_start');
        $extensionMysql = extension_loaded('mysqli');
        $extensionZip = extension_loaded('zip');

        if ($isSupportedPhpVersion){
            $result = true;
        }
        if (!$parameterAutostart){
            $result = true;
        }
        if ($extensionMysql){
            $result = true;
        }
        if ($extensionZip){
            $result = true;
        }

        return $result;
    }
}
