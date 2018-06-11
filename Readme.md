## Gobelins Datasource

Gobelins Datasource is an HTTP API for the database of objects of the Mobilier National.
The objective is the provide a clean data model to the main back-end application, and
anticipate the data migration to the new collections engine that should be implemented
in 2019. Once the data migration is complete, this application should be retired.

### Overview

This Laravel app loads the SQL files produced by the Migration.exe desktop application
into the default `postgres` database (yes, it must be the default database). The import
Artisan task makes a few minor modifications to the schema, in order to make it easier
to work with Eloquent.

Reloading the dump files will overwrite whatever is in the DB.

### API endpoints

All endpoints return data in JSON format.

```
GET /api/products/
GET /api/products/?page=2
```
Returns a list of 10 products, including authorships, authors, category, period, style, etc.
Includes a `links` object including `next` and `prev`, allowing the API consumer to crawl the
entire dataset.

```
GET /api/products/{id}
```
Individual product, with relationships.


### Setup
```shell
php artisan gobelins:import_scom -vvv
```

### Design tradeoffs

- SCOM: We don't have [Last Modified] data on the records, so we can't
  do updates since a given datetime.
- SCOM: DB schema might slightly change in the near future
- SCOM: In about 1 year, the DB will be replaced by another one, that
  should provide an API, or be crawlable (OAI repository?)
- We can assume that objects will never be deleted. Items removed from
  the DB will just be soft-deleted.
- NIMES: we have [Last Modified], but need to create relations with SCOM
  data, by fuzzy-searching the authors and titles. Postponed until
  further notice.

### Credits

- Ned Baldessin, development

### License

The Gobelins Datasource application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Copyright © 2018 Ministère de la Culture et de la Communication<br>
Mobilier national et manufactures des Gobelins, de Beauvais et de la Savonnerie.
