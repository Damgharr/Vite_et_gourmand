
# Vite et Gourmand # par Daegan GARNIER

**Ceci est un guide pour mettre en place l'environnement du site**

## Composer ##

Comme ceci est un projet Symfony, il faut installer composer à sa racine, car les dossiers var et vendor ne sont pas présent dans ce dépot.
Voici la documentation de l'installation : [composer](https://getcomposer.org/download/)

Vous pourrez ensuite lancer la commande ```composer install``` à la racine du projet.

## Mise en place des BDD relationelle et NoSQL ##

Pour ce projet, j'utilise les dernières versions de PostgreSQL et MongoDB (pour l'invite de commande, j'utilise ```mongosh```).
Je recommande d'ailleurs d'utiliser pgadmin4 et MongoDbCompass car ils permettent de retrouver et visualiser les données plus facilement qu'à la ligne de commande.
Pour la BDD de postgres, il vaut mieux l'installer sur le serveur local "Local PostgreSQL".

Les liens d'installation :
- [PostgreSQL](https://www.postgresql.org/download/)
- [MongoDB](https://www.mongodb.com/docs/manual/installation/)

### PostgreSQL ###

Après avoir écrit ``` psql -U postgres ``` sur l'invite de commande, vous pouvez exécutez les commandes suivantes :

```
CREATE DATABASE vite_et_gourmand_dev;
CREATE USER vite_et_gourmand WITH PASSWORD 'jGaNX5C5x96L';
GRANT ALL PRIVILEGES ON DATABASE vite_et_gourmand_dev TO vite_et_gourmand;
```
Vous pouvez ensuite quitter psql en écrivant ```\q```, et écrire les commandes suivantes dans l'ordre à la racine du projet, afin d'éxécuter les fichiers sql de mise en place des Tables, et insertion des données :

```
psql -U votre_user -d votre_db -f sql/postgresql_schema.sql
psql -U votre_user -d votre_db -f sql/postgresql_data.sql
```

Normalement, vous devriez retrouver toutes les données dans la BDD.

### MongoDB ###

Pour MongoDB, après avoir écrit ``` mongosh ```, vous pouvez exécutez les deux commandes suivantes :

```
use vite_et_gourmand
db.createUser({ user: "vite_et_gourmand", pwd: "HSnXnUFkzCxH", roles: [{ role: "readWrite", db: "vite_et_gourmand" }] })
```

Et comme avant, vous quittez en écrivant simplement ``` exit ```, puis lancez cette commande, aussi à la racine du projet :

```
mongosh 'mongodb://vite_et_gourmand:HSnXnUFkzCxH@127.0.0.1:27017' sql/mongodb_finished_orders.js
```

**Il sera aussi important d'installer et activer les extensions php tels que php-mongodb et php-pgsql, que vous pourrez retrouver dans votre php.ini**

## Lancer l'application localement ##

Maintenant que vous avez tout en place, vous pouvez lancer l'application localement avec la commande ```symfony serve``` exécuté à la racine, puis vous aller sur localhost:8000 et vous finirez sur la page d'accueil du site ! 


