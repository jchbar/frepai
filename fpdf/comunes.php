<?

    /*  
  
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	
	Autores: Pedro Obreg�n Mej�as
			 Rub�n D. Mancera Mor�n
	Versi�n: 1.0
	Fecha Liberaci�n del c�digo: 13/07/2004
	Galop�n para gnuLinEx 2004 -- Extremadura		 
	
	*/
class PDF extends FPDF
{
    //Cabecera de p�gina
    function Header()
    {
        //Logo
        $this->Image('fpdf/logo/logo.jpg',10,0,20);

     	$this->SetY(5);
     	$this->SetX(30);
    	$this->SetFont('Arial','B',11);
    	$this->Ln();   
        $this->Ln(10);	
    }

    //Pie de p�gina
    function Footer()
    {
        //Posici�n: a 1,5 cm del final
        $this->SetY(-21);
        //Arial italic 8
        $this->SetFont('Arial','',7);
        //N�mero de p�gina
        $this->Cell(0,10,'http://www.heros.com.ve -- E-Mail: juan.hernandez@heros.com.ve',0,0,'C');	
    	
    	//Posici�n: a 1,5 cm del final
        $this->SetY(-18);
        //Arial italic 8
        $this->SetFont('Arial','',7);
        //N�mero de p�gina
    //     $this->Cell(0,10,'Proyecto Galop�n Extremadura',0,0,'C');	
    	
        //Posici�n: a 1,5 cm del final
        $this->SetY(-10);
        //Arial italic 8
        $this->SetFont('Arial','',8);
        //N�mero de p�gina
        $this->Cell(0,10,'-- '.$this->PageNo().' --',0,0,'C');	
    //	}
    }
}
?>