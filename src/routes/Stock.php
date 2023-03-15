<?php
namespace Surricate;

class Stock extends Controller{
    use AchatsController;
    use LivraisonsController;
    use UtilisationsController;
    use JournauxController;

    
    public function achats(){
        $this->manageAchats(__FUNCTION__);
    }
    public function livraisons(){
        $this->manageLivraisons(__FUNCTION__);
    }
    public function utilisations(){
        $this->manageUtilisations(__FUNCTION__);
    }
    public function journaux($slug='articles'){
        $this->manageJournaux(__FUNCTION__,$slug);
    }
    
}
