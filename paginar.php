<?php

//Copyright (C) 2000-2006  Antonio Grandío Botella http://www.antoniograndio.com
//Copyright (C) 2000-2006  Inmaculada Echarri San Adrián http://www.inmaecharri.com

//This file is part of Catwin.

//CatWin is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.

//CatWin is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details:
//http://www.gnu.org/copyleft/gpl.html

//You should have received a copy of the GNU General Public License
//along with Catwin Net; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

//$resul: total de Asientos   $conta: Asiento actual+1  $num: Asientos por pg, $que: Asiento...
function pagina ($resul, $conta, $num, $que, $ord) {

	echo "<div class='noimpri' style='font-size:80%'><span class = 'verdeb'>P&aacute;gina</span>";
	$pag = ceil($resul/$num);
	$pagactu = ceil($conta/$num);

	$i = $pagactu - 5;

	if ($i > 1) {echo " <A class='btn btn-default' HREF=?conta=1&ord=$ord><<</a> <a href='?conta=".($conta-$num)."&ord=$ord'><</a>";}


	while ($i < $pagactu) {

		if ($i > 0) {echo " <a href=?conta=".((($i-1)*$num)+1)."&ord=$ord>$i</a>";}
		$i++;

	}

	$i++;

	echo " [<span class = 'verdeb'>$pagactu</span>]";

	$n = $pagactu + 7;

	while ($i < $n) {

		if ($i <= $pag) {echo " <a href=?conta=".((($i-1)*$num)+1)."&ord=$ord>$i</a> ";}
		$i++;

	}

	if ($i <= $pag) {
		echo "<a href='?conta=".($conta+$num)."&ord=$ord'>></a> <a href='?conta=".((((int)($resul/$num))*$num)+1)."&ord=$ord'>>></a>";
	}

	echo " de ".ceil($resul/$num)." (".$resul." $que)</div>";

	return $pagactu == ceil($resul/$num);

}
