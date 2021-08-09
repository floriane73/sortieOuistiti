<?php

namespace App\Data;

use App\Entity\Campus;
use DateTime;

class FiltresData {

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Campus
     */
    public $campus;

    /**
     * @var null|DateTime
     */
    public $dateMin;

    /**
     * @var null|DateTime
     */
    public $dateMax;

    /**
     * @var boolean
     */
    public $isOrganisateur = false;

    /**
     * @var boolean
     */
    public $isParticipant = false;

    /**
     * @var boolean
     */
    public $isNotParticipant = false;

    /**
     * @var boolean
     */
    public $isSortiePassee = false;


}