agileMantis
===========

Enables the Scrum Framework to your Mantis-Installation

Webpages
===========

For further information visit the following webpages:
- gadiv Webpage - https://gadiv.de 
- gadiv agileMantis Webpage - https://www.gadiv.de/de/opensource/agilemantis/agilemantisen.html
- Project page on sourceforge - https://sourceforge.net/p/agilemantis 
- Short documentations - https://www.gadiv.de/media/files/opensource/agilemantis/short_documentation_agileMantis_en.zip

## Creating Composer package

```
mkdir build
composer archive --format=zip --dir=build --file=agileMantis
curl -u%ARTIFACTORY_USERNAME%:%ARTIFACTORY_PASSWORD% "https://fidata.jfrog.io/fidata/composer-local/fidata/agileMantis.zip;composer.version=2.2.2" -T build/agileMantis.zip
```
