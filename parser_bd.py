## CDS
##>AAG54301 cds chromosome:ASM666v1:Chromosome:190:273:1 gene:Z0001 gene_biotype:protein_coding transcript_biotype:protein_coding gene_symbol:thrL description:thr operon leader peptide
#ATGAAACGCATTAGCACCACCATTACCACCACCATCACCACCACCATCACCATTACCATT
#ACCACAGGTAACGGTGCGGGCTGA

## peptide
#>AAN78502 pep chromosome:ASM744v1:Chromosome:534:911:1 gene:c0002 transcript:AAN78502 gene_biotype:protein_coding transcript_biotype:protein_coding description:Hypothetical protein
#MFYREKRRAIGCILRKLCEWKSVRILEAECCADHIHMLVEIPPKMSVSGFMGYLKGKSSL
#MPYEQFGDLKFKYRNREFWCRGYYVDTVGKNTAKIQDYIKHQLEEDKMGEQLSIPYPGSP
#FTGRK

## pas annoté
## >Chromosome dna:chromosome chromosome:ASM744v1:Chromosome:1:5231428:1 REF

#def read_files (directory):
# for file in os.listdir(directory):
#     if file.endswith(".fa"):
#         f = open(file, "r")
#         lines = f.readlines()
#         f.close()

with open("Escherichia_coli_cft073_cds.fa") as f:
    lines = f.readlines()
    f.close()

with open('instances_test.sql', 'a') as file:
    file.write('Begin transaction;'+"\n"+"\n")

    attributs = []
    for line in lines:
        word_list = line.split()
        if word_list[0].split(">")[0] == '>':
            chromosome = word_list[0].split(">")[1]
            gene_id = word_list[3].split(":")[1]
            genome_id = word_list[2].split(":")[1]
            start_seq = word_list[2].split(":")[3]
            end_seq = word_list[2].split(":")[4]
            gene_biotype = word_list[4].split(":")[1]
            transcript_biotype = word_list[5].split(":")[1]
            gene_symbol = word_list[6].split(":")[1]
            description = word_list[7].split(":")[1] + word_list[8] ###AJOUTER TOUS LES MOTS DE LA FIN
        else:
            gene_seq = []
            seq = line.replace('\n', '')
            gene_seq.append(seq)
            
            attributs.append(gene_id)
        file.write('INSERT INTO gene'+"\n")
        file.write('VALUES (' + "\n" +"\t"+ ';)' + "\n")
        file.write("%s = %s\n" %("essai", attributs))


        # file.write('INSERT INTO gene'+"\n")
        # file.write('VALUES (' + "\n" +"\t"+ ';)' + "\n")
        #file.write("%s = %s\n" %("essai", gene_id))
        #print(genone_id)
        file.write("commit;" + "\n" + "end transaction;")
        file.close()
# with open('instances_test.sql', 'w') as file:
#     file.write('Begin transaction;'+"\n")

    # file.write('INSERT INTO gene'+"\n")
    # file.write('VALUES (' +"\n")
    #file.write(gene_id)

    # file.write("%s = %s\n" %("essai", gene_id))
    #
    # file.write('test')
    # file.close()
