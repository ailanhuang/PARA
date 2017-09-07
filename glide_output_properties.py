#file name   :  glide_output_properties.py
#Function    : output properties, e. g. docking properties, ligand effiecncy, ... from the mmgbsa
#file needed : *mmgbsa-out.mae
#
#Usage       : run glide_output_properties.py -g yes -t out.txt -i 3G51_MODEL_1_RSK2_mmgbsa-out.mae -o aa.mae
#
#Author      : zjxu@mail.shcnc.ac.cn
#Date        : 3/18/2012
#Update      : (1) modify r_i_glide_rmsd_to_input to r_i_glide_rmsd to calculate the RMSD to the reference compound. (2) Does not output molecular title.
#Update      : 7/20/2015 remove r_i_glide_rmsd



import sys
def main(argv):
  from schrodinger import structure
  #from schrodinger.maestro import maestro
  #from schrodinger import project
  import getopt, sys
  import os
  
  if (len(sys.argv) != 9):
    print 'Usage: run glide_output_properties.py -g yes -t out.txt -i 3G51_MODEL_1_RSK2_mmgbsa-out.mae -o aa.mae'
    sys.exit()

  try:
    opts, args = getopt.getopt(sys.argv[1:], "g:t:i:o:")
  except getopt.GetoptError:
    print 'Usage: run glide_output_properties.py -g yes -t out.txt -i 3G51_MODEL_1_RSK2_mmgbsa-out.mae -o aa.mae'
   
    print 'run glide_output_properties.py -g yes -t out.txt -i 3G51_MODEL_1_RSK2_mmgbsa-out.mae -o aa.mae'
    sys.exit()

  for opt, value in opts:
    if opt == "-g":
      gbsa = value
    elif opt == "-t":
      out_txt = value
    elif opt == "-i":
      in_file = value
    elif opt == "-o":
      out_file = value
  #print "rank: ", rank
  #print "in_file: ", in_file
  #print "out_file: ", out_file
  if os.path.exists(out_file):
    os.remove(out_file)
  #pt = maestro.project_table_get()
  # Loop over the selected entries
  #for irow in xrange(1,len(pt)+1):
   # print irow.split('\t')[0]
    # Do something for all entries using pt[irow]
  myfile = open(out_txt, 'w')
  for (ix, st) in enumerate(structure.StructureReader(in_file)):
    #if ix == 0:
      #st.append(out_file)
    if (ix > 0):
      st.append(out_file)
      #print "title: ", st.property['s_m_title'], "\t", "glide_gscore: ", st.property['r_i_glide_gscore'], "\t", "glide_ligand_efficiency: ", st.property['r_i_glide_ligand_efficiency'], "\t", "MMGBSA_DG_bind: ", st.property['r_psp_Prime_MMGBSA_DG_bind'] 
      if gbsa == "yes":
        #print st.property['s_m_title'], "\t", st.property['r_i_glide_gscore'], "\t", st.property['r_i_glide_ligand_efficiency'], "\t", st.property['r_psp_Prime_MMGBSA_DG_bind'] 
        myfile.write( str(st.property['s_m_title']) + "\t" + str(st.property['r_i_glide_gscore']) + "\t" + str(st.property['r_i_glide_ligand_efficiency']) + "\t" + str(st.property['r_psp_Prime_MMGBSA_DG_bind']) + "\n" )
      elif gbsa == "no":
        #print st.property['s_m_title'], "\t", st.property['r_i_glide_gscore'], "\t", st.property['r_i_glide_ligand_efficiency']
        #myfile.write( str(st.property['s_m_title']) + "\t" + str(st.property['r_i_glide_gscore']) + "\t" + str(st.property['r_i_glide_ligand_efficiency']) + "\t" + str(st.property['r_i_glide_rmsd_to_input']) + "\n" )
        #myfile.write( str(st.property['r_i_glide_gscore']) + "\t" + str(st.property['r_i_glide_rmsd']) + "\n" )
        #print str(st.property['r_i_glide_gscore']), "\t", str(st.property['r_i_glide_rmsd'])
        #print str(st.property['r_i_glide_gscore'])
        myfile.write(str(st.property['r_i_glide_gscore'])+ "\n" )
      else:
        print "Warning: you should set -g yes or no. Here, the option no was used"
        myfile.write( str(st.property['s_m_title']) + "\t" + str(st.property['r_i_glide_gscore']) + "\t" + str(st.property['r_i_glide_ligand_efficiency']) + "\n" )


    #elif (ix > 0):
     # if st.property['r_i_glide_gscore'] < gscore:
      #if st.property['s_m_title'] == "4720_01.mol2":
       # st.append(out_file)
  myfile.close()

if __name__ == "__main__":
  main(sys.argv[1:])
