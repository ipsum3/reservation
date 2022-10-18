## Installation du package reservation de voiture

``` bash
# install the package
composer require ipsum3/reservation

# Run install
php artisan ipsum:reservation:install

# Optional publish views
php artisan vendor:publish --provider="Ipsum\Reservation\ReservationServiceProvider" --tag=views

```

### Add Reservation seeder to DatabaseSeeder.php file
`$this->call(\Ipsum\Reservation\database\seeds\DatabaseSeeder::class);`

### Add Commande to Kernel.php file
`$schedule->command('ipsum:reservation:joursferies')->quarterly(); // Trimestre`


## Lexique

Départ et retour  
: Départ/Début/Arrivée/Retrait/prise en charge =>  Retour/fin/Restitution. 
Privilègier Départ et retour


Prestation 
: Frais optionnel ou non optionnel en plus de la (prestation) de location

Contrat 
: Formalisation et matérialisation de la relation entre le loueur et le locataire.

Lieu 
: 

Stop sell
: Blocage d'une catégorie à la location

Carrosserie
: 

Catégorie
: Une catégorie représente un ensemble de modèles de véhicule avec les mêmes caractéristiques et le même tarif de réservation.

Modéle
: Marque et modéle d'un véhicule

Franchise
: Participation financière aux réparations. Cette somme n’est pas remboursée par l’assureur.

Caution
: La caution ou dépot de garantie est la garantie pour le loueur que le locataire est apte à payer la franchise s’il y avait un sinistre.

Saison
:

Véhicule
: Véhicule physique correspondant à une plaque d'immatriculation


