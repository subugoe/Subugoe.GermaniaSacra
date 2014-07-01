Subugoe.GermaniaSacra
=====================

## Import MS Access Dumps

Put the necessary files (klosterdatenbankdump.sql, GS-citekeys.csv) in the directory Build/GermaniaSacra/Access.

Then start the command `./flow germaniasacra:importaccess` and the database tables will be created.

Todo

* Add data conversion script
* Add solr import

Solr Configuration
==================

Configure your Solr connection in the settings.yaml according to the following syntax:


```yaml
Subugoe:
  GermaniaSacra:
    solr:
      host: '127.0.0.1'
      port: 8983
      path: '/solr/germaniasacra'
