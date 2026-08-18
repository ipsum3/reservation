@extends('layouts.website')
@section('title', 'Caution Sécurisée')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-4 p-md-5 text-center">

                        <!-- Icône de Succès -->
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 80px; height: 80px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16">
                                    <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.134a.75.75 0 0 0 .632 0q.112-.049.293-.134c.241-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.5 1.5 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.77 11.77 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.77 11.77 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.5 1.5 0 0 1 2.185 1.43 62 62 0 0 1 5.072.56"/>
                                    <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Titre Principal -->
                        <h2 class="fw-bold mb-3">Dépôt de caution sécurisé !</h2>
                        <p class="text-muted mb-4">
                            Votre empreinte bancaire a été enregistrée avec succès. Aucun montant n'a été débité de votre compte.
                        </p>

                        <!-- Détails du récapitulatif -->
                        @if(isset($reservation))
                            <div class="bg-light p-3 rounded text-start mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Référence Réservation :</span>
                                    <span class="fw-bold">#{{ $reservation->reference }}</span>
                                </div>
                                @if($reservation->paiementCaution?->montant)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Montant de la caution :</span>
                                        <span class="fw-bold text-success">@prix($reservation->paiementCaution->montant) €</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Statut de la caution :</span>
                                    <span class="badge bg-success-subtle text-success border border-success fw-semibold p-1">
                                    Empreinte Validée
                                </span>
                                </div>
                            </div>
                        @endif

                        <!-- Note d'information -->
                        <div class="alert alert-info border-0 bg-info-subtle text-start fs-7 mb-4">
                            <small>
                                💡 <strong>Information :</strong> Il s'agit d'une pré-autorisation temporaire. Les fonds seront libérés automatiquement à la fin de votre réservation si aucun dommage n'est constaté.
                            </small>
                        </div>

                        <!-- Bouton de retour -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg fw-semibold">
                                Retour à l'accueil
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection