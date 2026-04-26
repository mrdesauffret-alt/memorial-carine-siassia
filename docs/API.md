# 📡 Documentation API - Mémorial Carine SIASSIA

## Vue d'ensemble

L'API REST du mémorial permet de gérer les souvenirs, bougies virtuelles, et collecter des données d'analytics. Toutes les réponses sont au format JSON.

**Base URL** : `https://memorial-carine-siassia.page.gd/backend/api/`

## 🔐 Authentification

### Headers requis
```http
Content-Type: application/json
Access-Control-Allow-Origin: *
```

### Authentification Admin
Pour les endpoints d'administration, un token d'authentification est requis :
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

## 📝 Memories API

### Récupérer les souvenirs approuvés
```http
GET /memories.php
```

**Paramètres de requête :**
- `page` (int, optionnel) : Numéro de page (défaut: 1)
- `limit` (int, optionnel) : Nombre d'éléments par page (défaut: 20)

**Réponse :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "story",
      "content": "Un beau souvenir de Carine...",
      "author_name": "Jean Dupont",
      "image_path": "uploads/memory_123.jpg",
      "created_at": "2025-01-15T10:30:00Z"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 45,
    "pages": 3
  }
}
```

### Créer un nouveau souvenir
```http
POST /memories.php
```

**Corps de la requête :**
```json
{
  "type": "story",
  "content": "Contenu du souvenir",
  "author_name": "Nom de l'auteur",
  "author_email": "email@example.com"
}
```

**Types supportés :**
- `story` : Anecdote
- `photo` : Photo avec description
- `message` : Message simple

**Réponse :**
```json
{
  "success": true,
  "message": "Souvenir créé avec succès",
  "id": 123
}
```

### Récupérer un souvenir spécifique
```http
GET /memories.php/{id}
```

**Réponse :**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "type": "story",
    "content": "Contenu du souvenir",
    "author_name": "Jean Dupont",
    "author_email": "jean@example.com",
    "image_path": "",
    "is_approved": false,
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z"
  }
}
```

### Approuver un souvenir (Admin)
```http
PUT /memories.php/{id}
```

**Corps de la requête :**
```json
{
  "is_approved": true
}
```

### Supprimer un souvenir (Admin)
```http
DELETE /memories.php/{id}
```

## 🕯️ Candles API

### Récupérer les statistiques des bougies
```http
GET /candles.php
```

**Réponse :**
```json
{
  "success": true,
  "data": {
    "total_candles": 247,
    "unique_visitors": 150,
    "today_candles": 5,
    "week_candles": 25
  }
}
```

### Allumer une bougie
```http
POST /candles.php
```

**Corps de la requête :**
```json
{
  "visitor_name": "Nom du visiteur",
  "message": "Message pour Carine"
}
```

**Réponse :**
```json
{
  "success": true,
  "id": 248,
  "stats": {
    "total_candles": 248,
    "unique_visitors": 151,
    "today_candles": 6,
    "week_candles": 26
  }
}
```

**Limitations :**
- Une bougie par IP par jour maximum
- Les champs `visitor_name` et `message` sont optionnels

### Récupérer les bougies récentes
```http
GET /candles.php/recent
```

**Paramètres de requête :**
- `limit` (int, optionnel) : Nombre de bougies (défaut: 20)

**Réponse :**
```json
{
  "success": true,
  "data": [
    {
      "visitor_name": "Marie",
      "message": "En souvenir de Carine",
      "lit_at": "2025-01-15T14:30:00Z"
    }
  ]
}
```

## 📤 Upload API

### Uploader une image
```http
POST /upload.php
```

**Type de contenu :** `multipart/form-data`

**Corps de la requête :**
- `image` (file) : Fichier image (JPG, PNG, GIF, WebP)

**Limitations :**
- Taille maximale : 5MB
- Formats acceptés : JPG, PNG, GIF, WebP
- Images automatiquement optimisées

**Réponse :**
```json
{
  "success": true,
  "filename": "memory_1234567890.jpg",
  "url": "uploads/memory_1234567890.jpg",
  "message": "Image uploadée avec succès"
}
```

## 📊 Analytics API

### Envoyer des événements d'analytics
```http
POST /analytics.php
```

**Corps de la requête :**
```json
{
  "events": [
    {
      "name": "page_view",
      "data": {
        "page_title": "Mémorial Carine SIASSIA",
        "page_location": "https://memorial-carine-siassia.page.gd/"
      },
      "timestamp": "2025-01-15T10:30:00Z",
      "session_id": "session_1234567890_abc123",
      "user_agent": "Mozilla/5.0...",
      "url": "https://memorial-carine-siassia.page.gd/"
    }
  ]
}
```

**Types d'événements supportés :**
- `page_view` : Vue de page
- `click` : Clic sur un élément
- `scroll_depth` : Profondeur de scroll
- `form_submit` : Soumission de formulaire
- `candle_light` : Allumage de bougie
- `memory_modal_open` : Ouverture du modal de souvenir
- `meditation_start` : Début de méditation
- `social_share` : Partage sur réseaux sociaux
- `error` : Erreur JavaScript
- `web_vital` : Métriques Core Web Vitals

**Réponse :**
```json
{
  "success": true,
  "processed": 5,
  "total": 5,
  "errors": []
}
```

## 🚨 Codes d'erreur

### Codes HTTP
- `200` : Succès
- `201` : Créé avec succès
- `400` : Requête invalide
- `401` : Non autorisé
- `403` : Accès interdit
- `404` : Ressource non trouvée
- `405` : Méthode non autorisée
- `413` : Fichier trop volumineux
- `500` : Erreur serveur

### Format des erreurs
```json
{
  "error": "Message d'erreur détaillé",
  "code": "ERROR_CODE",
  "details": {
    "field": "validation_error"
  }
}
```

## 🔒 Sécurité

### Validation des entrées
- Tous les champs texte sont sanitizés
- Les emails sont validés
- Les fichiers uploadés sont vérifiés
- Protection contre les injections SQL

### Limitation de taux
- Bougies : 1 par IP par jour
- Upload : 10 fichiers par heure par IP
- API : 100 requêtes par minute par IP

### Headers de sécurité
```http
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
```

## 📝 Exemples d'utilisation

### JavaScript (Fetch API)
```javascript
// Récupérer les souvenirs
const response = await fetch('/backend/api/memories.php');
const data = await response.json();

// Créer un souvenir
const newMemory = await fetch('/backend/api/memories.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    type: 'story',
    content: 'Un beau souvenir de Carine',
    author_name: 'Jean Dupont'
  })
});

// Allumer une bougie
const candle = await fetch('/backend/api/candles.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    visitor_name: 'Marie',
    message: 'En souvenir de Carine'
  })
});
```

### PHP (cURL)
```php
// Récupérer les souvenirs
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://memorial-carine-siassia.page.gd/backend/api/memories.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
```

### Python (requests)
```python
import requests

# Récupérer les souvenirs
response = requests.get('https://memorial-carine-siassia.page.gd/backend/api/memories.php')
data = response.json()

# Créer un souvenir
new_memory = {
    'type': 'story',
    'content': 'Un beau souvenir de Carine',
    'author_name': 'Jean Dupont'
}

response = requests.post(
    'https://memorial-carine-siassia.page.gd/backend/api/memories.php',
    json=new_memory
)
```

## 🔄 Webhooks (Futur)

### Événements supportés
- `memory.created` : Nouveau souvenir créé
- `memory.approved` : Souvenir approuvé
- `candle.lit` : Bougie allumée
- `error.occurred` : Erreur détectée

### Format des webhooks
```json
{
  "event": "memory.created",
  "data": {
    "id": 123,
    "type": "story",
    "author_name": "Jean Dupont"
  },
  "timestamp": "2025-01-15T10:30:00Z"
}
```

## 📈 Monitoring

### Métriques disponibles
- Nombre de requêtes par endpoint
- Temps de réponse moyen
- Taux d'erreur
- Utilisation de la bande passante

### Logs
- Toutes les requêtes sont loggées
- Erreurs détaillées dans les logs serveur
- Analytics des performances

## 🆘 Support

Pour toute question sur l'API :
- 📧 Email : api-support@memorial-carine-siassia.com
- 🐛 Issues : [GitHub Issues](https://github.com/votre-repo/memorial-carine-siassia/issues)
- 📖 Documentation : [Wiki API](https://github.com/votre-repo/memorial-carine-siassia/wiki/API)

---

*Documentation API v1.0.0 - Mémorial Carine SIASSIA*
