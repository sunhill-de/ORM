<?php
/** 
*@mainpage Sunhill Framework
*@section Übersicht
*Das Sunhill Framework dient als Aufsatz auf das Laravel/Laravel Zero Framework der Speicherung von Objekten in einer Datenbank. 
*
*@section Problembeschreibung
*Das Datenbankobjektsystem von Laravel (Eloquent) ist ein nettes Tool, lässt aber das Speichern von Objekthirachien außer Acht. Es ist mehr eine objektorientierte Abstraktion  einer Datenbanktabelle. In diese Lücke soll das Sunhill Framework einspringen.
*
*@section Einführung
*Kern des Sunhill-Frameworks ist das @see Sunhill::Objects::oo_object, welches als abstrakte Basisklasse eines speicherbaren Objektes dient. Ein von oo_object abgeleitetes Objekt definiert die statische Methode @see initialize_properties(), welches die für dieses Objekt gültigen Properties festlegt. Optional definiert es die statische Methode @see initialize_hooks(), welche Hooks für verschiedene Aktionen festlegt. Die Properties werden wie normale Member angesprochen, gelesen und geschrieben. Sollen die aktuellen Änderungen in die Datenbank gespeichert werden, wird die Methode @see commit() aufgerufen, sollen sie rückgängig gemacht werden, wird die Methode @see rollback() aufgerufen.
* 
*@section Das Objekt
*Objekte sind von der php-Klasse oo_object abgeleitet. Als Minimum muss folgende Methode überschrieben werden:
* * static protected initialize_properties()
*Initialisiert die Properties für dieses Objekt.
*Außerdem muss das folgende Feld überschrieben werden:
* * protected static $table_name
* Legt den Namen der Datenbanktabelle fest, unter dem die Daten gespeichert werden sollen. 
*
*@section Begriffe
*
*@subsection Properties
*Eine Property ist ein Datenfeld eines Objektes, welches seine Daten in einer Datenbank ablegen kann. 
*
*@subsection Hooks
*Hooks sind Methoden, die an bestimmten Stellen der Objektbearbeitung eingehängt werden können, um bei bestimmten Ereignissen ausgeführt zu werden. 
*Siehe auch @see Hooks
*@subsection Attribute
*Ein Attribut ist ein einfaches Datenfeld (Integer, Float, String, Datum, Zeit, Zeitstempel oder Text), welches optional einem Objekt zugewiesen werden kann. Entsprechend kann
*es auch sein, dass ein Objekt, ein bestimmtes Attribut nicht besitzt.
