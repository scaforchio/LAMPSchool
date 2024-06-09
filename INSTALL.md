# Note di installazione di LAMPSchool

## Preambolo

Nonostante LAMPSchool sia software libero, questo purtroppo non significa che
chiunque abbia automaticamente tutte le competenze per usarlo, studiarlo, modificarlo
e diffonderlo in stabilità e sicurezza. Non perchè LAMPSchool sia particolarmente
sofisticato ma perché la sicurezza informatica di qualsiasi software lo è.

Il modo più efficace e sicuro per prendersi cura del tuo registro elettronico
e del tuo server è attivare un contratto di assistenza con il tuo tecnico o
consulente locale di riferimento, esperto in PHP e in pubblica amministrazione.
Incoraggiamo i tuoi consulenti a lavorare su ogni contributo per LAMPSchool,
facendo solo attenzione che questi contributi siano rilasciati sotto la stessa licenza
libera (la GNU AGPL).

Grazie mille e buona installazione!

## Requisiti

LAMPSchool è un semplice applicativo PHP e MySQL. Questo è apprezzabile perchè è
compatibile con praticamente qualsiasi server web.

Sistemi operativi consigliati:

* Debian GNU/Linux stable
* Ubuntu LTS

Software richiesto:
* PHP >= 7.3
* MySQL o MariaDB
* Composer

Versioni consigliate:
* PHP 8.1
* MariaDB 10.5

Specifiche consigliate per 100 persone:
* RAM: 1G liberi
* storage: 10G liberi
* CPU: 2 core

Per migliorare le performance puoi aumentare il numero di core e aumentare la velocità del disco adottando SSD.

NOTA: Quasi sicuramente hai già un server con specifiche superiori a quelle qui consigliate.   
NOTA BENE: AlterVista NON È una piattaforma supportata siccome è nota causare svariate problematiche con LAMPSchool.

## Installazione di LAMPSchool

Questi sono i passi per un'installazione da zero, singola o multi-ambiente di LAMPSchool.

NOTA: Se in qualsiasi punto ci fossero dei dubbi, consulta il tuo tecnico o consulente
locale di riferimento, esperto in PHP e sistemi GNU/Linux.

Prima di tutto è necessario trasferire via FTP o GIT (metodo consigliato)
i file del programma su una cartella del nostro web server.

Successivamente, bisogna scaricare le dipendenze composer. Per fare questo, una volta installato composer sul server
(vedi [Installazione Composer](https://getcomposer.org/download/)) bisogna lanciare nella cartella root il comando `composer install`.

Considerando che ogni anno la procedura potrebbe essere ripetuta
e che quindi, dopo qualche tempo, sul nostro web server potrebbero
venirsi a trovare parecchie, distinte istanze di LAMPSchool,
consigliamo di adottare un sistema che le renda facilmente riconoscibili.
Un'idea potrebbe essere quella di inserire l'anno scolastico
all'interno del nome, ad esempio: /lampschool_2018_2019,
oppure /registro_2019_2020 e così via.

Prima di proseguire è necessario accertarsi che le seguenti cartelle
siano scrivibili:
* abc
* install
* lampschooldata
* buffer

Sulla maggior parte dei servizi di hosting lo sono di default e non serve
assolutamente intervenire per modificarne gli attributi.
Anzi, alcuni segnalano che su Aruba ogni modifica manuale potrebbe impedire
la corretta installazione.

Via browser dobbiamo ora raggiungere la cartella `/install/` della nostra
nuova installazione. Questo significa che, aperto il browser web
(consigliamo Firefox o Chromium o Chrome, da escludere browser obsoleti come
Internet Explorer, poiché genera degli errori con i menù dinamici
all'interno del registro), dovremo digitare nella barra degli indirizzi
il percorso esatto, che avrà una forma simile a questa:

* http://www.miodominio.it/registro_2019_2020/install/

Oppure questo, più completo:

* http://www.miodominio.it/registro_2019_2020/install/index.php

A questo punto partirà la procedura di installazione.

Il primo passaggio prevede, da parte del programma d'installazione,
la verifica di alcuni parametri. Se tutto risulterà regolare, si potrà procedere
con il successivo passaggio, che prevede l'inserimento dei dati di collegamento al database.
Essi sono:
- il nome host, cioè l'indirizzo IP del server dove si trova il nostro database;
- il nome del database che intendiamo utilizzare;
- il nome utente (user) per poter accedere al database;
- la password;
- il prefisso che intendiamo assegnare, o meno, alle tabelle;
- il suffisso della nostra installazione;
- il nome della scuola;
- la password dell'amministratore.

I primi quattro dati ci sono sicuramente stati comunicati dal nostro fornitore di hosting.

Il suffisso dell'installazione serve nel caso si vogliano installare sullo stesso server
più ambienti (scuole diverse, primaria/secondaria, ecc.).
Se si è certi di non dover installare ulteriori ambienti si può lasciare vuoto il campo.
Se invece si prevedono delle multi-installazioni, è necessaria la sua compilazione,
che deve essere fatta in modo da poter riconoscere facilmente le singole installazioni/ambienti,
ad esempio: scuola01, scuola02, scuola03 e via dicendo.
Il prefisso delle tabelle, pur non essendo strettamente necessario
per procedere all'installazione, è fortemente raccomandato ed è praticamente idispensabile
in caso di multi installazione,
per poter utilizzare al meglio lo spazio e i d-base che ci fornisce
il nostro provider.
Aruba, ad esempio, dà in uso solo 5 d-base per ogni contratto.
Nulla vieta di installare più registri sullo stesso d-base,
basta semplicemente distinguere le varie tabelle attraverso, appunto, il prefisso
che siamo chiamati ad inserire a questo punto dell'installazione.
Consigliamo di adottare un prefisso simile al seguente: as1920_ che l'anno successivo
diventerà as2021_ e via dicendo.
L'underscore serve per separare e distinguere facilmente il prefisso dal nome della tabella.
A questo punto basta premere il pulsante e il programma d'installazione procederà
alla creazione nel d-base delle tabelle necessarie e alla creazione
del file php-ini nella radice della nostra nuova installazione.
Nel caso si sia inserito un suffisso, il file di inizializzazione (php-in.php) avrà il formato:
php-inisuffisso.php.

Durante il processo d'installazione, verranno inoltre create nella cartella abc e
nella cartella lampschooldata delle sottocartelle che conterranno rispettivamente
le immagini (da trasferire via ftp) e i dati (log e documenti caricati) della installazione specifica.

Nel caso volessimo installare più ambienti, finita la prima installazione
per procedere alle successive non dovremmo fare altro che raggiungere nuovamente
via browser la cartella install e ripetere le operazioni già effettuate per la prima
installazione anche per i successivi ambienti/scuole, inserendo i dati opportuni.
L'operazione va ripetuta tante volte quanti sono gli ambienti che vogliamo installare.
Ad esempio: se un Istituto Comprensivo avesse 3 plessi di scuola primaria
e uno di scuola secondaria di primo grado, potremmo voler installare 4 registri (uno per ogni plesso).
In questo caso sarebbe comodo gestire una multi-installazione, ripetendo le procedure
più sopra indicate per 4 volte ed inserendo per ogni plesso un suffisso diverso.

L'ultima operazione da fare (solo dopo aver effettuato l'installazione di tutti gli ambienti)
è una procedura di sicurezza e consiste nel cancellare la cartella /install dal server web.

Una volta concellata la cartella /install, non sarà ovviamente più possibile procedere ad ulteriori installazioni,
quindi se per qualche motivo in futuro avessimo la necessità di installare ulteriori ambienti,
non potremmo farlo senza aver prima ricaricato sul web-server la cartella /install della versione installata in precedenza.

Nel caso, per qualche motivo, non siano stati creati i file php-ini si potrà procedere
manualmente alla loro creazione:
- effettuare una copia del file php-ini.php dalla cartella install nella cartella principale;
- rinominarla eventualmente con il suffisso dell'ambiente;
- aprirla in locale con un semplice editor testuale
 (il blocco note o Notepad++ andranno benissimo);
- impostare i parametri di accesso al d-base come indicato
 più sopra per la procedura automatica;
- salvare il file e trasferirlo nella cartella principale
 del nostro server web

Per personalizzare le stampe dei dati che può produrre Lampschool
(pagelle, tabelloni, comunicazioni...) è necessario sostituire,
nella cartella /abc (o nella sottocartella relativa all'ambiente installato)
i file immagine presenti, con altri aventi
lo stesso nome e le stesse dimensioni, ma con contenuti relativi
ai dati della propria scuola.

Per lanciare il programma è necessario accedere via browser
(sempre Chrome o Firefox) alla cartella principale di LAMPSchool.
L'indirizzo da digitare nella barra degli indirizzi potrebbe essere
simile a questo:

* http://www.miodominio.it/registro_2019_2020/

A seconda di come abbiamo denominato la cartella contenente
l'installazione del nostro registro elettronico.
Facendo click sul logo di LAMPschool si arriva alla pagina di login.

Le credenziali di accesso iniziali dell'amministratore sono:

* Utente: `adminlamp`
* Password: `admin`

Si raccomanda vivamente di cambiare la password al primo accesso.

## Aggiornamento di LAMPSchool

ATTENZIONE!
Per evitare possibili perdite di dati dovute ad errori in fase di aggiornamento,
è sempre buona norma effettuare un backup del database e della cartella
del registro elettronico sul web server prima di procedere all'aggiornamento.
Si consiglia di effettare l'aggiornamento in orari di scarso traffico sul registro
o dopo aver inibito l'accesso allo stesso.

Per l'aggiornamento si dovrà sostituire sul server l'intero contenuto della cartella
di installazione con la versione aggiornata (tramite `git pull` o con un trasferimento FTP) 
AD ECCEZIONE DELLA CARTELLA `/abc` E DELLA CARTELLA `/lampschooldata`.
Una volta trasferiti i file accedere al registro con l'utenza di amministrazione e verificare
in fondo al log ([STATISTICHE E RIEPILOGHI][VISUALIZZA LOG]) se l'aggiornamento 
del database è andato a buon fine.
Nel caso di errori evidenziati nel log inviare le righe del log che evidenziano l'errore 
ai dati di contatto che trovate in fondo a questo file.

# Contatti

Prima di fare qualsiasi segnalazione dai un'occhiata al README:

* https://github.com/scaforchio/LAMPSchool#readme

Per segnalazioni tecniche e discussioni funzionalità:

* https://github.com/scaforchio/LAMPSchool/issues

Per discussioni generiche con i volontari di Italian Linux Society:

* https://forum.linux.it/t/domande-su-lampschool-e-giuia-school-registri-elettronici-liberi/264

Grazie per il tuo interesse in LAMPSchool, il registro elettronico libero!
