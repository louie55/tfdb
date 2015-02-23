<?php
$help = '<FONT size=+0><B>Sauvegarder une table</B></FONT>
	<BR><BR>
	Ici, vous pouvez sauvegarder votre (ou vos) table(s).
	<BR>Choisissez d\'abord le type de sauvegarde que vous souhaitez effectuer :
	<BR>- Sauvegarde SQL
	<BR>- Sauvegarde CSV
	<BR><BR>
	La sauvegarde SQL vous permet de sauvegarder vos donn�es de votre table afin de la restaurer
	plus tard. Vous pouvez effectuer diff�rents types de sauvegarde : la <B>structure seule</B> qui 
	sauvegardera uniquement l\'architecture de votre table,
	<B>structure + donn�es</B> qui sauvegardera la structure mais aussi toutes les donn�es de votre 
	table et enfin les <B>donn�es seules</B>.
	<BR>
	Optionnellement, vous pouvez demander de rajouter une ligne "<B>DROP TABLE</B>" qui vous permettra
	de restaurer votre table m�me si une table de ce nom existe d�j�.
	<BR>Vous pouvez �galement demander d\'avoir des insertions compl�tes.
	<BR>De plus, vous pouvez sp�cifier une requ�te SQL pour r�cup�rer uniquement les enregistrements
	qui vous int�ressent.
	<BR>Si vos sauvegardes sont trop <B>importantes</B>, vous pouvez d�cider de d�couper votre sauvegarde
	en plusieurs petites sauvegardes (fa�on archive ZIP). Taper uniquement le nombre de requ�te par 
	petites sauvegarde, et le tour est jou� !
	<BR><BR>
	La sauvegarde CSV vous permet essentiellement de r�cup�rer vos donn�es dans un format exploitable
	par <B>Microsoft Excel</B>.
	<BR>Sp�cifiez le caract�re qui s�parera les champs (par d�faut, le point-virgule), ainsi que le 
	caract�re qui marquera la fin d\'une ligne (le \n).
	<BR><BR>
	Enfin, choisissez si vous voulez <B>copier-coller</B> le r�sultat ou si votre navigateur vous propose de 
	le <B>sauvegarder</B>.';
?>