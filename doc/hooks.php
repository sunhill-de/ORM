<?php
/**
@page Hooks Nähere Erläuterungen zu Hooks
@section Namenskonventionen
* Hooks, die VOR dem eigentlichen Ereignis ausgeführt werden, enden auf -ing
* Hooks, die NACH dem eigentlichen Ereignis ausgeführt werden, enden auf -ed
Beispiel:
* committing() wird vor dem commit ausgeführt
* comitted() wird nach dem commit ausgeführt.
Dabei muss nicht jedes Ereignis beide Hooks anbieten. Details dazu sind der jeweiligen Dokumentation zu entnehmen.

@section Rückgabewert
Die aufrufende Instanz erwaret von einem Hook keinen Rückgabewert (void). Sollte ein ING-Hook das nachfolgende Ereignis abbrechen wollen, muss er eine Exception werfen.
ED-Hooks sollten keine Exception werfen, da das Ereignis bereits stattgefunden hat. Ausnahmen sind vom Ereignis unabhängige Fehlerzustände.

@section Hooktargets
Hooks können sich auf das gleiche Objekt beziehen, in welchem sie definiert werden (Interne-Hooks) oder auf ein anderen (Externe-Hooks). Da Externe Hooks eine Methode von 
einem Objekt aufrufen könnte, welches zur zeit nicht geladen ist, werden sie wie eine eigene Property geführt und laden bei Bedarf das Zielobjekt nach.

Es können mehrere Hooks auf ein Ereignis gesetzt werden (diese sollten sich aber nicht auf die Reihenfolge verlassen) oder ein Hook auf mehrere Ereignisse reagieren.

@section Parameter
Als Parameter erwartet ein Hook nur ein assoziatives Array, die einzelnen Felder des Array hängen vom Ereignis ab. Immer definiert werden:
* action
* subaction
* payload

@section Methoden
Methode       | Funktion
--------------|-----------------------------------------------------------------------------------------
setup_hooks() | Wird vom Constructor aufgerufen und initialisiert die Hooks @ref hookable::setup_hooks()
add_hook      | Fügt einen neuen Hook hinzu @ref hookable::add_hook()

@section Ereignisse
Ereignis      | Bedeutung
--------------|-----------------------------
CONSTRCTED    | Wird von Constructor aufgerufen (Vorsicht: Der Aufruf erfolgt von hookable::__construct() daher ist die Konstruktorkette noch nicht vollständig abgearbeitet
COMMITTING    | Wird vor dem Commit aufgerufen (Unabhängig ob ein insert-commit oder update-commit
COMMITTED     | Wird nach dem commit aufgerufen
INSERTING     | Wird vor dem Hinzufügen des Objektes aufgerufen
INSERTED      | Wird nach dem Hinzufügen des Objektes aufgerufen
UPDATING      | Wird vor dem Update des Objektes aufgerufen
UPDATED       | Wird nach dem Update des Objektes aufgerufen
DELETING      | Wird vor dem Löschen des Objektes aufgerufen
DELETED       | Wird nach dem Löschen des Objektes aufgerufen
*/