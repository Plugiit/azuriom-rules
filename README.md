# Plugiit Rules

Plugin Azuriom de règlement versionné avec acceptation obligatoire des joueurs.

## Fonctionnalités

- **Rédaction** : le règlement est composé de sections ordonnées (glisser-déposer) éditées avec l'éditeur riche d'Azuriom (TinyMCE). Ces sections forment un **brouillon**.
- **Publication de versions** : numéro (suggéré automatiquement), résumé des changements, option « exiger une nouvelle acceptation » (sinon correction mineure, sans nouvelle demande). Chaque version est un instantané immuable.
- **Différences résumées** calculées automatiquement entre deux versions (sections ajoutées, modifiées, supprimées).
- **Page publique** `/plugiit-rules` : version en vigueur, sommaire avec ancres, statut d'acceptation du joueur, historique des versions avec résumés et différences ; `/plugiit-rules/versions/{id}` pour consulter une ancienne version.
- **Acceptation obligatoire** pour les joueurs connectés qui n'ont pas accepté la dernière version exigée :
  - mode **fenêtre bloquante** (le site reste inutilisable tant que le joueur n'a pas accepté ; il peut se déconnecter) ;
  - mode **bandeau non bloquant** en haut des pages (masquable pour la session) ;
  - ou aucun affichage.
- **Blocage d'actions** tant que le règlement n'est pas accepté (envois de formulaires uniquement) : forum, boutique, commentaires, support, votes, ou toute route nommée (jokers acceptés). Réponse 403 JSON pour les requêtes AJAX.
- **Case à cocher à l'inscription** (option) : ajoutée automatiquement au formulaire d'inscription, vérifiée côté serveur, et l'acceptation est enregistrée à la création du compte.
- **Suivi** : statistiques (part des comptes ayant accepté la version exigée), dernières acceptations, acceptations par version, recherche par joueur (date, version, origine, IP hachée HMAC-SHA256) et carte « Règlement » sur la fiche utilisateur du panel.

## Installation

1. Copier le dossier dans `plugins/plugiit-rules` puis activer le plugin (les migrations sont lancées automatiquement).
2. **Règlement > Sections** : rédiger les sections, puis **Publier une nouvelle version**.
3. **Règlement > Réglages** : choisir le mode d'affichage, les actions bloquées, l'option d'inscription.
4. Ajouter la page « Règlement » au menu (**Navigation**, type « Plugin »).

Permission : `plugiit-rules.admin` (+ `admin.access`).

## Intégration

L'affichage est injecté par un middleware ajouté au groupe `web` : la fenêtre est insérée avant `</body>`, le bandeau juste après `<body>`, uniquement pour les réponses HTML GET 200 hors `admin/*`, `api/*` et AJAX. Il fonctionne donc avec tous les thèmes, sans modification.
Le CSS/JS (`assets/css/style.css`, `assets/js/script.js`) n'utilise que des classes et identifiants préfixés `plugiit-rules-`.

Widget pour les thèmes (statut d'acceptation du joueur connecté, lien vers la page) :

```blade
@include('plugiit-rules::widget')
{{-- ou --}}
@plugiitRulesStatus
```

## URLs

- Public : `/plugiit-rules`, `/plugiit-rules/versions/{id}`
- Admin : `/admin/plugiit-rules` (vue d'ensemble), `/sections`, `/versions`, `/users`, `/settings`
