<h1>Compte rendu de <?= $prenom . ' ' . $nom ?></h1>
<div class="calendar">
  <nav>
    <ul class="pagination pagination-lg justify-content-center">
      <li class="page-item">
        <a class="page-link" href="/<?=$chemin?>/<?= $cr->ID_SALARIE ?>/<?= date("Y-m-01", strtotime ( '-1 month' , strtotime ( $cr->DATE_CR ) )) ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <li class="page-item page-link"><?= date_format(new \Datetime($cr->DATE_CR), 'Y-m') ?></li>
      <li class="page-item">
        <a class="page-link" href="/<?=$chemin?>/<?= $cr->ID_SALARIE ?>/<?= date("Y-m-01", strtotime ( '+1 month' , strtotime ( $cr->DATE_CR ) )) ?>" aria-label="Next">
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
    <span class="col border border-dark text-center">Lundi</span>
    <span class="col border border-dark text-center">Mardi</span>
    <span class="col border border-dark text-center">Mercredi</span>
    <span class="col border border-dark text-center">Jeudi</span>
    <span class="col border border-dark text-center">Vendredi</span>
    <span class="col border border-dark text-center">Samedi</span>
    <span class="col border border-dark text-center">Dimanche</span>
  </div>
  <div class="row">
    <?php for ($i=1; $i < date_format(new \Datetime($jours[0]->getDate_jour()), 'N'); $i++): ?>
      <div class="col card">
      </div>
    <?php endfor; ?>
    <?php foreach($jours as $jour): ?>
      <div class="col card <?= date_format(new \Datetime($jour->getDate_jour()), 'N') == 6 || date_format(new \Datetime($jour->getDate_jour()), 'N') == 7 ? 'bg-light' : 'bg-white' ?>">
        <div class="card-body">
          <h3 class="card-title"><?= date_format(new \Datetime($jour->getDate_jour()), 'j') ?></h3>
          <form method="post" action="/jourCompteRendu/modifierTicket">
            <input type="hidden" id="id_salarie" name="id_salarie" value="<?= $jour->getId_salarie() ?>" />
            <input type="hidden" id="ticket" name="ticket" value="<?= $jour->getTicket() == 0 ? 1 : 0 ?>" />
            <?php if ((date_format(new \Datetime($jour->getDate_jour()), 'N') != 6 && date_format(new \Datetime($jour->getDate_jour()), 'N') != 7) 
            || ($jour->getConges_matin() == 'JTR' && $jour->getConges_apresmidi() == 'JTR')):?>
            <div class="btn-group btn-group-toggle pull-right" data-toggle="buttons">
              <?php if ((!$jour->getConges_matin() && !$jour->getConges_apresmidi()) 
              || ($jour->getConges_matin() == 'JTR' && $jour->getConges_apresmidi() == 'JTR')):?>
              <button class="btn btn-sm btn-<?= $jour->getTicket() == 0 ? "outline-dark" : "success" ?>" type="submit" name="date" value="<?= $jour->getDate_jour() ?>" ><?= $jour->getTicket() == 0 ? "+ " : "- " ?><i class="fa fa-credit-card" aria-hidden="true"></i></button>
              <?php endif; ?>
              <?php if (trim($jour->getNotes_jour()) == '' && empty(round($jour->getFrais_jour(), 2)) && empty(round($jour->getKm_vehicule_pro(), 2))  && empty(round($jour->getKm_vehicule_perso(), 2))): ?>
                <a class="btn btn-sm btn-outline-dark" href="/jourCompteRendu/modifierJour/<?= $jour->getId_salarie() ?>/<?= $jour->getDate_jour() ?>">+<i class="fa fa-file-text-o" aria-hidden="true"></i></a>
              <?php else: ?>
                <a class="btn btn-sm btn-outline-info" href="/jourCompteRendu/modifierJour/<?= $jour->getId_salarie() ?>/<?= $jour->getDate_jour() ?>"><i class="fa fa-eye" aria-hidden="true"></i><i class="fa fa-file-text-o" aria-hidden="true"></i></a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </form>
        </div>
        <?php if ($jour->getConges_matin() || $jour->getConges_apresmidi()):?>
        <div class="card-footer row bg-transparent border-0 p-0">
          <div class="col-6 text-center" style="background-color: <?=$jour->getColor_matin()?>"><?=$jour->getConges_matin()?></div>
          <div class="col-6 text-center" style="background-color: <?=$jour->getColor_apresmidi()?>"><?=$jour->getConges_apresmidi()?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php if (date_format(new \Datetime($jour->getDate_jour()), 'N') == 7) : ?>
        </div>
        <div class="row">
      <?php endif; ?>
    <?php endforeach; ?>
    <?php for ($i=date_format(new \Datetime(end($jours)->getDate_jour()), 'w'); $i < 7; $i++): ?>
      <div class="col card">
      </div>
    <?php endfor; ?>
  </div>
</div>
<div class="d-flex justify-content-around">
  <a class="btn btn-light" href="/compteRendu/modifierVehicule/<?= $cr->ID_SALARIE ?>/<?= $cr->DATE_CR ?>">Infos Véhicule<i class="fa fa-car" aria-hidden="true"></i></a>
  <a class="btn btn-success" href="/compteRendu/envoyer/<?= $cr->ID_SALARIE ?>/<?= $cr->DATE_CR ?>">Envoyer<i class="fa fa-paper-plane"></i></a>
</div>