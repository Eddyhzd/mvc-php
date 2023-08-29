<h1>Manager: liste des salariés</h1>
<nav>
    <ul class="pagination pagination-lg justify-content-center">
      <li class="page-item">
        <a class="page-link" href="/manager/affiche/<?= date("Y-m-01", strtotime('-1 month', strtotime($date))) ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <li class="page-item page-link"><?= date_format(new \Datetime($date), 'Y-m') ?></li>
      <li class="page-item">
        <a class="page-link" href="/manager/affiche/<?= date("Y-m-01", strtotime('+1 month', strtotime($date))) ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
</nav>
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
        <?php foreach ($users as $key => $user) :
            if (array_key_exists($user->ID_SALARIE,$crs)):
                $cr = $crs[$user->ID_SALARIE];
        ?>
            <tr class='row'>
                <td class='col'><?= $user->NOM ?></td>
                <td class='col'><?= $user->PRENOM ?></td>
                <td class='col'><?= $cr->STATUT_CR ?></td>
                <td class='col'><?= $cr->NB_TICKET ?></td>
                <td class='col'></td>
                <td class='col'><a href="#">Relancer</a></td>
                <td class='col'><a href="#">Voir PDF</a></td>
                <td class='col'><a href="#">Modifier</a></td>
                <td class='col'><a href="#">Valider</a></td>
            </tr>
            <?php else :?>
            <tr class='row'>
                <td class='col'><?= $user->NOM ?></td>
                <td class='col'><?= $user->PRENOM ?></td>
                <td class='col'>Non entamé</td>
                <td class='col'></td>
                <td class='col'></td>
                <td class='col'><a href="#">Relancer</a></td>
                <td class='col'><a href="#">Voir PDF</a></td>
                <td class='col'><a href="#">Modifier</a></td>
                <td class='col'><a href="#">Valider</a></td>
            </tr>
            <?php endif;?>
        <?php endforeach;?>
    </tbody>
</table>