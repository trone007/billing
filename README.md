# billing
Billing system with SOAP interface and AdminPanel on Sonata Admin

# Motivation

On the creation of this project I was prompted by the long search for a decent billing system 
with the support of information exchange through web-services.

# Requirement

First version of the billing system was based on:
### PHP 5.5+
### PostgreSql 9+

# Installation
Just clone this repo into your Apache/Nginx web server catalog. 

Change connection to your database in /app/config.parameters.yml
run command from shell

#### php app/console doctrine:database:create
#### php app/console doctrine:schema:update --force


# Based on
## SonataAdminBundle
