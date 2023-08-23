<h1>Liste des salariés</h1>
<table class='container'>
    <thead>
        <tr class='row'>
            <th class='col'>Nom</th>
            <th class='col'>Prenom</th>
            <th class='col'>Statut CR</th>
            <th class='col'>Nb Tickets</th>
            <th class='col'>Infos</th>
            <th class='col'></th>
            <th class='col'></th>
            <th class='col'></th>
            <th class='col'></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($crs as $key => $cr) : ?>
        <tr class='row'>
            <td class='col'><?= array_search($cr->ID_SALARIE, array_column($_SESSION['user']['subs'], 'ID_SALARIE')) ?></td>
            <td class='col'><?= $_SESSION['user']['subs'][array_search($cr->ID_SALARIE, array_column($_SESSION['user']['subs'], 'ID_SALARIE'))]['PRENOM'] ?></td>
            <td class='col'>A Remplir</td>
            <td class='col'>0</td>
            <td class='col'></td>
            <td class='col'><a href="#">Relancer</a></td>
            <td class='col'><a href="#">Voir PDF</a></td>
            <td class='col'><a href="#">Modifier</a></td>
            <td class='col'><a href="#">Valider</a></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>