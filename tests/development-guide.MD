# GUIDA SVILUPPATORI


## Quick installer

In fase di sviluppo, se non si vuole usare l'installazione tramite UI, è possibile utilizzare
il terminale per un'istallazione rapida. 
Per eseguire lo script:
- copiare il file tests/installer-dev.php.dist in tests/installer-dev.php 
- editare il nuovo file com i parametri necessari.
- eseguire lo script da terminale `php tests/installer-dev.php`

E' possibile eseguire la procedura per ogni istituto/installazione desiderata, è consigliabile
nel caso di installazioni multiple nominare i vari file con il pattern `installer-nome_istituto.php`
o quantomeno usare il prefisso `installer-`, tutti i file con questo prefisso vengono eclusi automaticamente (.gitignore)
dai commit.
