<?php
$help = '<FONT size=+0><B>Changer le type d\'une table</B></FONT>
	<BR><BR>
	Vous permet de changer le type de la table en cours.
	<BR>Les diff�rents type pr�sent�s varient en fonction de la configuration de votre serveur.
	<BR>Les diff�rents types sont :
	<BR>
	<B>1 - BDB (Berkeley_DB)</B>
	<BR>Berkeley DB a fourni � MySQL un syst�me de gestion des transactions : il r�siste aux crash et autorise les 
	commandes COMMIT et ROLLBACK avec les transactions. 
	<BR><BR>
	<B>2 - HEAP</B>
	<BR>Les tables HEAP utilisent des index sous forme de tables de hashage, et sont stock�es en m�moire. 
	Cela les rend tr�s rapides, mais si MySQL crashe, vous perdrez toutes les donn�es. Les tables HEAP sont tr�s pratiques pour les tables 
	temporaires. Les tables internes � MySQL utilisent des tables HEAP avec un hashage dynamique � 100%.
	<BR><BR>
	<B>3 - ISAM</B>
	<BR>Vous pouvez toujours utiliser le format de table obsol�te ISAM. Il disparaitra prochainement car MyISAM est une bien meilleure impl�mentation. ISAM utilise un index B-tree.
	<BR><BR>
	<B>4 - InnoDB</B>
	<BR>InnoDB fournit � MySQL une gestion de table avec des commit, rollback, et des possibilit�s de r�cup�ration
	an cas de crash. 
	<BR><BR>
	<B>5 - MERGE (MRG_MyISAM)</B>
	<BR>Une table MERGE est une collection de tables MyISAM identiques, qui sont utilis�es comme une seule table. Vous ne pouvez alors utiliser que des commandes SELECT, DELETE, et UPDATE sur ces ensembles de tables. 
	<BR><BR>
	<B>6 - MyISAM</B>
	<BR>MyISAM est le type de table par d�faut de MySQL Version 3.23. Il est bas� sur le code ISAM et propose de nombreuses fonctionnalit�s pratiques.';
	
?>