# Open Webconcept
Binnen het Open Webconcept werken gemeenten en bedrijven samen aan componenten voor de digitale dienstverlening van de overheid.
Gemeenten zijn eigenaar van deze componenten en beheren deze in hun eigen code beheerplatformen als Github en Bitbucket.
Op deze VNG GitHub hebben we een Wordpress-docker staan waarmee je alle bouwstenen eenvoudig zelf kunt installeren.
In de Open Webconcept Wordpress-plugin staat de code om alle componenten te kunnen installeren. Deze Wordpress-plugin maakt gebruik van het bestand componenten.xml die je ook in deze VNG Github omgeving kunt vinden. 

Alle componenten die nu zijn ontwikkeld maken gebruik van Wordpress beheeromgeving en ontsluiten de opgeslagen data via API's. Op deze manier kunnen de componenten als losse (data)diensten ingezet worden door marktpartijen en gemeenten bij hun digitale dienstverlening.
Ook online toepassingen die niet zijn gebaseerd op Wordpress kunnen van de API's van de Open Webconcept componenten gebruik maken!
Met deze diensten ondersteund de Open Webconcept beweging het Common Ground gedachtengoed (API gestuurd)
Met deze aanpak wil de Open Webconcept beweging de marktwerking rond de ICT-dienstverlening binnen de overheid vergroten. Inmiddels zijn er ook al meerdere bedrijven die de componenten hebben geinstalleerd en als dienst voor gemeenten inzetten!

De oorspronkelijke [intentieverklaring](../../blob/master/Intentieverklaring%20Wordpress%20Open%20Webconcept.pdf)

# Repository verplaatst
Deze repository is opgeschoond en voor archief doeleinden verplaatst naar: https://github.com/OpenWebconcept/-Archief-Openwebconcept 
De website van het openwebconcept val te vinden op: https://openwebconcept.nl/


## Fieldlab dienstverlening september 2018
Tijdens het VNG Fieldlab Dienstverlening hebben de gemeenten Buren, Heerenveen, Lansingerland, Súdwest-fryslân en de bedrijven Site.nu en Yard.nl in 3 dagen een eerste opzet voor een back- en frontend toepassing om meldingen in de openbare ruimte door te geven aan de overheid. De backend heeft als doel ontvangen meldingen via de NLx API te ontsluiten naar de gewenste afhandelsystemen. Tijdens het fieldlab hebben we dit gerealiseerd met het zaaksysteem van Haarlem welke ook op de NLx was aangesloten tijdens het fieldlab.
De code die we hier ontwikkeld hebben staat in de map fieldlab-201809

## Fieldlab dienstverlening september 2019
Tijdens dit VNG Fieldlab is een track-and-trace portaal ontwikkeld waarmee een inwoner de voortgang van aanvragen kan volgen. Dit mijn.gemeente portaal bepaald aan de hand van een irma login wie er inlogd om alleen de informatie van deze persoon te tonen. Hierbij werkt het system zonder zelf informatie op te slaan, alle vragen worden omgezet naar de zaakgewijswerken-api's, de voortgang komt uit het achterliggende (openzaak) zaaksysteem.
