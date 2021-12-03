# 1 - se connecter en ssh
ssh -X sandra.pijeaud@ssh1.pgip.universite-paris-saclay.fr
ssh -X benjamin.vacus@ssh1.pgip.universite-paris-saclay.fr
#mot de passe de session saclay

# 2 - déplacer les fichiers dont vous avez besoin pour le tp sur public_html en ssh
# demo_php_postgres c'est le fichier à dl et dezip sur ecampus 
scp -r /path-where-this-file-is/demo_php_postgres sandra.pijeaud@ssh1.pgip.universite-paris-saclay.fr:$~//path-where-this-directory-is/public_html
scp -r /path-where-this-file-is/demo_php_postgres benjamin.vacus@ssh1.pgip.universite-paris-saclay.fr:$~//path-where-this-directory-is/public_html

# -r pour transférer tout un directory, à enlever si on veut déplacer un fichier

# 3 - lien pour vérifier depuis un navigateur que tout est là
https://ssh1.pgip.universite-paris-saclay.fr/~spijeau/
https://ssh1.pgip.universite-paris-saclay.fr/~bvacus/

# sur le public_html navigateur, vous ne verrez que les fichiers html et php mais le reste des fichiers est bien là
# allons trouver ces fichiers depuis le terminal

# 4 - modifier des fichiers depuis le terminal
cd public_html
cd demo_php_postgres
vim test1.ph
# ->> mettre votre login à la place de [Login_court]
# sortir de vim = echap + :wq
vim .tp_bd_postgres_info.ini
# ->> mettre votre login à la place de [Login_court]
# le piège était là, ce fichier était pas visible

# 5 - Préparer la BD

# démarrer psql
psql -h tp-postgres -U spijeau_a
#mdp : spijeau_a
psql -h tp-postgres -U bvacus_a
#mdp : bvacus_a

# charger les tables et les instances
\i /home/tp-home006/sbellab/public_html/demo_php_postgres/sql/createTables.sql
\i /home/tp-home006/sbellab/public_html/demo_php_postgres/sql/insertTables.sql
# sortir de postgres avec ctrl + D ou \q

# 6 - Le moment de vérité
https://ssh1.pgip.universite-paris-saclay.fr/~spijeau/
https://ssh1.pgip.universite-paris-saclay.fr/~bvacus/

# aller dans demo_php_postgres
# ouvrir chacuun des formulaires et faire une recherche des noms TUTU ou TITI (en all caps, c'est comme ça qu'ils sont entrés dans la base)
# si tout se passe bien vous devriez être redirigés sur une page qui donnent des infos sur les journaux et jours associés à cette personne
# fin.
