# Open Webconcept plugin (WP)
	
Hieronder staan alle verschillende elementen beschreven om te kunnen beginnen met de doorontwikkelingen van de Open Webconcept plugin

## Overzicht

  * [Vereisten](#markdown-header-vereisten)
  * [Installatie](#markdown-header-installatie)
  * [Versiebeheer](#markdown-header-versiebeheer)
  * [Commando's voor Docker](#markdown-header-commandos-voor-docker)
  * [Changelog](#markdown-header-changelog)
  * [Vragen?](#markdown-header-vragen)
  
  
## Vereisten
Voor de doorontwikkeling heb je het programma Docker nodig, deze is via de volgende link te downloaden: [Docker](https://www.docker.com/community-edition).
Nadat je de setup doorlopen hebt, kun je beginnen met de [installatie](##markdown-header-installatie) van de testomgeving.

## Installatie
1. Download de meeste recente versie van het project
2. Open je Terminal(Mac OS) of CMD(Windows)
3. Navigeer naar de map waar je zojuist het project hebt geplaatst
4. Voer de volgende command uit: `docker-compose up --build`
5. Na het uitvoeren van deze command kun je in je browser naar [localhost](http://localhost/) (127.0.0.1) navigeren

## Commando's voor Docker
Activeer de docker omgeving en installeer de benodigde pakketten
`docker-compose up --build`

Activeer de docker omgeving
`docker-compose up`

Verwijder de docker omgeving (vergeet niet de tmp map te verwijderen om zo de database te resetten)
`docker-compose rm -v`
  
## Versiebeheer

## Changelog
Wijzigingen binnen de Open Webconcept plugin worden bijgehouden in de [CHANGELOG.md](CHANGELOG.md).