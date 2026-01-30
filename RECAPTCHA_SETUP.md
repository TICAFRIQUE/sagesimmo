# Configuration Google reCAPTCHA v2

## Pourquoi reCAPTCHA ?

reCAPTCHA protège votre formulaire d'inscription contre :
- Les bots automatisés
- Les inscriptions frauduleuses
- Le spam
- Les attaques par force brute

## Étapes de configuration

### 1. Obtenir vos clés reCAPTCHA

1. Allez sur [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Cliquez sur le bouton **"+"** pour créer un nouveau site
3. Remplissez le formulaire :
   - **Label** : Nom de votre site (ex: Sage Immo)
   - **Type de reCAPTCHA** : Sélectionnez **"reCAPTCHA v2"** puis **"Case à cocher 'Je ne suis pas un robot'"**
   - **Domaines** : Ajoutez vos domaines (ex: `localhost`, `sageimmo.com`)
   - Acceptez les conditions d'utilisation
4. Cliquez sur **"Envoyer"**
5. Vous recevrez deux clés :
   - **Clé du site** (Site Key) : À utiliser dans le frontend
   - **Clé secrète** (Secret Key) : À utiliser dans le backend

### 2. Configurer votre application

1. Ouvrez le fichier `.env` à la racine du projet
2. Ajoutez vos clés reCAPTCHA :

```env
RECAPTCHA_SITE_KEY=votre_cle_site_ici
RECAPTCHA_SECRET_KEY=votre_cle_secrete_ici
```

3. Remplacez `votre_cle_site_ici` et `votre_cle_secrete_ici` par vos vraies clés

### 3. Tester

1. Videz le cache de configuration :
```bash
php artisan config:clear
```

2. Accédez à la page d'inscription : `http://localhost/register`
3. Remplissez le formulaire et cochez la case reCAPTCHA
4. Essayez de soumettre sans cocher → vous devriez voir une erreur
5. Cochez la case et soumettez → l'inscription devrait fonctionner

## Environnement de développement

Pour les tests en local, vous pouvez utiliser les clés de test de Google :

- **Site Key** : `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`
- **Secret Key** : `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`

⚠️ **Important** : Ces clés de test acceptent toujours le reCAPTCHA. Ne les utilisez **JAMAIS en production** !

## Production

Pour la production :
1. Utilisez vos vraies clés obtenues sur Google reCAPTCHA
2. Assurez-vous d'ajouter votre domaine de production dans la console Google reCAPTCHA
3. Activez les alertes pour surveiller les tentatives d'abus

## Dépannage

### Le widget reCAPTCHA ne s'affiche pas
- Vérifiez que votre clé du site est correcte
- Vérifiez votre connexion internet
- Vérifiez que le domaine est autorisé dans la console Google

### Erreur "La vérification reCAPTCHA a échoué"
- Vérifiez que votre clé secrète est correcte
- Vérifiez que vous avez bien coché la case reCAPTCHA
- Videz le cache : `php artisan config:clear`

### Erreur "Invalid domain for site key"
- Ajoutez votre domaine dans la console Google reCAPTCHA
- Pour localhost, ajoutez `localhost` dans la liste des domaines

## Ressources

- [Documentation officielle reCAPTCHA](https://developers.google.com/recaptcha/docs/display)
- [Console d'administration reCAPTCHA](https://www.google.com/recaptcha/admin)
- [FAQ reCAPTCHA](https://developers.google.com/recaptcha/docs/faq)
