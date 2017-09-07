#! /usr/bin/env bash

USAGE()
{
        echo "-------------------------------------------------------"
        echo "./03_glide_docking.sh -l [ligand_file]"
        echo ""
        echo "for example:"
        echo "./03_glide_docking.sh -l absolute_path/ligand.mae"
        echo ""
        echo "-------------------------------------------------------"

}

program_state=0

while getopts l: OPT
do
        case $OPT in
                l) ligand_file=${OPTARG}
                   echo "ligand_file is ${ligand_file}"
                   program_state=1
                   ;;
                \?) USAGE
                   exit
                   ;;
        esac
done
if [ $program_state -eq 0 ]
then USAGE; exit 1
fi

echo "POSES_PER_LIG 5" >> glide.in
echo "POSTDOCK_NPOSE 100" >> glide.in
echo "DOCKING_METHOD confgen" >> glide.in
echo "PRECISION SP" >> glide.in
echo "GRIDFILE /home/gmwang/alhuang/commandline/glide-grid_4lde.zip" >> glide.in
echo "LIGANDFILE ${ligand_file}" >> glide.in
echo "WRITEREPT true" >> glide.in
echo "NREPORT 100" >> glide.in

jobid=`glide glide.in | awk '{print $2}'`
echo "${jobid} begin docking"
jobcontrol -wait ${jobid}
python glide_output_properties.py -g no -t glide.txt -i glide_pv.maegz -o docking.sdf
