<?php

class Pdf_Model extends Model{

    private $pdf;

    function __construct(){
        parent::__construct();

        $this->pdf = new PDF_Rotate();
    }

    public function invoice($data = [], $output = "stream")
    {
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->Header();
        $this->setOwner($data['utilizador']);
        $this->invoiceBody($data["encomenda"], $data["detalhes"]);
        if(in_array($data["encomenda"]["estado"], [Encomenda_Model::REJECTED, Encomenda_Model::CANCELED]))
            $this->watermark("Encomenda Anulada");
        $this->footer();

        if($output=="stream") {
            return $this->pdf->Output("", "S");
        }else{
            return $this->pdf->Output($data["encomenda"]["id_encomenda"], "I");
        }

    }

    public function watermark($text='')
    {
        $this->pdf->SetFont('Arial','B',40);
        $this->pdf->SetTextColor(173,216,230);
        $this->RotatedText(20,220,utf8_decode($text),15);
        $this->pdf->SetTextColor(0,0,0);
    }

    public function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->pdf->Rotate($angle,$x,$y);
        $this->pdf->Text($x,$y,$txt);
        $this->pdf->Rotate(0);
    }

    public function RotatedImage($file,$x,$y,$w,$h,$angle)
    {
        //Image rotated around its upper-left corner
        $this->pdf->Rotate($angle,$x,$y);
        $this->pdf->Image($file,$x,$y,$w,$h);
        $this->pdf->Rotate(0);
    }

    // Page header
    public function Header(){
        // Logo
        $this->pdf->Image(PDF_IMGS."logo_lightblue.png",13,6,70);
        // Arial bold 15
        $this->pdf->SetFont('Arial','B',15);
        $this->pdf->SetTextColor(0,0,0);
        // Move to the right
        $this->pdf->Cell(80);
        // Title
        $this->pdf->SetXY(-17,4);
        $this->pdf->SetFont('Arial','I',8);
        $this->pdf->Cell(15, 3, $this->pdf->PageNo().'/{nb}', 0, 0, 'C');
        // Line break
        $this->pdf->Ln(30);
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetX(13);
        $this->pdf->cell(0, 4, utf8_decode('Rua do cais, N.º 231'), 0, 1, 'L');
        $this->pdf->SetX(13);
        $this->pdf->cell(0, 4, '4830-345 Fonte Arcada PVL', 0, 1, 'L');
        $this->pdf->SetX(13);
        $this->pdf->cell(0, 4, 'Povoa de Lanhoso', 0, 1, 'L');
        $this->pdf->SetX(13);
        $this->pdf->cell(0, 4, 'Telef. 253 631 783  Fax. 253 738 194', 0, 0, 'L');
    }

    public function setOwner($utilizador){
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetX(130);
        $this->pdf->cell(0, 4, 'Exmo.(s) Sr.(s)', 0, 1, 'L');
        $this->pdf->SetX(130);
        $this->pdf->cell(0, 4, utf8_decode($utilizador["nome"]." ".$utilizador["apelido"]), 0, 1, 'L');
        $this->pdf->SetX(130);
        $this->pdf->cell(0, 4, utf8_decode($utilizador["rua"]." ".$utilizador["codigo_postal"]), 0, 1, 'L');
        $this->pdf->SetX(130);
        $this->pdf->cell(0, 4, utf8_decode($utilizador["localidade"]." ".$utilizador["cidade"]), 0, 1, 'L');
    }

    public function invoiceBody($encomenda, $detalhes){
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Ln(17);
        $this->pdf->SetX(13);
        $this->pdf->Cell(0, 5, "Encomenda ".$encomenda['id_encomenda'], 0, 0, 'L');
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->SetX(173);
        $this->pdf->Cell(0, 5, date("Y-m-d", strtotime($encomenda['data_encomenda'])), 0, 1, 'L');
        $this->pdf->SetX(13);
        $this->pdf->Cell(180, 1, "", 0, 1, 'L', true);
        $this->pdf->Ln(5);
        $this->pdf->SetFillColor(255,255,255);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetDrawColor(0,48,92);
        $this->pdf->SetLineWidth(.3);
        $this->pdf->SetFont('Arial','B',8);
        // Header
        $w = array(40, 81, 6, 26, 3, 23);
        $this->pdf->SetX(13);
        $fill = false;
        $this->pdf->Cell($w[0],6,"Artigo",'B',0,'L',$fill);
        $this->pdf->Cell($w[1],6,utf8_decode("Descrição"),'B',0,'L',$fill);
        $this->pdf->Cell($w[2],6,"Qtd",'B',0,'C',$fill);
        $this->pdf->Cell($w[3],6,utf8_decode("Pr.Unitário"),'B',0,'C',$fill);
        $this->pdf->Cell($w[4],6,"Iva",'B',0,'C',$fill);
        $this->pdf->Cell($w[5],6,"Ttl. Liquido",'B',0,'R',$fill);
        $this->pdf->Ln();
        // Color and font restoration
        $this->pdf->SetFillColor(240,240,240);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('Arial','',8);
        // Data
        $fill = false;
        $eco = 0; $qtd = 0;
        foreach($detalhes as $row){
            $this->pdf->SetX(13);
            $this->pdf->Cell($w[0],6,$row['id_primavera'],'',0,'L',$fill);
            $this->pdf->Cell($w[1],6,$this->truncate($row['produto'], 50),'',0,'L',$fill);
            $this->pdf->Cell($w[2],6,$row['qty'],'',0,'C',$fill);
            $this->pdf->Cell($w[3],6,$row['preco_sem_iva'],'',0,'C',$fill);
            $this->pdf->Cell($w[4],6,$row['iva'],'',0,'C',$fill);
            $subtotal = $row['preco_sem_iva'] * $row['qty'] * ( 1 + ($row['iva']/100) );
            $this->pdf->Cell($w[5],6,$subtotal,'',0,'R',$fill);
            $this->pdf->Ln();
            $fill = !$fill;
            $eco += $row['eco'];
            $qtd += $row['qty'];
        }
        $fill = false;
        $this->pdf->Ln(5);
        $this->pdf->SetX(13);
        $this->pdf->Cell(array_sum($w),0,'','T');
        $this->pdf->Ln(5);
        $this->pdf->SetDrawColor(255,255,255);
        //RESUMO:
        $sub = array(40, 16, 15, 16, 15, 20, 28, 10, 20);
        $this->pdf->SetX(13);
        $this->pdf->Cell($sub[0],6,"Desconto:",0,0,'R',0);
        $this->pdf->Cell($sub[1],6, (isset($encomenda['desconto']) ? $encomenda['desconto'] : 0)." ",1,0,'C',1);
        $this->pdf->Cell($sub[2],6,"Eco:",0,0,'R',0);
        $this->pdf->Cell($sub[3],6, $eco." ",1,0,'C',1);
        $this->pdf->Cell($sub[4],6,"IVA:",0,0,'R',0);
        $iva= round(($encomenda['total_sem_iva']+$eco)*($encomenda['iva']/100),2);
        $this->pdf->Cell($sub[5],6, $iva." ",0,0,'C',1);
        $this->pdf->Cell($sub[6],6,"Total:",0,0,'R',0);
        $this->pdf->Cell($sub[7],6, $qtd,1,0,'C',1);
        $this->pdf->Cell($sub[8],6, $encomenda['total_sem_iva']." ",1,0,'C',1);
        if($encomenda["taxas"]!=0){
            $this->pdf->Ln();
            $this->pdf->SetX(135);
            $this->pdf->Cell($sub[6],6,"Taxas Extra:",0,0,'R',$fill);
            $this->pdf->Cell($sub[7],6, " ",1,0,'C',1);
            $this->pdf->Cell($sub[8],6,$encomenda['taxas'],1,0,'C',1);
        }
        $this->pdf->Ln();
        $this->pdf->SetX(135);
        if($encomenda["taxas"]!=0){
            $this->pdf->Cell($sub[6],6,"Total + Eco + Iva + Taxas:",0,0,'R',$fill);
            $this->pdf->Cell($sub[7],6, $qtd,1,0,'C',1);
            $taxascomiva = $encomenda["taxas"]*(1+($encomenda["iva"]*0.01));
            $this->pdf->Cell($sub[8],6,$encomenda['total_sem_iva']+$eco+$iva+$taxascomiva." ",1,0,'C',1);
        }else{
            $this->pdf->Cell($sub[6],6,"Total + Eco + Iva:",0,0,'R',$fill);
            $this->pdf->Cell($sub[7],6, $qtd,1,0,'C',1);
            $this->pdf->Cell($sub[8],6,$encomenda['total_sem_iva']+$eco+$iva." ",1,0,'C',1);
        }
        if(!empty($encomenda["comentario"])){

            $this->pdf->Ln(15);
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->SetX(13);
            $this->pdf->Cell(0,5,utf8_decode("Comentário"),'B',1,'L',$fill);
            $this->pdf->SetFont('Arial','',8);
            $this->pdf->SetX(13);
            $this->pdf->MultiCell(0,5,utf8_decode($encomenda["comentario"]));
        }

        $this->watermark("Obrigado pela preferência!");
    }

    // Page footer
    public function Footer(){
        // Position at 1.5 cm from bottom
        $this->pdf->SetY(265);
        // Arial italic 8
        $this->pdf->SetFont('Arial','I',8);
        // Page number
        $this->pdf->Cell(0,10,utf8_decode('Página ').$this->pdf->PageNo().'/{nb}',0,1,'C');
    }
}

?>