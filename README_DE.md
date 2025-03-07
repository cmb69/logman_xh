# Logman_XH

Logman_XH ermöglicht die Verwaltung der Logdatei von CMSimple_XH,
die vom Kernsystem und Erweiterungen verwendet wird,
um relevante Ereignisse zu protokollieren.
Während CMSimple_XH bereits die Möglichkeit bietet,
den Inhalt der Logdatei im Administrationsbereich *anzuzeigen*,
bietet Logman_XH eine erweiterte Benutzeroberfläche,
die die Filterung des Protokolls, und sogar das Löschen
alter oder anderweitig unnötiger Einträge ermöglicht.

## Inhaltsverzeichnis

  - [Voraussetzungen](#voraussetzungen)
  - [Download](#download)
  - [Installation](#installation)
  - [Einstellungen](#einstellungen)
  - [Verwendung](#verwendung)
  - [Fehlerbehebung](#fehlerbehebung)
  - [Lizenz](#lizenz)
  - [Danksagung](#danksagung)

## Voraussetzungen

Logman_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0, und PHP ≥ 7.4.0.

## Download

Das [aktuelle Release](https://github.com/cmb69/logman_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins
auch.

1.  Sichern Sie die Daten auf Ihrem Server.
2.  Entpacken Sie die ZIP-Datei auf Ihrem Computer.
3.  Laden Sie das gesamte Verzeichnis `logman/` auf Ihren Server in
    das `plugins/` Verzeichnis von CMSimple_XH hoch.
4.  Vergeben Sie Schreibrechte für die Unterverzeichnisse `config/`,
    `css/` und `languages/`.
5.  Navigieren Sie zu *Plugins* → *Logman* im Administrationsbereich,
    und prüfen Sie, ob alle Voraussetzungen für den Betrieb erfüllt
    sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Homepage.
Wählen Sie *Plugins* → *Logman*.

Sie können die Original-Einstellungen von Logman_XH in der
*Konfiguration* ändern. Beim Überfahren der Hilfe-Icons mit der Maus
werden Hinweise zu den Einstellungen angezeigt.

Die Lokalisierung wird unter *Sprache* vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen, falls keine
entsprechende Sprachdatei zur Verfügung steht, oder sie entsprechend
Ihren Anforderungen anpassen.

Das Aussehen von Logman_XH kann unter *Stylesheet* angepasst werden.

## Verwendung

Gehen Sie im Administrationsbereich Ihrer Website zu
*Plugins*→*Logman*→*Log-Datei* um die ältesten Protokolleinträge
gemäß der Konfigurationseinstellung `entries_max` anzuzeigen.
Sie können durch Verwendung der Suchfelder oberhalb der Tabelle
Filter anwenden, so dass nur diejenigen Protokolleinträge angezeigt
werden, die Sie interessieren.
Sie können beispielsweise nach einem bestimmten Modul oder einer
Kategorie filtern.
Sie können ebenfalls alle angezeigten Protokolleinträge aus der
Logdatei entfernen, was besonders nützlich ist, wenn Sie nach
Zeitstempel gefiltert haben.

## Fehlerbehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/logman_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Logman_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Logman_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Logman_XH erhalten haben. Falls nicht, siehe
<http://www.gnu.org/licenses/>.

Copyright © 2025 Christoph M. Becker

## Danksagung

Das Plugin Logo stammt von
[Log icons created by Freepik - Flaticon](https://www.flaticon.com/free-icons/log).
Vielen Dank für die freie Verfügbarkeit dieses Icons.

Zu guter Letzt vielen Dank an [Peter Harteg](http://harteg.dk/), den
„Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](http://www.cmsimple-xh.org/), ohne die dieses
fantastische CMS nicht existieren würde.
