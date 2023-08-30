<?php
namespace App\Models;

class CongeModel extends Model{
    protected $id;
    protected $id_salarie;  //PCN_SALARIE
    protected $debut;       //PCN_DATEDEBUTABS
    protected $fin;         //PCN_DATEFINABS
    protected $debutj;      //PCN_DEBUTDJ
    protected $finj;       //PCN_FINDJ
    protected $conge;        //PCN_TYPECONGE
    protected $valide;        //PCN_VALIDRESP
    

    public function __construct()
    {
        $this->table = 'PGMVTABS';
        $this->base = 'PGI';
    }

    public function findByDateAndSalarie(string $dateDebut, string $dateFin, int $id_salarie){
        return $this->requete(
            "WITH TEMP AS (
                SELECT PCN_SALARIE,
                PCN_DATEDEBUTABS,
                PCN_DATEFINABS,
                PCN_DEBUTDJ,
                PCN_FINDJ,
                PCN_TYPECONGE,
            	PCN_VALIDRESP
                FROM PGMVTABS
                UNION 
                SELECT PCN_SALARIE,
                PCN_DATEDEBUTABS,
                PCN_DATEFINABS,
                PCN_DEBUTDJ,
                PCN_FINDJ,
                PCN_TYPECONGE,
            	PCN_VALIDRESP
                FROM [TECMATEL].[dbo].PGMVTABS 
            )
            SELECT DISTINCT * FROM TEMP
            WHERE RIGHT(PCN_SALARIE,6) = ?
            AND PCN_VALIDRESP IN ('VAL','ATT')
            AND PCN_DATEFINABS >= CAST(? as date) AND PCN_DATEDEBUTABS <= CAST(? as date)
            ORDER BY PCN_DATEDEBUTABS", [$id_salarie, $dateDebut, $dateFin])->fetchAll();
    }
}