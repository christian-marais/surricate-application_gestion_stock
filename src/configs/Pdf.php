<?php
namespace Surricate;
require(ROOT.'src/lib/FPDF/FPDF.php');
require(ROOT.'src/lib/FPDF/PDF_Linegraph.php');
use \Fpdf\Fpdf;
use \Fpdf\PDF_Linegraph;

class PDF extends PDF_Linegraph {

  protected $col = 0; 
  protected $y0;    
  protected static $pdf;



  public function Header()
  {
    global $title;
    $title='CENTRE DE FORMATION AATI';
    $this->setMargins(5,0);
    $this->Image(ROOT.'images/banque/AATI/logo.png',10,8,33);
    $this->SetFont('Arial','B',15);
    $w = $this->GetStringWidth($title)+12;
    $this->SetX((210-$w)/2);
    $this->SetDrawColor(56,80,180);
    $this->SetFillColor(255,255,255);
    $this->SetTextColor(33,158,188);
    $this->SetLineWidth(0.5);
    $this->Cell($w,12,$title,1,1,'C',true);
    $this->Ln(10);
    $this->y0 = $this->GetY();
  }
 
  public function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->SetTextColor(128);
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
  }
   
  public function SetCol($col)
  {
    $this->col = $col;
    $x = 10+$col*65;
    $this->SetLeftMargin($x);
    $this->SetX($x);
  }

  public function AcceptPageBreak()
  {
    if($this->col<2)
    {
      $this->SetCol($this->col+1);
      $this->SetY($this->y0);
      return false;
    }
    else
    {
      $this->SetCol(0);
      return true;
    }
  }

  public function title($num, $label)
  {
    $this->SetFont('Arial','',12);
    $this->SetFillColor(200,220,255);
    $this->Cell(0,6,"Titre $num : $label",0,1,'L',true);
    $this->Ln(4);
    $this->y0 = $this->GetY();
  }
 
  public function body($file)
  {
    $txt = file_get_contents(ROOT.'src/config/Mail.php');
    $this->SetFont('Times','',12);
    $this->MultiCell(60,5,$txt);
    $this->Ln();
    $this->SetFont('','I');
    $this->Cell(0,5,'(end of excerpt)');
    $this->SetCol(0);
  }

  public function Print($num, $title, $file)
  {
    $this->AddPage();
    $this->title($num,$title);
    $this->body($file);
  }

  public function createPdf(){
  $pdfs = new PDF();
  $title = '';
  $pdfs->SetTitle($title);
  $pdfs->SetAuthor('');
  $pdfs->Print(1,'','20k_c1.txt');
  $pdfs->Print(2,'','20k_c2.txt');
  $pdfs->Output();
  }

  public function createBon($name,$bon,$articles){
    $pdf = new PDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',20);
    $pdf->SetY(60);
    $pdf->Cell(60 ,10,'',0,0);
    $pdf->Cell(59 ,5,'Bon de '.$name,0,0);
    $pdf->Cell(59 ,30,'',0,1);
    if($name=='livraison'){
      $numero= $bon['id_reception'];
      $date=$bon['date_reception'];
      $adresse = '';
      $cp = '';
      $email ='';
      $tel='';

    }else{
      $numero = $bon['num_com'];
      $date = $bon['date_Com'];
      $adresse= $bon['adresse_fournisseur'];
      $cp = $bon['cp_fournisseur'];
      $email = $bon['email'];
      $tel= $bon['tel'] ;

    }
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(71 ,5,'Numero de bon : '.$numero,0,1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial','i',12);
    $pdf->Cell(121 ,5,'',0,0);
    $pdf->Cell(71 ,5,'Date : '.$date,0,1);
    $pdf->Ln(5);
    $pdf->Cell(71 ,5,'Fournisseur : '.ucfirst($bon['nom_fournisseur']),0,0);
    $pdf->Cell(50 ,5,'',0,0);
    $pdf->Cell(130 ,5,'Centre de formation AATI ',0,0);
    $pdf->Cell(59 ,5,'',0,1);
    $pdf->Cell(71 ,5,'Adresse : '.$adresse,0,0);
    $pdf->Cell(50 ,5,'',0,0);
    $pdf->Cell(130 ,5,'525 rue Andropolis',0,0);
    $pdf->Cell(59 ,5,'',0,1);
    $pdf->Cell(71 ,5,'Code Postal : '.$cp,0,0);
    $pdf->Cell(50 ,5,'',0,0);
    $pdf->Cell(130 ,5,'Saint-Andre',0,0);
    $pdf->Cell(59 ,5,'',0,1);
    $pdf->Cell(71 ,5,'Email :'.$email,0,0);
    $pdf->Cell(50 ,5,'',0,0);
    $pdf->Cell(130 ,5,'97440 La Reunion',0,0);
    $pdf->Cell(59 ,5,'',0,1);
    $pdf->Cell(71 ,5,'Telephone : '.$tel,0,1);
    $pdf->Cell(71, 10,'',0,1);
    

    $pdf->Cell(50 ,10,'',0,1);

    $pdf->SetFont('Arial','B',10);
    if($name=='commande'){
      $pdf->Cell(10 ,6,'id',1,0,'C');
      $pdf->Cell(23 ,6,'Reference',1,0,'C');
      $pdf->Cell(80 ,6,'Description',1,0,'C');
      $pdf->Cell(30 ,6,'PU',1,0,'C');
      $pdf->Cell(20 ,6,'Quantite',1,0,'C');
      $pdf->Cell(25 ,6,'Montant euro',1,1,'C');
      $somme='';
      
      $pdf->SetFont('Arial','',10);
          foreach($articles as $article){
            
          $pdf->Cell(10 ,6,' '.$article['id_article'],1,0,'C');
          $pdf->Cell(23 ,6,$article['reference_article'],1,0,'C');
          $pdf->Cell(80 ,6,$article['description_article'],1,0,'C');
          $pdf->Cell(30 ,6,$article['pu'],1,0,'R');
          $pdf->Cell(20 ,6,$article['qte'],1,0,'R');
          $pdf->Cell(25 ,6,$article['Montant TTC'],1,1,'R');
          $somme.=$article['Montant TTC'].',';
        }
          $somme=explode(',',$somme);
          
  
      $pdf->Cell(118 ,6,'',0,0);
      $pdf->SetFont('Arial','B',10);
      $pdf->Cell(25 ,6,'TOTAL',0,0);
      $pdf->Cell(45 ,6,array_sum($somme),1,1,'R');

    }elseif($name=='livraison'){
      $pdf->SetFont('Arial','B',8);
      $pdf->Cell(10 ,6,'id',1,0,'C');
      $pdf->Cell(50 ,6,'Libelle',1,0,'C');
      $pdf->Cell(23 ,6,'Reference',1,0,'C');
      $pdf->Cell(25 ,6,'Fournisseur',1,0,'C');
      $pdf->Cell(20 ,6,'Prix',1,0,'C');
      $pdf->Cell(15 ,6,'Qte Com',1,0,'C');
      $pdf->Cell(15,6,'Qte Livr',1,0,'C');
      $pdf->Cell(40 ,6,'Statut',1,1,'C');
      
      $pdf->SetFont('Arial','',8);
          foreach($articles as $article){
          $pdf->Cell(10 ,6,' '.$article['id_article'],1,0,'C');
          $pdf->Cell(50 ,6,$article['Libelle'],1,0,'C');
          $pdf->Cell(23 ,6,$article['Référence'],1,0,'C');
          $pdf->Cell(25 ,6,$article['id_fournisseur'],1,0,'R');
          $pdf->Cell(20 ,6,$article['Prix'],1,0,'R');
          $pdf->Cell(15 ,6,$article['Qte commandee'],1,0,'R');
          $pdf->Cell(15 ,6,$article['Qte livree'],1,0,'R');
          $pdf->Cell(40 ,6,$article['Statut'],1,1,'C');
        }

    }
    
    $pdf->Output();

  }
  public function testPDF(){
    $pdf= new PDF;
    $pdf->addPage();
    $pdf->SetTitle('');
    $pdf->output();
  }

  public function createGraph(){
    
    $pdf = new PDF();
    $pdf->SetFont('Arial','',10);
    $data = array(
        'Groupe 1' => array(
            '08-02' => 2.7,
            '08-23' => 3.0,
            '09-13' => 3.3928571,
            '10-04' => 3.2903226,
            '10-25' => 3.1
        ),
        'Groupe 2' => array(
            '08-02' => 2.5,
            '08-23' => 2.0,
            '09-13' => 3.1785714,
            '10-04' => 2.9677419,
            '10-25' => 3.33333
        )
    );
    $colors = array(
        'Groupe 1' => array(114,171,237),
        'Groupe 2' => array(163,36,153)
    );

    $pdf->AddPage();

    $pdf->LineGraph(190,100,$data,'VHkBvBgBdB',$colors,6,3);

    $pdf->AddPage();

    $pdf->LineGraph(190,100,$data,'HvB');

    $pdf->AddPage();
  
    $pdf->LineGraph(190,100,$data,'VkB');

    $pdf->AddPage();

    $pdf->LineGraph(190,100,$data,'HgBdB',null,20,10);

    $pdf->Output();
  }
}
?>  