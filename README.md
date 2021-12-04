# Web Project

This file describes how to initialize and use the website created for the web project course at AMI2B.  

## Clone Git in public_html

TODO

## Initialize database

First, one must connect to the database. To do so, go to the root of the project and set *username* and *password*. Use command:
```
vim website/.projetWEB.ini
```
Replace `[login_court]` with your short login. For example, *Michel Arsouin* should replace it with `marsouin`.  
Save the editing with `Esc` followed by command `:x`.  
To then initialize the database, use command:
```
./reset_database.sh
```

## Add new genome into the database

To add a new genome, first put the three corresponding *fasta* files in the `database` folder:
```
[new_genome].fa #contains the whole genome sequence
[new_genome]_cds.fa #contains gene sequences
[new_genome]_pep.fa #contains the protein sequences
```
Then, go back to the root folder, and use command:
```
./add_genome.sh [genome_name]
```
Example:
```
./add_genome.sh Escherichia_coli_cft073
```

