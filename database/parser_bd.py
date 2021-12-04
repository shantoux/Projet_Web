#imports
import sys
import os
import time

# check that repository and passed args verify demanded conditions
def check_rep(args):
    if len(args) != 1:
        return (False, "\nPlease pass the name of the genome file as argument.\nE.g.: python parser_bd.py new_coli.fa")
    genome_file_name = args[0]
    if genome_file_name.split(".")[-1] != "fa":
        return(False, "\nPlease provide genome as a fasta file.\nE.g.: python parser_bd.py new_coli.fa")
    genome_name = genome_file_name[:-3]
    for r, d, f in os.walk(".", topdown=False):
        files = f
    if (not genome_file_name in files) or (not genome_name + "_cds.fa" in files) or (not genome_name + "_pep.fa" in files):
        return(False, "\nPlease make sure " + genome_file_name + ", " + genome_name + "_cds.fa, " + genome_name + "_pep.fa all are in the same repository as the python script.\nE.g.: python parser_bd.py new_coli.fa")
    else:
        return(True, "\nAll files - ok.")

# check whether provided genome is annotated or not
def is_annotated(genome_id):
    with open(genome_id + "_cds.fa") as f:
        line = f.readline()
        f.close()
    return len(line.split(" ")) > 3

# Reading file to parse
def open_file(FILE_NAME):
    with open(FILE_NAME) as f:
        lines = f.readlines()
        f.close()
    return(lines)

# parse genome file
def parse_genome(genome_id, file):
    # read all lines except header
    lines = open_file(genome_id + ".fa")[1:]
    lines = [line.strip() for line in lines]
    genome_seq = ""
    for line in lines:
        genome_seq += line
    file.write("Begin transaction;\n\n")
    file.write("INSERT INTO genome (genome_id, genome_seq) VALUES (\'" + genome_id + "\', \'" + genome_seq + "\');\n")
    file.write("\ncommit;\nend transaction;\n")
    return

# parse genes file
def parse_genes(genome_id, annotated, file):
    lines = open_file(genome_id + "_cds.fa")
    lines = [line.strip() for line in lines]
    file.write('\nBegin transaction;'+"\n"+"\n")
    first_gene = True

    # Running through all lines
    for line in lines:
        # check if new gene
        if line[0] == '>':
            # if not first gene, insert previous one
            if not first_gene:
                file.write(gene_text_to_write + "\');\n")
                if annotated:
                    file.write(annotation_text_to_write + "\');\n")
            first_gene = False
            # parse new gene infos
            gene_text_to_write = "INSERT INTO gene (sequence_id, genome_id, start_seq, end_seq, chromosome, gene_seq) VALUES ("
            word_list = line.split()
            # retrieve gene info
            gene_text_to_write += "\'" + word_list[0][1:] + "\', " # sequence_id (et non word_list[3].split(":")[1])
            gene_text_to_write += "\'" + genome_id + "\', " # genome_id
            gene_text_to_write += word_list[2].split(":")[3] + ", " # start_seq
            gene_text_to_write += word_list[2].split(":")[4] + ", " # end_seq
            gene_text_to_write += "\'" + word_list[2].split(":")[1] + "\', \'" # chromosome

            if annotated:
                annotation_text_to_write = "INSERT INTO annotations (genome_id, gene_id, sequence_id, gene_biotype, transcript_biotype, description, annotator, status, comments) VALUES ("
                annotation_text_to_write += "\'" + genome_id + "\', " # genome_id
                annotation_text_to_write += "\'" + word_list[3].split(":")[1] + "\', " # gene_id
                annotation_text_to_write += "\'" + word_list[0][1:] + "\', " # sequence_id
                annotation_text_to_write += "\'" + word_list[4].split(":")[1] + "\', " # gene_biotype
                annotation_text_to_write += "\'" + word_list[5].split(":")[1] + "\', " # transcript_biotype
                # check whether gene_symbol is there or not
                descr_index = 6
                if word_list[6].split(":")[0] == "gene_symbol":
                    annotation_text_to_write = annotation_text_to_write[:91] + " gene_symbol," + annotation_text_to_write[91:]
                    annotation_text_to_write += "\'" + word_list[6].split(":")[1].replace("\'","") + "\', " # gene_symbol
                    descr_index = 7
                annotation_text_to_write += "\'" + word_list[descr_index].split(":")[1].replace("\'"," ") # description
                for word in word_list[(descr_index+1):]: # ADD ALL WORDS AT THE END OF ANNOTATION IN THE DESCRIPTION
                    annotation_text_to_write += " " + word.replace("\'"," ")
                annotation_text_to_write += "\', "
                annotation_text_to_write += "\'" + "olivia@gmail.com" + "\', " # annotator
                annotation_text_to_write += "\'" + "validated" + "\', " # status
                annotation_text_to_write += "\'" + "Downloaded from ecampus examples data." # comments
        else:
            gene_text_to_write += line

    file.write(gene_text_to_write + "\');\n")
    if annotated:
        file.write(annotation_text_to_write + "\');\n")
    file.write("\ncommit;\nend transaction;\n")
    return

# parse proteins file
def parse_proteins(genome_id, file):
    lines = open_file(genome_id + "_pep.fa")
    lines = [line.strip() for line in lines]
    file.write('\nBegin transaction;'+"\n"+"\n")
    first_prot = True

    # Running through all lines
    for line in lines:
        # check if new gene
        if line[0] == '>':
            # if not first gene, insert previous one
            if not first_prot:
                file.write(prot_text_to_write + "\'\nWHERE sequence_id = \'" + seq_id + "\';\n")
            first_prot = False
            word_list = line.split()
            seq_id = word_list[0][1:]
            # parse new prot sequence
            prot_text_to_write = "UPDATE gene\nSET prot_seq = \'"
        else:
            prot_text_to_write += line

    file.write(prot_text_to_write + "\'\nWHERE sequence_id = \'" + seq_id + "\';\n")
    file.write("\ncommit;\nend transaction;\n")
    return

if __name__ == "__main__":
    # retrieve arguments
    args = sys.argv[1:]

    # control one argument has been passed, and all files are present
    is_ok, error_msg = check_rep(args)
    if not is_ok:
        print("ERROR:", error_msg)

    genome_id = args[0][:-3]
    # check whether the provided genome is annotated or not
    annotated = is_annotated(genome_id)
    
    file_name = "instances_" + genome_id + ".sql"
    with open(file_name, 'w') as file:
        print("\nParsing genome " + genome_id + "...")
        start_time = time.time()
        # parse genome
        parse_genome(genome_id, file)

        # parse gene sequences
        parse_genes(genome_id, annotated, file)

        # parse protein sequences
        parse_proteins(genome_id, file)

        end_time = time.time()
        parsing_time = end_time - start_time
        print("Parsing all 3 files required %0.2f" % parsing_time, "seconds.")
        file.close()

    print("\nthe end")
