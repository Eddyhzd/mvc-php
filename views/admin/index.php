<h1>Admin: liste des salariés</h1>
<nav>
    <ul class="pagination pagination-lg justify-content-center">
      <li class="page-item">
        <a class="page-link" href="/admin/affiche/<?= date("Y-m-01", strtotime('-1 month', strtotime($date))) ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <li class="page-item page-link"><?= date_format(new \Datetime($date), 'Y-m') ?></li>
      <li class="page-item">
        <a class="page-link" href="/admin/affiche/<?= date("Y-m-01", strtotime('+1 month', strtotime($date))) ?>" aria-label="Next">
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
      <?php foreach ($users as $key => $user) : ?>
        <tr class='row align-items-center <?= $key%2 == 0 ? 'bg-light' : 'bg-white'?>'>
          <td class='col'><?= $user->getNom() ?></td>
          <td class='col'><?= $user->getPrenom() ?></td>
          <?php if (array_key_exists($user->getId(),$crs)):
                  $cr = $crs[$user->getId()];
          ?>
          <td class='col'><?= $cr->STATUT_CR ?></td>
          <td class='col'><?= $cr->NB_TICKET ?></td>
          <td class='col'></td>
          <td class='col'><a class='btn btn-primary' href="#">Relancer</a></td>
          <td class='col'><a class='btn btn-primary <?= in_array($cr->STATUT_CR, array('Envoyé', 'Validé')) ? '' : ' disabled' ?>' href="/admin/pdf/<?=$user->getNom()?>/<?=$user->getNom()?>/<?=date("Y-m-01",strtotime( $cr->DATE_CR))?>">Voir PDF</a></td>
          <td class='col'><a class='btn btn-primary' href="/admin/compteRendu/<?= $cr->ID_SALARIE ?>/<?= date("Y-m-01",strtotime( $cr->DATE_CR )) ?>">Modifier</a></td>
          <td class='col'><a class='btn btn-primary' href="#">Valider</a></td>
          <?php else :?>
          <td class='col'>Non entamé</td>
          <td class='col'></td>
          <td class='col'></td>
          <td class='col'><a class='btn btn-primary disabled' href="#">Relancer</a></td>
          <td class='col'><a class='btn btn-primary disabled' href="#">Voir PDF</a></td>
          <td class='col'><a class='btn btn-primary disabled' href="#">Modifier</a></td>
          <td class='col'><a class='btn btn-primary disabled' href="#">Valider</a></td>
          <?php endif;?>
        </tr>
      <?php endforeach;?>
    </tbody>
</table>