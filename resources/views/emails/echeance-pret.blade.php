<x-mail::message>
# Rappel d'échéance de prêt

Bonjour {{ $pret->client->name }},

Nous vous informons que votre prêt arrive à échéance dans **3 jours**.

## Détails du prêt

- **Date d'échéance :** {{ $dateEcheance }}
- **Montant restant à payer :** {{ $montantRestant }} FCFA
- **Numéro de prêt :** #{{ $pret->id }}

## Action requise

**Il est important de vous présenter dans nos bureaux pour effectuer le paiement avant la date d'échéance.**

Pour toute question ou information complémentaire, n'hésitez pas à nous contacter.

---

Cordialement,<br>
L'équipe **G Micro Service**
</x-mail::message>
