<?php

/**
 * Created by PhpStorm.
 * User: pietro
 * Date: 16/06/15
 * Time: 18.20
 */
function getIdMoodle($token, $domainname, $username) {


    /// SETUP - NEED TO BE CHANGED

    $functionname = 'core_user_get_users_by_field';

    $restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'


    $cam = "username";
    $campo = $cam;

    $valore1 = $username;

    $param = array($valore1);
    //$par=array('values' => $param);
    $params = array('field' => $campo, 'values' => $param);

/// REST CALL
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');

    $curl = new curl;

//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';

    // print $params;
    $resp = $curl->post($serverurl . $restformat, $params);

    $utente = json_decode($resp);
    $id = $utente[0]->id;
    return $id;
}

/**
 * Funzione che trasforma i decimali del voto in modificatori
 *
 * @param string $voto
 * @return string
 */
function cambiaPasswordMoodle($token, $domainname, $idutente, $username, $newpassword) {

    $functionname = 'core_user_update_users';

    $restformat = 'xml'; //Also possible in Moodle 2.2 and later: 'json'
    //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
    $user1 = new stdClass();
    $user1->id = $idutente;
    $user1->username = $username;
    $user1->password = $newpassword;
    $users = array($user1);
    $params = array('users' => $users);

/// REST CALL
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    //   print_r($resp);
}

function creaCategoriaMoodle($token, $domainname, $nomecategoria, $siglacategoria, $categoriagenitore) {

    $functionname = 'core_course_create_categories';

    $restformat = 'json';

    $categoria = new stdClass();
    $categoria->name = $nomecategoria;
    $categoria->parent = $categoriagenitore;
    $categoria->idnumber = $siglacategoria;
    $categoria->descriptionformat = 1;
    //print $categoria;
    $categorie = array($categoria);
    $params = array('categories' => $categorie);

    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;

    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    // Sostituire con la restituzione dell'ID del corso creato
    print $resp;
    $categoriacreata = json_decode($resp);
    return $categoriacreata[0]->id;
}

function creaUtenteMoodle($token, $domainname, $username, $password, $cognome, $nome, $email) {


    $functionname = 'core_user_create_users';

    $restformat = 'xml'; //Also possible in Moodle 2.2 and later: 'json'
    //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
    $user1 = new stdClass();

    $user1->username = $username;
    $user1->password = $password;
    $user1->firstname = $nome;
    $user1->lastname = $cognome;
    $user1->email = $email;
    $users = array($user1);
    $params = array('users' => $users);

/// REST CALL
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);

    return $resp;
}

function getCategoriaMoodle($token, $domainname, $siglacat) {

    $functionname = 'core_course_get_categories';

    $richieste = array();


    $restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
    //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $richieste);
    // print $resp;
    $categorie = json_decode($resp);
    foreach ($categorie as $categoria) {
        if ($siglacat == $categoria->idnumber)
            return $categoria->id;
    }
    return -1;
}

function getCorsiMoodle($token, $domainname) {

    $functionname = 'core_course_get_courses';

    $richieste = array();


    $restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
    //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $richieste);
    // print $resp;
    $corsi = json_decode($resp);
    return $resp;
}

function getIdCorsoMoodle($token, $domainname, $siglacorso) {

    $functionname = 'core_course_get_courses';

    $richieste = array();


    $restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
    //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $richieste);
    // print $resp;
    $corsi = json_decode($resp);
    $categorie = json_decode($resp);
    foreach ($corsi as $corso) {
        if ($siglacorso == $corso->shortname)
            return $corso->id;
    }
    return -1;
}

function creaCorsoMoodle($token, $domainname, $nomecorso, $siglacorso, $categoria) {


    $functionname = 'core_course_create_courses';

    $restformat = 'json';

    $corso1 = new stdClass();
    $corso1->fullname = $nomecorso;
    $corso1->shortname = $siglacorso;
    $corso1->categoryid = $categoria;
    $corso1->format = 'topics';
    $corso1->numsections = 10;
    $corso1->maxbytes = 20971520;
    $corso1->lang = "it";

    $corsi = array($corso1);
    $params = array('courses' => $corsi);

    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;

    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    // Sostituire con la restituzione dell'ID del corso creato
    $corsocreato = json_decode($resp);
    return $corsocreato[0]->id;
}

function aggiornaCategoriaCorso($token, $domainname, $idcorso, $idcategoria) {


    $functionname = 'core_course_update_courses';

    $restformat = 'json';

    $corso1 = new stdClass();
    $corso1->id = $idcorso;
    $corso1->categoryid = $idcategoria;

    $corsi = array($corso1);
    $params = array('courses' => $corsi);

    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;

    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    return $resp;
    // Sostituire con la restituzione dell'ID del corso creato
    // $corsocreato=json_decode($resp);
    // return $corsocreato[0]->id;
}

function iscriviUtenteMoodle($token, $domainname, $idcorso, $idutente, $idruolo) {

    // IDRUOLO    3 = docente,  5 = studente

    $functionname = 'enrol_manual_enrol_users';

    $restformat = 'json';

    $iscrizione1 = new stdClass();
    $iscrizione1->roleid = $idruolo;
    $iscrizione1->userid = $idutente;
    $iscrizione1->courseid = $idcorso;


    $iscrizioni = array($iscrizione1);
    $params = array('enrolments' => $iscrizioni);

    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;

    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    // print $resp;
}

function disiscriviUtenteMoodle($token, $domainname, $idcorso, $idutente) {


    $functionname = 'enrol_manual_unenrol_users';

    $restformat = 'json';

    $iscrizione1 = new stdClass();
    $iscrizione1->userid = $idutente;
    $iscrizione1->courseid = $idcorso;


    $iscrizioni = array($iscrizione1);
    $params = array('enrolments' => $iscrizioni);

    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;

    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    print $resp;
}

function getUtentiCorsoMoodle($token, $domainname, $idcorso) {




    $functionname = 'core_enrol_get_enrolled_users';

    $params = array('courseid' => $idcorso);


    $restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
    //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
    //header('Content-Type: text/plain');
    $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname;
    require_once('curl.php');
    $curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
    $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
    $resp = $curl->post($serverurl . $restformat, $params);
    //print $resp;
    //return $resp;
    $corsi = json_decode($resp);
    return $corsi;
}

function costruisciUsernameMoodle($idutente) {


    if ($idutente >= 1000000000) {
        return "doc" . $_SESSION['suffisso'] . ($idutente - 1000000000);
    } else {
        return "al" . $_SESSION['suffisso'] . $idutente;
    }
}
