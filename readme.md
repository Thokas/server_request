# Das WoW RP-Server Abfrage Script #

Ein Abfrage Script für RP-Welten Partner um verschiedene Serverdaten in XML Form auszugeben.

Abgefragt Daten
-------------------
1. Accounts
 - ID
 - Username
 - Joindate
 - Mutetime
 - GM Level
 - Bandate, Bannedby, Banreason
 - Lastlogin
 
2. Gilden
 - ID
 - Name
 - leader GUID
 - Emblem Style
 - Info
 - Message of the Day
 - Createdate
 - Bankmoney
 - Ranks
 	- Name
 	- Rights
 	- Moneyallow
	
3. Charaktere
 - GUID
 - Onlinestate
 - Name
 - Race
 - Class
 - Gender
 - Money
 - PlayerBytes
 - PlayerBytes2
 - Position X, Position Y, Position Z, Orientation, MapID
 - Bandate, Bannedby, Banreason
 - Lastlogin
 - Playedtime
 - Guild ID
 - Friends
 	- Friend GUID
 	- Flags
 	- Note
 - Current Equipment

Setup
-----
Das Abfrage Script braucht einen Webserver mit PHP sowie eine WoW Server Emu von Trinity, Arcemu oder Mangos.

Die Installation ist ziemlich einfach:

1. Downloade den Quellcode von github.

2. Uploade alle Daten auf deinen Webserver.

3. Passe die 'config.php' Datei auf deine Webserver und Emulator Daten an.

4. Fertig

Credits
-------
![CreativeCommons](http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png)

the Script is licensed under the [Creative Commons License](http://creativecommons.org/licenses/by-nc-sa/3.0/
) and available in source code form at GitHub.