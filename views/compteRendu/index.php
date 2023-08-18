<h1>Compte rendu de <?= $prenom . ' ' . $nom ?></h1>
<div class="calendar">
  <nav>
    <ul class="pagination justify-content-center">
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
  <div class="row">
    <span class="col border border-dark">Lundi</span>
    <span class="col border border-dark">Mardi</span>
    <span class="col border border-dark">Mercredi</span>
    <span class="col border border-dark">Jeudi</span>
    <span class="col border border-dark">Vendredi</span>
    <span class="col border border-dark">Samedi</span>
    <span class="col border border-dark">Dimanche</span>
  </div>
  <div class="row justify-content-end">
    <?php foreach($jcr as $jour): ?>
      <div class="col card">
        <div class="card-body">
          <h3 class="card-title"><?= date_format(new \Datetime($jour->DATE_JOUR), 'j') ?></h3>
          <form method="post" action="/jourCompteRendu/modifierTicket">
            <input type="hidden" id="id_salarie" name="id_salarie" value="<?= $cr->ID_SALARIE ?>" />
            <input type="hidden" id="ticket" name="ticket" value="<?= $jour->TICKET == 0 ? 1 : 0 ?>" />
            <button class="btn btn-<?= $jour->TICKET == 0 ? "outline-dark" : "success" ?>" type="submit" name="date" value="<?= $jour->DATE_JOUR ?>" ><?= $jour->TICKET == 0 ? "+ " : "- " ?><i class="fa fa-credit-card" aria-hidden="true"></i></button>
            <a class="btn btn-light" href="/jourCompteRendu/modifierJour/<?= $jour->ID_SALARIE ?>/<?= $jour->DATE_JOUR ?>"><?= trim($jour->NOTES_JOUR) == '' ? "+ " : "<i class=\"fa fa-eye\" aria-hidden=\"true\"></i>" ?><i class="fa fa-file-text-o" aria-hidden="true"></i></a>
          </form>
        </div>
      </div>
      <?php if (date_format(new \Datetime($jour->DATE_JOUR), 'w') == 0) : ?>
        </div>
        <div class="row justify-content-start">
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
<a class="btn btn-light" href="/compteRendu/modifierVehicule/<?= $jour->ID_SALARIE ?>/<?= $cr->DATE_CR ?>">Infos Véhicule<i class="fa fa-car" aria-hidden="true"></i></a>