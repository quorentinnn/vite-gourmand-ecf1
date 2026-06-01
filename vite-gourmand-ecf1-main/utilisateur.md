# Manuel Utilisateur - Vite & Gourmand

## Rôles

**Visiteur :** Consultation des menus  
**Client :** Commande en ligne  
**Employé :** Gestion commandes et contenu  
**Administrateur :** Accès complet + statistiques  

---

## Visiteur

**Consulter les menus**
- Menu > Menus
- Filtrer par prix, thème, régime, nombre de personnes
- Cliquer sur "Voir détails" pour consulter un menu

**Créer un compte**
- Menu > Inscription
- Remplir le formulaire (mot de passe : 10 caractères min, 1 majuscule, 1 chiffre, 1 caractère spécial)

**Mot de passe oublié**
- Page connexion > "Mot de passe oublié"
- Suivre le lien reçu

---

## Client

**Passer une commande**
1. Sélectionner un menu > Commander
2. Remplir : nombre de personnes, date/heure livraison, adresse
3. Valider

Prix calculé automatiquement : menu × personnes - réduction 10% (si 5+ personnes) + livraison (5€ + 0,59€/km hors Bordeaux)

**Consulter ses commandes**
- Mon compte > Mes commandes
- Statuts : En attente > Accepté > En préparation > En cours de livraison > Livré > Terminée

**Annuler une commande**
- Possible uniquement si statut "En attente"
- Après acceptation : contacter le service client

**Laisser un avis**
- Mes commandes > Laisser un avis (si commande terminée)
- Validé par employé avant publication

---

## Employé

**Gérer les commandes**
- Commandes > Modifier
- Changer le statut selon avancement
- Pour annulation après acceptation : contacter d'abord le client

**Gérer les menus**
- Gestion des menus > Ajouter/Modifier/Supprimer
- Remplir : titre, description, prix, minimum personnes, thème, régime, image

**Gérer plats, thèmes, régimes, allergènes**
- Pages de gestion dédiées
- Ajouter/Modifier/Supprimer
- Suppression impossible si utilisé

**Valider les avis**
- Avis en attente > Approuver/Rejeter

---

## Administrateur

Toutes fonctionnalités employé +

**Gérer les employés**
- Gérer les employés > Créer un compte (email + mot de passe)
- Désactiver/Activer un compte

**Statistiques**
- Statistiques > Graphiques (commandes et CA par menu)
- Filtrer par menu et période

---

## Questions fréquentes

**Pas d'emails reçus** : Normal en version développement, consulter l'interface directement  
**Modifier commande** : Impossible après acceptation, appeler le service  
**Supprimer menu** : Impossible si commandes existent  
**Statistiques vides** : Vérifier que MongoDB est démarré  
**Créer admin** : Utiliser phpMyAdmin avec role='admin'  

---

**Version :** 1.0 | **Date :** 30/01/2026