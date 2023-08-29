<h1>Compte rendu de <?= $prenom . ' ' . $nom ?></h1>
<div class="calendar">
  <nav>
    <ul class="pagination pagination-lg justify-content-center">
      <li class="page-item">
        <a class="page-link" href="/compteRendu/affiche/<?= $cr->ID_SALARIE ?>/<?= date("Y-m-01", strtotime ( '-1 month' , strtotime ( $cr->DATE_CR ) )) ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <li class="page-item page-link"><?= date_format(new \Datetime($cr->DATE_CR), 'Y-m') ?></li>
      <li class="page-item">
        <a class="page-link" href="/compteRendu/affiche/<?= $cr->ID_SALARIE ?>/<?= date("Y-m-01", strtotime ( '+1 month' , strtotime ( $cr->DATE_CR ) )) ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>
  <div class='text-center'>
    <h3>
      Statut du compte rendu:
      <small class="text-muted"><?= $cr->STATUT_CR ?></small>
    </h3>
    <h3>
      Nombre de ticket:
      <small class="text-muted"><?= $cr->NB_TICKET ? $cr->NB_TICKET : 0 ?><i class="fa fa-credit-card" aria-hidden="true"></i></small>
    </h3>
  </div>
  <div class="row">
    <span class="col border border-dark">Lundi</span>
    <span class="col border border-dark">Mardi</span>
    <span class="col border border-dark">Mercredi</span>
    <span class="col border border-dark">Jeudi</span>
    <span class="col border border-dark">Vendredi</span>
    <span class="col border border-dark">Samedi</span>
    <span class="col border border-dark">Dimanche</span>
  </div>
  <div class="row">
    <?php for ($i=1; $i < date_format(new \Datetime($jcr[0]->DATE_JOUR), 'N'); $i++): ?>
      <div class="col card">
      </div>
    <?php endfor; ?>
    <?php foreach($jcr as $jour): ?>
      <div class="col card <?= date_format(new \Datetime($jour->DATE_JOUR), 'N') == 6 || date_format(new \Datetime($jour->DATE_JOUR), 'N') == 7 ? 'bg-light' : 'bg-white' ?>">
        <div class="card-body">
          <h3 class="card-title"><?= date_format(new \Datetime($jour->DATE_JOUR), 'j') ?></h3>
          <form method="post" action="/jourCompteRendu/modifierTicket">
            <input type="hidden" id="id_salarie" name="id_salarie" value="<?= $cr->ID_SALARIE ?>" />
            <input type="hidden" id="ticket" name="ticket" value="<?= $jour->TICKET == 0 ? 1 : 0 ?>" />
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <button class="btn btn-sm btn-<?= $jour->TICKET == 0 ? "outline-dark" : "success" ?>" type="submit" name="date" value="<?= $jour->DATE_JOUR ?>" ><?= $jour->TICKET == 0 ? "+ " : "- " ?><i class="fa fa-credit-card" aria-hidden="true"></i></button>
              <?php if (trim($jour->NOTES_JOUR) == '' && empty(round($jour->FRAIS_JOUR, 2)) && empty(round($jour->KM_VEHICULE_PRO, 2))  && empty(round($jour->KM_VEHICULE_PERSO, 2))): ?>
                <a class="btn btn-sm btn-outline-dark" href="/jourCompteRendu/modifierJour/<?= $jour->ID_SALARIE ?>/<?= $jour->DATE_JOUR ?>">+<i class="fa fa-file-text-o" aria-hidden="true"></i></a>
              <?php else: ?>
                <a class="btn btn-sm btn-outline-info" href="/jourCompteRendu/modifierJour/<?= $jour->ID_SALARIE ?>/<?= $jour->DATE_JOUR ?>"><i class="fa fa-eye" aria-hidden="true"></i><i class="fa fa-file-text-o" aria-hidden="true"></i></a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
      <?php if (date_format(new \Datetime($jour->DATE_JOUR), 'N') == 7) : ?>
        </div>
        <div class="row">
      <?php endif; ?>
    <?php endforeach; ?>
    <?php for ($i=date_format(new \Datetime(end($jcr)->DATE_JOUR), 'w'); $i < 7; $i++): ?>
      <div class="col card">
      </div>
    <?php endfor; ?>
  </div>
</div>
<div class="d-flex justify-content-around">
  <a class="btn btn-light" href="/compteRendu/modifierVehicule/<?= $jour->ID_SALARIE ?>/<?= $cr->DATE_CR ?>">Infos Véhicule<i class="fa fa-car" aria-hidden="true"></i></a>
  <a class="btn btn-success" href="/compteRendu/envoyer/<?= $jour->ID_SALARIE ?>/<?= $cr->DATE_CR ?>">Envoyer<i class="fa fa-paper-plane"></i></a>
</div>