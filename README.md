# LAMPSchool - Il registro elettronico libero italiano

Questo è il codice sorgente di LAMPSchool, uno dei primi registri elettronici progettati per il sistema scolastico italiano. Questo codice ti permette di creare un server LAMPSchool da fornire "chiavi in mano" (_on premise_) o erogare LAMPSchool come servizio (_SaaS_).

LAMPSchool è software libero, rispondendo alle esigenze del [codice dell'amministrazione digitale](https://docs.italia.it/italia/piano-triennale-ict/codice-amministrazione-digitale-docs/it/v2018-09-28/index.html).

## Architettura

LAMPSchool è stato progettato con pochissime dipendenze: PHP, MySQL e jQuery. Questo è stato pensato per semplificare la curva di apprendimento dei contributori, i quali non dovranno necessariamente imparare un ulteriore framework di sviluppo.

## Licenza

La licenza di LAMPSchool ti permette immediatamente di usare, studiare, modificare e diffondere LAMPSchool e ogni tua modifica per qualsiasi finalità, anche per scopi commerciali, a patto che eventuali contributi siano a loro volta rilasciati con queste libertà, per un beneficio collettivo. Dovresti aver ricevuto una copia della licenza [GNU Affero General Public License](https://www.gnu.org/licenses/agpl-3.0.html).

Attenzione: come per ogni software, LAMPSchool viene fornito senza alcuna garanzia, a meno che tu non abbia stipulato un contratto di [assistenza commerciale per LAMPSchool](https://github.com/scaforchio/LAMPSchool#assistenza-commerciale).

## Installazione

Vedi le note di installazione:

* [INSTALL.md](https://github.com/scaforchio/LAMPSchool/blob/master/INSTALL.md#note-di-installazione-di-lampschool)

## Assistenza commerciale

Se non sai a chi rivolgerti per commissionare sviluppi personalizzati, richiedere assistenza commerciale o ottenere garanzie di fornitura di servizi legati a LAMPSchool, segnalalo nel forum:

https://forum.linux.it/t/domande-su-lampschool-giuaschool/264

Se vorresti iniziare a fornire assistenza commerciale su LAMPSchool, prepara una lettera di presentazione e inoltrala in privato ad Italian Linux Society all'indirizzo `direttore@linux.it` con oggetto `Fornitura commerciale LAMPSchool`. Per trasparenza segnala la cosa anche nel forum (dallo stesso link in alto).

## Progetti fratelli

Si segnala anche l'esistenza dei seguenti registri elettronici liberi, attivi sul territorio:

* [registro elettronico giuia@school](https://github.com/trinko/giuaschool) - nato successivamente a LAMPSchool e al momento focalizzato solo sull'istituto I.I.S. Michele Giua

## Segnalazioni

Per aprire una segnalazione o discussione su problemi o funzionalità riguardanti LAMPSchool, vedi qui:

* https://github.com/scaforchio/LAMPSchool/issues

Attenzione: per avere i tuoi sviluppi realizzati in tempi certi, il modo più efficace è farli preventivare e coprire le spese (vedi [assistenza commerciale per LAMPSchool](https://github.com/scaforchio/LAMPSchool#assistenza-commerciale)).

Grazie per ogni idea e contributo!

## Sviluppo

Si incoraggia i contributori (volontari, società di sviluppo, ..) a sviluppare nuove funzionalità per LAMPSchool!

Ogni contributo è bene accetto ma deve essere pensato con retro-compatibilità e rilasciato con una licenza di software libero prima di poter essere unito al ramo principale di sviluppo.

A seconda della modifica fatta al `composer.json` potrebbe essere richiesto un'aggiornamento del file `composer.lock` (da non modificare però a mano). In questo caso lanciare `composer update --lock`.

In caso di domande aprire una issue. Grazie!

## Roadmap

Prossime funzionalità desiderate in LAMPSchool:

* [ ] 2021 nov: completamento interfacciamento con SPID
* [ ] 2022 gen: interfaccia con il SIDI
* [ ] 2022 apr: importazione/esportazione da/per altri gestionali

## Crediti

Grazie a Pietro Tamburrano, l'autore originale di LAMPSchool. Si prega di non contattarlo privatamente per richiedere nuovi sviluppi ma di seguire invece le indicazioni della sezione precedente.

Grazie anche a tutti i contributori:

* [grafico contributi](https://github.com/scaforchio/LAMPSchool/graphs/contributors)
* [elenco contributi dalla prima modifica](https://github.com/scaforchio/LAMPSchool/commits/master?after=ec0c6b3b0b71e1e147ac35344276deb99dd0edaa+209&branch=master)
* [elenco contributi dall'ultima modifica](https://github.com/scaforchio/LAMPSchool/commits/master)

## Sostieni

Se desideri sostenere il progetto LAMPSchool, puoi farlo tramite l'Italian Linux Society:

https://www.ils.org/sostieni/

Grazie per la diffusione!
