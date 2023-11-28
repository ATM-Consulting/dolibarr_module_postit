# ChangeLog for postit

## UNRELEASED

# Version 2.2
- NEW: compatibilité dolibarr 19 / php 8.2 - *28/11/2023* - 2.2.0

## Version 2.1
- FIX : missing hook function return *17/11/2023* - 2.1.1
- NEW : séparation par entité et gestion du partage multi-société *07/03/2023* - 2.1.0

## Version 2.0

- FIX : Postit ne s'affiche plus sur l'index en 14 en raison du hook "beforeBodyClose" qui n'existe pas *13/01/2023* - 2.0.5
- FIX : Card overflow displays and live edit style *15/07/2022* - 2.0.4
- FIX : Css and icons *13/07/2022* - 2.0.3
- FIX : Missing icon *02/06/2022* - 2.0.2
- FIX : PHP 8 Compatibility - Undefined variables *12/07/2022* - 2.0.1
- NEW : Refonte du module sur le modèle module builder *02/06/2022* - 2.0.0
- NEW : Ajout de la class TechATM pour l'affichage de la page "A propos" *10/05/2022* - 1.6.0

## Version 1.5

- FIX: Compatibility V16 : newToken *02/06/2022* - 1.5.5
- NEW: Modification date *2021-07-06* - 1.5.3
- FIX: *2021-07-06* - 1.5.3
  - restrict what is sent over the network in ajax calls (esp. `$db`)
  - display line breaks correctly
  - when title and/or content of note has changed, update timestamp + author
  - minor cleanup
- FIX: Dolibarr 14 compatibility after hook removal - *2021-07-06* - 1.5.2
- FIX: Dolibarr 13 Token compatibility - *2021-02-22* - 1.5.1
