#01_ligprep.sh
#zjxu@simm.ac.cn 7/2/2015
#Function: Generate 3D structure with ligrep module by including tautomeric, stereochemical, and ionization variations, as well as energy minimization and flexible filters to generate fully customized ligand libraries that are optimized for further computational analyses.
#Usage: 01_ligprep.sh -l ligand.mol2 -o ligprep-ligand.mae 
#update: 7/2/2015


USAGE()
{
        echo "-------------------------------------------------------"
        echo "Function: Generate 3D structure with ligrep module by including tautomeric, stereochemical, and ionization variations, as well as energy minimization and flexible filters to generate fully customized ligand libraries that are optimized for further computational analyses."
        echo "./01_ligprep.sh  -l [ligand.suffix, the suffix should be mol2/sdf/mae] -o [output.suffix, the suffix should be mol2/sdf/mae]"
        echo ""
        echo "for example:"
        echo "./01_ligprep.sh -l ligand.mol2 -o ligprep-ligand.mae "
        echo ""
        echo "-------------------------------------------------------"

}

program_state=0

while getopts l:o: OPT
do
        case $OPT in
                l) ligand_file=${OPTARG}
                   echo "input ligand file is ${ligand_file}"
                   program_state=1
                   ;;
                o) ligprep_out=${OPTARG}
                   echo "output ligprep file would be ${ligprep_out}"
                   ;;
                \?) USAGE
                   exit
                   ;;
        esac
done
if [ $program_state -eq 0 ]
  then USAGE; exit 1
fi

#--------------------------------------------------------------------------
# do some tests
if [ ! -f ${ligand_file} ]
 then
  echo "can't find file ${ligand_file}"
  USAGE
  exit
fi

#--------------------------------------------------------------------------

#the suffix would be mol2/sdf/mae
suffix_input=${ligand_file##*.} 
suffix_output=${ligprep_out##*.}

if [ "${suffix_input}" = "mol2" ]
  then mol2convert -imol2 ${ligand_file} -omae ${ligand_file%.mol2}.mae
elif [ "${suffix_input}" = "sdf" ]
  then sdconvert -isd ${ligand_file} -omae ${ligand_file%.sdf}.mae
elif [ "${suffix_input}" = "mae" ]
  then continue
else
  echo "the input file should be in mol2/sdf/mae format"
  USAGE
  exit
fi

#run ligpre module.
ligprep -WAIT -epik -ph 7.0 -pht 2.0 -nd -bff 14 -s 32 -r 1 -imae ${ligand_file%.*}.mae -omae ${ligprep_out%.*}.mae

if [ ! -f ${ligprep_out%.*}.mae ]
  then echo "ligrep module failed"
       echo "${ligand_file} could not generate proper optimized 3D structure"
       exit
fi

if [ "${suffix_output}" = "mol2" ]
  then mol2convert -imae ${ligprep_out%.*}.mae -omol2 ${ligprep_out}
elif [ "${suffix_output}" = "sdf" ]
  then sdconvert -imae ${ligprep_out%.*}.mae -osd ${ligprep_out}
elif [ "${suffix_output}" = "mae" ]
  then cp ${ligprep_out%.*}.mae ${ligprep_out}
else
  echo "the output file should be in mol2/sdf/mae format"
  USAGE
  exit
fi
