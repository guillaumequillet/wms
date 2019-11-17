# Projet 5 - Formation DWJ Quillet Guillaume 2019

Le dernier projet de la formation Développeur Web Junior par OpenClassrooms est un projet libre ou bien la réalisation d’un stage. 

## Un projet conjuguant mes expériences professionnelles

J’ai choisi de réaliser un logiciel de type WMS (Warehouse Management System), principalement orienté gestion de stocks, en écho avec ma carrière précédente de Directeur logistique.

### Périmètre

Il s’agit de réaliser un logiciel de gestion de stocks disposant des fonctionnalités élémentaires afin de pouvoir gérer effectivement un stock. Ce projet sera développé en maximum 1.5 mois et doit donc aller à l’essentiel tout en proposant au minimum les fonctionnalités suivantes :

* Accès par IDENTIFICATION à l’ensemble du logiciel.
* Il y aura un compte administrateur. On prévoit une gestion des  UTILISATEURS afin de créer des comptes supplémentaires, aux droits restreints.
* Un menu ARTICLES permettant de créer un article par saisie formulaire ou import fichier type (format CSV), ou bien les rechercher / éditer.
Informations : Code, Description, [Code EAN13, poids, longueur, largeur, hauteur].
* un menu STOCKS permettant de consulter l'état des stocks aux emplacements, résultants des mouvements.
* Un menu EMPLACEMENTS permettant de créer des zones de stockage, ou bien les rechercher / éditer : Zone, Allée, Colonne, Niveau. 
* Un menu MOUVEMENTS permettant de créer des mouvements de stock de type : réception / livraison / ajustement inventaire. 
Informations : code, quantité, emplacement, fournisseur, destinataire, référence mouvement. 

### Évolutions possibles

On peut prévoir un menu CLIENTS afin d’avoir plusieurs clients dans notre entrepôt, à qui rattacher des ARTICLES voire des EMPLACEMENTS.

On peut ajouter un menu HISTORIQUE afin de consulter les différents mouvements d’un ARTICLE.

### Technologies

HTML / CSS, PHP / MySQL, Javascript. Responsive tablette / mobile.
AJAX pour la saisie du mouvement (pour les champs article, description, emplacement : recherche dynamique en BDD).

## Menu Identification

Métier : 
* Menu invitant à se connecter à l’application (login / mot de passe). 
* Message d’erreur en cas d’échec d’identification.

Technique : 
* Connexion à une base de données MySQL en PHP pour vérification du login et mot de passe enregistré. 
* Comparaison case sensitive avec cryptage du mot de passe. 
* Protection du formulaire via Token et contrôle javascript des champs de saisie. 

## Menu Articles

Métier : 
* Menu recherche permettant de saisir un code article pour en afficher la fiche.
* Bouton importer pour créer des articles en masse via un fichier.
* Formulaire de saisie pour un nouvel article.


Technique :
* Recherche via une requête SQL de l’article demandé pour en afficher la vue.
* Traitement d’un fichier CSV pour multi import des articles et log à l’utilisateur du résultat.
* Contrôle du formulaire par Token et javascript pour les champs. 
* Contrôle du code article inexistant avant création.

## Menu Stocks

Métier : 
* Menu recherche permettant de saisir un code article ou une adresse pour en afficher le stock.

Technique :
* Recherche via une requête SQL des articles ou emplacements demandés pour en afficher la vue.
* Contrôle du formulaire par Token et javascript pour les champs. 

## Menu Emplacements

Métier : 
* Possibilité de créer une zone / allée / colonne / niveau et d’en supprimer si non utilisée. 
* Création unique ou pas intervalles.

Technique :
* Listes déroulantes dynamiques.
* Formulaire protégé par Token et javascript pour les champs.
* Contrôle de la non utilisation avant suppression d’un emplacement.

## Menu Mouvements

Métier :
* on choisit par un bouton radio le type de mouvement souhaité : “entrée”, “sortie”, "transfert" et “inventaire”. L’entrée correspond à une réception (fournisseur, retour…), la sortie à une commande de vente, un transfert au déplacement d'une quantité de produit et l’inventaire à un mouvement autre (contrôle de stock ponctuel ou annuel par exemple).

On a alors accès à un formulaire dont l’entête diffère selon le choix (informations fournisseur / informations destinataire / motif ajustement) et à une liste d’articles à saisir (code, quantité, emplacement)

* Dans le cadre d’une sortie, l’emplacement est automatiquement affecté par le système.

* on peut importer des fichiers de type CSV à la place d’une saisie manuelle.

Technique :
* tous les types de mouvement ont un impact similaire sur le stock : faire varier des quantités à des emplacements (pouvant amener à la création ou la destruction de stocks) donc on souhaite factoriser le code en ce sens.

* formulaire protégé par Token et javascript pour les champs.

* traitement de fichiers CSV pour les imports en masse et retour utilisateur.

## Design

Le design sera composé de trois zones principales :

* le Header au sein d’une balise <header> qui contiendra un logo (image) et une navigation (balise <nav> qui contiendra une liste non ordonnée pour afficher le menu vers Articles | Mouvements | Emplacements | Admin.
* le Main au sein d’une balise <main> qui contiendra les sections de chaque catégorie.
* le footer contenant les informations légales et un retour top.

## Back-End

Le back-end sera uniquement accessible aux profils de type administrateur. Il permettra notamment de créer / modifier les comptes utilisateur, créer / modifier les clients et autres informations sensibles (comptes FTP client par exemple).

L'entrepôt sera également créé sous le Back-End (Emplacements).

## UML Design / Arborescence du site

Le schéma UML est disponible [ici](https://docs.google.com/spreadsheets/d/1e8GH1DivJCnVaNDi2MvVgp4dRYz9yQcRUVLldaiGxB0/edit?usp=sharing).

L'arborescence du site [ici](https://docs.google.com/spreadsheets/d/1SVmNaadsA0J5EWMEUMeJGEDcW7I8_bL9ZaFVG9w5zTE/edit?usp=sharing).