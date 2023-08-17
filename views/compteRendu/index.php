<h1>Compte rendu de <?= $prenom . ' ' . $nom?></h1>
<div class="calendar">
  <div class="month"><a href="#" class="nav"><i class="fa fa-angle-left"></i></a><div>January <span class="year">2019</span></div><a href="#" class="nav"><i class="fa fa-angle-right"></i></a></div>
  <div class="days">
    <span>Lundi</span>
    <span>Mardi</span>
    <span>Mercredi</span>
    <span>Jeudi</span>
    <span>Vendredi</span>
    <span>Samedi</span>
    <span>Dimanche</span>
  </div>
  <div class="dates">
    <form method="post" action="/compteRendu/modifierTicket">
        <input type="hidden" id="id_salarie" name="id_salarie" value="<?= $cr->ID_SALARIE ?>" />
        <?php foreach($jcr as $jour): ?>
            <div>
                <h3><?= date_format(new Datetime($jour->DATE_JOUR), 'j') ?></h3>
                <input type="hidden" id="ticket" name="ticket" value="<?= $jour->TICKET == 0 ? 1 : 0 ?>" />
                <button type="submit" name="date" value="<?= $jour->DATE_JOUR ?>" ><?= $jour->TICKET == 0 ? "+" : "-" ?><i class="fa fa-credit-card" aria-hidden="true"></i></button>
                <p><?= $jour->NOTES_JOUR ?></p>
            </div>
        <?php endforeach; ?>
    <form>
  </div>
</div>
