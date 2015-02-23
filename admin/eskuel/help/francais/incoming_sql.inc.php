<?php
$help = '<FONT size=+0><B>Insertion dump</B></FONT>
	<BR><BR>Sur cette page, vous pouvez restaurer vos donn�es sauvegard�es pr�c�demment.
	<BR>Vous avez plusieurs possibilit�s :
	<BR><BR>
	<B>1 - Insertion depuis le FTP :</B>
	<BR>Vous pouvez d�poser vos fichiers de sauvegarde dans le r�pertoire <B>incoming</B> situ� dans le r�pertoire
	o� vous avez install� eskuel. Les fichiers stock�s � cet emplacement doivent �tre termin�s par "<B>.sql</B>" pour �tre
	pris en compte.
	<BR>A noter que cette m�thode peut �tre plus pratique lorsque votre h�bergeur vous interdit l\'upload de fichier.
	<BR><BR>
	<B>2 - Insertion depuis une fichier CSV :</B>
	<BR>Si vous pouvez uploader des fichiers via votre navigateur <B>et que vous avez s�lectionn� une table</B>, vous avez alors
	la possibilit� de compl�ter une table avec un fichier CSV (Champs S�par�s par des Virgules).
	<BR>Plusieurs options s\'offrent � vous :
	<BR>- Remplacer les donn�es de la table en cours avec les donn�es de votre fichier CSV
	<BR>- Sp�cifier le s�parateur de champs, c\'est � dire le caract�re qui d�limite chaque champ (par d�faut, le point-virgule)
	<BR>- Sp�cifier le caract�re qui entoure les champs
	<BR>- Indiquer le caract�re qui enl�ve (d�sp�cialise) les caract�res sp�ciaux
	<BR>- Sp�cifier le(s) caract�re(s) qui termine(nt) chaque ligne de votre fichier CSV
	<BR>- Sp�cifier les nom des colonnes que vous souhaitez mettre � jour, en les s�parant par des <B>virgules</B>
	<BR><BR>
	<B>3 - Insertion depuis un fichier SQL :</B>
	<BR>Vous pouvez reconstituer une table avec vos sauvegardes <B>MySQL</B> avec cette methode. S�lectionner juste le fichier sur votre disque dur, 
	et eskuel se chargera de l\'uploader et de l\'ins�rer dans la base en cours. 
	<BR><font color="#FF0000">Attention, vous ne pourrez effectuer cette op�ration que si
	votre h�bergeur autorise l\'upload de fichier</font>.
	';
?>