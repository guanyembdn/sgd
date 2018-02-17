<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1>Sistema de gestió de dades (SGD)</h1>

<h2>Introducció</h2>

<p>Benvingut/benvinguda al <strong>sistema de gestió de dades</strong> (SGD). Aquest sistema permet:</p>
<ul>
<li>Gestionar comptes d'usuaris</li>
<li>Gestionar pagaments</li>
<li>Gestionar grups</li>
<li>Gestionar votacions</li>
<li>Gestionar reunions</li>
</ul>
<p>Es pot accedir a aquestes funcions des de la pàgina principal.<br>
Per anar a la pàgina principal en qualsevol moment, fes clic a "SGD" a dalt de la pàgina.</p>

<h2>Informació general</h2>

<p>En qualsevol taula pots fer clic sobre una columna per ordenar la taula.</p>

<h2>Funcions d'usuari/a</h2>

<h3>Compte</h3>

<p>Una vegada has iniciat sessió amb el teu compte, pots editar la informació pertinent a aquesta dins de la funció "Editar el meu compte". Introdueix la teva informació i desa-la. Si alguna vegada la tens que canviar, torna a fer el mateix.<br>
Algunes informacions només les poden canviar els administradors del sistema.<br>
També pots canviar el disseny que utilitza el sistema (<i>Moderne</i> o <i>Simple</i>) per mostrar les pagines. Cada usuari te la seva preferencia.<br>
Es posible que <i>Simple</i> sigui mes apropiat per treballar de forma mes eficient.<br>
<i>Moderne</i> pot ser mes atractiu visualment.</p>

<h3>Gestió pagaments</h3>

<p>Si vols establir un pagament periòdic tens que anar a "Gestió meus pagaments" des de la pàgina principal. Necessitaràs dades com: els teus noms i cognoms, el teu codi IBAN de la teva compte bancaria, i el codi BIC del teu banc (busca-ho).<br>
Quan hagis introduït aquestes dades, comprova que tot sigui correcte i fes clic al botó "Enviar". Això et mostrara una informació del que tindràs que pagar.<br>
Si estàs d'acord, fes clic al botó "Confirmar". Això establirà un pagament periòdic.</p>

<h3>Grups</h3>

<p>Per treballar conjuntament amb altres persones, pots utilitzar la funció de grups. Al apartat "Els meus grups", trobaràs una llista dels grups del que ets membre, a més dels públics i semipúblics.<br>
Pots entrar a la pàgina d'un grup seleccionant l'opció "Veure" dins d'aquesta pàgina.<br>
Si vols crear un grup, tens que sol·licitar-ho als administradors del sistema.<br>
Tothom pot veure la informació dels grups semipúblics; però tens que sol·licitar la adhesió als administradors del grup.<br>
Tothom pot unir-se a un grup públic fent clic a un botó dins de la pàgina del grup.<br>
Els grups privats només els poden veure els membres del grup i els administradors del sistema.<br>
Hi ha tres tipus de membre d'un grup (permisos/rol):</p>
<ul>
<li><strong>Pot veure</strong>: Aquest tipus de membre pot veure el grup i la llista de membres del grup.</li>
<li><strong>Usuari/a</strong>: Pot veure i pot votar.</li>
<li><strong>Administrador/a</strong>: Pot veure, votar, editar la informació, afegir i eliminar membres dels grups, canviar el tipus de membre, i crear votacions.</li>
</ul>
<p>Els administradors d'un grup poden crear una votació anant a la pàgina del grup i seleccionant "Afegir votació" dins de la secció "Tasques del grup". Només poden votar els membres del grup.<br>
També tenen la opció de crear una reunió del grup. Hi ha un botó per convocar tots els membres del grup. També es poden convocar membres específics. Només els administradors poden marcar qui ha assistit.<br>
També poden enviar e-mails als membres del seu grup des de l'opciò "Enviar e-mail" a la mateixa pàgina.</p>

<h3>Les meves votacions</h3>
<p>Aquí pots veure les votacions globals (per a tots en general) i les del teus grups, i pots accedir per votar.</p>

<h3>Les meves reunions</h3>
<p>Pots veure les reunions a les que has sigut convocat i expresar si assistiras o no.</p>

<?php
if(isadmin()){
?>

<h2>Funcions d'administració</h2>

<h3>Tipus de compte</h3>

<p>Tothom el que utilitza el sistema te una compte. Hi ha diversos tipus de compte amb diferents permisos:</p>
<ul>
<li><strong>Compte no verificada</strong>: No pot accedir.</li>
<li><strong>Administrador/a</strong>: Pot fer-ho tot.</li>
<li><strong>Compte verificada</strong>: En realitat aquest nom es incorrecte: no han sigut verificades, els usuaris auto-registrats començaran anomenats així. Pot accedir a editar informació propia i gestionar pagaments propis.</li>
<li><strong>Compte verificada completament</strong>: Pot accedir a totes les opcions dels usuaris.</li>
<li><strong>Desactivada</strong>: No pot accedir.</li>
</ul>

<h3>Gestor de comptes</h3>

<p>Pots veure totes les comptes registrats en el sistema. Pots editar la seva informació. Pots canviar els tipus de compte. Pots eliminar comptes al editar una compte, a traves d'un enllaç al peu de la pàgina. Pots afegir noves comptes.<br>
Pots filtrar la informació introduint dades als camps.</p>

<h3>Gestor de grups</h3>

<p>Pots veure tots els grups. Afegir nous grups, eliminar grups i editar informació de tots els grups.</p>

<h3>Gestor de votacions</h3>

<p>Pots veure les votacions globals i de tots els grups. Pots crear votacions globals, tothom podrà votar. Pots tancar i eliminar votacions.<br>
Hi ha 3 tipus de votació:<br>
<ul>
<li><strong>Selecció única</strong>: Només es pot seleccionar una opció.</li>
<li><strong>Selecció múltiple</strong>: Es poden seleccionar múltiples opciones.</li>
<li><strong>Cumulatiu</strong>: El votant assigna a cada opció un número de vots. El número de vots possibles per persona es assignat al crear la votació.</li>
</ul>
</p></p>

<h3>Gestor de reunions</h3>

<p>Pots veure les votacions globals i de tots els grups. Pots crear reunions globals. Pots tancar reunions.<br>
Una vegada has creat una reunió a una data i hora determinada, podrás especificar el Lloc, Ordre del dia i Acta de la reunió.<br>
Pots convocar a persones específiques, i hi ha un botó per convocar a tots els membres registrats al sistema.<br>
Pots actualitzar la reunió en tot moment. Pots modificar l'estatus de cada persona, si ha sigut convocat, si assistirá o si ha assistit.<br>
Els usuaris poden marcar si assitirán o no, pero només el/la administrador/a pot marcar els que han assistit.</p>

<h3>Administració de pagaments</h3>

<p>Pots generar informes dels pagaments periòdics dels membres, i generar fitxers SEPA "Direct Debit" per automatitzar els pagaments.<br>
Per fer aixo tendrás que seleccionar el mes del que vols generar el informe. Cap tenir en compte que si seleccionas un mes pasat es generará el informe en base a informació actual, i si un membre ha cancelat el seu pagament periódic no quedará registrat en el informe, encara si a aquest membre el tocaba pagar aquest mes.<br>
Per generar un fitxer SEPA, tens que introduir informació sobre el nom, l'identificador privat, i el IBAN de l'entitat que rebrà el pagament, a més del codi BIC del seu banc.<br>
Una vegada introduïda aquesta informació, confirma que sigui correcta i utilitza el botó "Desa". Fet això podràs descarregar el fitxer SEPA.</p>

<h4>Informe de previsió dels pagaments</h4>

<p>Pots fer una previsió de quanta quantitat es rebrà en els pròxims mesos.<br>
Per fer això accedeix a administració dels pagaments, i fes clic al enllaç "Previsió pagaments".<br>
Selecciona de quin mes a quin mes vols fer la previsió.<br>
Una vegada has fet la previsió es mostraran els resultats en format taula.<br>
Pots elegir descarregar la informació com a CSV.<br>
Pots obrir un CSV amb el programa <a href="https://www.libreoffice.org/"><i>LibreOffice Calc</i></a>.</p>

<h3>Enviar e-mail</h3>
<p>Com a administrador pots enviar e-mails globals.<br>
Pots fer això des de l'opció "Enviar e-mail global" des de la pàgina principal.<br>
Introdueix el Assumpte, Contingut del mail i activa l'opció "Contingut HTML" si vols enviar codi HTML.<br>
Pots seleccionar si vols enviar només a les comptes que fan pagaments periòdics, a les que no, i a les dues.<br>
Pots seleccionar el tipus de comptes que rebran el e-mail.<br>
Pots seleccionar si vols enviar el e-mail a uns barris concrets.</p>

<h3>Exportar CSV</h3>
<p>Els gestors tenen opció de exportar dades com a CSV.<br>
Una vegada descarregat, pots obrir un CSV amb el programa <a href="https://www.libreoffice.org/"><i>LibreOffice Calc</i></a>.<br>
Aixo serveix per traballar a les dades.<br>
Pero <strong>NO</strong> serveix per fer un backup. No és suficient i fa falta fer backups reals.<br>
Per fer un backup sería més útil utilitzar un backup SQL o del sistema complet.</p>

<?php
}
?>

<p><a href="/">Torna a la pàgina principal...</a></p>

<?php
require_once "internal/foot.php";
?>