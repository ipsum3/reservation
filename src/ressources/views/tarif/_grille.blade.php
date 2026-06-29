<div class="box {{ (isset($loop) and !$loop->first) ? 'mt-20' : '' }}">
    <div class="box-header">
        <h2 class="box-title">Grille de tarifs {{ isset($condition) ? $condition->nom : '' }}</h2>
        @if (!isset($loop) or $loop->first)
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <a href="{{ route('admin.tarif.impression', [$saison->id]) }}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Imprimer la grille de tarif"><i class="fas fa-print"></i></a>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
            </div>
        @endif
    </div>
    <div class="box-body">
        <div class="form-inline mt-4 mb-1 adjust">
            <input type="number" class="form-control mb-2 mr-sm-2 ajust-valeur" placeholder="Valeur" size="2" style="width: 90px">
            <select class="form-control mb-2 mr-sm-2 ajust-type">
                <option value="montant">Montant</option>
                <option value="pourcentage">Pourcentage</option>
            </select>
            <button type="button" class="btn btn-outline-secondary mb-2 ajust-button">Ajuster la grille</button>
        </div>
        <div class="table-wrapper">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th></th>
                @foreach ($durees as $duree)
                    <th style="width: 150px;">
                        <x-reservation::tranche_de_duree :min="$duree->min_display" :max="$duree->max"/>
                        @if ($duree->nom)
                            <br>{{ $duree->nom }}
                        @endif
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $categorie)
                <tr class="{{ $loop->even ? " pair" : "impair" }}">
                    <td>{{ $categorie->nom }}</td>
                    @foreach ($durees as $duree)
                        <td class="center">
                            <input type="number" name="{{ 'tarifs['.(isset($condition) ? $condition->id : 'x').']['.$categorie->id.']['.$duree->id.']' }}" value="{{ $tarifs[(isset($condition) ? $condition->id : null)][$categorie->id][$duree->id] ?? null }}" step=".01" class="form-control text-right montant">
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>